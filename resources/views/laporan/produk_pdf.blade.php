<h2 style="text-align:center;">Laporan Produk</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach($produk as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->kategori->nama ?? '-' }}</td>
            <td>Rp{{ number_format($item->harga,0,',','.') }}</td>
            <td>{{ $item->stok }}</td>
        </tr>
        @endforeach
    </tbody>
</table> 