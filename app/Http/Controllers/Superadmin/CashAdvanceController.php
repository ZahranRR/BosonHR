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
        $kasbon = CashAdvance::with('employee')->latest()
            ->orderBy('employee_id')
            ->orderBy('start_month')
            ->get();
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
    
    public function edit($id)
    {
        $kasbon = CashAdvance::findOrFail($id);
        $employees = Employee::where('status', 'Active')->orderBy('first_name')->get();

        return view('Superadmin.kasbon.update', compact('kasbon', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $kasbon = CashAdvance::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'total_amount' => 'required|numeric|min:1',
            'installments' => 'required|integer|min:1',
            'start_month' => 'required|date',
            'status' => 'nullable|string|in:pending,completed',
        ]);

        $installmentAmount = $request->total_amount / $request->installments;
        $startMonth = Carbon::parse($request->start_month);
    
        CashAdvance::where('employee_id', $request->employee_id)
            ->where('start_month', '>=', $startMonth->format('Y-m'))
            ->delete();
    
        for ($i = 0; $i < $request->installments; $i++) {
            $month = $startMonth->copy()->addMonths($i)->format('Y-m');
    
            CashAdvance::create([
                'employee_id' => $request->employee_id,
                'total_amount' => $request->total_amount,
                'installments' => $request->installments,
                'installment_amount' => $installmentAmount,
                'remaining_installments' => 1,
                'start_month' => $month,
                'status' => 'completed',
            ]);
        }
    
        return redirect()->route('kasbon.index')->with('success', 'Cash advance updated successfully.');
    }

    public function destroy($id)
    {
        $kasbon = CashAdvance::findOrFail($id);
        $kasbon->delete();

        return redirect()->route('kasbon.index')->with('success', 'Kasbon deleted successfully.');
    }

}
