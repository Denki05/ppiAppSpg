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
            <li class="breadcrumb-item active" aria-current="page"><i class="fa-solid fa-basket-shopping"></i> Penjualan</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <a class="btn btn-success" href="{{ route('penjualan.create') }}" role="button">
                <i class="fa fa-plus" aria-hidden="true"></i> Create
            </a>

            <br>
            <br>

            <table id="salesTable" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Kode</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $sale->kode }}</td>
                            <td class="text-center">{{ $sale->nama_customer }}</td>
                            <td class="text-center">{{ $sale->tanggal_order }}</td>
                            <td class="text-center">
                                <a class="btn btn-info" href="" role="button">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                                <a class="btn btn-warning" href="" role="button">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                <a class="btn btn-danger" href="" role="button">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>
                                <a class="btn btn-primary" href="" role="button">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                </a>
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
       $('#salesTable').DataTable({
           paging: true,           // Enable pagination
           pageLength: 5,          // Number of records per page
           lengthMenu: [25, 50, 100], // Dropdown menu options for page length
           
       });
   });
</script>
@endsection