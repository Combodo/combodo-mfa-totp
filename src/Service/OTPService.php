<?php
/**
 * @copyright   Copyright (C) 2010-2023 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Service;

use Combodo\iTop\MFATotp\QRCode\QRCode;
use MFAUserSettings;
use MFAUserSettingsTOTP;
use MFAUserSettingsTOTPApp;
use OTPHP\TOTP;

class OTPService
{
	private MFAUserSettingsTOTP $oMFAUserSettings;
	public string $sLabel;
	public string $sIssuer;
	public TOTP $oTotp;

	public function __construct(MFAUserSettingsTOTP $oMFAUserSettings)
	{
		$this->oMFAUserSettings = $oMFAUserSettings;
		$this->oTotp = $this->GetTotp();
	}

	private function GetQRCodeData(): string
	{
		return urldecode($this->oTotp->getProvisioningUri());
	}

	public function GetQRCodeSVG(): string
	{
		$sQRCodeData = $this->GetQRCodeData();

		$oQRCode = QRCode::getMinimumQRCode($sQRCodeData, QR_ERROR_CORRECT_LEVEL_M);
		$oQRCode->setFgColor('#2A4265');
		$oQRCode->setBgColor('#FCFCFD');

		$sQRCodeSVG = $oQRCode->getImageSVG(6);

		return $sQRCodeSVG;
	}

	/**
	 * Get 2FA validation code
	 *
	 * @return string
	 */
	public function GetCode(): string
	{
		return $this->oTotp->now();
	}

	public function GetSecret(): string
	{
		return $this->oMFAUserSettings->Get('secret');
	}

	/**
	 * @return \OTPHP\TOTP
	 * @throws \CoreException
	 * @throws \CoreUnexpectedValue
	 * @throws \MySQLException
	 */
	private function GetTotp(): TOTP
	{
		$oTotp = TOTP::create($this->oMFAUserSettings->Get('secret'),
			$this->oMFAUserSettings->Get('code_validity'),
			TOTP::DEFAULT_DIGEST, TOTP::DEFAULT_DIGITS,
			$this->oMFAUserSettings->Get('epoch'));

		if ($this->oMFAUserSettings instanceof MFAUserSettingsTOTPApp) {
			$this->sLabel = $this->oMFAUserSettings->Get('user_id_friendlyname');
		} else {
			$this->sLabel = $this->oMFAUserSettings->Get('email');
		}
		$this->sIssuer = ITOP_APPLICATION;
		$oTotp->setLabel($this->sLabel);
		$oTotp->setIssuer($this->sIssuer);
		$oTotp->setIssuerIncludedAsParameter(true);

		return $oTotp;
	}
}