{{-- resources/views/supplies/dashboard.blade.php --}}
@extends('layouts.supplies')

@section('content')
<div class="container py-4">

  {{-- ─── SUMMARY CARDS ─── --}}
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center py-3">
          <h6 class="text-muted mb-1">Supplies Given</h6>
          <h3 class="mb-0">{{ $suppliesGiven->count() }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center py-3">
          <h6 class="text-muted mb-1">Patients Served</h6>
          <h3 class="mb-0">{{ $patientsServe }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center py-3">
          <h6 class="text-muted mb-1">Pending Orders</h6>
          <h3 class="mb-0">{{ $pendingOrders }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body text-center py-3">
          <h6 class="text-muted mb-1">Top Item</h6>
          <h3 class="mb-0">
            @if($mostServedSupply->isNotEmpty())
              {{ $mostServedSupply->first()->service->service_name }}
            @else
              &mdash;
            @endif
          </h3>
        </div>
      </div>
    </div>
  </div>

  {{-- ─── RECENT SUPPLIES ─── --}}
  <div class="card mb-4 shadow-sm">
    <div class="card-header py-2">
      <h5 class="mb-0">5 Most-Recent Supplies</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
        <table class="table table-hover table-sm mb-0">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Patient</th>
              <th>Item</th>
              <th>Qty</th>
              <th>By</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentSupplies as $rs)
              <tr>
                <td>{{ $rs->created_at->format('Y-m-d') }}</td>
                <td>{{ $rs->patient->patient_first_name }} {{ $rs->patient->patient_last_name }}</td>
                <td>{{ $rs->service->service_name }}</td>
                <td>{{ $rs->quantity }}</td>
                <td>{{ $rs->creator->name }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-3">No supplies yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ─── ITEMS / SERVICES ─── --}}
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
      <h5 class="mb-0">Items / Services</h5>
      <button class="btn btn-primary btn-sm" id="newItemBtn"
              data-bs-toggle="modal" data-bs-target="#itemModal">
        <i class="fa fa-plus me-1"></i> Add Item
      </button>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
        <table class="table table-striped table-hover table-sm mb-0">
          <thead class="table-light">
            <tr>
              <th>Name</th>
              <th>Department</th>
              <th>Price (₱)</th>
              <th>Quantity</th>          {{-- ← new --}}
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($services as $svc)
              <tr data-id="{{ $svc->service_id }}"
                  data-name="{{ $svc->service_name }}"
                  data-dept="{{ $svc->department_id }}"
                  data-price="{{ $svc->price }}"
                  data-qty="{{ $svc->quantity }}">   {{-- ← dataset --}}
                <td>{{ $svc->service_name }}</td>
                <td>{{ $svc->department->department_name }}</td>
                <td>{{ number_format($svc->price,2) }}</td>
                <td>{{ $svc->quantity }}</td>          {{-- ← show --}}
                <td class="text-end">
                  <button class="btn btn-outline-secondary btn-sm editItem"
                          data-bs-toggle="modal" data-bs-target="#itemModal">
                    <i class="fa fa-pen"></i>
                  </button>
                <form action="{{ route('supplies.items.destroy', $svc) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Delete this item?')">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ─── ITEM CRUD MODAL ─── --}}
<div class="modal fade" id="itemModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="itemForm" method="POST">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="itemModalLabel">Add Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <div class="mb-3">
          <label class="form-label">Name</label>
          <input name="service_name" class="form-control" required>
        </div>

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
          <label class="form-label">Price (₱)</label>
          <input name="price" type="number" step="0.01" min="0" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Quantity</label>    {{-- ← new field --}}
          <input name="quantity" type="number" min="0" class="form-control" required>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light me-auto" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const form      = document.getElementById('itemForm');
  const title     = document.getElementById('itemModalLabel');
  const submitBtn = document.getElementById('modalSubmitBtn');

  // Add
  document.getElementById('newItemBtn').addEventListener('click', () => {
    form.action = "{{ route('supplies.items.store') }}";
    form.method = "POST";
    title.textContent = 'Add Item';
    submitBtn.textContent = 'Add';
    form.reset();
    form.querySelector('[name="_method"]')?.remove();
  });

  // Edit
  document.querySelectorAll('.editItem').forEach(btn => {
    btn.addEventListener('click', () => {
      const row = btn.closest('tr');
      const id  = row.dataset.id;
      form.action = "{{ url('supplies/items') }}/" + id;
      if (!form.querySelector('[name="_method"]')) {
        const m = document.createElement('input');
        m.type = 'hidden';
        m.name = '_method';
        m.value = 'PUT';
        form.appendChild(m);
      }
      form.service_name.value  = row.dataset.name;
      form.department_id.value = row.dataset.dept;
      form.price.value         = row.dataset.price;
      form.quantity.value      = row.dataset.qty;   // ← populate
      title.textContent = 'Edit Item';
      submitBtn.textContent = 'Update';
    });
  });
})();
</script>
@endpush