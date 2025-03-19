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
            <li class="breadcrumb-item active" aria-current="page"><i class="fa-solid fa-eye"></i> Index</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-12">
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
                            <td class="text-center">{{ $sale->tanggal_order }}</td>
                            <td class="text-center">{{ $sale->brand_name }}</td>
                            <td class="text-center">
                                @if($sale->type == 0)
                                    {{ $sale->customer->nama }}, {{ $sale->customer->kecamatan->name }} - {{ $sale->customer->kabupaten->name }} - {{ $sale->customer->provinsi->name }}
                                @else
                                    CASH
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('penjualan.destroy', $sale->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah kamu yakin menghapus jurnal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </form>
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
           pageLength: 5,          // Number of records per page
           lengthMenu: [25, 50, 100], // Dropdown menu options for page length
           responsive: true, // Enable responsiveness
           order: [[2, 'desc']]  // Ensure the table starts from the last row (counter)
       });
       
        setTimeout(function() {
            $(".alert").fadeOut('slow');
        }, 2000);
   });
</script>
@endsection