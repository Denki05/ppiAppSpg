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

    <form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
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

        <div class="col-md-6">
            <label for="role" class="form-label">Role</label>
            <select class="form-select select2" id="role" name="role" required>
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="spg">Spg</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="vendor" class="form-label">Vendors</label>
            <select class="form-select select2" id="vendor" name="vendor" required>
                <option value="">Pilih Vendor</option>
                @foreach( $vendors as $vendor )
                <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="provinsi" class="form-label">Provinsi</label>
            <select class="form-select select2" id="provinsi" name="provinsi" required>
                <option value="">Pilih Provinsi</option>
                @foreach($provinsi as $key)
                <option value="{{ $key->id }}">{{ $key->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="kota" class="form-label">Kota</label>
            <select class="form-select select2" id="kota" name="kota" required>
                <option value="">Pilih Kota</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="kecamatan" class="form-label">Kecamatan</label>
            <select class="form-select select2" id="kecamatan" name="kecamatan" required>
                <option value="">Pilih Kecamatan</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="kelurahan" class="form-label">Kelurahan</label>
            <select class="form-select select2" id="kelurahan" name="kelurahan" required>
                <option value="">Pilih Kelurahan</option>
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
                        value="1">
                    <label class="form-check-label" for="is_cash">
                        Iya (centang jika cash)
                    </label>
                </div>
        </div>
    </div>

    <div class="mt-4">
        <a class="btn btn-danger" href="{{ route('admin.users') }}" role="button">Back</a>
        <button type="submit" class="btn btn-primary">Create User</button>
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

    $(document).ready(function() {
        $('.select2').select2();

            $('#provinsi').change(function () {
                let provinsiID = $(this).val();
                $('#kota').empty().append('<option value="">Pilih Kota</option>');

                if (provinsiID) {
                    $.ajax({
                        url: '{{ route('admin.users.getKabupaten', ':provinsiID') }}'.replace(':provinsiID', provinsiID),
                        type: 'GET',
                        success: function (data) {
                            data.forEach(function (kabupaten) {
                                $('#kota').append(
                                    `<option value="${kabupaten.id}">${kabupaten.name}</option>`
                                );
                            });
                        }
                    });
                }
            });

            $('#kota').change(function () {
                let kabupatenID = $(this).val();
                $('#kecamatan').empty().append('<option value="">Pilih Kecamatan</option>');

                if (kabupatenID) {
                    $.ajax({
                        url: '{{ route('admin.users.getKecamatan', ':kabupatenID') }}'.replace(':kabupatenID', kabupatenID),
                        type: 'GET',
                        success: function (data) {
                            data.forEach(function (kecamatan) {
                                $('#kecamatan').append(
                                    `<option value="${kecamatan.id}">${kecamatan.name}</option>`
                                );
                            });
                        }
                    });
                }
            });

            $('#kecamatan').change(function () {
                let kecamatanID = $(this).val();
                $('#kelurahan').empty().append('<option value="">Pilih Kelurahan</option>');

                if (kecamatanID) {
                    $.ajax({
                        url: '{{ route('admin.users.getKelurahan', ':kecamatanID') }}'.replace(':kecamatanID', kecamatanID),
                        type: 'GET',
                        success: function (data) {
                            data.forEach(function (kelurahan) {
                                $('#kelurahan').append(
                                    `<option value="${kelurahan.id}">${kelurahan.name}</option>`
                                );
                            });
                        }
                    });
                }
            });
    })
</script>
@endsection
