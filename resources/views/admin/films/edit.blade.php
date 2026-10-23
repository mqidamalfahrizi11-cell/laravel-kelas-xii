@extends('admin.layouts.app')

@section('title', 'Edit Film - ' . $film->title)

@section('content')
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fa fa-pencil-square-o" style="color: #FF8D1B;"></i> Edit Film: {{ $film->title }}</span>
        <a href="{{ route('admin.films.index') }}" class="btn btn-xs btn-default">
          <i class="fa fa-arrow-left"></i> Kembali ke Daftar
        </a>
      </div>

      <div class="panel-body">
        <form action="{{ route('admin.films.update', $film->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <!-- Judul Film -->
          <div class="form-group @error('title') has-error @enderror">
            <label for="title" class="control-label">Judul Film <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $film->title) }}" required>
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
                    <option value="{{ $genre->id }}" {{ old('genre_id', $film->genre_id) == $genre->id ? 'selected' : '' }}>
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
                <input type="number" name="year" id="year" class="form-control" value="{{ old('year', $film->year) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                @error('year')
                  <span class="help-block">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          <!-- Sinopsis -->
          <div class="form-group @error('sinopsis') has-error @enderror">
            <label for="sinopsis" class="control-label">Sinopsis <span class="text-danger">*</span></label>
            <textarea name="sinopsis" id="sinopsis" rows="5" class="form-control" required>{{ old('sinopsis', $film->sinopsis) }}</textarea>
            @error('sinopsis')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Poster Saat Ini & Ganti Poster -->
          <div class="form-group @error('poster') has-error @enderror">
            <label class="control-label">Poster Film</label>
            @if($film->poster)
              <div style="margin-bottom: 10px;">
                <p class="text-muted" style="font-size: 12px; margin-bottom: 5px;">Poster saat ini:</p>
                <img src="/{{ $film->poster }}" alt="{{ $film->title }}" style="width: 100px; height: 140px; object-fit: cover; border-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.2);">
              </div>
            @endif

            <label for="poster" style="font-weight: normal; font-size: 13px; margin-top: 5px;">
              Ganti Poster (Biarkan kosong jika tidak ingin mengubah poster):
            </label>
            <input type="file" name="poster" id="poster" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
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
              <i class="fa fa-save"></i> Perbarui Film
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
