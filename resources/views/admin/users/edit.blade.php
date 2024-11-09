@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit User</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="spg" {{ $user->role == 'spg' ? 'selected' : '' }}>Spg</option>
            </select>
        </div>

        <!-- Password Field (Optional) -->
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank if not changing">
        </div>

        <!-- Password Confirmation Field -->
        <div class="form-group mb-3">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Leave blank if not changing">
        </div>

        <hr>

        <div class="row g-3 mt-4">
            <div class="col">
                <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                <select name="provinsi" id="provinsi" class="form-control select2" required style="width: 100%;">
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsi as $key)
                    <option value="{{ $key->prov_id }}" {{ old('provinsi', $user->provinsi) == $key->prov_id ? 'selected' : '' }}>{{ $key->prov_name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="text_provinsi" id="text_provinsi" value="{{ old('text_provinsi', $user->text_provinsi) }}">
            </div>

            <div class="col">
                <label for="kota" class="form-label">Kota <span class="text-danger">*</span></label>
                <select name="kota" id="kota" class="form-control select2" required style="width: 100%;">
                    <option value="">Pilih Kota</option>
                </select>
                <input type="hidden" name="text_kota" id="text_kota" value="{{ old('text_kota', $user->text_kota) }}">
            </div>
        </div>

        <br>

        <a class="btn btn-danger" href="{{ route('admin.users') }}" role="button">Back</a>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#provinsi').on('change', function() {
            var provinceId = $(this).val();
            var provinceText = $('#provinsi option:selected').text(); // Get selected text
            $('#text_provinsi').val(provinceText); // Set to text_provinsi

            $('#kota').html('<option value="">Pilih Kota</option>'); // Reset city dropdown
            $('#text_kota').val(''); // Reset text_kota field

            if (provinceId) {
                $.ajax({
                    url: '/admin/users/getCities/' + provinceId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $.each(data, function(key, city) {
                            $('#kota').append(`<option value="${city.city_id}">${city.city_name}</option>`);
                        });
                    },
                    error: function() {
                        alert('Error loading cities');
                    }
                });
            }
        });

        $('#kota').on('change', function() {
            var cityText = $('#kota option:selected').text(); // Get selected text for city
            $('#text_kota').val(cityText); // Set to text_kota
        });
    })
</script>
@endsection