@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create User</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ e($error) }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ e(session('success')) }}
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- User Details -->
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    class="form-control" 
                    value="{{ old('name', $user->name) }}" 
                    required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    class="form-control" 
                    value="{{ old('email', $user->email) }}" 
                    required>
            </div>
            
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control" 
                        placeholder="Leave blank if not changing">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', 'togglePasswordIcon')">
                        <i id="togglePasswordIcon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        class="form-control" 
                        placeholder="Leave blank if not changing">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation', 'toggleConfirmPasswordIcon')">
                        <i id="toggleConfirmPasswordIcon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Dropdowns -->
            <div class="col-md-6">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-control select2" required>
                    <option value="dev" {{ $user->role === 'dev' ? 'selected' : '' }}>Dev</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="spg" {{ $user->role === 'spg' ? 'selected' : '' }}>SPG</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="vendor" class="form-label">Vendor</label>
                <select name="vendor" id="vendor" class="form-control select2" required>
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor', $user->vendor_id) == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Cascading Dropdowns -->
            <div class="col-md-6">
                <label for="provinsi" class="form-label">Provinsi</label>
                <select name="provinsi" id="provinsi" class="form-control select2" required>
                    <option value="">Select Provinsi</option>
                    @foreach($provinsi as $prov)
                        <option value="{{ $prov->id }}" {{ old('provinsi', $user->provinsi_id) == $prov->id ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                @php
                    $kabupaten = DB::table('regencies')->where('id', $user->kabupaten_id)->first();
                @endphp
                <label for="kota" class="form-label">Kota</label>
                <select name="kota" id="kota" class="form-control select2" required>
                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->name }}</option>
                </select>
            </div>
            <div class="col-md-6">
                @php
                    $kecamatan = DB::table('districts')->where('id', $user->kecamatan_id)->first();
                @endphp
                <label for="kecamatan" class="form-label">Kecamatan</label>
                <select name="kecamatan" id="kecamatan" class="form-control select2" required>
                    <option value="{{ $kecamatan->id ?? '' }}">{{ $kecamatan->name ?? '' }}</option>
                </select>
            </div>
            <div class="col-md-6">
                @php
                    $kelurahan = DB::table('villages')->where('id', $user->kelurahan_id)->first();
                @endphp
                <label for="kelurahan" class="form-label">Kelurahan</label>
                <select name="kelurahan" id="kelurahan" class="form-control select2" required>
                    <option value="{{ $kelurahan->id ?? '' }}">{{ $kelurahan->name ?? '' }}</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Is Cash</label>
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="is_cash" 
                        id="is_cash" 
                        value="1" 
                        {{ old('is_cash', $user->is_cash) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_cash">
                        Iya (centang jika cash)
                    </label>
                </div>
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
    function togglePassword(fieldId, iconId) {
        let passwordField = document.getElementById(fieldId);
        let icon = document.getElementById(iconId);

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    $(document).ready(function () {
        $('.select2').select2();

        $('#provinsi').change(function () {
            let provinsiID = $(this).val();
            $('#kota').empty().append('<option value="">Pilih Kota</option>').prop('disabled', true);
            $('#kecamatan, #kelurahan').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);

            if (provinsiID) {
                $.ajax({
                    url: '{{ route('admin.users.getKabupaten', ':provinsiID') }}'.replace(':provinsiID', provinsiID),
                    type: 'GET',
                    success: function (data) {
                        $('#kota').prop('disabled', false);
                        data.forEach(function (kabupaten) {
                            $('#kota').append(`<option value="${kabupaten.id}">${kabupaten.name}</option>`);
                        });
                    },
                    error: function () {
                        alert('Failed to load Kota. Please try again later.');
                    }
                });
            }
        });

        $('#kota').change(function () {
            let kabupatenID = $(this).val();
            $('#kecamatan').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
            $('#kelurahan').empty().append('<option value="">Pilih Kelurahan</option>').prop('disabled', true);

            if (kabupatenID) {
                $.ajax({
                    url: '{{ route('admin.users.getKecamatan', ':kabupatenID') }}'.replace(':kabupatenID', kabupatenID),
                    type: 'GET',
                    success: function (data) {
                        $('#kecamatan').prop('disabled', false);
                        data.forEach(function (kecamatan) {
                            $('#kecamatan').append(`<option value="${kecamatan.id}">${kecamatan.name}</option>`);
                        });
                    },
                    error: function () {
                        alert('Failed to load Kecamatan. Please try again later.');
                    }
                });
            }
        });

        $('#kecamatan').change(function () {
            let kecamatanID = $(this).val();
            $('#kelurahan').empty().append('<option value="">Pilih Kelurahan</option>').prop('disabled', true);

            if (kecamatanID) {
                $.ajax({
                    url: '{{ route('admin.users.getKelurahan', ':kecamatanID') }}'.replace(':kecamatanID', kecamatanID),
                    type: 'GET',
                    success: function (data) {
                        $('#kelurahan').prop('disabled', false);
                        data.forEach(function (kelurahan) {
                            $('#kelurahan').append(`<option value="${kelurahan.id}">${kelurahan.name}</option>`);
                        });
                    },
                    error: function () {
                        alert('Failed to load Kelurahan. Please try again later.');
                    }
                });
            }
        });
    });
</script>
@endsection