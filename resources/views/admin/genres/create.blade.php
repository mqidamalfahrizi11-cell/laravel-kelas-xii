@extends('admin.layouts.app')

@section('title', 'Tambah Genre')

@section('content')
<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="panel panel-admin">
      <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fa fa-plus-circle" style="color: #FF8D1B;"></i> Tambah Genre Baru</span>
        <a href="{{ route('admin.genres.index') }}" class="btn btn-xs btn-default">
          <i class="fa fa-arrow-left"></i> Kembali
        </a>
      </div>

      <div class="panel-body">
        <form action="{{ route('admin.genres.store') }}" method="POST">
          @csrf

          <div class="form-group @error('name') has-error @enderror">
            <label for="name" class="control-label">Nama Genre <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Action, Komedi, Horror" required maxlength="45">
            @error('name')
              <span class="help-block">{{ $message }}</span>
            @enderror
          </div>

          <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
            <button type="submit" class="btn btn-admin-primary">
              <i class="fa fa-save"></i> Simpan Genre
            </button>
            <a href="{{ route('admin.genres.index') }}" class="btn btn-default" style="margin-left: 5px;">
              Batal
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
