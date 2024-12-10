@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create User</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank if not changing">
        </div>

        <div class="col-md-6">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Leave blank if not changing">
        </div>

        <div class="col-md-12">
            <label for="role" class="form-label select2">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="spg" {{ $user->role == 'spg' ? 'selected' : '' }}>Spg</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="provinsi" class="form-label">Provinsi</label>
            <select name="provinsi" id="provinsi" class="form-control select2" required style="width: 100%;">
                <option value="">Pilih Provinsi</option>
                @foreach($provinsi as $key)
                <option value="{{ $key->id }}" {{ old('provinsi', $user->provinsi_id) == $key->id ? 'selected' : '' }}>{{ $key->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="kota" class="form-label">Kota</label>
            <select class="form-select select2" id="kota" name="kota" required>
                <option value="">Pilih Kota</option>
                @foreach($kabupaten as $key)
                <option value="{{ $key->id }}" {{ old('kota', $user->kabupaten_id) == $key->id ? 'selected' : '' }}>{{ $key->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="kecamatan" class="form-label">Kecamatan</label>
            <select class="form-select select2" id="kecamatan" name="kecamatan" required>
                <option value="">Pilih Kecamatan</option>
                @foreach($kecamatan as $key)
                <option value="{{ $key->id }}" {{ old('kecamatan', $user->kecamatan_id) == $key->id ? 'selected' : '' }}>{{ $key->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="kelurahan" class="form-label">Kelurahan</label>
            <select class="form-select select2" id="kelurahan" name="kelurahan" required>
                <option value="">Pilih Kelurahan</option>
                @foreach($kelurahan as $key)
                <option value="{{ $key->id }}" {{ old('kelurahan', $user->kelurahan_id) == $key->id ? 'selected' : '' }}>{{ $key->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-4">
        <a class="btn btn-danger" href="{{ route('admin.users') }}" role="button">Back</a>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    })
</script>
@endsection