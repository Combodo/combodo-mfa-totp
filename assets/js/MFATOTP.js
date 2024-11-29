// @copyright   Copyright (C) 2010-2024 Combodo SARL
// @license     http://opensource.org/licenses/AGPL-3.0

function CheckLoginCode() {
	var sCode = $("#totp_code").val();
	if (sCode.length === 6) {
		$("#totp_form").trigger("submit");
	}
}

$(".ibo-mfa-login-totp-alt").hide();

$(".ibo-mfa-login-totp-switch-action").click(function() {
	$(".ibo-mfa--qr-code").hide();
	$(".ibo-mfa-login-totp-switch-to-data").hide();
	$(".ibo-mfa-login-totp-alt").show();
});