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
            <li class="breadcrumb-item active" aria-current="page"> Customer</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <a class="btn btn-success" href="{{ route('master.customer.create') }}" role="button">
                <i class="fa fa-plus" aria-hidden="true"></i> Create
            </a>

            <br>
            <br>

            <table id="customerTable" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Kecamatan</th>
                        <th class="text-center">Kota</th>
                        <th class="text-center">Provinsi</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($customers as $customer)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $customer->nama }}</td>
                        <td class="text-center">{{ $customer->kecamatan->name }}</td>
                        <td class="text-center">{{ $customer->kabupaten->name }}</td>
                        <td class="text-center">{{ $customer->provinsi->name }}</td>
                        <td class="text-center">
                            <a class="btn btn-primary" href="{{ route('master.customer.show', $customer->id) }}" role="button">Show</a>
                            <a class="btn btn-warning" href="{{ route('master.customer.edit', $customer->id) }}" role="button">Edit</a>
                            <form action="{{ route('master.customer.destroy', $customer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
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
           lengthMenu: [25, 50, 100],
           order: [[1, 'asc']],
       });
   });
</script>
@endsection