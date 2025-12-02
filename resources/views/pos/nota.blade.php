<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota #{{ $transaksi->kode_transaksi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10px;
            width: 80mm; /* Thermal printer width */
            margin: 0 auto;
        }
        
        .nota {
            width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
            letter-spacing: 2px;
        }
        
        .store-info {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .transaction-info {
            margin: 8px 0;
            font-size: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        
        .transaction-info div {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .items {
            margin: 8px 0;
        }
        
        .item {
            margin: 6px 0;
            font-size: 11px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .item-detail {
            display: flex;
            justify-content: space-between;
            margin: 1px 0;
            padding-left: 10px;
        }
        
        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        
        .totals {
            margin: 8px 0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 11px;
        }
        
        .total-row.grand-total {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            margin: 6px 0;
        }
        
        .summary {
            margin: 8px 0;
            font-size: 11px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        
        .footer-message {
            margin: 5px 0;
        }
        
        @media print {
            body {
                width: 80mm;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
        
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background: #45a049;
        }
        
        .back-button {
            position: fixed;
            top: 10px;
            left: 10px;
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            text-decoration: none;
            display: inline-block;
        }
        
        .back-button:hover {
            background: #0b7dda;
        }
    </style>
</head>
<body>
    <a href="{{ route('pos.index') }}" class="back-button no-print"> Kembali ke POS</a>
    <button class="print-button no-print" onclick="window.print()"> Print Nota</button>
    
    <div class="nota">
        <!-- Header -->
        <div class="header">
            <div class="store-name">TOKO </div>
            <div class="store-info">GROSIR DAN ECERAN</div>
            <div class="store-info">JL. UNNES</div>
            <div class="store-info">TELP. 081229676261</div>
        </div>
        
        <!-- Transaction Info -->
        <div class="transaction-info">
            <div>
                <span>Metode Pembayaran:</span>
                <span>{{ $transaksi->metode_transaksi }}</span>
            </div>
            <div>
                <span>Kasir:</span>
                <span>{{ strtoupper($transaksi->kasir) }}</span>
            </div>
            <div>
                <span>Nomor:</span>
                <span>{{ $transaksi->kode_transaksi }}</span>
            </div>
            @if($transaksi->nama_customer)
            <div>
                <span>Cust.:</span>
                <span>{{ strtoupper($transaksi->nama_customer) }}</span>
            </div>
            @endif
            <div>
                <span></span>
                <span>{{ $transaksi->tanggal_transaksi->format('d/m/Y') }}</span>
            </div>
            <div>
                <span></span>
                <span>{{ $transaksi->created_at->format('H:i:s') }}</span>
            </div>
        </div>
        
        <div class="separator"></div>
        
        <!-- Items -->
        <div class="items">
            @foreach($items as $item)
                <div class="item">
                    <div class="item-name">{{ strtoupper($item->merk ?? '-') }} {{ strtoupper($item->jenis ?? '') }}</div>
                    <div class="item-detail">
                        <span>{{ $item->ukuran_kemasan ?? '-' }} x {{ $item->qty }} @ Rp {{ number_format($item->harga_satuan,0,',','.') }}</span>
                        <span>Rp {{ number_format($item->subtotal,0,',','.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="separator"></div>
        
        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span>Jml Brg:</span>
                <span>{{ $items->sum('qty') }}</span>
            </div>
            <div class="summary-row">
                <span>Jml Item:</span>
                <span>{{ $items->count() }}</span>
            </div>
        </div>
        
        <!-- Totals -->
        <div class="totals">
            <div class="total-row grand-total">
                <span>Sub Total:</span>
                <span>{{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Bayar Tunai:</span>
                <span>{{ number_format($transaksi->bayar_tunai, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Kembali:</span>
                <span>{{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">TERIMA KASIH</div>
            <div class="footer-message">ATAS KUNJUNGAN ANDA</div>
        </div>
    </div>
    

</body>
</html>
