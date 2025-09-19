<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KasbonController extends Controller
{
    public function index()
    {
        $kasbon = Payroll::all(); // atau pakai filter kalau hanya payroll aktif

        $kasbon = Payroll::with('employee')
        ->where('cash_advance', '>', 0)
        ->get();
        
        return view('Superadmin.Kasbon.index', compact('kasbon'));
    }

    public function create()
    {
        return view('Superadmin.Kasbon.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:employees,employee_id',
            'cash_advance'  => 'required|numeric|min:0',
        ]);

        $kasbon = Payroll::create([
            'employee_id'   => $request->employee_id,
            'cash_advance'  => $request->cash_advance,
            // tambahkan field lain sesuai kebutuhan
        ]);

        Log::info('Kasbon created', [
            'payroll_id' => $kasbon->id,
            'cash_advance' => $kasbon->cash_advance,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('kasbon.index')->with('success', 'Kasbon berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kasbon = Payroll::findOrFail($id);
        return view('Superadmin.Kasbon.edit', compact('kasbon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cash_advance' => 'required|numeric|min:0',
        ]);

        $kasbon = Payroll::findOrFail($id);
        $oldValue = $kasbon->cash_advance;

        $kasbon->cash_advance = $request->cash_advance;
        $kasbon->save();

        Log::info('Kasbon updated', [
            'payroll_id' => $id,
            'old_value' => $oldValue,
            'new_value' => $kasbon->cash_advance,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('kasbon.index')->with('success', 'Kasbon berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kasbon = Payroll::findOrFail($id);
        $kasbon->delete();

        Log::info('Kasbon deleted', [
            'payroll_id' => $id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('kasbon.index')->with('success', 'Kasbon berhasil dihapus.');
    }

    // Opsional: kalau masih mau pakai AJAX update kasbon
    public function updateCashAdvance(Request $request, $id)
    {
        try {
            $request->validate([
                'cash_advance' => 'required|numeric|min:0',
            ]);

            $payroll = Payroll::findOrFail($id);
            $oldValue = $payroll->cash_advance;

            $payroll->cash_advance = $request->cash_advance;
            $payroll->save();

            Log::info("Cash Advance updated", [
                'payroll_id' => $id,
                'old_value' => $oldValue,
                'new_value' => $payroll->cash_advance,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Cash advance updated successfully.',
                'cash_advance' => number_format($payroll->cash_advance, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update cash advance', [
                'payroll_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan kasbon.',
            ], 500);
        }
    }
}
