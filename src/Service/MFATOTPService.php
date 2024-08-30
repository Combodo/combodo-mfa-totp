<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Service;

use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use LoginTwigContext;
use LoginWebPage;
use MFAUserSettings;
use MFAUserSettingsTOTP;
use MFAUserSettingsTOTPApp;
use ParagonIE\ConstantTime\Base32;
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
		$oTOTPService = new OTPService($oMFAUserSettings);

		$aParams = [];
		$aParams['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
		$aParams['sLabel'] = $oTOTPService->sLabel;
		$aParams['sIssuer'] = $oTOTPService->sIssuer;
		$aParams['sSecret'] = $oTOTPService->GetSecret();

		$oLoginContext = new LoginTwigContext();
		$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');

		$oLoginContext->AddBlockExtension('mfa_configuration', new \LoginBlockExtension('MFATOTPAppConfig.html.twig', $aParams));

		$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

		return $oLoginContext;
	}

	public function GetTwigContextForLoginValidation(MFAUserSettings $oMFAUserSettings): LoginTwigContext
	{
		$oLoginContext = new LoginTwigContext();
		/** @var \MFAUserSettingsTOTP $oMFAUserSettings */
		$oTOTPService = new OTPService($oMFAUserSettings);

		$aData = [];
		$aData['sLabel'] = $oTOTPService->sLabel;
		$aData['sIssuer'] = $oTOTPService->sIssuer;

		$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');

		$oLoginContext->AddBlockExtension('mfa_validation', new \LoginBlockExtension('MFATOTPAppValidate.html.twig', $aData));

		$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

		return $oLoginContext;

	}

	public function HasToDisplayValidation(MFAUserSettingsTOTP $oMFAUserSettings): bool
	{
		if ($oMFAUserSettings instanceof MFAUserSettingsTOTPApp) {
			$sCode = utils::ReadPostedParam('totp_code', false, utils::ENUM_SANITIZATION_FILTER_INTEGER);

			return ($sCode === false);
		}

		return true;
	}

	public function ValidateLogin(MFAUserSettingsTOTP $oMFAUserSettings): bool
	{
		$sRet = $this->ValidateCode($oMFAUserSettings);
		switch ($sRet) {
			case self::NO_CODE:
			case self::WRONG_CODE:
				LoginWebPage::ResetSession();

				return false;

			default:
				return true;
		}
	}

	public const NO_CODE = 'no_code';
	public const WRONG_CODE = 'wrong_code';
	public const CODE_OK = 'code_ok';

	public function ValidateCode(MFAUserSettingsTOTP $oMFAUserSettings): string
	{
		$sCode = utils::ReadPostedParam('totp_code', 0, utils::ENUM_SANITIZATION_FILTER_INTEGER);
		if ($sCode === 0) {
			return self::NO_CODE;
		}

		if ($sCode === false) {
			return self::WRONG_CODE;
		}

		$oTOTPService = new OTPService($oMFAUserSettings);
		$sRealCode = $oTOTPService->GetCode();

		if ($sRealCode === $sCode) {
			return self::CODE_OK;
		}

		return self::WRONG_CODE;
	}

	public function GetConfigurationURLForMyAccountRedirection(MFAUserSettingsTOTP $oMFAUserSettings): string
	{
		if ($oMFAUserSettings instanceof MFAUserSettingsTOTPApp) {
			return utils::GetAbsoluteUrlModulePage(MFATOTPHelper::MODULE_NAME, 'index.php', ['operation' => 'MFATOTPAppConfig']);
		}

		return utils::GetAbsoluteUrlModulePage(MFATOTPHelper::MODULE_NAME, 'index.php', ['operation' => 'MFATOTPMailConfig']);
	}

	public function OnBeforeCreate(MFAUserSettingsTOTP $oMFAUserSettings): void
	{
		$oMFAUserSettings->Set('secret', Base32::encodeUpper(random_bytes(64)));
	}
}