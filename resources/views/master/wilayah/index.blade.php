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
            <li class="breadcrumb-item active" aria-current="page"> Wilayah</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <a class="btn btn-success" href="{{ route('master.wilayah.create') }}" role="button">
                <i class="fa fa-plus" aria-hidden="true"></i> Create
            </a>

            <br>
            <br>

            <table id="customerTable" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Provinsi</th>
                        <th class="text-center">Kota</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wilayah as $key => $value)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="text-center">{{ $value->nama_kawasan }}</td>
                        <td class="text-center">{{ $value->provinsi->name }}</td>
                        <td class="text-center">
                            {{ $value->kabupaten->name }}
                        </td>
                        <td class="text-center">
                            <form action="{{ route('master.wilayah.destroy', encrypt($value->id)) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </form>
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
        $('#customerTable').DataTable({
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