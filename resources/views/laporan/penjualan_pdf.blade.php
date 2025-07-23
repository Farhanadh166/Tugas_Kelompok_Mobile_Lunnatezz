<h2 style="text-align:center;">Laporan Penjualan</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>No Pesanan</th>
            <th>Pembeli</th>
            <th>Produk</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pesanan as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date('d-m-Y', strtotime($item->tanggal_pesanan)) }}</td>
            <td>{{ $item->order_number }}</td>
            <td>{{ $item->user->nama ?? '-' }}</td>
            <td>
                @foreach($item->detailPesanan as $detail)
                {{ $detail->produk->nama ?? '-' }} ({{ $detail->jumlah }})<br>
                @endforeach
            </td>
            <td>Rp{{ number_format($item->total_harga,0,',','.') }}</td>
            <td>{{ $item->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>