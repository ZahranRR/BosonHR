<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashAdvance;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;

class CashAdvanceController extends Controller
{
    public function index()
    {
        $kasbon = CashAdvance::with('employee')->latest()->get();
        return view('Superadmin.kasbon.index', compact('kasbon'));
    }

    public function create($id = null)
    {   
        $employee_id = $id;
        
        $employees = Employee::with('division')
            ->where('status', 'Active')
            ->orderBy('first_name', 'asc')
            ->get();
        return view('Superadmin.kasbon.create', compact('employees', 'employee_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'total_amount' => 'required|numeric|min:1',
            'installments' => 'required|integer|in:1,2,3',
            'start_month' => 'required|date',
        ]);

        $installmentAmount = $request->total_amount / $request->installments;
        $startMonth = Carbon::parse($request->start_month);

        // Loop untuk generate setiap bulan cicilan
        for ($i = 0; $i < $request->installments; $i++) {
            $month = $startMonth->copy()->addMonths($i)->format('Y-m');

            // Cek apakah kasbon untuk bulan ini sudah ada
            $exists = CashAdvance::where('employee_id', $request->employee_id)
                ->whereRaw("LEFT(start_month, 7) = ?", [$month])
                ->exists();

            if ($exists) continue; // skip kalau sudah ada

            CashAdvance::create([
                'employee_id' => $request->employee_id,
                'total_amount' => $request->total_amount,
                'installments' => $request->installments,
                'installment_amount' => $installmentAmount,
                'remaining_installments' => 1, // tiap bulan 1x cicilan
                'start_month' => $month,
                'status' => 'completed', // tandai default jadwal
            ]);
        }

        return redirect()->route('kasbon.index')->with('success', 'Cash advance schedule created successfully.');
    }

    // public function processPayrollApproval($payrollId)
    // {
    //     $payroll = Payroll::findOrFail($payrollId);
    //     $employeeId = $payroll->employee_id;
    //     $month = Carbon::parse($payroll->month)->format('Y-m');
    
    //     $cashAdvance = CashAdvance::where('employee_id', $employeeId)
    //         ->where('status', 'ongoing')
    //         ->where('remaining_installments', '>', 0)
    //         ->whereRaw("LEFT(start_month, 7) = ?", [$month])
    //         ->first();
    
    //     if ($cashAdvance) {
    //         $installment = $cashAdvance->installment_amount;
    //         $lastProcessed = $cashAdvance->last_processed_month;
    //         $payrollStatus = $payroll->status;
    
    //         if ($lastProcessed) {
    //             $lastProcessed = Carbon::parse($lastProcessed)->format('Y-m');
    //         }
    
    //         // ✅ hanya proses jika bulan lebih besar dari last processed
    //         if (strtolower($payrollStatus === 'pending')) {
    //             $payroll->cash_advance = $installment;
    //             $payroll->total_salary -= $installment;
    
    //             $cashAdvance->remaining_installments -= 1;
    //             $cashAdvance->last_processed_month = Carbon::parse($month)->format('Y-m');
    
    //             if ($cashAdvance->remaining_installments <= 0) {
    //                 $cashAdvance->remaining_installments = 0;
    //                 $cashAdvance->status = 'completed';
    //             }
    
    //             $cashAdvance->save();
    //             \Log::info("✅ Kasbon processed | {$employeeId} | {$month} | Remaining: {$cashAdvance->remaining_installments}");
    //         } else {
    //             \Log::info("⚠️ Skip kasbon | Already processed for {$month} (Last: {$lastProcessed})");
    //         }
    
    //         $payroll->status = 'Approved';
    //         $payroll->save();
    //     } else {
    //         $payroll->status = 'Approved';
    //         $payroll->save();
    //         \Log::info("ℹ️ Payroll approved tanpa kasbon | {$employeeId} | {$month}");
    //     }
    
    //     return redirect()->back()->with('success', 'Payroll approved successfully.');
    // }    
}
