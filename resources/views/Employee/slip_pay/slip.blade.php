<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .company-info { width: 60%; float: left; }
        .employee-info { width: 40%; float: right; }
        .section-title { font-weight: bold; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        .right { text-align: right; }
        .total { font-weight: bold; background: #f2f2f2; }
        .footer { margin-top: 40px; font-size: 11px; }
        .signature { margin-top: 60px; width: 100%; }
        .signature td { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SLIP GAJI</h2>
    </div>

    <div class="company-info">
        <strong>PT BosonHR</strong><br>
        Gedung Makmur<br>
        Jl. Sejahtera No. 33<br>
        Bekasi
    </div>

    <div class="employee-info">
        <table>
            <tr><th>Periode</th><td>{{ $payroll->month }}</td></tr>
            <tr><th>Nama Karyawan</th><td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td></tr>
            <tr><th>Divisi</th><td>{{ $payroll->employee->division->name ?? '-' }}</td></tr>
            <tr><th>Status</th><td>{{$payroll->employee->employee_type}}</td></tr>
            {{-- <tr><th>PTKP</th><td>K/0</td></tr> --}}
        </table>
    </div>
    <div style="clear: both;"></div>

    <p class="section-title">PENERIMAAN</p>
    <table>
        <tr><td>Gaji Pokok</td><td class="right">Rp {{ number_format($payroll->current_salary,0,',','.') }}</td></tr>
        <tr><td>Tunjangan Jabatan</td><td class="right">Rp {{ number_format($payroll->positional_allowance ?? 0,0,',','.') }}</td></tr>
        <tr><td>Tunjangan Transportasi</td><td class="right">Rp {{ number_format($payroll->transport_allowance ?? 0,0,',','.') }}</td></tr>
        <tr><td>Tunjangan Kehadiran</td><td class="right">Rp {{ number_format($payroll->attendance_allowance ?? 0,0,',','.') }}</td></tr>
        <tr><td>Lembur</td><td class="right">Rp {{ number_format($payroll->overtime_pay,0,',','.') }}</td></tr>
        <tr><td>Bonus</td><td class="right">Rp {{ number_format($payroll->bonus ?? 0,0,',','.') }}</td></tr>
        <tr class="total"><td>Total Penerimaan</td><td class="right">Rp {{ number_format(($payroll->current_salary + ($payroll->attendance_allowance ?? 0) + $payroll->overtime_pay + ($payroll->bonus ?? 0) + $payroll->positional_allowance + $payroll->transport_allowance),0,',','.') }}</td></tr>
    </table>

    <p class="section-title">PENGURANGAN</p>
    <table>
        <tr><td>Kasbon</td><td class="right">Rp {{ number_format($payroll->cash_advance,0,',','.') }}</td></tr>
        <tr><td>Potongan Telat</td><td class="right">Rp {{ number_format($payroll->deduction ?? 0,0,',','.') }}</td></tr>
        <tr><td>Potongan Absent</td><td class="right">Rp {{ number_format($payroll->deduction ?? 0,0,',','.') }}</td></tr>
        <tr class="total"><td>Total Pengurangan</td><td class="right">Rp {{ number_format(($payroll->cash_advance + ($payroll->deduction ?? 0)),0,',','.') }}</td></tr>
    </table>

    <p class="section-title">TOTAL DITERIMA KARYAWAN</p>
    <table>
        <tr class="total">
            <td class="right">Rp {{ number_format($payroll->total_salary,0,',','.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Bandung, {{ \Carbon\Carbon::now()->format('d F Y') }}
    </div>

    <table class="signature">
        <tr>
            <td>Penerima</td>
            <td>PT BosonHR</td>
        </tr>
        <tr>
            <td style="padding-top:50px">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
            <td style="padding-top:50px">Manager HRD</td>
        </tr>
    </table>
</body>
</html>
