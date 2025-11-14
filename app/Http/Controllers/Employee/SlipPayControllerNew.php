<?php

namespace App\Http\Controllers\Employee;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SlipPayControllerNew extends Controller
{
    public function index(Request $request)
    {
        $employee = Auth::user()->employee;
    
        if (!$employee) {
            Log::warning('SlipPayControllerNew@index: employee relasi tidak ditemukan untuk user login', [
                'user_id' => Auth::id(),
            ]);
            return view('Employee.slip_pay.index', [
                'payroll' => null,
                'availableMonths' => collect(),
                'selectedMonth' => null,
            ]);
        }
    
        Log::info('SlipPayControllerNew@index: user login', [
            'user_id' => Auth::id(),
            'employee_id' => $employee->employee_id,
        ]);
    
        // daftar bulan yang tersedia untuk employee ini
        $availableMonths = Payroll::where('employee_id', $employee->employee_id)
            ->orderBy('month', 'desc')
            ->pluck('month');
    
        // bulan dipilih dari filter, default = bulan terbaru
        $selectedMonth = $request->query('month', $availableMonths->first());
    
        // ambil payroll sesuai bulan
        $payroll = Payroll::with('employee.division')
            ->where('employee_id', $employee->employee_id)
            ->where('month', $selectedMonth)
            ->first();
    
        Log::info('SlipPayControllerNew@index: payroll query result', [
            'payroll_id' => $payroll?->payroll_id,
            'month' => $payroll?->month,
            'total_salary' => $payroll?->total_salary,
        ]);
    
        return view('Employee.slip_pay.index', compact('payroll', 'availableMonths', 'selectedMonth'));
    }    

    public function previewPDF($id)
    {
        Log::info('SlipPayControllerNew@previewPDF dipanggil', ['payroll_id' => $id]);

        $payroll = Payroll::with('employee.division')->find($id);
        $companyname = \App\Models\CompanyName::first();

        if (!$payroll) {
            Log::error('SlipPayControllerNew@previewPDF: payroll tidak ditemukan', ['payroll_id' => $id]);
            abort(404, 'Payroll not found');
        }

        $pdf = Pdf::loadView('Employee.slip_pay.slip', [
            'payroll' => $payroll,
            'companyname' => $companyname,
        ]);

        return $pdf->stream('Slip_Gaji_' . $payroll->employee->first_name . '_' . $payroll->month . '.pdf');
    }

    public function downloadPDF($id)
    {
        Log::info('SlipPayControllerNew@downloadPDF dipanggil', ['payroll_id' => $id]);

        $payroll = Payroll::with('employee.division')->find($id);
        $companyname = \App\Models\CompanyName::first();

        if (!$payroll) {
            Log::error('SlipPayControllerNew@downloadPDF: payroll tidak ditemukan', ['payroll_id' => $id]);
            abort(404, 'Payroll not found');
        }

        $pdf = Pdf::loadView('Employee.slip_pay.slip', [
            'payroll' => $payroll,
            'companyname' => $companyname,
        ]);

        return $pdf->download('Slip_Gaji_' . $payroll->employee->first_name . '_' . $payroll->month . '.pdf');
    }
}
