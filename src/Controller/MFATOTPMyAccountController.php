<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Controller;

use Combodo\iTop\Application\TwigBase\Controller\Controller;
use Combodo\iTop\MFABase\Service\MFAUserSettingsService;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Combodo\iTop\MFATotp\Service\MFATOTPService;
use Combodo\iTop\MFATotp\Service\OTPService;
use DBObject;
use Dict;
use MetaModel;
use MFAUserSettingsTOTP;
use MFAUserSettingsTOTPApp;
use UserRights;

class MFATOTPMyAccountController extends Controller
{
	public function OperationMFATOTPAppConfig()
	{
		$aParams = [];
		$sUserId = UserRights::GetUserId();
		/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
		$oMFAUserSettings = $this->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPApp::class);

		$oTOTPService = new OTPService($oMFAUserSettings);

		$aParams['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
		$aParams['sLabel'] = $oTOTPService->sLabel;
		$aParams['sIssuer'] = $oTOTPService->sIssuer;
		$aParams['sSecret'] = $oMFAUserSettings->Get('secret');

		$sRet = MFATOTPService::GetInstance()->ValidateCode($oMFAUserSettings);
		switch ($sRet) {
			case MFATOTPService::NO_CODE:
				if ($oMFAUserSettings->Get('status') === 'active') {
					$aParams['sMessage'] = Dict::S('MFATOTP:App:UI:Validated');
				}
				break;

			case MFATOTPService::WRONG_CODE:
				$aParams['sError'] = Dict::S('MFATOTP:App:UI:NotValidated');
				break;

			case MFATOTPService::CODE_OK:
				$aParams['sMessage'] = Dict::S('MFATOTP:App:UI:Validated');
				$oMFAUserSettings->Set('status', 'active');
				$oMFAUserSettings->DBUpdate();
				break;
		}

		$this->AddSaas(MFATOTPHelper::GetSCSSFile());
		$this->m_sOperation = 'MFATOTPAppConfig';
		$this->DisplayPage($aParams);
	}

	public function OperationMFATOTPAppCheckCode()
	{
		$aParams = [];

		$aParams['sCode'] = OTPService::GetInstance()->GetCode();
		$aParams['sLabel'] = OTPService::GetInstance()->sLabel;

		$this->AddSaas(MFATOTPHelper::GetSCSSFile());
		$this->m_sOperation = 'MFATOTPAppCheckCode';
		$this->DisplayPage($aParams);
	}

	private function GetMFAUserSettings(string $sUserId, string $sMFAUserSettingsClass): DBObject
	{
		$aSettings = MFAUserSettingsService::GetInstance()->GetActiveMFASettings($sUserId);
		/** @var DBObject $oSettings */
		foreach ($aSettings as $oSettings) {
			if (get_class($oSettings) === $sMFAUserSettingsClass) {
				$oSettings->Reload();
				return $oSettings;
			}
		}

		$oSettings = MetaModel::NewObject($sMFAUserSettingsClass, ['user_id' => $sUserId]);
		$oSettings->DBInsert();
		return $oSettings;
	}
}