<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attandance;
use App\Models\Employee;

// class CheckAbsent
// {
//     public function handle(Request $request, Closure $next)
//     {
//         // Ambil parameter employee_id dan bulan dari request
//         $employee_id = $request->route('employee_id');
//         $month = $request->input('month', now()->format('Y-m'));
        
//         // Temukan employee berdasarkan ID
//         $employee = Employee::find($employee_id);
        
//         if (!$employee) {
//             return redirect()->back()->with('error', 'Karyawan tidak ditemukan');
//         }

//         // Ambil data absensi karyawan untuk bulan tersebut
//         $startOfMonth = Carbon::parse($month)->startOfMonth();
//         $endOfMonth = Carbon::parse($month)->endOfMonth();
//         $daysInMonth = $startOfMonth->daysInMonth;

//         // Array untuk mencatat hari yang sudah terhitung absensinya
//         $daysChecked = [];

//         // Ambil data absensi untuk bulan tersebut
//         $attendances = Attandance::where('employee_id', $employee_id)
//             ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
//             ->orderBy('created_at', 'desc')
//             ->get();

//         // Cek setiap absensi dan simpan tanggalnya
//         foreach ($attendances as $attendance) {
//             $attendanceDay = Carbon::parse($attendance->created_at)->format('Y-m-d');
//             $daysChecked[] = $attendanceDay;
//         }

//         // Cek untuk setiap hari di bulan tersebut
//         $totalAbsent = 0;
//         for ($day = 1; $day <= $daysInMonth; $day++) {
//             $date = Carbon::parse($month)->day($day)->format('Y-m-d');
            
//             // Jika tidak ada entri absensi pada hari tersebut, hitung sebagai absen
//             if (!in_array($date, $daysChecked)) {
//                 $totalAbsent++;
//             }
//         }

//         // Menyimpan total absen ke session atau ke dalam request, supaya controller bisa menggunakannya
//         $request->merge(['totalAbsent' => $totalAbsent]);

//         return $next($request);
//     }
// }