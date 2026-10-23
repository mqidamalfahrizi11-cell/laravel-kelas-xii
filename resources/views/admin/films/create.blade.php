@extends('admin.layouts.app')

@section('title', 'Tambah Film')

@section('content')
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fa fa-plus-circle" style="color: #FF8D1B;"></i> Tambah Film Baru</span>
        <a href="{{ route('admin.films.index') }}" class="btn btn-xs btn-default">
          <i class="fa fa-arrow-left"></i> Kembali ke Daftar
        </a>
      </div>

      <div class="panel-body">
        <form action="{{ route('admin.films.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <!-- Judul Film -->
          <div class="form-group @error('title') has-error @enderror">
            <label for="title" class="control-label">Judul Film <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: The Dark Knight" required>
            @error('title')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <div class="row">
            <!-- Genre -->
            <div class="col-md-6">
              <div class="form-group @error('genre_id') has-error @enderror">
                <label for="genre_id" class="control-label">Genre <span class="text-danger">*</span></label>
                <select name="genre_id" id="genre_id" class="form-control" required>
                  <option value="">-- Pilih Genre --</option>
                  @foreach($genres as $genre)
                    <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                      {{ $genre->name }}
                    </option>
                  @endforeach
                </select>
                @error('genre_id')
                  <span class="help-block">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <!-- Tahun Rilis -->
            <div class="col-md-6">
              <div class="form-group @error('year') has-error @enderror">
                <label for="year" class="control-label">Tahun Rilis <span class="text-danger">*</span></label>
                <input type="number" name="year" id="year" class="form-control" value="{{ old('year', date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                @error('year')
                  <span class="help-block">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          <!-- Sinopsis -->
          <div class="form-group @error('sinopsis') has-error @enderror">
            <label for="sinopsis" class="control-label">Sinopsis <span class="text-danger">*</span></label>
            <textarea name="sinopsis" id="sinopsis" rows="5" class="form-control" placeholder="Tuliskan sinopsis alur cerita film..." required>{{ old('sinopsis') }}</textarea>
            @error('sinopsis')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Poster -->
          <div class="form-group @error('poster') has-error @enderror">
            <label for="poster" class="control-label">Poster Film <span class="text-danger">*</span></label>
            <input type="file" name="poster" id="poster" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
            <p class="help-block" style="font-size: 12px; margin-top: 5px;">
              Format: JPG, JPEG, PNG, WEBP. Maksimal ukuran file: 2 MB (2048 KB).
            </p>
            @error('poster')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Buttons -->
          <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
            <button type="submit" class="btn btn-admin-primary">
              <i class="fa fa-save"></i> Simpan Film
            </button>
            <a href="{{ route('admin.films.index') }}" class="btn btn-default" style="margin-left: 5px;">
              Batal
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
