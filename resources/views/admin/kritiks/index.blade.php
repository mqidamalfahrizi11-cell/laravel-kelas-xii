@extends('admin.layouts.app')

@section('title', 'Kelola Komentar')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <span style="font-size: 18px;">
          <i class="fa fa-comments" style="color: #FF8D1B;"></i> Daftar Komentar & Rating Film
        </span>
      </div>

      <div class="panel-body">
        <!-- Search Bar -->
        <div class="row" style="margin-bottom: 20px;">
          <div class="col-md-6 col-sm-8">
            <form action="{{ route('admin.kritiks.index') }}" method="GET" class="form-inline">
              <div class="input-group" style="width: 100%;">
                <input type="text" name="search" class="form-control" placeholder="Cari komentar / nama user / film..." value="{{ request('search') }}">
                <span class="input-group-btn">
                  <button class="btn btn-admin-primary" type="submit">
                    <i class="fa fa-search"></i> Cari
                  </button>
                  @if(request('search'))
                    <a href="{{ route('admin.kritiks.index') }}" class="btn btn-default" title="Reset">
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
                <th style="width: 170px;">Nama User</th>
                <th style="width: 200px;">Judul Film</th>
                <th style="width: 100px;" class="text-center">Rating</th>
                <th>Isi Komentar</th>
                <th style="width: 150px;" class="text-center">Tanggal</th>
                <th style="width: 90px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($kritiks as $index => $kritik)
                <tr>
                  <td class="text-center">{{ $kritiks->firstItem() + $index }}</td>
                  <td>
                    <i class="fa fa-user text-muted"></i>
                    <strong style="margin-left: 5px;">{{ $kritik->user?->name ?? 'Pengguna dihapus' }}</strong>
                  </td>
                  <td>
                    <i class="fa fa-film text-muted"></i>
                    <span style="margin-left: 5px;">{{ $kritik->film->title ?? '-' }}</span>
                  </td>
                  <td class="text-center">
                    <span class="label label-warning" style="font-size: 12px; background: #FF8D1B;">
                      <i class="fa fa-star"></i> {{ $kritik->rating }} / 5
                    </span>
                  </td>
                  <td>
                    <span>{{ Str::limit($kritik->comment, 80) }}</span>
                  </td>
                  <td class="text-center" style="font-size: 13px;">
                    {{ $kritik->created_at ? $kritik->created_at->format('d M Y, H:i') : '-' }}
                  </td>
                  <td class="text-center">
                    <form action="{{ route('admin.kritiks.destroy', $kritik->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-danger" title="Hapus Komentar">
                        <i class="fa fa-trash"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                    <i class="fa fa-info-circle fa-2x" style="margin-bottom: 10px; display: block;"></i>
                    Tidak ada komentar yang ditemukan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="row">
          <div class="col-md-6 col-sm-6" style="padding-top: 15px;">
            <p class="text-muted">
              Menampilkan {{ $kritiks->firstItem() ?? 0 }} sampai {{ $kritiks->lastItem() ?? 0 }} dari {{ $kritiks->total() }} komentar
            </p>
          </div>
          <div class="col-md-6 col-sm-6 text-right">
            {{ $kritiks->links() }}
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
