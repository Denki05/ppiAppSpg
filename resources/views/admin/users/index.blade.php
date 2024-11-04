@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-12">
            <a class="btn btn-success" href="{{ route('admin.users.create') }}" role="button">
                <i class="fa fa-plus" aria-hidden="true"></i> Create
            </a>

            <br>
            <br>

            <table id="userTable" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $user->name }}</td>
                        <td class="text-center">{{ $user->email }}</td>
                        <td class="text-center">{{ $user->role }}</td>
                        <td class="text-center">
                            <a class="btn btn-warning" href="{{ route('admin.users.edit', $user->id) }}" role="button">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
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
       $('#userTable').DataTable({
           paging: true,           // Enable pagination
           pageLength: 5,          // Number of records per page
           lengthMenu: [25, 50, 100], // Dropdown menu options for page length
       });
   });
</script>
@endsection