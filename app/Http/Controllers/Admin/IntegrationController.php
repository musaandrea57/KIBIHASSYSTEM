<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrationLog;
use App\Services\IntegrationService;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    protected $service;

    public function __construct(IntegrationService $service)
    {
        $this->service = $service;
    }

    public function verifyNecta(Request $request)
    {
        $request->validate([
            'index_number' => 'required|string',
            'year' => 'required|integer',
        ]);

        $result = $this->service->verifyNecta($request->index_number, $request->year);

        return response()->json($result);
    }

    public function verifyNacte(Request $request)
    {
        $request->validate([
            'avn' => 'required|string',
        ]);

        $result = $this->service->verifyNacte($request->avn);

        return response()->json($result);
    }

    public function logs()
    {
        $logs = IntegrationLog::latest()->paginate(20);
        return view('admin.integration.logs', compact('logs'));
    }
}
