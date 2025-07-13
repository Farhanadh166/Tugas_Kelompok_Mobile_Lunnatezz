<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand" style="font-size:2rem; font-weight:900; color:#7c3aed; letter-spacing:2px; text-shadow:0 2px 8px #f3e8ff;">
      <a href="{{ url('dashboard') }}">LUNNEETTEZ</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="{{ url('dashboard') }}">LZ</a>
    </div>
    <ul class="sidebar-menu">
      <li class="menu-header" style="color:#a78bfa;">MENU UTAMA</li>
      <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a href="{{ url('dashboard') }}" class="nav-link">
          <i class="fas fa-home" style="color:#7c3aed;"></i>
          <span>Dashboard</span>
        </a>
      </li>
      
      <li class="menu-header" style="color:#a78bfa; margin-top:20px;">MASTER DATA</li>
      <li class="nav-item {{ request()->is('kategori*') ? 'active' : '' }}">
        <a href="{{ route('kategori.index') }}" class="nav-link">
          <i class="fas fa-tags" style="color:#7c3aed;"></i>
          <span>Kategori Produk</span>
        </a>
      </li>
      <li class="nav-item {{ request()->is('produk*') ? 'active' : '' }}">
        <a href="{{ route('produk.index') }}" class="nav-link">
          <i class="fas fa-gem" style="color:#7c3aed;"></i>
          <span>Produk</span>
        </a>
      </li>
      
      <li class="menu-header" style="color:#a78bfa; margin-top:20px;">TRANSAKSI</li>
      <li class="nav-item {{ request()->is('pesanan*') ? 'active' : '' }}">
        <a href="{{ route('pesanan.index') }}" class="nav-link">
          <i class="fas fa-shopping-bag" style="color:#7c3aed;"></i>
          <span>Daftar Pesanan</span>
        </a>
      </li>
      <li class="nav-item {{ request()->is('keranjang*') ? 'active' : '' }}">
        <a href="{{ route('keranjang.index') }}" class="nav-link">
          <i class="fas fa-shopping-cart" style="color:#7c3aed;"></i>
          <span>Keranjang Aktif</span>
        </a>
      </li>
      <li class="nav-item {{ request()->is('pembayaran*') ? 'active' : '' }}">
        <a href="{{ route('pembayaran.index') }}" class="nav-link">
          <i class="fas fa-credit-card" style="color:#7c3aed;"></i>
          <span>Pembayaran</span>
        </a>
      </li>
      
      <li class="menu-header" style="color:#a78bfa; margin-top:20px;">LAPORAN</li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-chart-bar" style="color:#7c3aed;"></i>
          <span>Laporan Penjualan</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-chart-pie" style="color:#7c3aed;"></i>
          <span>Laporan Produk</span>
        </a>
      </li>
      
      <li class="menu-header" style="color:#a78bfa; margin-top:20px;">PENGATURAN</li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-user-cog" style="color:#7c3aed;"></i>
          <span>Profil Admin</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-cog" style="color:#7c3aed;"></i>
          <span>Pengaturan Aplikasi</span>
        </a>
      </li>
    </ul>
  </aside>
</div> 