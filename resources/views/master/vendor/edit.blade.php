@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Vendor</li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <form action="{{ route('master.vendor.update', $vendors->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Nama Toko</label>
                <input type="text" name="name" id="name" class="form-control" 
                       value="{{ old('name', $vendors->nama) }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="alamat" class="form-label">Alamat Toko</label>
                <input type="text" name="alamat" id="alamat" class="form-control" 
                       value="{{ old('alamat', $vendors->alamat) }}" required>
                @error('alamat') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">Telepon</label>
                <input type="number" class="form-control" id="phone" name="phone" 
                       value="{{ old('phone', $vendors->phone) }}" required>
                @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="owner" class="form-label">Owner</label>
                <input type="text" class="form-control" id="owner" name="owner" 
                       value="{{ old('owner', $vendors->owner) }}" required>
                @error('owner') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="provinsi" class="form-label">Provinsi</label>
                <select name="provinsi" id="provinsi" class="form-control select2" required>
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsi as $key)
                        <option value="{{ $key->id }}" 
                                {{ old('provinsi', $vendors->provinsi_id) == $key->id ? 'selected' : '' }}>
                            {{ $key->name }}
                        </option>
                    @endforeach
                </select>
                @error('provinsi') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="kota" class="form-label">Kota</label>
                <select class="form-select select2" id="kota" name="kota" required disabled>
                    <option value="">Pilih Kota</option>
                </select>
                @error('kota') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="kecamatan" class="form-label">Kecamatan</label>
                <select class="form-select select2" id="kecamatan" name="kecamatan" required disabled>
                    <option value="">Pilih Kecamatan</option>
                </select>
                @error('kecamatan') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="kelurahan" class="form-label">Kelurahan</label>
                <select class="form-select select2" id="kelurahan" name="kelurahan" required disabled>
                    <option value="">Pilih Kelurahan</option>
                </select>
                @error('kelurahan') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4">
                <a class="btn btn-danger" href="{{ route('master.vendor.index') }}" role="button">Back</a>
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