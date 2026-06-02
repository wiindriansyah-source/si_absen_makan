<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Dashboard</title>
    <style>
        @page { margin: 40px 40px 60px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #334155; line-height: 1.5; }

        .header-table { width: 100%; border-bottom: 3px solid #3b82f6; padding-bottom: 15px; margin-bottom: 25px; }
        .header-logo { width: 20%; text-align: left; vertical-align: middle; }
        .header-logo img { max-width: 120px; max-height: 70px; object-fit: contain; }
        .header-text { width: 80%; text-align: right; vertical-align: middle; }
        .header-text h1 { margin: 0; color: #0f172a; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; }
        .header-text h2 { margin: 5px 0 0 0; color: #475569; font-size: 14px; font-weight: normal; }
        .header-text .period { margin-top: 8px; display: inline-block; background-color: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; }

        .section-title { font-size: 14px; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 25px; margin-bottom: 15px; font-weight: bold; text-transform: uppercase; }

        .summary-container { width: 100%; border-spacing: 10px; border-collapse: separate; margin-left: -10px; margin-right: -10px; }
        .summary-card { background-color: #f8fafc; border: 1px solid #cbd5e1; border-top: 4px solid #3b82f6; padding: 15px; text-align: center; width: 25%; border-radius: 4px; }
        .summary-card .title { display: block; font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-card .value { display: block; font-size: 22px; font-weight: bold; margin-top: 8px; color: #0f172a; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 8px; font-size: 11px; } /* Ukuran font tabel dikecilkan agar muat banyak */
        table.data-table th { background-color: #f1f5f9; color: #334155; font-weight: bold; text-align: left; text-transform: uppercase; font-size: 10px; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }

        /* Mencegah row tabel terpotong di tengah saat ganti halaman PDF */
        table.data-table tr { page-break-inside: avoid; }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }

        .badge-success { color: #16a34a; font-weight: bold; }
        .badge-danger { color: #dc2626; font-weight: bold; }

        .footer { position: fixed; bottom: -30px; left: 0; right: 0; font-size: 10px; text-align: center; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }

        /* Utility page break */
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ public_path('images/logo/logo_imm.png') }}" alt="Logo">
            </td>
            <td class="header-text">
                <h1>Laporan Sistem Dashboard</h1>
                <h2>
                    @if($reportType === 'stats') Ringkasan Statistik Umum & Personel
                    @elseif($reportType === 'attendance') Analisis & Detail Kehadiran Makan
                    @elseif($reportType === 'satisfaction') Evaluasi & Umpan Balik Kepuasan
                    @else Laporan Komprehensif
                    @endif
                </h2>
                <div class="period">Periode: {{ $startDate }} s/d {{ $endDate }}</div>
            </td>
        </tr>
    </table>

    @if($reportType === 'all' || $reportType === 'stats')
        <div class="section-title">Ringkasan Eksekutif</div>
        <table class="summary-container">
            <tr>
                <td class="summary-card">
                    <span class="title">Karyawan Aktif</span><span class="value">{{ $karyawanAktif }}</span>
                </td>
                <td class="summary-card">
                    <span class="title">Total Pengunjung</span><span class="value">{{ $totalPengunjung }}</span>
                </td>
                <td class="summary-card" style="border-top-color: #f59e0b;">
                    <span class="title">Total Porsi Makan</span><span class="value">{{ $totalAbsensi }}</span>
                </td>
                <td class="summary-card" style="border-top-color: #10b981;">
                    <span class="title">Kepuasan (Puas/Tdk)</span><span class="value">{{ $puas }} / {{ $tidakPuas }}</span>
                </td>
            </tr>
        </table>

        <div class="section-title">Daftar Karyawan Aktif</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="15%">NIK</th>
                    <th width="30%">Nama Karyawan</th>
                    <th width="25%">Departemen</th>
                    <th width="25%">Jabatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employeesList as $index => $emp)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $emp->employee_no }}</td>
                        <td>{{ $emp->name }}</td>
                        <td>{{ $emp->department ? $emp->department->name : '-' }}</td>
                        <td>{{ $emp->position_name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Tidak ada data karyawan aktif.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Daftar Pengunjung (Periode Ini)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="20%">Tanggal</th>
                    <th width="30%">Nama Pengunjung</th>
                    <th width="20%">Tipe</th>
                    <th width="25%">Institusi/Asal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visitorsList as $index => $vis)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $vis->created_at->translatedFormat('d M Y, H:i') }}</td>
                        <td>{{ $vis->name }}</td>
                        <td>{{ $vis->type ?? '-' }}</td>
                        <td>{{ $vis->institution ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Tidak ada data pengunjung pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($reportType === 'all') <div class="page-break"></div> @endif
    @endif


    @if($reportType === 'all' || $reportType === 'attendance')
        <div class="section-title">Riwayat Kehadiran Makan Harian (Ringkasan)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="10%" class="text-center">No.</th>
                    <th width="50%">Hari & Tanggal</th>
                    <th width="40%" class="text-center">Total Porsi Terlayani</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trenHarian as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($item->date)->translatedFormat('l, d F Y') }}</td>
                        <td class="text-center"><strong>{{ $item->total }}</strong> Porsi</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center">Tidak ada tren kehadiran makan.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Rincian Data Kehadiran Makan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="20%">Waktu</th>
                    <th width="30%">Nama (Karyawan/Tamu)</th>
                    <th width="25%">Departemen/Status</th>
                    <th width="20%">Sesi Makan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceDetails as $index => $att)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $att->created_at->translatedFormat('d M Y, H:i') }}</td>
                        <td>
                            @if($att->employee)
                                {{ $att->employee->name }} <br><span style="font-size: 9px; color:#64748b;">(Karyawan)</span>
                            @elseif($att->visitor)
                                {{ $att->visitor->name }} <br><span style="font-size: 9px; color:#64748b;">(Pengunjung)</span>
                            @else
                                Tidak Diketahui
                            @endif
                        </td>
                        <td>
                            @if($att->employee && $att->employee->department) {{ $att->employee->department->name }}
                            @elseif($att->visitor && $att->visitor->type) {{ $att->visitor->type }}
                            @else - @endif
                        </td>
                        <td>{{ $att->meal_time ?? $att->meal_type ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Belum ada data kehadiran makan di periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($reportType === 'all') <div class="page-break"></div> @endif
    @endif


    @if($reportType === 'all' || $reportType === 'satisfaction')
        <div class="section-title">Detail Tingkat Kepuasan Pelayanan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="40%">Kategori Kepuasan</th>
                    <th width="30%" class="text-center">Jumlah Responden</th>
                    <th width="30%" class="text-center">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $totalVote = $puas + $tidakPuas; @endphp
                <tr>
                    <td class="badge-success">Puas 😊</td>
                    <td class="text-center">{{ $puas }}</td>
                    <td class="text-center">{{ $totalVote > 0 ? round(($puas / $totalVote) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td class="badge-danger">Tidak Puas ☹️</td>
                    <td class="text-center">{{ $tidakPuas }}</td>
                    <td class="text-center">{{ $totalVote > 0 ? round(($tidakPuas / $totalVote) * 100, 1) : 0 }}%</td>
                </tr>
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td>TOTAL RESPONDEN</td>
                    <td class="text-center">{{ $totalVote }}</td>
                    <td class="text-center">100%</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Rincian Umpan Balik & Masukan (Feedback)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="20%">Tanggal</th>
                    <th width="25%">Nama</th>
                    <th width="15%" class="text-center">Kepuasan</th>
                    <th width="35%">Komentar / Feedback</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Filter hanya data yang mengisi tingkat kepuasan (atau abaikan null)
                    $feedbackData = $attendanceDetails->filter(fn($att) => $att->satisfaction != null);
                @endphp

                @forelse($feedbackData as $index => $fb)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $fb->created_at->translatedFormat('d M Y') }}</td>
                        <td>
                            {{ $fb->employee ? $fb->employee->name : ($fb->visitor ? $fb->visitor->name : 'Tanpa Nama') }}
                        </td>
                        <td class="text-center">
                            @if($fb->satisfaction === 'Puas') <span class="badge-success">Puas</span>
                            @elseif($fb->satisfaction === 'Tidak Puas') <span class="badge-danger">Tidak Puas</span>
                            @else {{ $fb->satisfaction }}
                            @endif
                        </td>
                        <td><i>{{ $fb->feedback ?? '-' }}</i></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Belum ada feedback / evaluasi kepuasan di periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dicetak secara otomatis oleh Sistem pada {{ now()->translatedFormat('d F Y - H:i:s') }} WIB
    </div>

</body>
</html>
