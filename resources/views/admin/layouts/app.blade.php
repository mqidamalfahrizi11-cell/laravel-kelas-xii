<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin Panel') - OneMovies</title>

  <link href="{{ asset('template/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('template/css/font-awesome.min.css') }}" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700" rel="stylesheet" type="text/css">

  <style>
    body {
      font-family: 'Roboto Condensed', sans-serif;
      background-color: #f4f6f9;
      color: #333;
    }
    .navbar-admin {
      background: #212121;
      border: none;
      border-radius: 0;
      margin-bottom: 0;
      border-bottom: 3px solid #FF8D1B;
    }
    .navbar-admin .navbar-brand {
      color: #fff;
      font-weight: bold;
      font-size: 20px;
    }
    .navbar-admin .navbar-brand span {
      color: #FF8D1B;
    }
    .navbar-admin .navbar-nav > li > a {
      color: #ccc;
    }
    .navbar-admin .navbar-nav > li > a:hover {
      color: #fff;
      background: #333;
    }
    .admin-wrapper {
      display: flex;
      min-height: calc(100vh - 53px);
    }
    .admin-sidebar {
      width: 240px;
      background: #2b2b2b;
      flex-shrink: 0;
      padding-top: 20px;
    }
    .admin-sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .admin-sidebar ul li a {
      display: block;
      padding: 12px 20px;
      color: #ccc;
      text-decoration: none;
      font-size: 15px;
      border-left: 4px solid transparent;
      transition: 0.2s;
    }
    .admin-sidebar ul li a:hover {
      background: #1f1f1f;
      color: #fff;
      border-left-color: #FF8D1B;
    }
    .admin-sidebar ul li.active a {
      background: #1f1f1f;
      color: #fff;
      border-left-color: #FF8D1B;
      font-weight: bold;
    }
    .admin-sidebar ul li a i {
      width: 24px;
      margin-right: 5px;
    }
    .admin-sidebar .sidebar-heading {
      padding: 10px 20px 5px;
      font-size: 12px;
      text-transform: uppercase;
      color: #888;
      letter-spacing: 1px;
    }
    .admin-content {
      flex-grow: 1;
      padding: 25px 30px;
      overflow-x: auto;
    }
    .panel-admin {
      border: none;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      margin-bottom: 25px;
    }
    .panel-admin .panel-heading {
      background: #fff;
      border-bottom: 2px solid #eee;
      font-weight: bold;
      font-size: 16px;
      padding: 15px 20px;
    }
    .panel-admin .panel-body {
      background: #fff;
      padding: 20px;
    }
    .btn-admin-primary {
      background: #FF8D1B;
      color: #fff;
      border: none;
      font-weight: bold;
    }
    .btn-admin-primary:hover {
      background: #e67a0d;
      color: #fff;
    }
    .table > tbody > tr > td {
      vertical-align: middle;
    }
  </style>

  @stack('style')
</head>
<body>

  <!-- Top Navbar -->
  <nav class="navbar navbar-default navbar-admin">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
          One<span>Movies</span> Admin
        </a>
      </div>
      <ul class="nav navbar-nav navbar-right" style="margin-right: 10px;">
        <li>
          <a href="{{ route('home') }}" target="_blank">
            <i class="fa fa-globe"></i> Lihat Website Publik
          </a>
        </li>
        <li style="padding: 12px 15px; color: #fff;">
          <i class="fa fa-user"></i> {{ Auth::user()->name }}
        </li>
        <li style="padding-top: 8px; padding-right: 15px;">
          <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-sm" style="background: #FF8D1B; color: #fff; border: none; font-weight: bold; padding: 6px 14px;">
              <i class="fa fa-sign-out"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Content & Sidebar -->
  <div class="admin-wrapper">
    <!-- Sidebar -->
    <div class="admin-sidebar">
      <div class="sidebar-heading">Menu Utama</div>
      <ul>
        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}">
            <i class="fa fa-dashboard"></i> Dashboard
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.films.*') ? 'active' : '' }}">
          <a href="{{ route('admin.films.index') }}">
            <i class="fa fa-film"></i> Film
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.genres.*') ? 'active' : '' }}">
          <a href="{{ route('admin.genres.index') }}">
            <i class="fa fa-tags"></i> Genre
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.casts.*') ? 'active' : '' }}">
          <a href="{{ route('admin.casts.index') }}">
            <i class="fa fa-users"></i> Cast / Aktor
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.perans.*') ? 'active' : '' }}">
          <a href="{{ route('admin.perans.index') }}">
            <i class="fa fa-id-badge"></i> Peran
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.kritiks.*') ? 'active' : '' }}">
          <a href="{{ route('admin.kritiks.index') }}">
            <i class="fa fa-comments"></i> Komentar
          </a>
        </li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Terjadi kesalahan input:</strong>
          <ul style="margin-top: 5px; margin-bottom: 0;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </div>
  </div>

  <script src="{{ asset('template/js/jquery-2.1.4.min.js') }}"></script>
  <script src="{{ asset('template/js/bootstrap.min.js') }}"></script>
  @stack('script')
</body>
</html>
