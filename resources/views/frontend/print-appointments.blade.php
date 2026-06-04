<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient List — {{ \Carbon\Carbon::parse($printDate)->format('d M Y') }} | Dr. {{ $doctor->user->name }}</title>
    <style>
        /* ── Reset & Base ─────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1a2e44;
            background: #f4f6f9;
        }

        /* ── Print Page ───────────────────────────── */
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .page { box-shadow: none; margin: 0; border-radius: 0; }
            table { page-break-inside: avoid; }
        }

        /* ── Layout ───────────────────────────────── */
        .page {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.12);
            overflow: hidden;
        }

        /* ── Header ───────────────────────────────── */
        .print-header {
            background: linear-gradient(135deg, #1a2e44 0%, #0f6b8f 100%);
            color: #fff;
            padding: 28px 36px 22px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
        }
        .print-header .clinic-info h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -.5px;
        }
        .print-header .clinic-info p {
            font-size: .82rem;
            opacity: .8;
            margin-top: 3px;
        }
        .print-header .doc-info {
            text-align: right;
        }
        .print-header .doc-info h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }
        .print-header .doc-info p {
            font-size: .82rem;
            opacity: .8;
            margin-top: 2px;
        }

        /* ── Meta bar ─────────────────────────────── */
        .meta-bar {
            background: #e8f8fd;
            border-bottom: 1px solid #b2dff0;
            padding: 12px 36px;
            display: flex;
            align-items: center;
            gap: 28px;
            flex-wrap: wrap;
        }
        .meta-item { display: flex; align-items: center; gap: 7px; font-size: .85rem; font-weight: 600; color: #0f6b8f; }
        .meta-item .icon { font-size: 1rem; }
        .type-pill {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .type-pill.all     { background: #1a2e44; color: #fff; }
        .type-pill.online  { background: #20c0f3; color: #fff; }
        .type-pill.offline { background: #ff8c00; color: #fff; }

        /* ── Summary strip ────────────────────────── */
        .summary-strip {
            padding: 10px 36px;
            border-bottom: 1px solid #e8edf2;
            display: flex;
            gap: 24px;
            font-size: .82rem;
            color: #5a6a85;
        }
        .summary-strip strong { color: #1a2e44; }

        /* ── Table ────────────────────────────────── */
        .table-wrap { padding: 20px 36px 28px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr {
            background: #1a2e44;
            color: #fff;
        }
        thead th {
            padding: 11px 14px;
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            text-align: left;
        }
        thead th.center { text-align: center; }
        tbody tr {
            border-bottom: 1px solid #e8edf2;
            transition: background .15s;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #e8f8fd; }
        tbody td { padding: 10px 14px; font-size: .88rem; vertical-align: middle; }
        tbody td.center { text-align: center; }

        /* Serial # */
        .serial-no {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px; height: 30px;
            background: #1a2e44;
            color: #fff;
            border-radius: 50%;
            font-weight: 800;
            font-size: .8rem;
        }
        /* Type badges */
        .badge-type {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 700;
        }
        .badge-type.online  { background: #e0f7fc; color: #006994; }
        .badge-type.offline { background: #fff3e0; color: #b45309; }
        /* Status badges */
        .badge-status {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .73rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-status.pending   { background: #fef9c3; color: #854d0e; }
        .badge-status.confirmed { background: #dcfce7; color: #166534; }
        .badge-status.completed { background: #e0f2fe; color: #075985; }
        .badge-status.cancelled { background: #fee2e2; color: #991b1b; }
        /* Token */
        .token-tag {
            background: #fef3c7;
            color: #92400e;
            border-radius: 5px;
            padding: 2px 8px;
            font-size: .73rem;
            font-weight: 700;
        }

        /* ── Empty state ──────────────────────────── */
        .empty-print { text-align: center; padding: 48px 20px; color: #a0aec0; }
        .empty-print p { font-size: 1rem; }

        /* ── Footer ───────────────────────────────── */
        .print-footer {
            border-top: 1px solid #e8edf2;
            padding: 14px 36px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .78rem;
            color: #94a3b8;
        }

        /* ── Print Button ─────────────────────────── */
        .action-bar {
            max-width: 900px;
            margin: 0 auto 16px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding: 0 10px;
        }
        .btn-do-print {
            background: #1a2e44;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background .2s, transform .15s;
        }
        .btn-do-print:hover { background: #0f6b8f; transform: translateY(-1px); }
        .btn-back {
            background: #f1f5f9;
            color: #5a6a85;
            border: 1px solid #d0d7e2;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: background .2s;
        }
        .btn-back:hover { background: #e2e8f0; }
    </style>
</head>
<body>

{{-- ── Action Buttons (no-print) ──────────────────── --}}
<div class="action-bar no-print">
    <a href="{{ route('doctors.appointments') }}" class="btn-back">
        ← Appointments এ ফিরুন
    </a>
    <button class="btn-do-print" onclick="window.print()">
        🖨️ প্রিন্ট করুন
    </button>
</div>

{{-- ── Print Page ───────────────────────────────────── --}}
<div class="page">

    {{-- Header --}}
    <div class="print-header">
        <div class="clinic-info">
            <h1>{{ $siteSettings['site_name'] ?? 'abcsheba' }}</h1>
            <p>Patient Appointment List — Official Print</p>
        </div>
        <div class="doc-info">
            <h2>Dr. {{ $doctor->user->name }}</h2>
            <p>{{ optional($doctor->speciality)->name ?? 'Specialist' }}</p>
            @if($doctor->qualification)
                <p>{{ $doctor->qualification }}</p>
            @endif
        </div>
    </div>

    {{-- Meta bar --}}
    <div class="meta-bar">
        <div class="meta-item">
            📅 তারিখ:
            <strong>{{ \Carbon\Carbon::parse($printDate)->translatedFormat('l, d F Y') }}</strong>
        </div>
        <div class="meta-item">
            🏷️ ধরন:
            <span class="type-pill {{ $printType }}">
                {{ $printType === 'all' ? 'সব (Online + Offline)' : ucfirst($printType) }}
            </span>
        </div>
        <div class="meta-item">
            🖨️ প্রিন্ট সময়: <strong>{{ now()->format('h:i A') }}</strong>
        </div>
    </div>

    {{-- Summary --}}
    @php
        $onlineCount  = $appointments->where('type', 'online')->count();
        $offlineCount = $appointments->where('type', 'offline')->count();
    @endphp
    <div class="summary-strip">
        <span>মোট রোগী: <strong>{{ $appointments->count() }}</strong></span>
        @if($printType === 'all')
        <span>Online: <strong>{{ $onlineCount }}</strong></span>
        <span>Offline: <strong>{{ $offlineCount }}</strong></span>
        @endif
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        @if($appointments->isEmpty())
            <div class="empty-print">
                <p>এই তারিখে কোনো appointment নেই।</p>
            </div>
        @else
        <table>
            <thead>
                <tr>
                    <th class="center" style="width:50px">SL</th>
                    <th>রোগীর নাম</th>
                    <th>ফোন</th>
                    <th>সময়</th>
                    <th class="center">ধরন</th>
                    <th class="center">Token</th>
                    <th class="center">Status</th>
                    <th class="center">ফি (৳)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $index => $appt)
                <tr>
                    <td class="center">
                        <span class="serial-no">{{ $index + 1 }}</span>
                    </td>
                    <td>
                        <strong>{{ optional(optional($appt->patient)->user)->name ?? '—' }}</strong>
                        @if(optional(optional($appt->patient)->user)->email)
                            <br><small style="color:#94a3b8">{{ $appt->patient->user->email }}</small>
                        @endif
                    </td>
                    <td>{{ optional($appt->patient)->phone ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</td>
                    <td class="center">
                        <span class="badge-type {{ $appt->type }}">
                            {{ $appt->type === 'online' ? '🎥 Online' : '🏥 Offline' }}
                        </span>
                    </td>
                    <td class="center">
                        @if($appt->token_number)
                            <span class="token-tag">{{ $appt->token_number }}</span>
                        @else
                            <span style="color:#ccc">—</span>
                        @endif
                    </td>
                    <td class="center">
                        <span class="badge-status {{ $appt->status }}">{{ ucfirst($appt->status) }}</span>
                    </td>
                    <td class="center">
                        <strong>{{ number_format($appt->fee ?? 0) }}</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Footer --}}
    <div class="print-footer">
        <span>Generated by {{ $siteSettings['site_name'] ?? 'abcsheba' }} • {{ now()->format('d M Y, h:i A') }}</span>
        <span>Doctor: Dr. {{ $doctor->user->name }}</span>
    </div>

</div>

<script>
    // Auto-print on load (optional — comment out if not wanted)
    // window.onload = () => window.print();
</script>
</body>
</html>
