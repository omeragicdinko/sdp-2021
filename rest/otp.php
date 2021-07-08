<?php
include 'vendor/autoload.php';

use OTPHP\TOTP;

// STEP 1: REGISTER ADD OTP TO ACCOUNT
// A random secret will be generated from this.
// You should store the secret with the user for verification.
// $otp = TOTP::create();
// echo "The OTP secret is: {$otp->getSecret()}\n";
// save the above to DB:

function generate_otp_secret() {
    $otp = TOTP::create();
    $otp_secret = $otp->getSecret();
    return $otp_secret;
}

function generate_qrcode($secret_from_db) {
    $otp = TOTP::create($secret_from_db);
    $otp->setLabel('dinko@MYIBUPROJECT');
    $grCodeUri = $otp->getQrCodeUri('https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M', '[DATA]');
    return $grCodeUri;
}

// STEP 3
// Note: use your own way to load the user secret.
function is_otp_code_valid($secret_from_db, $otp_code) {
    $otp = TOTP::create($secret_from_db);
    $otp->setLabel('dinko@MYIBUPROJECT');
    if($otp->now() == $otp_code) {
        return true;
    }
    return false;
}
?>