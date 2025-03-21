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
            <li class="breadcrumb-item active" aria-current="page"> Vendor</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <a class="btn btn-success" href="{{ route('master.vendor.create') }}" role="button">
                <i class="fa fa-plus" aria-hidden="true"></i> Create
            </a>

            <br>
            <br>

            <table id="vendorTable" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Vendor</th>
                        <th class="text-center">Kecamatan</th>
                        <th class="text-center">Kota</th>
                        <th class="text-center">Provinsi</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $vendor)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $vendor->nama }}</td>
                        <td class="text-center">{{ $vendor->kecamatan->name ?? '-' }}</td>
                        <td class="text-center">{{ $vendor->kabupaten->name ?? '-' }}</td>
                        <td class="text-center">{{ $vendor->provinsi->name ?? '-' }}</td>
                        <td>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#vendorModal{{ $vendor->id }}">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="vendorModal{{ $vendor->id }}" tabindex="-1" aria-labelledby="vendorModalLabel{{ $vendor->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vendorModalLabel{{ $vendor->id }}">Vendor Detail {{ $vendor->nama  ?? '-'}}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Nama</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ $vendor->nama ?? '-'}}" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Alamat</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ $vendor->alamat ?? '-'}}" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Telepone</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ $vendor->phone ?? '-'}}" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Owner</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ $vendor->owner ?? '-'}}" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Provinsi</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ $vendor->provinsi->name ?? '-'}}" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Kota</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ $vendor->kabupaten->name ?? '-'}}" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Kecamatan</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ $vendor->kecamatan->name ?? '-'}}" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Kelurahan</label>
                                                    <input type="text" name="name" id="name" class="form-control" value="{{ $vendor->kelurahan->name ?? '-'}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (auth()->user()->role == 'dev' OR auth()->user()->role == 'admin')
                            <a class="btn btn-warning" href="{{ route('master.vendor.edit', $vendor->id) }}" role="button"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <form action="{{ route('master.vendor.destroy', $vendor->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
   $(document).ready(function() {
        $('#vendorTable').DataTable({
           paging: true,
           pageLength: 5,
           lengthMenu: [5, 25, 50, 100],
           order: [[1, 'asc']],
           responsive: true, // Enable responsiveness
           columnDefs: [
               { targets: [0, 5], orderable: false } // Disable sorting on # and Action columns
           ]
       });
   });
</script>
@endsection