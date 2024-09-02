<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Service;

use Combodo\iTop\Application\Helper\Session;
use Combodo\iTop\MFABase\Helper\MFABaseLog;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Dict;
use LoginTwigContext;
use MetaModel;
use MFAUserSettingsTOTPMail;
use User;
use utils;

class MFATOTPMailService
{
	private static MFATOTPMailService $oInstance;

	protected function __construct()
	{
	}

	final public static function GetInstance(): MFATOTPMailService
	{
		if (!isset(static::$oInstance)) {
			static::$oInstance = new static();
		}

		return static::$oInstance;
	}

	public function SendCodeByEmail(MFAUserSettingsTOTPMail $oMFAUserSettings)
	{
		$oTOTPService = new OTPService($oMFAUserSettings);
		$sCode = $oTOTPService->GetCode();

		$sEmail = $oMFAUserSettings->Get('email');

		// TODO Send the mail
		MFABaseLog::Error("Send MFA code", MFABaseLog::CHANNEL_DEFAULT, ['user_id' => $oMFAUserSettings->Get('user_id'), 'email' => $sEmail, 'code' => $sCode]);
	}

	public function OnBeforeCreate(MFAUserSettingsTOTPMail $oMFAUserSettings): void
	{
		// Initialize email with the user's contact email
		$sUserId = $oMFAUserSettings->Get('user_id');
		$oUser = MetaModel::GetObject(User::class, $sUserId, true, true);
		$sEmail = $oUser->Get('email');
		if (utils::IsNullOrEmptyString($sEmail)) {
			$sEmail = 'please.replace@combodo.com';
		}
		$oMFAUserSettings->Set('email', $sEmail);
	}

	public function OnBeforeUpdate(MFAUserSettingsTOTPMail $oMFAUserSettings): void
	{
		// If email changed invalidate settings (need verification again)
		if (in_array('email', $oMFAUserSettings->ListChanges())) {
			$oMFAUserSettings->Set('validated', 'no');
		}
	}

	public function GetTwigContextForConfiguration(MFAUserSettingsTOTPMail $oMFAUserSettings): LoginTwigContext
	{
		$oTOTPService = new OTPService($oMFAUserSettings);
		$aData = [];

		$aData['sTitle'] = Dict::S('MFATOTP:Mail:Config:Title');
		$aData['sLabel'] = $oTOTPService->sLabel;
		$aData['sIssuer'] = $oTOTPService->sIssuer;

		$oLoginContext = $this->ValidateCode($oMFAUserSettings, $aData);
		if (!is_null($oLoginContext)) {
			return $oLoginContext;
		}

		$this->SendCodeByEmail($oMFAUserSettings);

		$oLoginContext = new LoginTwigContext();
		$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
		$oLoginContext->AddBlockExtension('mfa_configuration', new \LoginBlockExtension('MFATOTPMailConfig.html.twig', $aData));
		$oLoginContext->AddBlockExtension('mfa_title', new \LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
		$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

		return $oLoginContext;
	}

	public function GetTwigContextForLoginValidation(MFAUserSettingsTOTPMail $oMFAUserSettings): LoginTwigContext
	{
		/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
		$oTOTPService = new OTPService($oMFAUserSettings);

		$aData = [];
		$aData['sTitle'] = Dict::S('MFATOTP:Mail:Validation:Title');
		$aData['sLabel'] = $oTOTPService->sLabel;
		$aData['sIssuer'] = $oTOTPService->sIssuer;

		$oLoginContext = $this->ValidateCode($oMFAUserSettings, $aData);
		if (!is_null($oLoginContext)) {
			return $oLoginContext;
		}

		$this->SendCodeByEmail($oMFAUserSettings);

		$oLoginContext = new LoginTwigContext();
		$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
		$oLoginContext->AddBlockExtension('mfa_validation', new \LoginBlockExtension('MFATOTPMailValidate.html.twig', $aData));
		$oLoginContext->AddBlockExtension('mfa_title', new \LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
		$oLoginContext->AddBlockExtension('script', new \LoginBlockExtension('MFATOTPMailValidate.ready.js.twig', $aData));
		$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

		return $oLoginContext;

	}

	private function ValidateCode(MFAUserSettingsTOTPMail $oMFAUserSettings, array &$aData): ?LoginTwigContext
	{
		$sRet = MFATOTPService::GetInstance()->ValidateCode($oMFAUserSettings);
		switch ($sRet) {
			case MFATOTPService::WRONG_CODE:
				$aData['sError'] = Dict::S('MFATOTP:NotValidated');
				break;

			case MFATOTPService::CODE_OK:
				$oMFAUserSettings->Set('validated', 'yes');
				$oMFAUserSettings->AllowWrite();
				$oMFAUserSettings->DBUpdate();

				Session::Set('mfa-configuration-validated', 'true');
				$aData['sURL'] = utils::GetAbsoluteUrlAppRoot();
				$aData['sTitle'] = Dict::S('MFATOTP:Redirection:Title');
				$oLoginContext = new LoginTwigContext();
				$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
				$oLoginContext->AddBlockExtension('mfa_title', new \LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
				$oLoginContext->AddBlockExtension('script', new \LoginBlockExtension('MFATOTPRedirect.ready.js.twig', $aData));

				return $oLoginContext;
		}

		return null;
	}
}