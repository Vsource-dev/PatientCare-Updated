{{-- resources/views/admin/resources/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1>Create Room</h1>
  <a href="{{ route('admin.resources.index') }}" class="btn btn-sm btn-secondary">
    ← Back to List
  </a>
</div>

<div class="row">
  {{-- New Room --}}
  <div class="col-md-6">
    <div class="card mb-4">
      <div class="card-header">
        <strong>New Room</strong>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.resources.store') }}" method="POST">
          @csrf
          <input type="hidden" name="type" value="room">

          <div class="mb-3">
            <label for="department_id" class="form-label">Department</label>
            <select name="department_id" id="department_id"
                    class="form-select @error('department_id') is-invalid @enderror"
                    required>
              <option value="">Select department…</option>
              @foreach($departments as $d)
                <option value="{{ $d->department_id }}"
                  {{ old('department_id') == $d->department_id ? 'selected' : '' }}>
                  {{ $d->department_name }}
                </option>
              @endforeach
            </select>
            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label for="room_number" class="form-label">Room Number</label>
            <input type="text" name="room_number" id="room_number"
                   value="{{ old('room_number') }}"
                   class="form-control @error('room_number') is-invalid @enderror"
                   placeholder="e.g. 101A" required>
            @error('room_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label for="capacity" class="form-label">Capacity (no. of beds)</label>
            <input type="number" name="capacity" id="capacity"
                   value="{{ old('capacity', 1) }}"
                   min="1" class="form-control @error('capacity') is-invalid @enderror"
                   required>
            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label for="rate" class="form-label">Daily Rate (₱)</label>
            <input type="number" name="rate" id="rate"
                   value="{{ old('rate', 0) }}"
                   step="0.01" min="0"
                   class="form-control @error('rate') is-invalid @enderror"
                   required>
            @error('rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status"
                    class="form-select @error('status') is-invalid @enderror"
                    required>
              <option value="available" {{ old('status')=='available'?'selected':'' }}>
                Available
              </option>
              <option value="unavailable" {{ old('status')=='unavailable'?'selected':'' }}>
                Unavailable
              </option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <button type="submit" class="btn btn-primary">Create Room</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
