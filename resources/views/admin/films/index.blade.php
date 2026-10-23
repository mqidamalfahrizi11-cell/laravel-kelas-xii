@extends('admin.layouts.app')

@section('title', 'Kelola Film')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <span style="font-size: 18px;">
          <i class="fa fa-film" style="color: #FF8D1B;"></i> Daftar Film
        </span>
        <a href="{{ route('admin.films.create') }}" class="btn btn-sm btn-admin-primary">
          <i class="fa fa-plus-circle"></i> Tambah Film Baru
        </a>
      </div>

      <div class="panel-body">
        <!-- Search Bar -->
        <div class="row" style="margin-bottom: 20px;">
          <div class="col-md-6 col-sm-8">
            <form action="{{ route('admin.films.index') }}" method="GET" class="form-inline">
              <div class="input-group" style="width: 100%;">
                <input type="text" name="search" class="form-control" placeholder="Cari judul film..." value="{{ request('search') }}">
                <span class="input-group-btn">
                  <button class="btn btn-admin-primary" type="submit">
                    <i class="fa fa-search"></i> Cari
                  </button>
                  @if(request('search'))
                    <a href="{{ route('admin.films.index') }}" class="btn btn-default" title="Reset Pencarian">
                      <i class="fa fa-times"></i>
                    </a>
                  @endif
                </span>
              </div>
            </form>
          </div>
          @if(request('search'))
            <div class="col-md-6 col-sm-4 text-right" style="padding-top: 7px;">
              <span class="text-muted">Hasil pencarian untuk: "<strong>{{ request('search') }}</strong>"</span>
            </div>
          @endif
        </div>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-striped">
            <thead>
              <tr style="background: #fafafa;">
                <th style="width: 50px;" class="text-center">No</th>
                <th style="width: 60px;" class="text-center">Poster</th>
                <th>Judul Film</th>
                <th style="width: 90px;" class="text-center">Tahun</th>
                <th style="width: 130px;">Genre</th>
                <th style="width: 160px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($films as $index => $film)
                <tr>
                  <td class="text-center">
                    {{ $films->firstItem() + $index }}
                  </td>
                  <td class="text-center">
                    @if($film->poster)
                      <img src="/{{ $film->poster }}" alt="{{ $film->title }}" style="width: 45px; height: 60px; object-fit: cover; border-radius: 2px;">
                    @else
                      <span class="text-muted" style="font-size: 10px;">No Image</span>
                    @endif
                  </td>
                  <td>
                    <strong style="font-size: 15px;">{{ $film->title }}</strong>
                    <br>
                    <small class="text-muted">{{ Str::limit($film->sinopsis, 100) }}</small>
                  </td>
                  <td class="text-center">
                    <span class="label label-default">{{ $film->year }}</span>
                  </td>
                  <td>
                    <span class="label label-info">{{ $film->genre->name ?? '-' }}</span>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('admin.films.edit', $film->id) }}" class="btn btn-xs btn-primary" title="Edit Film">
                      <i class="fa fa-pencil"></i> Edit
                    </a>

                    <form action="{{ route('admin.films.destroy', $film->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus film &quot;{{ addslashes($film->title) }}&quot;? Data peran dan komentar terkait akan ikut terhapus.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-danger" title="Hapus Film">
                        <i class="fa fa-trash"></i> Hapus
                      </button>
                    </form>

                    <a href="{{ route('movies.show', $film->id) }}" target="_blank" class="btn btn-xs btn-default" title="Lihat di Halaman Publik">
                      <i class="fa fa-external-link"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                    <i class="fa fa-info-circle fa-2x" style="margin-bottom: 10px; display: block;"></i>
                    Tidak ada film yang ditemukan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="row">
          <div class="col-md-6 col-sm-6" style="padding-top: 15px;">
            <p class="text-muted">
              Menampilkan {{ $films->firstItem() ?? 0 }} sampai {{ $films->lastItem() ?? 0 }} dari {{ $films->total() }} film
            </p>
          </div>
          <div class="col-md-6 col-sm-6 text-right">
            {{ $films->links() }}
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
