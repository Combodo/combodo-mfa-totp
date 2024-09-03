<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Controller;

use Combodo\iTop\Application\TwigBase\Controller\Controller;
use Combodo\iTop\MFABase\Helper\MFABaseException;
use Combodo\iTop\MFABase\Service\MFAUserSettingsService;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Combodo\iTop\MFATotp\Service\MFATOTPMailService;
use Combodo\iTop\MFATotp\Service\MFATOTPService;
use Combodo\iTop\MFATotp\Service\OTPService;
use Dict;
use MFAUserSettingsTOTP;
use MFAUserSettingsTOTPApp;
use MFAUserSettingsTOTPMail;
use UserRights;
use utils;

class MFATOTPMyAccountController extends Controller
{
	public function OperationMFATOTPAppConfig()
	{
		$aParams = [];
		$sUserId = UserRights::GetUserId();
		/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
		$oMFAUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPApp::class);

		$oTOTPService = new OTPService($oMFAUserSettings);

		$aParams['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
		$aParams['sLabel'] = $oTOTPService->sLabel;
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

		$this->AddSaas(MFATOTPHelper::GetSCSSFile());
		$this->AddLinkedScript(MFATOTPHelper::GetJSFile());
		$this->m_sOperation = 'MFATOTPAppConfig';
		$this->DisplayPage($aParams);
	}

	public function OperationMFATOTPMailConfig()
	{
		$aParams = [];
		$sUserId = UserRights::GetUserId();
		/** @var \MFAUserSettingsTOTPMail $oMFAUserSettings */
		$oMFAUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPMail::class);
		try {
			MFATOTPMailService::GetInstance()->SendCodeByEmail($oMFAUserSettings);
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
			$aParams['sError'] = $e->getMessage();
		}

		$aParams['sModuleId'] = MFATOTPHelper::MODULE_NAME;
		$aParams['sEmailAttributeValue'] = $oMFAUserSettings->Get('email');
		$aParams['sAjaxURL'] = utils::GetAbsoluteUrlExecPage();

		$this->AddSaas(MFATOTPHelper::GetSCSSFile());
		$this->AddLinkedScript(MFATOTPHelper::GetJSFile());
		$this->m_sOperation = 'MFATOTPMailConfig';
		$this->DisplayPage($aParams);
	}
}