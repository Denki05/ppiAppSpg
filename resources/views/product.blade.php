@extends('layouts.app')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Product</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <table id="productTable" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Brand</th>
                        <th class="text-center">Kode</th>
                        <th class="text-center">Nama</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items AS $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $item['brand_name'] }}</td>
                        <td class="text-center">{{ $item['code'] }}</td>
                        <td class="text-center">{{ $item['name'] }}</td>
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
       $('#productTable').DataTable({
           paging: true,           // Enable pagination
           pageLength: 5,          // Number of records per page
           lengthMenu: [25, 50, 100], // Dropdown menu options for page length
           
       });
   });
</script>
@endsection