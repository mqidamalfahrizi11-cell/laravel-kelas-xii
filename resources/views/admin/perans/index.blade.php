@extends('admin.layouts.app')

@section('title', 'Kelola Peran Film')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <span style="font-size: 18px;">
          <i class="fa fa-id-badge" style="color: #FF8D1B;"></i> Daftar Peran Pemeran Film
        </span>
        <a href="{{ route('admin.perans.create') }}" class="btn btn-sm btn-admin-primary">
          <i class="fa fa-plus-circle"></i> Tambah Peran Baru
        </a>
      </div>

      <div class="panel-body">
        <!-- Search & Filter Bar -->
        <div class="row" style="margin-bottom: 20px;">
          <div class="col-md-8 col-sm-10">
            <form action="{{ route('admin.perans.index') }}" method="GET" class="form-inline">
              <div class="form-group" style="margin-right: 5px; margin-bottom: 5px;">
                <select name="film_id" class="form-control" onchange="this.form.submit()">
                  <option value="">-- Semua Film --</option>
                  @foreach($films as $f)
                    <option value="{{ $f->id }}" {{ request('film_id') == $f->id ? 'selected' : '' }}>
                      {{ $f->title }} ({{ $f->year }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="input-group" style="min-width: 260px; margin-bottom: 5px;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama karakter / film / aktor..." value="{{ request('search') }}">
                <span class="input-group-btn">
                  <button class="btn btn-admin-primary" type="submit">
                    <i class="fa fa-search"></i> Cari
                  </button>
                  @if(request('search') || request('film_id'))
                    <a href="{{ route('admin.perans.index') }}" class="btn btn-default" title="Reset Filter">
                      <i class="fa fa-times"></i>
                    </a>
                  @endif
                </span>
              </div>
            </form>
          </div>
          @if(request('search') || request('film_id'))
            <div class="col-md-4 col-sm-2 text-right" style="padding-top: 7px;">
              <span class="text-muted">
                Filter aktif
                @if(request('film_id') && ($selectedFilm = $films->firstWhere('id', request('film_id'))))
                  : Film "<strong>{{ $selectedFilm->title }}</strong>"
                @endif
              </span>
            </div>
          @endif
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-hover table-striped">
            <thead>
              <tr style="background: #fafafa;">
                <th style="width: 50px;" class="text-center">No</th>
                <th>Judul Film</th>
                <th>Nama Cast / Aktor</th>
                <th>Nama Peran / Karakter</th>
                <th style="width: 160px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($perans as $index => $peran)
                <tr>
                  <td class="text-center">{{ $perans->firstItem() + $index }}</td>
                  <td>
                    <i class="fa fa-film text-muted"></i>
                    <strong style="margin-left: 5px;">{{ $peran->film->title ?? '-' }}</strong>
                    @if(optional($peran->film)->year)
                      <small class="text-muted">({{ $peran->film->year }})</small>
                    @endif
                  </td>
                  <td>
                    <i class="fa fa-user text-muted"></i>
                    <span style="margin-left: 5px; font-weight: 600;">{{ $peran->cast->name ?? '-' }}</span>
                  </td>
                  <td>
                    <span class="label label-warning" style="font-size: 13px; color: #333; background: #ffe2c2;">
                      {{ $peran->actor }}
                    </span>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('admin.perans.edit', $peran->id) }}" class="btn btn-xs btn-primary" title="Edit Peran">
                      <i class="fa fa-pencil"></i> Edit
                    </a>

                    <form action="{{ route('admin.perans.destroy', $peran->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran &quot;{{ addslashes($peran->actor) }}&quot;?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-danger" title="Hapus Peran">
                        <i class="fa fa-trash"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted" style="padding: 30px;">
                    <i class="fa fa-info-circle fa-2x" style="margin-bottom: 10px; display: block;"></i>
                    Tidak ada data peran yang ditemukan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="row">
          <div class="col-md-6 col-sm-6" style="padding-top: 15px;">
            <p class="text-muted">
              Menampilkan {{ $perans->firstItem() ?? 0 }} sampai {{ $perans->lastItem() ?? 0 }} dari {{ $perans->total() }} peran
            </p>
          </div>
          <div class="col-md-6 col-sm-6 text-right">
            {{ $perans->links() }}
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
