<?php
namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayrollExport implements FromCollection
{
    /**
     * Mengambil data yang akan diekspor ke Excel.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Mengambil data payroll yang telah terverifikasi
        return Payroll::where('validation_status', 'approved')->get(); // Sesuaikan kondisi sesuai kebutuhan Anda
    }
}
