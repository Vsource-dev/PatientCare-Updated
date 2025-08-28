{{--resources/views/admin/users/index.blade.php--}}2
@extends('layouts.admin')

@section('content')

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        timer: 2500,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}'
    });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        html: `{!! implode('<br>', $errors->all()) !!}`
    });
</script>
@endif
  <div class="d-flex justify-content-between align-items-center  g-2">
    <div class="col">
        <h4 class="fw-bold">🆕 Users Management</h4>
        <p class="text-muted">Always double check the information you will put it to avoid problems later.</p>
    </div>
    <div class="col-auto">
      <a href="{{ route('admin.users.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> New User
      </a>
    </div>
  </div>

  <table class="table align-middle table-hover mb-0">
    <thead class="table-light">
      <tr>
        <th>#</th><th>Username</th><th>Email</th><th>Role</th><th width="150">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $u)
        <tr>
          <td>{{ $u->user_id }}</td>
          <td>{{ $u->username }}</td>
          <td>{{ $u->email }}</td>
          <td>{{ ucfirst($u->role) }}</td>
          <td>
            <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-sm btn-outline-secondary">✎</a>
        
            <form method="POST" action="{{ route('admin.users.destroy',$u) }}" class="d-inline">
              @csrf @method('DELETE')
              <button onclick="return confirm('Delete?')" class="btn btn-sm btn-outline-danger">🗑</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

@if ($users->hasPages())
  {{ $users->links() }}
@endif

@endsection
