@extends('layouts.app')

@section('content')
<div class="container">
    
    <!--Notifikasi-->
    @if(session()->has('collect_success') || session()->has('collect_error'))
        <div class="container">
            <div class="row">
                <div class="col pl-0">
                    <div class="alert alert-success alert-dismissable" role="alert" style="max-height: 300px; overflow-y: auto;">
                        <h3 class="alert-heading font-size-h4 font-w400">Successful Import</h3>
                        @if(session()->has('collect_success'))
                            @foreach (session()->get('collect_success', []) as $msg)
                                <p class="mb-0">{{ $msg }}</p>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col pr-0">
                    <div class="alert alert-danger alert-dismissable" role="alert" style="max-height: 300px; overflow-y: auto;">
                        <h3 class="alert-heading font-size-h4 font-w400">Failed Import</h3>
                        @if(session()->has('collect_error'))
                            @foreach (session()->get('collect_error', []) as $msg)
                                <p class="mb-0">{{ $msg }}</p>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
            
            @if(Auth::user()->role == "dev" OR Auth::user()->role == "admin")
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importExportModal">
                Manage
            </button>
            @endif

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
                        <td class="text-center">{{ $customer->kecamatan->name ?? '-' }}</td>
                        <td class="text-center">{{ $customer->kabupaten->name ?? '-' }}</td>
                        <td class="text-center">{{ $customer->provinsi->name ?? '-' }}</td>
                        <td class="text-center">
                            <a class="btn btn-primary" href="{{ route('master.customer.show', encrypt($customer->id)) }}" role="button"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            @if (auth()->user()->role == 'dev' OR auth()->user()->role == 'admin')
                            <a href="{{ route('master.customer.edit', encrypt($customer->id)) }}" class="btn btn-warning"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <form action="{{ route('master.customer.destroy', encrypt($customer->id)) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
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

<!-- Modal Import & Export -->
<div class="modal fade" id="importExportModal" tabindex="-1" aria-labelledby="importExportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExportModalLabel">Manage Import & Export Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('master.customer.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Import Excel File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Import</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('master.customer.export') }}" class="btn btn-info">Download Template</a>
            </div>
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