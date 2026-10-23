@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
  <div class="col-md-12">
    <h2 style="margin-top: 0; margin-bottom: 20px; font-weight: bold; color: #212121;">
      <i class="fa fa-dashboard" style="color: #FF8D1B;"></i> Dashboard Ringkasan
    </h2>
  </div>
</div>

<!-- Stat Cards -->
<div class="row">
  <!-- Film Card -->
  <div class="col-md-2 col-sm-4 col-xs-6">
    <div class="panel panel-admin" style="border-left: 4px solid #FF8D1B;">
      <div class="panel-body text-center">
        <i class="fa fa-film fa-2x" style="color: #FF8D1B; margin-bottom: 10px;"></i>
        <div style="font-size: 26px; font-weight: bold;">{{ number_format($filmCount) }}</div>
        <div class="text-muted" style="text-transform: uppercase; font-size: 12px;">Total Film</div>
      </div>
    </div>
  </div>

  <!-- Genre Card -->
  <div class="col-md-2 col-sm-4 col-xs-6">
    <div class="panel panel-admin" style="border-left: 4px solid #337ab7;">
      <div class="panel-body text-center">
        <i class="fa fa-tags fa-2x" style="color: #337ab7; margin-bottom: 10px;"></i>
        <div style="font-size: 26px; font-weight: bold;">{{ number_format($genreCount) }}</div>
        <div class="text-muted" style="text-transform: uppercase; font-size: 12px;">Total Genre</div>
      </div>
    </div>
  </div>

  <!-- Cast Card -->
  <div class="col-md-2 col-sm-4 col-xs-6">
    <div class="panel panel-admin" style="border-left: 4px solid #5cb85c;">
      <div class="panel-body text-center">
        <i class="fa fa-users fa-2x" style="color: #5cb85c; margin-bottom: 10px;"></i>
        <div style="font-size: 26px; font-weight: bold;">{{ number_format($castCount) }}</div>
        <div class="text-muted" style="text-transform: uppercase; font-size: 12px;">Total Cast</div>
      </div>
    </div>
  </div>

  <!-- User Card -->
  <div class="col-md-3 col-sm-6 col-xs-6">
    <div class="panel panel-admin" style="border-left: 4px solid #f0ad4e;">
      <div class="panel-body text-center">
        <i class="fa fa-user fa-2x" style="color: #f0ad4e; margin-bottom: 10px;"></i>
        <div style="font-size: 26px; font-weight: bold;">{{ number_format($userCount) }}</div>
        <div class="text-muted" style="text-transform: uppercase; font-size: 12px;">Total Pengguna</div>
      </div>
    </div>
  </div>

  <!-- Komentar Card -->
  <div class="col-md-3 col-sm-6 col-xs-6">
    <div class="panel panel-admin" style="border-left: 4px solid #d9534f;">
      <div class="panel-body text-center">
        <i class="fa fa-comments fa-2x" style="color: #d9534f; margin-bottom: 10px;"></i>
        <div style="font-size: 26px; font-weight: bold;">{{ number_format($komentarCount) }}</div>
        <div class="text-muted" style="text-transform: uppercase; font-size: 12px;">Total Komentar</div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Films Table -->
<div class="row" style="margin-top: 15px;">
  <div class="col-md-12">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fa fa-clock-o" style="color: #FF8D1B;"></i> 5 Film Terbaru</span>
        <a href="{{ route('admin.films.index') }}" class="btn btn-xs btn-admin-primary">
          Lihat Semua Film &raquo;
        </a>
      </div>
      <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
          <table class="table table-hover table-striped" style="margin-bottom: 0;">
            <thead>
              <tr style="background: #fafafa;">
                <th style="width: 70px;">Poster</th>
                <th>Judul Film</th>
                <th style="width: 100px;">Tahun</th>
                <th>Genre</th>
                <th style="width: 150px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentFilms as $film)
                <tr>
                  <td>
                    @if($film->poster)
                      <img src="/{{ $film->poster }}" alt="{{ $film->title }}" style="width: 50px; height: 70px; object-fit: cover; border-radius: 2px;">
                    @else
                      <span class="text-muted" style="font-size: 11px;">No Poster</span>
                    @endif
                  </td>
                  <td>
                    <strong>{{ $film->title }}</strong>
                    <br>
                    <small class="text-muted">{{ Str::limit($film->sinopsis, 90) }}</small>
                  </td>
                  <td><span class="label label-default">{{ $film->year }}</span></td>
                  <td>
                    <span class="label label-info">{{ $film->genre->name ?? '-' }}</span>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('admin.films.edit', $film->id) }}" class="btn btn-xs btn-primary">
                      <i class="fa fa-pencil"></i> Edit
                    </a>
                    <a href="{{ route('movies.show', $film->id) }}" target="_blank" class="btn btn-xs btn-default">
                      <i class="fa fa-eye"></i> Lihat
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted" style="padding: 20px;">
                    Belum ada film yang terdaftar.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
