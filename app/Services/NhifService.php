<?php

namespace App\Services;

use App\Models\NhifMembership;
use App\Models\NhifVerificationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NhifService
{
    /**
     * Validate NHIF Number format (Simulated rule: 12 digits or specific pattern)
     * For simulation, we'll assume a pattern like '123456789012' (12 digits) or 'NHIF-...'
     * Let's enforce 12 digits for simplicity or standard regex.
     */
    public function validateNumberFormat($nhifNumber)
    {
        // Simple regex: 12 digits
        if (!preg_match('/^\d{12}$/', $nhifNumber)) {
            return [
                'valid' => false,
                'message' => 'NHIF Number must be 12 digits.',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Format valid.',
        ];
    }

    /**
     * Verify Membership (Simulated)
     */
    public function verifyMembership($nhifNumber, $userId = null)
    {
        // 1. Validate format first
        $validation = $this->validateNumberFormat($nhifNumber);
        if (!$validation['valid']) {
            return [
                'found' => false,
                'status' => 'error',
                'message' => $validation['message'],
            ];
        }

        // 2. Deterministic Simulation
        // Hash the number to get a consistent pseudo-random value
        $hash = crc32($nhifNumber);
        
        // 10% chance of not found
        if ($hash % 10 === 0) {
            $result = [
                'found' => false,
                'status' => 'not_found',
            ];
        } else {
            // Determine status based on modulo
            // 70% Active, 20% Expired, 10% Inactive
            $mod = $hash % 100;
            if ($mod < 70) {
                $status = 'active';
                // Expire in future (random days 30-365)
                $expiryDate = Carbon::now()->addDays(($hash % 335) + 30)->format('Y-m-d');
            } elseif ($mod < 90) {
                $status = 'expired';
                // Expired in past (random days 1-100)
                $expiryDate = Carbon::now()->subDays(($hash % 100) + 1)->format('Y-m-d');
            } else {
                $status = 'inactive';
                $expiryDate = null;
            }

            $result = [
                'found' => true,
                'status' => $status,
                'expiry_date' => $expiryDate,
                'scheme_name' => 'NHIF Student Bundle',
                'membership_type' => 'student',
            ];
        }

        // 3. Log the check if membership exists locally
        // We find the membership by number, if it exists
        $membership = NhifMembership::where('nhif_number', $nhifNumber)->first();
        if ($membership) {
            NhifVerificationLog::create([
                'nhif_membership_id' => $membership->id,
                'checked_at' => now(),
                'result_status' => $result['status'],
                'response_payload' => $result,
                'checked_by' => $userId ?? Auth::id(),
            ]);

            // Auto-update membership if found
            if ($result['found']) {
                $membership->update([
                    'status' => $result['status'],
                    'expiry_date' => $result['expiry_date'],
                    'scheme_name' => $result['scheme_name'],
                    'last_checked_at' => now(),
                    'verified_at' => $result['status'] === 'active' ? now() : $membership->verified_at,
                    'verified_by' => $result['status'] === 'active' ? ($userId ?? Auth::id()) : $membership->verified_by,
                    'source' => 'api_simulated',
                ]);
            }
        }

        return $result;
    }
}
