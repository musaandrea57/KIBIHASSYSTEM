<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Services\PrincipalDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(PrincipalDashboardService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getDashboardData();
        return view('principal.dashboard', $data);
    }
}
