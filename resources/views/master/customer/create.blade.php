@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="breadcrumb-item" aria-current="page"> Customer</li>
            <li class="breadcrumb-item active" aria-current="page"> Create</li>
        </ol>
    </nav>

    <form action="{{ route('master.customer.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Nama Toko</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="col-md-6">
                <label for="alamat" class="form-label">Alamat Toko</label>
                <input type="text" class="form-control" id="alamat" name="alamat" required>
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">Telepon</label>
                <input type="number" class="form-control" id="phone" name="phone" required>
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">Owner</label>
                <input type="text" class="form-control" id="owner" name="owner" required>
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
        
        <div class="mt-4">
            <a class="btn btn-danger" href="{{ route('master.customer.index') }}" role="button">Back</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
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
    });
</script>
@endsection