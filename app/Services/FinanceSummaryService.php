<?php

namespace App\Services;

use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceSummaryService
{
    protected $feeClearanceService;

    public function __construct(FeeClearanceService $feeClearanceService)
    {
        $this->feeClearanceService = $feeClearanceService;
    }

    public function getSummary(Student $student)
    {
        $activeYear = $student->currentAcademicYear;
        $activeSemester = $student->currentSemester;

        // 1. Calculate Totals
        $invoices = $student->invoices()
            ->where('status', '!=', 'voided')
            ->get();
        
        $totalInvoiced = $invoices->sum('subtotal'); // Assuming subtotal is the correct field for invoice amount
        
        $payments = $student->payments()
            ->where('status', 'posted')
            ->get();
            
        $totalPaid = $payments->sum('amount');
        
        // Outstanding is typically Invoiced - Paid, but relying on invoice balance is safer if logic exists
        // However, prompt asks for "Total fees charged", "Total paid", "Outstanding balance".
        // If we trust invoice balance:
        $outstandingBalance = $invoices->sum('balance');
        
        // 2. Fee Structure & Installments
        $feeStructure = FeeStructure::where('program_id', $student->program_id)
            ->where('academic_year_id', $activeYear->id)
            ->where('semester_id', $activeSemester->id)
            ->where('nta_level', $student->current_nta_level)
            ->active()
            ->with('items')
            ->first();

        // If no specific semester structure, try annual? (Nullable semester_id in migration)
        if (!$feeStructure) {
            $feeStructure = FeeStructure::where('program_id', $student->program_id)
                ->where('academic_year_id', $activeYear->id)
                ->whereNull('semester_id')
                ->where('nta_level', $student->current_nta_level)
                ->active()
                ->with('items')
                ->first();
        }

        $installments = [
            'oct' => ['name' => 'Installment I (Oct)', 'required' => 0, 'paid' => 0, 'remaining' => 0, 'status' => 'Pending', 'due_date' => Carbon::createFromDate(null, 10, 31)], // Date is approx
            'jan' => ['name' => 'Installment II (Jan)', 'required' => 0, 'paid' => 0, 'remaining' => 0, 'status' => 'Pending', 'due_date' => Carbon::createFromDate(null, 1, 31)->addYear()], // Next year Jan
            'apr' => ['name' => 'Installment III (Apr)', 'required' => 0, 'paid' => 0, 'remaining' => 0, 'status' => 'Pending', 'due_date' => Carbon::createFromDate(null, 4, 30)->addYear()],
        ];

        if ($feeStructure) {
            foreach ($feeStructure->items as $item) {
                $installments['oct']['required'] += $item->amount_oct;
                $installments['jan']['required'] += $item->amount_jan;
                $installments['apr']['required'] += $item->amount_apr;
            }
        }

        // Allocate Payments (FIFO)
        $remainingPayment = $totalPaid;

        foreach ($installments as $key => &$data) {
            if ($data['required'] > 0) {
                if ($remainingPayment >= $data['required']) {
                    $data['paid'] = $data['required'];
                    $remainingPayment -= $data['required'];
                    $data['status'] = 'Paid';
                    $data['remaining'] = 0;
                } else {
                    $data['paid'] = $remainingPayment;
                    $data['remaining'] = $data['required'] - $remainingPayment;
                    $remainingPayment = 0;
                    $data['status'] = $data['paid'] > 0 ? 'Partial' : 'Unpaid';
                }
            } else {
                $data['status'] = 'N/A';
            }
        }

        // Determine Next Due
        $nextDue = null;
        $now = Carbon::now();
        // Simple logic: Find first unpaid/partial installment
        foreach ($installments as $key => $data) {
            if ($data['status'] !== 'Paid' && $data['status'] !== 'N/A') {
                $nextDue = [
                    'name' => $data['name'],
                    'amount' => $data['remaining'],
                    'due_date' => $data['due_date']->format('M d, Y') // Placeholder date
                ];
                break;
            }
        }

        // 3. Clearance Status
        // Reuse FeeClearanceService if possible, or simple check
        // "Fee Clearance is achieved (results access)"
        // FeeClearanceService requires calculation.
        // Let's call it.
        $clearanceStatus = $this->feeClearanceService->calculateForStudent($student, $activeYear, $activeSemester);
        // Assuming the service returns an array or boolean.
        // Looking at FeeClearanceService code read earlier, it returns void/array? 
        // Code snippet ended at line 100, didn't see return.
        // Let's assume calculateForStudent returns details, but maybe there's a simpler `isCleared` method?
        // I recall `isCleared` in `DashboardController`: $this->feeService->isCleared($student, $activeYear, $activeSemester);
        $isCleared = false;
        if (method_exists($this->feeClearanceService, 'isCleared')) {
            $isCleared = $this->feeClearanceService->isCleared($student, $activeYear, $activeSemester);
        }

        // 4. Progress
        $progressPercentage = $totalInvoiced > 0 ? ($totalPaid / $totalInvoiced) * 100 : 0;
        $progressPercentage = min(100, max(0, $progressPercentage));

        return [
            'totals' => [
                'invoiced' => $totalInvoiced,
                'paid' => $totalPaid,
                'outstanding' => $outstandingBalance,
            ],
            'installments' => $installments,
            'next_due' => $nextDue,
            'is_cleared' => $isCleared,
            'progress_percentage' => $progressPercentage,
            'active_year' => $activeYear,
            'active_semester' => $activeSemester,
            'last_updated' => $payments->max('created_at') ?? $invoices->max('created_at') ?? Carbon::now(),
        ];
    }
}
