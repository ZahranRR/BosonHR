<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashAdvance;
use App\Models\Employee;
use Carbon\Carbon;

class CashAdvanceController extends Controller
{
    public function index()
    {
        $kasbon = CashAdvance::with('employee')->latest()->get();
        return view('Superadmin.kasbon.index', compact('kasbon'));
    }

    public function create($employee_id = null)
    {
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

        $existingKasbon = CashAdvance::where('employee_id', $request->employee_id)
            ->where('start_month', $request->start_month)
            ->first();

        if ($existingKasbon) {
            return redirect()->back()->withErrors([
                'start_month' => 'Kasbon untuk bulan ' . $request->start_month . ' sudah ada untuk pegawai ini.'
            ])->withInput();
        }

        $installmentAmount = $request->total_amount / $request->installments;

        CashAdvance::create([
            'employee_id' => $request->employee_id,
            'total_amount' => $request->total_amount,
            'installments' => $request->installments,
            'installment_amount' => $installmentAmount,
            'remaining_installments' => $request->installments,
            'start_month' => $request->start_month,
        ]);

        return redirect()->route('kasbon.index')->with('success', 'Cash advance added successfully.');
    }
}
