@extends('admin.layouts.app')

@section('title', 'Kelola Cast / Aktor')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <span style="font-size: 18px;">
          <i class="fa fa-users" style="color: #FF8D1B;"></i> Daftar Cast / Aktor
        </span>
        <a href="{{ route('admin.casts.create') }}" class="btn btn-sm btn-admin-primary">
          <i class="fa fa-plus-circle"></i> Tambah Cast Baru
        </a>
      </div>

      <div class="panel-body">
        <!-- Search Bar -->
        <div class="row" style="margin-bottom: 20px;">
          <div class="col-md-6 col-sm-8">
            <form action="{{ route('admin.casts.index') }}" method="GET" class="form-inline">
              <div class="input-group" style="width: 100%;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama aktor/aktris..." value="{{ request('search') }}">
                <span class="input-group-btn">
                  <button class="btn btn-admin-primary" type="submit">
                    <i class="fa fa-search"></i> Cari
                  </button>
                  @if(request('search'))
                    <a href="{{ route('admin.casts.index') }}" class="btn btn-default" title="Reset">
                      <i class="fa fa-times"></i>
                    </a>
                  @endif
                </span>
              </div>
            </form>
          </div>
          @if(request('search'))
            <div class="col-md-6 col-sm-4 text-right" style="padding-top: 7px;">
              <span class="text-muted">Hasil pencarian: "<strong>{{ request('search') }}</strong>"</span>
            </div>
          @endif
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-hover table-striped">
            <thead>
              <tr style="background: #fafafa;">
                <th style="width: 50px;" class="text-center">No</th>
                <th style="width: 220px;">Nama Cast / Aktor</th>
                <th style="width: 80px;" class="text-center">Usia</th>
                <th>Biografi</th>
                <th style="width: 120px;" class="text-center">Total Peran</th>
                <th style="width: 160px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($casts as $index => $cast)
                <tr>
                  <td class="text-center">{{ $casts->firstItem() + $index }}</td>
                  <td>
                    <strong style="font-size: 15px;">{{ $cast->name }}</strong>
                  </td>
                  <td class="text-center">
                    <span class="badge" style="background: #666;">{{ $cast->age }} th</span>
                  </td>
                  <td>
                    <small class="text-muted">{{ Str::limit($cast->bio, 120) }}</small>
                  </td>
                  <td class="text-center">
                    <span class="label label-success">{{ $cast->perans_count ?? 0 }} Peran</span>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('admin.casts.edit', $cast->id) }}" class="btn btn-xs btn-primary" title="Edit Cast">
                      <i class="fa fa-pencil"></i> Edit
                    </a>

                    <form action="{{ route('admin.casts.destroy', $cast->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus cast &quot;{{ addslashes($cast->name) }}&quot;?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-danger" title="Hapus Cast">
                        <i class="fa fa-trash"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                    <i class="fa fa-info-circle fa-2x" style="margin-bottom: 10px; display: block;"></i>
                    Tidak ada data cast yang ditemukan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="row">
          <div class="col-md-6 col-sm-6" style="padding-top: 15px;">
            <p class="text-muted">
              Menampilkan {{ $casts->firstItem() ?? 0 }} sampai {{ $casts->lastItem() ?? 0 }} dari {{ $casts->total() }} cast
            </p>
          </div>
          <div class="col-md-6 col-sm-6 text-right">
            {{ $casts->links() }}
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
