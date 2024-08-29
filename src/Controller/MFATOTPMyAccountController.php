<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Controller;

use Combodo\iTop\Application\TwigBase\Controller\Controller;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Combodo\iTop\MFATotp\Service\OTPService;
use MetaModel;

class MFATOTPMyAccountController extends Controller
{
	public function __construct($sViewPath = '', $sModuleName = 'core', $aAdditionalPaths = [])
	{
		$sModuleName = MFATOTPHelper::MODULE_NAME;
		$sViewPath = MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/my_account';

		parent::__construct($sViewPath, $sModuleName, $aAdditionalPaths);

		// Previously in index.php
		$this->DisableInDemoMode();
		$this->CheckAccess();
	}

	public function OperationMFATOTPAppConfig()
	{
		$aParams = [];
		$sUserId = \UserRights::GetUserId();
		/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
		$oMFAUserSettings = MetaModel::NewObject(\MFAUserSettingsTOTPApp::class, ['user_id' => $sUserId]);
		$oTOTPService = new OTPService($oMFAUserSettings);

		$aParams['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
		$aParams['sLabel'] = $oTOTPService->sLabel;
		$aParams['sIssuer'] = $oTOTPService->sIssuer;
		$aParams['sSecret'] = $oTOTPService->GetSecret();


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
}