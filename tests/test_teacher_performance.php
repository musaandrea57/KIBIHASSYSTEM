<?php

use App\Services\TeacherPerformanceService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mock Auth
$user = User::whereHas('roles', function($q) { $q->where('name', 'principal'); })->first();
if (!$user) {
    echo "No principal found. Creating dummy principal context.\n";
} else {
    Auth::login($user);
}

$service = new TeacherPerformanceService();

echo "Testing TeacherPerformanceService...\n";

try {
    // 1. Test Overview
    echo "1. Testing getOverview()...\n";
    $overview = $service->getOverview([]);
    echo "   KPIs: " . json_encode($overview['kpis']) . "\n";
    echo "   Teacher Count: " . $overview['teachers']->count() . "\n";
    echo "   [PASS]\n";

    // 2. Test Alerts
    echo "2. Testing getSystemAlerts()...\n";
    $alerts = $service->getSystemAlerts([]);
    echo "   Alert Count: " . count($alerts) . "\n";
    echo "   [PASS]\n";

    // 3. Test Teacher Scorecard (if teachers exist)
    if ($overview['teachers']->count() > 0) {
        $teacherId = $overview['teachers']->first()->id;
        echo "3. Testing getTeacherScorecard($teacherId)...\n";
        $scorecard = $service->getTeacherScorecard($teacherId);
        echo "   Perf Index: " . $scorecard['metrics']['performance_index'] . "\n";
        echo "   [PASS]\n";
    } else {
        echo "3. Skipping Scorecard (No teachers found)\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
