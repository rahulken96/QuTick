<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tiket QuTick</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 15px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
        }
        .stats-grid {
            margin-bottom: 25px;
            width: 100%;
        }
        .stats-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 15px;
            text-align: center;
        }
        .stats-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stats-value {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
        }
        .table td {
            border-bottom: 1px solid #f1f5f9;
            padding: 8px 10px;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-open { background-color: #eff6ff; color: #1d4ed8; }
        .badge-progress { background-color: #fffbeb; color: #b45309; }
        .badge-resolved { background-color: #ecfdf5; color: #047857; }
        .badge-rejected { background-color: #fef2f2; color: #b91c1c; }
        .badge-low { background-color: #f0fdf4; color: #16a34a; }
        .badge-medium { background-color: #fef3c7; color: #d97706; }
        .badge-high { background-color: #fef2f2; color: #dc2626; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="title">Laporan Tiket QuTick</div>
                    <div class="subtitle">
                        Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d-m-Y') : 'Awal' }} s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d-m-Y') : 'Sekarang' }}
                    </div>
                </td>
                <td style="text-align: right; vertical-align: bottom; font-size: 10px; color: #64748b;">
                    Diekspor pada: {{ now()->format('d-m-Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <table class="stats-grid" style="width: 100%;">
        <tr>
            <td style="width: 20%; padding: 0 5px 0 0;">
                <div class="stats-box">
                    <div class="stats-label">Total Tiket</div>
                    <div class="stats-value">{{ $total }}</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 5px;">
                <div class="stats-box" style="border-left: 3px solid #3b82f6;">
                    <div class="stats-label">Open</div>
                    <div class="stats-value">{{ $open }}</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 5px;">
                <div class="stats-box" style="border-left: 3px solid #f59e0b;">
                    <div class="stats-label">On Progress</div>
                    <div class="stats-value">{{ $inProgress }}</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 5px;">
                <div class="stats-box" style="border-left: 3px solid #10b981;">
                    <div class="stats-label">Resolved</div>
                    <div class="stats-value">{{ $resolved }}</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 0 0 5px;">
                <div class="stats-box" style="border-left: 3px solid #ef4444;">
                    <div class="stats-label">Rejected</div>
                    <div class="stats-value">{{ $rejected }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 15%;">Kode Tiket</th>
                <th style="width: 30%;">Judul</th>
                <th style="width: 15%;">Pelapor</th>
                <th style="width: 10%;">Prioritas</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Tgl Dibuat</th>
                <th style="width: 10%;">Tgl Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    <td style="font-family: monospace; font-weight: bold; color: #475569;">#{{ $ticket->code }}</td>
                    <td>
                        <strong style="color: #0f172a;">{{ $ticket->title }}</strong><br>
                        <span style="font-size: 9px; color: #64748b;">{{ Str::limit($ticket->description, 60) }}</span>
                    </td>
                    <td>{{ $ticket->user?->name ?? 'N/A' }}</td>
                    <td>
                        @if($ticket->priority == 0)
                            <span class="badge badge-low">Low</span>
                        @elseif($ticket->priority == 1)
                            <span class="badge badge-medium">Medium</span>
                        @else
                            <span class="badge badge-high">High</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->status == 0)
                            <span class="badge badge-open">Open</span>
                        @elseif($ticket->status == 1)
                            <span class="badge badge-progress">On Progress</span>
                        @elseif($ticket->status == 2)
                            <span class="badge badge-resolved">Resolved</span>
                        @else
                            <span class="badge badge-rejected">Rejected</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}</td>
                    <td>{{ $ticket->completed_at ? \Carbon\Carbon::parse($ticket->completed_at)->format('d-m-Y') : '-' }}</td>
                </tr>
            @endforeach
            @if($tickets->isEmpty())
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 20px;">Tidak ada data tiket pada periode ini.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh Sistem Ticketing QuTick. Halaman 1 dari 1
    </div>
</body>
</html>
