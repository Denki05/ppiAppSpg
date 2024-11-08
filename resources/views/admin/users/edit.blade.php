@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit User</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="spg" {{ $user->role == 'spg' ? 'selected' : '' }}>Spg</option>
            </select>
        </div>

        <!-- Password Field (Optional) -->
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank if not changing">
        </div>

        <!-- Password Confirmation Field -->
        <div class="form-group mb-3">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Leave blank if not changing">
        </div>

        <hr>

        <div class="row g-3 mt-4">
            <div class="col-md-6">
                <label for="provinsi" class="form-label">Provinsi <span class="text-danger">*</span></label>
                <select name="provinsi" id="provinsi" class="form-control select2" required style="width: 100%;">
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsi as $item)
                        <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <br>

        <a class="btn btn-danger" href="{{ route('admin.users') }}" role="button">Back</a>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection