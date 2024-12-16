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
            <li class="breadcrumb-item active" aria-current="page"> Show</li>
        </ol>
    </nav>

    <form>
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Nama Toko</label>
                <input type="text" class="form-control" value="{{ $customer->nama }}" readonly>
            </div>

            <div class="col-md-6">
                <label for="alamat" class="form-label">Alamat Toko</label>
                <input type="text" class="form-control" value="{{ $customer->alamat }}" readonly>
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">Telepon</label>
                <input type="text" class="form-control" value="{{ $customer->phone }}" readonly>
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">Owner</label>
                <input type="text" class="form-control" value="{{ $customer->owner }}" readonly>
            </div>

            <div class="col-md-6">
            <label for="provinsi" class="form-label">Provinsi</label>
            <input type="text" class="form-control" value="{{ $customer->provinsi->name }}" readonly>
        </div>

        <div class="col-md-6">
            <label for="kota" class="form-label">Kota</label>
            <input type="text" class="form-control" value="{{ $customer->kabupaten->name }}" readonly>
        </div>

        <div class="col-md-6">
            <label for="kecamatan" class="form-label">Kecamatan</label>
            <input type="text" class="form-control" value="{{ $customer->kecamatan->name }}" readonly>
        </div>

        <div class="col-md-6">
            <label for="kelurahan" class="form-label">Kelurahan</label>
            <input type="text" class="form-control" value="{{ $customer->kelurahan->name }}" readonly>
        </div>
        
        <div class="mt-4">
            <a class="btn btn-danger" href="{{ route('master.customer.index') }}" role="button">Back</a>
            @if (auth()->user()->role == 'admin' OR auth()->user()->role == 'user')
            <a class="btn btn-warning" href="{{ route('master.customer.index') }}" role="button">Edit</a>
            @endif
        </div>
    </div>
    </form>
</div>

@endsection