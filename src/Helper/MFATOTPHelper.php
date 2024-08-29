<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Helper;

use utils;

class MFATOTPHelper
{
	const MODULE_NAME = 'combodo-mfa-totp';

	private static MFATOTPHelper $oInstance;

	protected function __construct()
	{
	}

	final public static function GetInstance(): MFATOTPHelper
	{
		if (!isset(static::$oInstance)) {
			static::$oInstance = new static();
		}

		return static::$oInstance;
	}

	public static function GetSCSSFile()
	{
		return 'env-'.utils::GetCurrentEnvironment().'/'.self::MODULE_NAME.'/assets/css/MFATOTP.css';
	}

}