<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Service;

use Combodo\iTop\Application\Helper\Session;
use Combodo\iTop\MFABase\Helper\MFABaseException;
use Combodo\iTop\MFABase\Helper\MFABaseHelper;
use Combodo\iTop\MFABase\Helper\MFABaseLog;
use Combodo\iTop\MFABase\Service\MFABaseLoginService;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Dict;
use Exception;
use LoginBlockExtension;
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

	/**
	 * @param \MFAUserSettingsTOTPApp $oMFAUserSettings
	 *
	 * @return \LoginTwigContext
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	public function GetTwigContextForConfiguration(MFAUserSettingsTOTPApp $oMFAUserSettings): LoginTwigContext
	{
		$aData = [];
		try {
			$oTOTPService = new OTPService($oMFAUserSettings);

			$sRet = MFATOTPService::GetInstance()->ValidateCode($oMFAUserSettings);
			switch ($sRet) {
				case MFATOTPService::WRONG_CODE:
					$aData['sError'] = Dict::S('MFATOTP:App:NotValidated');
					break;

				case MFATOTPService::CODE_OK:
					$oMFAUserSettings->Set('validated', 'yes');
					$oMFAUserSettings->Set('is_default', 'yes');
					$oMFAUserSettings->AllowWrite();
					$oMFAUserSettings->DBUpdate();

					Session::Set('mfa_configuration_validated', 'true');
					$aData['sURL'] = utils::GetCurrentAbsoluteUrl();
					$aData['sTitle'] = Dict::S('MFATOTP:Redirection:Title');
					$oLoginContext = new LoginTwigContext();
					$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
					$oLoginContext->AddBlockExtension('mfa_title', new LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
					$oLoginContext->AddBlockExtension('script', new LoginBlockExtension('MFATOTPRedirect.ready.js.twig', $aData));

					return $oLoginContext;

				case MFATOTPService::NO_CODE:
					break;
			}

			$aData['sTitle'] = Dict::S('MFATOTP:App:Config:Title');
			$aData['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
			$aData['sLabel'] = $oTOTPService->sLabel;
			$aData['sIssuer'] = $oTOTPService->sIssuer;
			$aData['sSecret'] = $oTOTPService->GetSecret();
			MFABaseHelper::GetInstance()->PassPostedParams($aData);
			$aData['sTransactionId'] = utils::GetNewTransactionId();
		} catch (MFABaseException $e) {
			$aData['sError'] = Dict::S('MFATOTP:App:Configuration:Error');
		} catch (Exception $e) {
			$aData['sError'] = Dict::S('MFATOTP:App:Configuration:Error');
			MFABaseLog::Error(__FUNCTION__.' MFA Configuration failed', null, ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
		}

		try {
			$oLoginContext = new LoginTwigContext();
			$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
			$oLoginContext->AddBlockExtension('mfa_configuration', new LoginBlockExtension('MFATOTPAppConfig.html.twig', $aData));
			$oLoginContext->AddBlockExtension('mfa_title', new LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
			$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

			return $oLoginContext;
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}
	}

	/**
	 * @param \MFAUserSettingsTOTPApp $oMFAUserSettings
	 *
	 * @return \LoginTwigContext
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	public function GetTwigContextForLoginValidation(MFAUserSettingsTOTPApp $oMFAUserSettings): LoginTwigContext
	{
		$aData = [];
		try {
			/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
			$oTOTPService = new OTPService($oMFAUserSettings);

			$aData['sTitle'] = Dict::S('Login:MFA:Validation:Title');
			$aData['sLabel'] = $oTOTPService->sLabel;
			$aData['sIssuer'] = $oTOTPService->sIssuer;
			$aData['sTransactionId'] = utils::GetNewTransactionId();
			MFABaseHelper::GetInstance()->PassPostedParams($aData);
			if (Session::IsSet(MFABaseLoginService::MFA_LOGIN_VALIDATION_ERROR)) {
				$aData['sError'] = Dict::S('Login:MFA:Validation:Error');
			}
		} catch (MFABaseException $e) {
			$aData['sError'] = Dict::S('MFATOTP:App:Validation:Error');
		} catch (Exception $e) {
			$aData['sError'] = Dict::S('MFATOTP:App:Validation:Error');
			MFABaseLog::Info(__FUNCTION__.' MFA Configuration failed', null, ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
		}

		try {
			$oLoginContext = new LoginTwigContext();
			$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
			$oLoginContext->AddBlockExtension('mfa_validation', new LoginBlockExtension('MFATOTPAppValidate.html.twig', $aData));
			$oLoginContext->AddBlockExtension('mfa_title', new LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
			$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

			return $oLoginContext;
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}
	}

}
