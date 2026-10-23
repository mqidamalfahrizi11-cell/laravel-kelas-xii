@extends('admin.layouts.app')

@section('title', 'Tambah Cast')

@section('content')
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fa fa-plus-circle" style="color: #FF8D1B;"></i> Tambah Cast / Aktor Baru</span>
        <a href="{{ route('admin.casts.index') }}" class="btn btn-xs btn-default">
          <i class="fa fa-arrow-left"></i> Kembali
        </a>
      </div>

      <div class="panel-body">
        <form action="{{ route('admin.casts.store') }}" method="POST">
          @csrf

          <div class="row">
            <div class="col-md-8">
              <div class="form-group @error('name') has-error @enderror">
                <label for="name" class="control-label">Nama Aktor/Aktris <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Robert Downey Jr." required maxlength="45">
                @error('name')
                  <span class="help-block">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group @error('age') has-error @enderror">
                <label for="age" class="control-label">Usia (Tahun) <span class="text-danger">*</span></label>
                <input type="number" name="age" id="age" class="form-control" value="{{ old('age') }}" min="1" max="120" placeholder="Contoh: 45" required>
                @error('age')
                  <span class="help-block">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>

          <div class="form-group @error('bio') has-error @enderror">
            <label for="bio" class="control-label">Biografi <span class="text-danger">*</span></label>
            <textarea name="bio" id="bio" rows="5" class="form-control" placeholder="Tuliskan biografi singkat pemeran..." required>{{ old('bio') }}</textarea>
            @error('bio')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
            <button type="submit" class="btn btn-admin-primary">
              <i class="fa fa-save"></i> Simpan Cast
            </button>
            <a href="{{ route('admin.casts.index') }}" class="btn btn-default" style="margin-left: 5px;">
              Batal
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
