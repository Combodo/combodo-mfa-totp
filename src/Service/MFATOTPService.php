<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Service;

use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use LoginTwigContext;
use MetaModel;
use MFAUserSettings;
use MFAUserSettingsTOTP;
use MFAUserSettingsTOTPApp;
use utils;

class MFATOTPService
{
	private static MFATOTPService $oInstance;

	protected function __construct()
	{
	}

	final public static function GetInstance(): MFATOTPService
	{
		if (!isset(static::$oInstance)) {
			static::$oInstance = new static();
		}

		return static::$oInstance;
	}

	public function GetTwigContextForConfiguration(MFAUserSettings $oMFAUserSettings): LoginTwigContext
	{
		$sUserId = \UserRights::GetUserId();
		/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
		$oMFAUserSettings = MetaModel::NewObject(MFAUserSettingsTOTPApp::class, ['user_id' => $sUserId]);
		$oTOTPService = new OTPService($oMFAUserSettings);

		$aParams = [];
		$aParams['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
		$aParams['sLabel'] = $oTOTPService->sLabel;
		$aParams['sIssuer'] = $oTOTPService->sIssuer;
		$aParams['sSecret'] = $oTOTPService->GetSecret();

		$oLoginContext =  new LoginTwigContext();
		$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');

		$oLoginContext->AddBlockExtension('mfa_configuration', new \LoginBlockExtension('MFATOTPAppConfig.html.twig', $aParams));
		$oLoginContext->AddBlockExtension('script', new \LoginBlockExtension('MFATOTPAppConfig.ready.js.twig', $aParams));

		return $oLoginContext;
	}

	public function GetTwigContextForLoginValidation(MFAUserSettings $oMFAUserSettings): LoginTwigContext
	{

		$oLoginContext =  new LoginTwigContext();
		/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
		$oTOTPService = new OTPService($oMFAUserSettings);

		$aData = [];
		$aData['sLabel'] = $oTOTPService->sLabel;
		$aData['sIssuer'] = $oTOTPService->sIssuer;

		$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');

		$oLoginContext->AddBlockExtension('mfa_configuration', new \LoginBlockExtension('MFATOTPAppValidate.html.twig', $aData));
		$oLoginContext->AddBlockExtension('script', new \LoginBlockExtension('MFATOTPAppValidate.ready.js.twig', $aData));

		return $oLoginContext;
	}

	public function HasToDisplayValidation(MFAUserSettingsTOTP $oMFAUserSettings): bool
	{
		return true;
	}

	public function ValidateLogin(MFAUserSettingsTOTP $oMFAUserSettings): bool
	{
		return true;
	}

	public function GetConfigurationURLForMyAccountRedirection(MFAUserSettingsTOTP $oMFAUserSettings): string
	{
		return utils::GetAbsoluteUrlModulePage(MFATOTPHelper::MODULE_NAME, 'index.php', ['operation' => 'MFATOTPAppConfig']);
	}

}