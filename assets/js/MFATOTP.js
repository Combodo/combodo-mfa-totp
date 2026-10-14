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


// Avoid double submit when user presses enter
const form = document.getElementById('totp_form');
let isSubmitting = false; // Flag to track submission status

form.addEventListener('submit', async (e) => {
	e.preventDefault();

	// Exit if already submitting (blocks race conditions)
	if (isSubmitting) return;

	// Mark as submitting
	isSubmitting = true;

	try {
		await fetch('/api/submit', { method: 'POST', body: new FormData(form) });
	} catch (error) {
		// Reset state on failure
		isSubmitting = false;
	}
});