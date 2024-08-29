<?php
/**
 *  @copyright   Copyright (C) 2010-2019 Combodo SARL
 *  @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp;


use Combodo\iTop\MFATotp\Controller\MFATOTPMyAccountController;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;

require_once(APPROOT.'application/startup.inc.php');

$sTemplates = MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/my_account';


$oUpdateController = new MFATOTPMyAccountController($sTemplates, MFATOTPHelper::MODULE_NAME);
$oUpdateController->SetDefaultOperation('MFATOTPAppConfig');
$oUpdateController->HandleOperation();
