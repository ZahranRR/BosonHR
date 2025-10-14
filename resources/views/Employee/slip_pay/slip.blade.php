<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 30px;
            /* jarak antara kiri dan kanan */
        }

        .company-info {
            flex: 1;
            max-width: 55%;
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.4;
        }

        .employee-info {
            width: 40%;
        }

        .employee-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-info th,
        .employee-info td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
        }

        .section-titlee {
            font-weight: bold;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
        }

        .signature {
            margin-top: 20px;
            width: 100%;
        }

        .signature td {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>SLIP GAJI</h2>
    </div>
    <table style="width:100%; border-collapse: collapse; margin-bottom:20px; margin-top:20px;">
        <tr>
            <td style="
                        width: 55%;
                        vertical-align: top;
                        max-width: 300px;
                        line-height: 1.4;
                        word-wrap: break-word;
                        overflow-wrap: break-word;
                        word-break: break-all;
                        white-space: normal;
                        border:none;
                ">
                <strong>{{ $companyname->name_company ?? 'Nama Perusahaan' }}</strong><br>
                {{ $companyname->company_address ?? 'Alamat perusahaan belum diatur' }}
            </td>

            <td style="width:45%; vertical-align: top; border: none;">
                <table style="width:100%; border-collapse: collapse; border:1px solid #000;">
                    <tr>
                        <th style="border:1px solid #000; padding:6px; text-align:left;">Periode</th>
                        <td style="border:1px solid #000; padding:6px;">{{ $payroll->month }}</td>
                    </tr>
                    <tr>
                        <th style="border:1px solid #000; padding:6px; text-align:left;">Nama Karyawan</th>
                        <td style="border:1px solid #000; padding:6px;">
                            {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
                        </td>
                    </tr>
                    <tr>
                        <th style="border:1px solid #000; padding:6px; text-align:left;">Divisi</th>
                        <td style="border:1px solid #000; padding:6px;">
                            {{ $payroll->employee->division->name ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th style="border:1px solid #000; padding:6px; text-align:left;">Status</th>
                        <td style="border:1px solid #000; padding:6px;">
                            {{ $payroll->employee->employee_type }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


    <p class="section-titlee">PENERIMAAN</p>
    <table>
        <tr>
            <td>Gaji Pokok</td>
            <td class="right">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tunjangan Jabatan</td>
            <td class="right">Rp {{ number_format($payroll->positional_allowance ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tunjangan Transportasi</td>
            <td class="right">Rp {{ number_format($payroll->transport_allowance ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tunjangan Kehadiran</td>
            <td class="right">Rp {{ number_format($payroll->attendance_allowance ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Lembur</td>
            <td class="right">Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bonus</td>
            <td class="right">Rp {{ number_format($payroll->bonus ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr class="total">
            <td>Total Penerimaan</td>
            <td class="right">Rp
                {{ number_format(($payroll->base_salary + ($payroll->attendance_allowance ?? 0) + $payroll->overtime_pay + ($payroll->bonus ?? 0) + $payroll->positional_allowance + $payroll->transport_allowance), 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <p class="section-title">PENGURANGAN</p>
    <table>
        <tr>
            <td>Kasbon</td>
            <td class="right">Rp {{ number_format($payroll->cash_advance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Potongan Telat</td>
            <td class="right">Rp {{ number_format($payroll->deduction ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Potongan Absent</td>
            <td class="right">Rp {{ number_format($payroll->absent_deduction ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr class="total">
            <td>Total Pengurangan</td>
            <td class="right">Rp
                {{ number_format(($payroll->cash_advance ?? 0) + ($payroll->deduction ?? 0) + ($payroll->absent_deduction ?? 0), 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <p class="section-title">TOTAL DITERIMA KARYAWAN</p>
    <table>
        <tr class="total">
            <td class="right">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Bandung, {{ \Carbon\Carbon::now()->format('d F Y') }}
    </div>

    <table class="signature">
        <tr>
            <td>Penerima</td>
            <td>{{$companyname->name_company ?? 'Nama perusahaan'}}</td>
        </tr>
        <tr>
            <td style="padding-top:50px">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
            <td style="padding-top:50px">Manager HRD</td>
        </tr>
    </table>
</body>

</html>