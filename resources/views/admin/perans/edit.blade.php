@extends('admin.layouts.app')

@section('title', 'Edit Peran - ' . $peran->actor)

@section('content')
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fa fa-pencil" style="color: #FF8D1B;"></i> Edit Peran: {{ $peran->actor }}</span>
        <a href="{{ route('admin.perans.index') }}" class="btn btn-xs btn-default">
          <i class="fa fa-arrow-left"></i> Kembali
        </a>
      </div>

      <div class="panel-body">
        <form action="{{ route('admin.perans.update', $peran->id) }}" method="POST">
          @csrf
          @method('PUT')

          <!-- Dropdown Film -->
          <div class="form-group @error('film_id') has-error @enderror">
            <label for="film_id" class="control-label">Pilih Film <span class="text-danger">*</span></label>
            <select name="film_id" id="film_id" class="form-control" required>
              <option value="">-- Pilih Film --</option>
              @foreach($films as $film)
                <option value="{{ $film->id }}" {{ old('film_id', $peran->film_id) == $film->id ? 'selected' : '' }}>
                  {{ $film->title }} ({{ $film->year }})
                </option>
              @endforeach
            </select>
            @error('film_id')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Dropdown Cast -->
          <div class="form-group @error('cast_id') has-error @enderror">
            <label for="cast_id" class="control-label">Pilih Aktor / Aktris (Cast) <span class="text-danger">*</span></label>
            <select name="cast_id" id="cast_id" class="form-control" required>
              <option value="">-- Pilih Cast --</option>
              @foreach($casts as $cast)
                <option value="{{ $cast->id }}" {{ old('cast_id', $peran->cast_id) == $cast->id ? 'selected' : '' }}>
                  {{ $cast->name }} (Usia: {{ $cast->age }} th)
                </option>
              @endforeach
            </select>
            @error('cast_id')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Nama Peran / Karakter -->
          <div class="form-group @error('actor') has-error @enderror">
            <label for="actor" class="control-label">Nama Peran / Karakter dalam Film <span class="text-danger">*</span></label>
            <input type="text" name="actor" id="actor" class="form-control" value="{{ old('actor', $peran->actor) }}" required maxlength="255">
            @error('actor')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
            <button type="submit" class="btn btn-admin-primary">
              <i class="fa fa-save"></i> Perbarui Peran
            </button>
            <a href="{{ route('admin.perans.index') }}" class="btn btn-default" style="margin-left: 5px;">
              Batal
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
