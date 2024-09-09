<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Service;

use Combodo\iTop\MFABase\Helper\MFABaseLog;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use MFAUserSettings;
use MFAUserSettingsTOTP;
use MFAUserSettingsTOTPApp;
use ParagonIE\ConstantTime\Base32;
use utils;

class MFATOTPService
{
	// Code validation return values
	public const NO_CODE = 'no_code';
	public const WRONG_CODE = 'wrong_code';
	public const CODE_OK = 'code_ok';

	private static MFATOTPService $oInstance;

	protected function __construct()
	{
		MFABaseLog::Enable();
	}

	final public static function GetInstance(): MFATOTPService
	{
		if (!isset(static::$oInstance)) {
			static::$oInstance = new static();
		}

		return static::$oInstance;
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
				return false;

			case self::WRONG_CODE:;
				// this value stays from one call to another ???
				unset($_POST['totp_code']);
				return false;

			default:
				return true;
		}
	}

	public function ValidateCode(MFAUserSettingsTOTP $oMFAUserSettings): string
	{
		$sCode = utils::ReadPostedParam('totp_code', 0, utils::ENUM_SANITIZATION_FILTER_INTEGER);
		if ($sCode === 0) {
			MFABaseLog::Debug("TOTP code validation : no 'totp_code' received", null,
				[
					'class' => get_class($oMFAUserSettings),
					'user_id' => $oMFAUserSettings->Get('user_id'),
				]);
			return self::NO_CODE;
		}

		if ($sCode === false) {
			MFABaseLog::Debug("TOTP code validation : invalid 'totp_code' received (sanitization)", null,
				[
					'class' => get_class($oMFAUserSettings),
					'user_id' => $oMFAUserSettings->Get('user_id'),
				]);
			return self::WRONG_CODE;
		}

		$oTOTPService = new OTPService($oMFAUserSettings);
		$sRealCode = $oTOTPService->GetCode();

		if ($sRealCode === $sCode) {
			MFABaseLog::Debug("TOTP code validation : correct 'totp_code' received", null,
				[
					'class' => get_class($oMFAUserSettings),
					'user_id' => $oMFAUserSettings->Get('user_id'),
				]);
			return self::CODE_OK;
		}

		MFABaseLog::Debug("TOTP code validation : wrong 'totp_code' received", null,
			[
				'class' => get_class($oMFAUserSettings),
				'user_id' => $oMFAUserSettings->Get('user_id'),
			]);
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
		// Fill in mandatory value
		$this->RegenerateSecret($oMFAUserSettings);
	}

	public function RegenerateSecret(MFAUserSettingsTOTP $oMFAUserSettings): void
	{
		$oMFAUserSettings->Set('secret', Base32::encodeUpper(random_bytes(64)));
	}
}
