<?php

namespace App\Services;

class PhoneNumberService
{
    /**
     * Normalize a phone number to E.164 format for Tanzania (+255...).
     *
     * @param string $phone
     * @return string|null
     */
    public function normalize(string $phone): ?string
    {
        // Remove non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // Handle various formats
        if (preg_match('/^255([67]\d{8})$/', $phone, $matches)) {
            // Already 255XXXXXXXXX
            return '+255' . $matches[1];
        } elseif (preg_match('/^0([67]\d{8})$/', $phone, $matches)) {
            // 0XXXXXXXXX -> +255XXXXXXXXX
            return '+255' . $matches[1];
        } elseif (preg_match('/^([67]\d{8})$/', $phone, $matches)) {
            // XXXXXXXXX -> +255XXXXXXXXX (missing 0 or 255)
            return '+255' . $matches[1];
        }

        // Return null if invalid format
        return null;
    }

    /**
     * Validate a phone number.
     *
     * @param string $phone
     * @return bool
     */
    public function validate(string $phone): bool
    {
        return $this->normalize($phone) !== null;
    }
}
