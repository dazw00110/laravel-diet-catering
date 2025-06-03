<?php

namespace App\Services;

use OTPHP\TOTP;

class TOTPService
{
    /**
     * Generate a new TOTP secret.
     */
    public function generateSecret(): string
    {
        return TOTP::create()->getSecret();
    }

    /**
     * Generate a provisioning URI for the QR code.
     */
    public function getProvisioningUri(string $secret, string $label): string
    {
        $totp = TOTP::create($secret);
        $totp->setLabel($label);
        return $totp->getProvisioningUri();
    }

    /**
     * Verify a TOTP code.
     */
    public function verifyCode(string $secret, string $code): bool
    {
        $totp = TOTP::create($secret);
        return $totp->verify($code);
    }
}
