<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Student Performance Thresholds
    |--------------------------------------------------------------------------
    |
    | These configuration values determine the thresholds for identifying
    | at-risk students and other performance metrics.
    |
    */

    'at_risk' => [
        // GPA below this value is considered at risk
        'gpa_warning_threshold' => 2.0,

        // Number of failed modules to trigger a warning
        'fails_warning_threshold' => 2,
        
        // Number of carried modules to trigger a warning
        'carries_warning_threshold' => 1,

        // Attendance percentage below this value is considered low (if data available)
        'attendance_warning_threshold' => 75,
    ],

    'gpa_bands' => [
        '0-1.9' => [0, 1.99],
        '2.0-2.9' => [2.0, 2.99],
        '3.0-3.4' => [3.0, 3.49],
        '3.5-4.0' => [3.5, 4.0], // Adjust max based on your grading system (e.g., 5.0)
        '4.1-5.0' => [4.1, 5.0],
    ],

    'pagination' => [
        'per_page' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Teacher Performance Configuration
    |--------------------------------------------------------------------------
    */
    'weights' => [
        'delivery_rate' => 30,         // Weight for class delivery completion
        'attendance_completion' => 20, // Weight for marking attendance
        'assessment_timeliness' => 20, // Weight for timely CA uploads
        'evaluation_rating' => 20,     // Weight for student feedback
        'results_compliance' => 10,    // Weight for submitting results on time
    ],

    'thresholds' => [
        'delivery_rate' => 75,          // Below this % triggers an alert
        'attendance_completion' => 80,  // Below this % triggers an alert
        'upload_lateness_days' => 7,    // More than X days late triggers alert
        'min_evaluation_rating' => 3.0, // Below this rating (out of 5) triggers an alert
    ],

    'rating_scale' => [
        'excellent' => 85, // Score >= 85
        'good' => 70,      // Score >= 70
    ],
];
