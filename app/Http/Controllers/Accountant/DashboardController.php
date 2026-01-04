<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_collected' => Payment::where('status', 'posted')->sum('amount'),
            'outstanding_invoices' => Invoice::where('status', '!=', 'voided')->sum('balance'),
            'recent_payments' => Payment::with('student.user')->where('status', 'posted')->latest()->take(10)->get(),
        ];

        return view('accountant.dashboard', compact('stats'));
    }
}
