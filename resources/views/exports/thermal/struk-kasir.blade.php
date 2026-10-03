<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk SPP #{{ $nomor_transaksi }}</title>
    <style>
        @page {
            margin: 0;
            size: 58mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            width: 54mm;
            margin: 2mm auto;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #000; margin: 4px 0; }
        .bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <div class="bold" style="font-size: 12px;">SMK NEGERI CONTOH</div>
        <div>BUKTI PEMBAYARAN SPP</div>
    </div>

    <div class="divider"></div>

    <table>
        <tr><td>No:</td><td class="text-right">{{ $nomor_transaksi }}</td></tr>
        <tr><td>Tgl:</td><td class="text-right">{{ $tanggal_bayar }}</td></tr>
        <tr><td>Siswa:</td><td class="text-right">{{ $nama_siswa }}</td></tr>
        <tr><td>NIS:</td><td class="text-right">{{ $nis }}</td></tr>
        <tr><td>Kelas:</td><td class="text-right">{{ $kelas }}</td></tr>
        <tr><td>Periode:</td><td class="text-right">{{ $periode }}</td></tr>
        <tr><td>Metode:</td><td class="text-right">{{ strtoupper(is_object($metode) ? $metode->value : $metode) }}</td></tr>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="bold">DIBAYAR:</td>
            <td class="text-right bold">Rp {{ number_format($jumlah_dibayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Sisa:</td>
            <td class="text-right">Rp {{ number_format($sisa_tagihan, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center">
        <div>Kasir: {{ $petugas }}</div>
        <div>Terima kasih atas pembayarannya.</div>
    </div>

</body>
</html>
