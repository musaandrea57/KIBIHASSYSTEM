<?php

namespace App\Services;

use App\Models\IntegrationLog;
use Illuminate\Support\Facades\Http;

class IntegrationService
{
    /**
     * Simulate NECTA Verification
     */
    public function verifyNecta($indexNumber, $year)
    {
        // In real world, this would be an API call
        // $response = Http::post('https://api.necta.go.tz/verify', [...]);
        
        // Normalize index number: ensure it has the year
        // If index is S0101/0001 and year is 2020, make it S0101/0001/2020
        if (preg_match('/^S\d{4}\/\d{4}$/', $indexNumber) && $year) {
            $indexNumber = $indexNumber . '/' . $year;
        }

        // Simulation logic: Validate format Sxxxx/yyyy/zzzz
        $isValid = preg_match('/^S\d{4}\/\d{4}\/\d{4}$/', $indexNumber);
        
        $result = [
            'status' => $isValid ? 'VALID' : 'INVALID',
            'candidate_name' => $isValid ? 'JOHN DOE' : null,
            'division' => $isValid ? 'I' : null,
            'points' => $isValid ? 7 : null,
            'subjects' => $isValid ? [
                'PHYSICS' => 'A',
                'CHEMISTRY' => 'B',
                'BIOLOGY' => 'A',
                'MATHEMATICS' => 'C',
                'ENGLISH' => 'A'
            ] : []
        ];

        // Log the attempt
        IntegrationLog::create([
            'provider' => 'NECTA',
            'action' => 'verify_candidate',
            'request_data' => ['index_number' => $indexNumber, 'year' => $year],
            'response_data' => $result,
            'status' => $isValid ? 'success' : 'failed',
            'ip_address' => request()->ip(),
        ]);

        return $result;
    }

    /**
     * Simulate NACTE Verification/Registration
     */
    public function verifyNacte($avn)
    {
        // Simulation logic
        $isValid = strlen($avn) > 5;

        $result = [
            'status' => $isValid ? 'VERIFIED' : 'NOT_FOUND',
            'registration_status' => $isValid ? 'Eligible' : 'Unknown',
            'award_level' => $isValid ? 'NTA Level 4' : null,
        ];

        // Log
        IntegrationLog::create([
            'provider' => 'NACTE',
            'action' => 'check_status',
            'request_data' => ['avn' => $avn],
            'response_data' => $result,
            'status' => $isValid ? 'success' : 'failed',
            'ip_address' => request()->ip(),
        ]);

        return $result;
    }
}
