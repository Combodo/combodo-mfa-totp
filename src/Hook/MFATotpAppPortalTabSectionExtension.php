<?php

namespace Combodo\iTop\MFATotp\Hook;

use Combodo\iTop\MFABase\Service\MFAPortalService;
use Combodo\iTop\MFABase\Service\MFAUserSettingsService;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Combodo\iTop\MFATotp\Service\MFATOTPService;
use Combodo\iTop\MFATotp\Service\OTPService;
use Combodo\iTop\Portal\Hook\iPortalTabSectionExtension;
use Combodo\iTop\Portal\Twig\PortalBlockExtension;
use Combodo\iTop\Portal\Twig\PortalTwigContext;
use Dict;
use MFAUserSettingsTOTPApp;
use UserRights;
use utils;

class MFATotpAppPortalTabSectionExtension implements iPortalTabSectionExtension
{

	/**
	 * @inheritDoc
	 */
	public function IsActive(): bool
	{
		return MFAPortalService::GetInstance()->IsUserSettingsConfigurationRequired(MFAUserSettingsTOTPApp::class);
	}

	/**
	 * @inheritDoc
	 */
	public function GetTabCode(): string
	{
		return 'MyAccount-Tab-MFA';
	}

	/**
	 * @inheritDoc
	 */
	public function GetSectionRank(): float
	{
		return 0;
	}

	public function GetTarget(): string
	{
		return 'p_user_profile_brick';
	}

	public function GetPortalTwigContext(): PortalTwigContext
	{
		$oPortalTwigContext = new PortalTwigContext();

		$sUserId = UserRights::GetUserId();
		/** @var MFAUserSettingsTOTPApp $oUserSettings */
		$oUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPApp::Class);

		/*if (utils::ReadPostedParam("operation") === "rebuild_code"){
			$oUserSettingsRecoveryCodesService->RebuildCodes($oUserSettings);
		}*/

		$oTOTPService = new OTPService($oUserSettings);

		$aData = [];
		$aData['sAction'] = MFAPortalService::GetInstance()->GetSelectedAction();
		$aData['sClass'] = MFAUserSettingsTOTPApp::class;
		$aData['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
		$aData['sLabel'] = $oTOTPService->sLabel;
		$aData['sIssuer'] = $oTOTPService->sIssuer;
		$aData['sSecret'] = $oUserSettings->Get('secret');

		$sRet = MFATOTPService::GetInstance()->ValidateCode($oUserSettings);
		switch ($sRet) {
			case MFATOTPService::NO_CODE:
				if ($oUserSettings->Get('validated') === 'yes') {
					$aData['sMessage'] = Dict::S('MFATOTP:Validated');
				}
				break;

			case MFATOTPService::WRONG_CODE:
				$aData['sError'] = Dict::S('MFATOTP:NotValidated');
				break;

			case MFATOTPService::CODE_OK:
				$aData['sMessage'] = Dict::S('MFATOTP:Validated');
				$oUserSettings->Set('validated', 'yes');
				$oUserSettings->AllowWrite();
				$oUserSettings->DBUpdate();
				break;
		}

		$sPath = MFATOTPHelper::MODULE_NAME.'/templates/portal/MFATotpAppView.html.twig';
		$oPortalTwigContext->AddBlockExtension('html', new PortalBlockExtension($sPath, $aData));
		$sPath = MFATOTPHelper::MODULE_NAME.'/templates/portal/MFATotpAppView.js.twig';
		$oPortalTwigContext->AddBlockExtension('script', new PortalBlockExtension($sPath, $aData));

		return $oPortalTwigContext;
	}
}
