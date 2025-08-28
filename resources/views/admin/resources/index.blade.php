{{-- resources/views/admin/resources/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  
  <div>
    <h1 class="mb-0">Rooms & Beds</h1>
    <p class="">Shit</p>
  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
      <i class="fas fa-door-open me-1"></i> Add Room
    </button>
  </div>

</div>

<table class="table table-hover">
  <thead class="table-light">
    <tr>
      <th>Room #</th>
      <th>Department</th>
      <th>Total Beds</th>
      <th>Available Beds</th>
      <th>Beds (status)</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rooms as $room)
      @php
        $total     = $room->beds->count();
        $available = $room->beds->where('status','available')->count();
      @endphp
      <tr>
        <td>{{ $room->room_number }}</td>
        <td>{{ $room->department->department_name }}</td>
        <td>{{ $total }}</td>
        <td>{{ $available }}</td>
        <td>
          <ul class="mb-0 ps-3">
            @foreach($room->beds as $bed)
              <li class="small d-flex align-items-center">
                {{ $bed->bed_number }}
                <span class="badge bg-{{ $bed->status==='available'?'success':'secondary' }} ms-1 me-2">
                  {{ ucfirst($bed->status) }}
                </span>
                <a href="{{ route('admin.resources.edit',['type'=>'bed','id'=>$bed->bed_id]) }}">✎</a>
              </li>
            @endforeach
          </ul>
        </td>
        <td class="d-flex gap-2">
          <a href="{{ route('admin.resources.edit',['type'=>'room','id'=>$room->room_id]) }}">Edit</a>
          <form action="{{ route('admin.resources.destroy',['type'=>'room','id'=>$room->room_id]) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this room and its beds?')">Delete</button>
          </form>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

{{-- Add Room Modal --}}
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="{{ route('admin.resources.store') }}" method="POST">
      @csrf
      <input type="hidden" name="type" value="room">
      <div class="modal-header">
        <h5 class="modal-title">New Room</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Department</label>
          <select name="department_id" class="form-select" required>
            <option value="">Select…</option>
            @foreach($departments as $d)
              <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Room Number</label>
          <input name="room_number" class="form-control" placeholder="e.g. 101A" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Capacity (number of beds)</label>
          <input name="capacity" type="number" min="1" value="1" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Daily Rate (₱ per bed)</label>
          <input name="rate" type="number" step="0.01" min="0" value="0" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select" required>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Create Room</button>
      </div>
    </form>
  </div>
</div>

</div>


@endsection
