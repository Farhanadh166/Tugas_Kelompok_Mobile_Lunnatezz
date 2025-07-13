<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Lunneettez | @yield('title', 'Dashboard')</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('assets/modules/jqvmap/dist/jqvmap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css') }}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #f3e8ff 100%);
    }
    .main-sidebar {
      background: linear-gradient(135deg, #a78bfa 0%, #f3e8ff 100%);
      border-top-right-radius: 32px;
      border-bottom-right-radius: 32px;
      box-shadow: 2px 0 16px rgba(160,120,200,0.08);
    }
    .sidebar-brand a, .sidebar-brand-sm a {
      color: #7c3aed !important;
      font-weight: 900;
      font-size: 2rem;
      letter-spacing: 2px;
      text-shadow: 0 2px 8px #f3e8ff;
    }
    .sidebar-menu .nav-link {
      color: #6d28d9 !important;
      font-weight: 600;
      border-radius: 12px;
      margin-bottom: 6px;
      transition: background 0.2s, color 0.2s;
    }
    .sidebar-menu .nav-link.active, .sidebar-menu .nav-link:hover {
      background: #f3e8ff !important;
      color: #7c3aed !important;
    }
    .main-navbar {
      background: linear-gradient(90deg, #a78bfa 0%, #f3e8ff 100%);
      box-shadow: 0 2px 12px rgba(160,120,200,0.08);
    }
    .main-footer {
      background: #f3e8ff;
      color: #7c3aed;
      font-weight: 600;
      border-top-left-radius: 24px;
      border-top-right-radius: 24px;
      box-shadow: 0 -2px 12px rgba(160,120,200,0.08);
    }
    @media (max-width: 991.98px) {
      .main-sidebar {
        border-radius: 0;
      }
    }
  </style>
</head>
<body>
  <div id="app">
    <div class="main-wrapper">
      <!-- Navbar -->
      @include('layouts.partials.navbar')
      <!-- Sidebar -->
      @include('layouts.partials.sidebar')
      <!-- Main Content -->
      <div class="main-content">
        @yield('content')
      </div>
      <!-- Footer -->
      @include('layouts.partials.footer')
    </div>
  </div>
  <!-- General JS Scripts -->
  <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/modules/popper.js') }}"></script>
  <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>
  <!-- JS Libraies -->
  <script src="{{ asset('assets/modules/jquery.sparkline.min.js') }}"></script>
  <script src="{{ asset('assets/modules/chart.min.js') }}"></script>
  <script src="{{ asset('assets/modules/owlcarousel2/dist/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('assets/modules/summernote/summernote-bs4.js') }}"></script>
  <script src="{{ asset('assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
  <!-- Page Specific JS File -->
  <script src="{{ asset('assets/js/page/index.js') }}"></script>
  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>
</body>
</html> 