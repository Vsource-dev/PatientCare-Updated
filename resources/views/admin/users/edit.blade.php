@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4">Edit User</h1>

  <form action="{{ route('admin.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input name="username" value="{{ old('username', $user->username) }}"
             class="form-control @error('username') is-invalid @enderror">
      @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" type="email" value="{{ old('email', $user->email) }}"
             class="form-control @error('email') is-invalid @enderror">
      @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" class="form-select @error('role') is-invalid @enderror">
        @foreach($roles as $r)
          <option value="{{ $r }}" @selected(old('role', $user->role)==$r)>
            {{ ucfirst($r) }}
          </option>
        @endforeach
      </select>
      @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3 doctor-rate-field" style="display: none;">
      <label class="form-label">Consultation Fee (₱)</label>
      <input name="rate" type="number" step="0.01" min="0"
        value="{{ old('rate', $user->doctor->rate ?? 0) }}"
        class="form-control @error('rate') is-invalid @enderror">
      @error('rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Password (leave blank to keep)</label>
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
      <button type="submit" class="btn btn-primary">Update</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const roleSelect = document.querySelector('select[name="role"]');
    const rateField  = document.querySelector('.doctor-rate-field');
    function toggleRateField() {
      rateField.style.display = (roleSelect.value === 'doctor') ? 'block' : 'none';
    }
    roleSelect.addEventListener('change', toggleRateField);
    toggleRateField();
  });
</script>
@endpush
