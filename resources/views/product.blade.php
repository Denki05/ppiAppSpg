@extends('layouts.app')

@section('content')
<div class="container">
    <!-- <nav class="breadcrumb bg-white push">
        <span class="breadcrumb-item">Product</span>
        <span class="breadcrumb-item active">Index</span>
    </nav> -->

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
           dom: 'Bfrtip',           // Dom structure for buttons
           buttons: [
               { extend: 'copy' },
               { extend: 'excel' },
               { extend: 'pdf' },
               { extend: 'print' }
           ],
           language: {
               paginate: {
                   first: "First",
                   last: "Last",
                   next: "Next",
                   previous: "Previous"
               }
           }
       });
   });
</script>
@endsection