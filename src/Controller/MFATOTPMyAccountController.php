<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Controller;

use Combodo\iTop\Application\TwigBase\Controller\Controller;
use Combodo\iTop\MFABase\Helper\MFABaseException;
use Combodo\iTop\MFABase\Helper\MFABaseHelper;
use Combodo\iTop\MFABase\Helper\MFABaseLog;
use Combodo\iTop\MFABase\Service\MFAUserSettingsService;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Combodo\iTop\MFATotp\Service\MFATOTPMailService;
use Combodo\iTop\MFATotp\Service\MFATOTPService;
use Combodo\iTop\MFATotp\Service\OTPService;
use CoreCannotSaveObjectException;
use Dict;
use MFAUserSettingsTOTPApp;
use MFAUserSettingsTOTPMail;
use UserRights;
use utils;

class MFATOTPMyAccountController extends Controller
{
	public function OperationMFATOTPAppConfig()
	{
		$aParams = [];

		try {
			MFABaseHelper::GetInstance()->ValidateTransactionId();

			$sUserId = UserRights::GetUserId();
			/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
			$oMFAUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPApp::class);
			$aParams['oMFAUserSettings'] = $oMFAUserSettings;

			$oTOTPService = new OTPService($oMFAUserSettings);

			$aParams['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
			$aParams['sLabel'] = $oTOTPService->sLabel;
			$aParams['sTransactionId'] = utils::GetNewTransactionId();
			$aParams['sIssuer'] = $oTOTPService->sIssuer;
			$aParams['sSecret'] = $oMFAUserSettings->Get('secret');

			$sRet = MFATOTPService::GetInstance()->ValidateCode($oMFAUserSettings);
			switch ($sRet) {
				case MFATOTPService::NO_CODE:
					if ($oMFAUserSettings->Get('validated') === 'yes') {
						$aParams['sMessage'] = Dict::S('MFATOTP:Validated');
					}
					break;

				case MFATOTPService::WRONG_CODE:
					$aParams['sError'] = Dict::S('MFATOTP:NotValidated');
					break;

				case MFATOTPService::CODE_OK:
					$aParams['sMessage'] = Dict::S('MFATOTP:Validated');
					$oMFAUserSettings->Set('validated', 'yes');
					$oMFAUserSettings->AllowWrite();
					$oMFAUserSettings->DBUpdate();
					break;
			}
		} catch (MFABaseException $e) {
			$aParams['sError'] = Dict::S('MFATOTP:Error:ConfigurationFailed');
		}

		$this->AddSaas(MFATOTPHelper::GetSCSSFile());
		$this->AddLinkedScript(MFATOTPHelper::GetJSFile());
		$this->AddLinkedScript(MFABaseHelper::GetJSFile());

		$this->DisplayPage($aParams);
	}

	public function OperationMFATOTPMailConfig()
	{
		$aParams = [];
		try {
			MFABaseHelper::GetInstance()->ValidateTransactionId();

			$sUserId = UserRights::GetUserId();
			/** @var \MFAUserSettingsTOTPMail $oMFAUserSettings */
			$oMFAUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPMail::class);
			$aParams['oMFAUserSettings'] = $oMFAUserSettings;

			$sRet = MFATOTPService::GetInstance()->ValidateCode($oMFAUserSettings);
			switch ($sRet) {
				case MFATOTPService::NO_CODE:
					if ($oMFAUserSettings->Get('validated') === 'yes') {
						$aParams['sMessage'] = Dict::S('MFATOTP:Validated');
					} else {
						$aParams['sError'] = Dict::S('MFATOTP:NotValidated');
					}
					break;

				case MFATOTPService::WRONG_CODE:
					$aParams['sError'] = Dict::S('MFATOTP:NotValidated');
					break;

				case MFATOTPService::CODE_OK:
					$aParams['sMessage'] = Dict::S('MFATOTP:Validated');
					$oMFAUserSettings->Set('validated', 'yes');
					// Only one validation allowed
					MFATOTPService::GetInstance()->RegenerateSecret($oMFAUserSettings);
					$oMFAUserSettings->AllowWrite();
					$oMFAUserSettings->DBUpdate();
					break;
			}
		} catch (MFABaseException $e) {
			$aParams['sError'] = Dict::S('MFATOTP:Error:SendMailFailed');
		}

		$aParams['sModuleId'] = MFATOTPHelper::MODULE_NAME;
		$aParams['sTransactionId'] = utils::GetNewTransactionId();
		$aParams['sAjaxURL'] = utils::GetAbsoluteUrlExecPage();

		$this->AddSaas(MFATOTPHelper::GetSCSSFile());
		$this->AddLinkedScript(MFATOTPHelper::GetJSFile());
		$this->AddLinkedScript(MFABaseHelper::GetJSFile());

		$this->DisplayPage($aParams);
	}

	public function OperationUpdateMailSettings()
	{
		// Ajax call
		$aParams = [];

		try {
			MFABaseHelper::GetInstance()->ValidateTransactionId();

			$sEmail = utils::ReadPostedParam('email', '', utils::ENUM_SANITIZATION_FILTER_STRING);
			$sUserId = UserRights::GetUserId();
			$oMFAUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPMail::class);
			$oMFAUserSettings->Set('email', $sEmail);
			$oMFAUserSettings->AllowWrite();
			$oMFAUserSettings->DBUpdate();
			$aParams['code'] = 0;
			$aParams['message'] = Dict::Format('MFATOTP:Mail:Settings:Saved:Done', $sEmail);
		} catch (CoreCannotSaveObjectException $e) {
			MFABaseLog::Error(__FUNCTION__.' '.$e->getMessage());
			$aParams['code'] = 400;
			$aParams['error'] = Dict::S('MFATOTP:Error:SaveSettingsFailed');
		}

		$this->DisplayJSONPage($aParams);
	}

	public function OperationResendEmail()
	{
		// Ajax call
		$aParams = [];

		try {
			MFABaseHelper::GetInstance()->ValidateTransactionId();

			$sUserId = UserRights::GetUserId();
			$oMFAUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPMail::class);
			MFATOTPMailService::GetInstance()->SendCodeByEmail($oMFAUserSettings);
			$aParams['code'] = 0;
			$aParams['message'] = Dict::S('MFATOTP:Mail:ResendEmail:Done');
		} catch (MFABaseException $e) {
			$aParams['code'] = 400;
			$aParams['error'] = Dict::S('MFATOTP:Error:SendMailFailed');
		}

		$this->DisplayJSONPage($aParams);
	}
}
