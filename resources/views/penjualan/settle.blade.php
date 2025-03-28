@extends('layouts.app')

@section('content')
<div class="container">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><i class="fa-solid fa-eye"></i> Settle</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- <a class="btn btn-success" href="{{ route('penjualan.cek_jurnal') }}" role="button">
                <i class="fa fa-check" aria-hidden="true"></i> Cek
            </a> -->
            <br>
            <br>

            <table id="salesTable" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Kode</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Brand</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                        @if(in_array($sale->status, ['1', '2', '3']))
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $sale->kode }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($sale->tanggal_order)->format('d-m-Y') }}</td>
                            <td class="text-center">{{ $sale->brand_name }}</td>
                            <td class="text-center">
                                @if($sale->type == 0)
                                    {{ $sale->customer->nama }}, {{ $sale->customer->kecamatan->name ?? '-' }} - {{ $sale->customer->kabupaten->name  ?? '-'}} - {{ $sale->customer->provinsi->name ?? '-' }}
                                @else
                                    CASH
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->role == "dev" || Auth::user()->role == "admin")
                                    <a href="{{ route('penjualan.edit_settel', encrypt($sale->id)) }}" class="btn btn-warning"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @endif

                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#saleModal{{ $sale->id }}">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>

                                <!-- modal show -->
                                <div class="modal fade" id="saleModal{{ $sale->id }}" tabindex="-1" aria-labelledby="saleModalLabel{{ $sale->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="saleModalLabel{{ $sale->id }}">Jurnal Detail {{ $sale->kode }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p><strong>Kode:</strong> {{ $sale->kode }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Tanggal:</strong> {{ $sale->tanggal_order }}</p>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p><strong>Brand:</strong> {{ $sale->brand_name }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Customer:</strong>
                                                            @if($sale->type == 0)
                                                                {{ $sale->customer->nama }} - {{ $sale->customer->kecamatan->name ?? '-' }} - {{ $sale->customer->kabupaten->name ?? '-' }} - {{ $sale->customer->provinsi->name ?? '-' }}
                                                            @else
                                                                CASH
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <p><strong>Item Jurnal Transaksi:</strong></p>
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Variant</th>
                                                                    <th>Qty</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($sale->item as $items)
                                                                    @php
                                                                        // Fetch product data
                                                                        $productData = $items->getProductDataFromApi($items->product_id);
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $productData['code'] ?? 'Unknown Product' }} - {{ $productData['name'] ?? 'Unknown Product' }}</td>
                                                                        <td>{{ $items->qty }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @if($sale->ga->isNotEmpty())
                                                    <div class="col-md-12">
                                                        <p><strong>Item Give Away:</strong></p>
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Variant</th>
                                                                    <th>Qty</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($sale->ga as $key)
                                                                    @php
                                                                        // Fetch product data
                                                                        $productData = $key->getProductDataFromApi($key->product_packaging_id);
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $productData['code'] ?? 'Unknown Product' }} - {{ $productData['name'] ?? 'Unknown Product' }}</td>
                                                                        <td>{{ $key->pcs }}</td>
                                                                    </tr>
                                                                    @if(!$productData)
                                                                        <tr>
                                                                            <td colspan="3" class="text-danger">
                                                                                Product not found for ID: {{ $key->product_packaging_id }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
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
           pageLength: 10,          // Number of records per page
           lengthMenu: [25, 50, 100], // Dropdown menu options for page length
           responsive: true, // Enable responsiveness
           order: [[2, 'asc']]  // Ensure the table starts from the last row (counter)
       });
   });
</script>
@endsection