<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #1a1a2e; }

        .ticket-wrapper { width: 100%; padding: 16px; }

        .ticket {
            border: 3px solid #1d4ed8;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Header biru */
        .ticket-header {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
            color: white;
            padding: 20px 24px;
            text-align: center;
        }
        .ticket-header .league { font-size: 11px; letter-spacing: 2px; opacity: 0.8; margin-bottom: 6px; }
        .ticket-header .teams { font-size: 20px; font-weight: bold; }
        .ticket-header .vs { font-size: 13px; opacity: 0.7; margin: 0 8px; }

        /* Body tiket */
        .ticket-body { padding: 20px 24px; background: white; }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .info-row { display: table-row; }
        .info-label {
            display: table-cell;
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 10px;
            width: 40%;
            font-weight: bold;
        }
        .info-value {
            display: table-cell;
            font-weight: bold;
            font-size: 12px;
            padding-bottom: 10px;
        }

        /* Divider putus-putus */
        .divider {
            border-top: 2px dashed #d1d5db;
            margin: 16px 0;
            position: relative;
        }

        /* QR area */
        .qr-section {
            display: table;
            width: 100%;
        }
        .qr-left { display: table-cell; vertical-align: middle; }
        .qr-right { display: table-cell; text-align: center; vertical-align: middle; width: 130px; }

        .qr-placeholder {
            width: 110px;
            height: 110px;
            border: 2px solid #1d4ed8;
            border-radius: 8px;
            display: inline-block;
            text-align: center;
            padding: 8px;
            font-size: 8px;
            color: #374151;
            word-break: break-all;
            font-family: monospace;
        }

        .seat-badge {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 12px;
            text-align: center;
        }
        .seat-badge .label { font-size: 10px; color: #6b7280; }
        .seat-badge .value { font-size: 22px; font-weight: bold; color: #1d4ed8; }

        /* Footer -->*/
        .ticket-footer {
            background: #f9fafb;
            padding: 12px 24px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
        .ticket-footer strong { color: #374151; }

        .category-badge {
            display: inline-block;
            background: #1d4ed8;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
<div class="ticket-wrapper">
    <div class="ticket">

        <!-- Header -->
        <div class="ticket-header">
            <div class="league">🏆 TIKET PERTANDINGAN SEPAK BOLA</div>
            <div class="teams">
                {{ $ticket->ticketCategory->footballMatch->home_team }}
                <span class="vs">VS</span>
                {{ $ticket->ticketCategory->footballMatch->away_team }}
            </div>
        </div>

        <!-- Body -->
        <div class="ticket-body">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">📅 Tanggal</div>
                    <div class="info-value">{{ $ticket->ticketCategory->footballMatch->match_date->format('d M Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">🕐 Waktu</div>
                    <div class="info-value">{{ $ticket->ticketCategory->footballMatch->match_date->format('H:i') }} WIB</div>
                </div>
                <div class="info-row">
                    <div class="info-label">🏟️ Stadion</div>
                    <div class="info-value">{{ $ticket->ticketCategory->footballMatch->stadium->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">📍 Kota</div>
                    <div class="info-value">{{ $ticket->ticketCategory->footballMatch->stadium->city }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">👤 Pemegang</div>
                    <div class="info-value">{{ $ticket->order->user->name }}</div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="qr-section">
                <div class="qr-left">
                    <span class="category-badge">{{ $ticket->ticketCategory->name }}</span>
                    <div class="seat-badge">
                        <div class="label">NOMOR KURSI</div>
                        <div class="value">{{ $ticket->seat_number }}</div>
                    </div>
                    <p style="font-size:10px; color:#6b7280;">
                        No. Tiket: <strong>{{ $ticket->id }}</strong><br>
                        No. Order: <strong>#{{ $ticket->order->id }}</strong>
                    </p>
                </div>
                <div class="qr-right">
                    {{-- QR Code dari library simplesoftwareio/simple-qrcode --}}
                    @if(class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class) && !empty($ticket->qr_code))
                        <img src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(110)->generate($ticket->qr_code)) }}" width="110" height="110" alt="QR Code" style="display:inline-block;">
                    @else
                        <div class="qr-placeholder">
                            <strong>QR CODE</strong><br><br>
                            {{ $ticket->qr_code ?? 'TKT-' . $ticket->id }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
            Tunjukkan tiket ini kepada petugas saat memasuki stadion.<br>
            <strong>Tiket ini hanya berlaku untuk 1 orang. Dilarang dipindahtangankan.</strong>
        </div>

    </div>
</div>
</body>
</html>
