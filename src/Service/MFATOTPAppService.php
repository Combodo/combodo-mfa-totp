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
use MFAUserSettingsTOTPApp;
use utils;


class MFATOTPAppService
{
	private static MFATOTPAppService $oInstance;

	protected function __construct()
	{
		MFABaseLog::Enable();
	}

	final public static function GetInstance(): MFATOTPAppService
	{
		if (!isset(static::$oInstance)) {
			static::$oInstance = new static();
		}

		return static::$oInstance;
	}

	public function GetTwigContextForConfiguration(MFAUserSettingsTOTPApp $oMFAUserSettings): LoginTwigContext
	{
		$oTOTPService = new OTPService($oMFAUserSettings);
		$aData = [];

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

		$aData['sTitle'] = Dict::S('MFATOTP:App:Config:Title');
		$aData['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
		$aData['sLabel'] = $oTOTPService->sLabel;
		$aData['sIssuer'] = $oTOTPService->sIssuer;
		$aData['sSecret'] = $oTOTPService->GetSecret();

		$oLoginContext = new LoginTwigContext();
		$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
		$oLoginContext->AddBlockExtension('mfa_configuration', new \LoginBlockExtension('MFATOTPAppConfig.html.twig', $aData));
		$oLoginContext->AddBlockExtension('mfa_title', new \LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
		$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

		return $oLoginContext;
	}

	public function GetTwigContextForLoginValidation(MFAUserSettingsTOTPApp $oMFAUserSettings): LoginTwigContext
	{
		$oLoginContext = new LoginTwigContext();
		/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
		$oTOTPService = new OTPService($oMFAUserSettings);

		$aData = [];
		$aData['sTitle'] = Dict::S('MFATOTP:App:Validation:Title');
		$aData['sLabel'] = $oTOTPService->sLabel;
		$aData['sIssuer'] = $oTOTPService->sIssuer;

		$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
		$oLoginContext->AddBlockExtension('mfa_validation', new \LoginBlockExtension('MFATOTPAppValidate.html.twig', $aData));
		$oLoginContext->AddBlockExtension('mfa_title', new \LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
		$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

		return $oLoginContext;

	}

}