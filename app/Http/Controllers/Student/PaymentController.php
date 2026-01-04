<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function blocked()
    {
        $user = auth()->user();
        $student = $user->student;
        
        $invoices = $student->invoices()->with('items')->where('status', '!=', 'paid')->get();
        $balance = $student->balance;

        return view('student.payment.blocked', compact('invoices', 'balance', 'student'));
    }
}
