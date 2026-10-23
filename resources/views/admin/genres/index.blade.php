@extends('admin.layouts.app')

@section('title', 'Kelola Genre')

@section('content')
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 18px;">
          <i class="fa fa-tags" style="color: #FF8D1B;"></i> Daftar Genre
        </span>
        <a href="{{ route('admin.genres.create') }}" class="btn btn-sm btn-admin-primary">
          <i class="fa fa-plus-circle"></i> Tambah Genre Baru
        </a>
      </div>

      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-striped">
            <thead>
              <tr style="background: #fafafa;">
                <th style="width: 60px;" class="text-center">No</th>
                <th>Nama Genre</th>
                <th style="width: 140px;" class="text-center">Jumlah Film</th>
                <th style="width: 160px;" class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($genres as $index => $genre)
                <tr>
                  <td class="text-center">{{ $genres->firstItem() + $index }}</td>
                  <td>
                    <strong>{{ $genre->name }}</strong>
                  </td>
                  <td class="text-center">
                    <span class="label label-info" style="font-size: 12px;">
                      {{ $genre->films_count ?? 0 }} Film
                    </span>
                  </td>
                  <td class="text-center">
                    <a href="{{ route('admin.genres.edit', $genre->id) }}" class="btn btn-xs btn-primary" title="Edit Genre">
                      <i class="fa fa-pencil"></i> Edit
                    </a>

                    <form action="{{ route('admin.genres.destroy', $genre->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus genre &quot;{{ addslashes($genre->name) }}&quot;?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-danger" title="Hapus Genre">
                        <i class="fa fa-trash"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted" style="padding: 30px;">
                    <i class="fa fa-info-circle fa-2x" style="margin-bottom: 10px; display: block;"></i>
                    Belum ada data genre.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="row">
          <div class="col-md-6 col-sm-6" style="padding-top: 15px;">
            <p class="text-muted">
              Menampilkan {{ $genres->firstItem() ?? 0 }} sampai {{ $genres->lastItem() ?? 0 }} dari {{ $genres->total() }} genre
            </p>
          </div>
          <div class="col-md-6 col-sm-6 text-right">
            {{ $genres->links() }}
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
