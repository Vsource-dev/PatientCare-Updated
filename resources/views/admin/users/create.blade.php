{{--resources/views/admin/users/create.blade.php--}}
@extends('layouts.admin')

@section('content')

<div class="container-fluid">
  <h1 class="h3 mb-4">Create User</h1>

  <form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input name="username" value="{{ old('username') }}"
             class="form-control @error('username') is-invalid @enderror">
      @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" type="email" value="{{ old('email') }}"
             class="form-control @error('email') is-invalid @enderror">
      @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
    <label class="form-label">Role</label>
      <select name="role" class="form-select @error('role') is-invalid @enderror">
        <option value="">Select role…</option>
        @foreach($roles as $r)
          <option value="{{ $r }}" @selected(old('role') === $r)>
            {{ ucwords(str_replace('_',' ',$r)) }}
          </option>
        @endforeach
      </select>
      @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <div class="mb-3 doctor-rate-field" style="display: none;">
  <label class="form-label">Consultation Fee (₱)</label>
  <input
    name="rate"
    type="number"
    step="0.01"
    min="0"
    value="{{ old('rate', 0) }}"
    class="form-control @error('rate') is-invalid @enderror"
  >
  @error('rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
 <label class="form-label">Department</label>
<select
    name="department_id"
    class="form-select @error('department_id') is-invalid @enderror"
>
    <option value="">-- Select Department --</option>
    @foreach($departments as $department)
        <option value="{{ $department->department_id }}"
            {{ old('department_id') == $department->department_id ? 'selected' : '' }}>
            {{ $department->department_name }}
        </option>
    @endforeach
</select>
@error('department_id')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror


</div>
      @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Password</label>
        <input name="password" type="password"
               class="form-control @error('password') is-invalid @enderror">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Confirm Password</label>
        <input name="password_confirmation" type="password" class="form-control">
      </div>
    </div>

    <div class="mt-4 text-end">
      <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create</button>
    </div>
  </form>
</div>

@push('scripts')
<script>
  const roleSelect = document.querySelector('select[name="role"]');
  const rateField  = document.querySelector('.doctor-rate-field');

  function toggleRateField() {
    rateField.style.display = roleSelect.value === 'doctor' ? 'block' : 'none';
  }

  roleSelect.addEventListener('change', toggleRateField);
  document.addEventListener('DOMContentLoaded', toggleRateField);
</script>

@endpush
@endsection


