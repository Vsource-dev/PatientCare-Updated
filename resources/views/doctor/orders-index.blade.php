@extends('layouts.doctor')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold text-primary mb-3">Patients With Orders</h3>

    <div class="row g-3">
        @foreach($patients as $p)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm patient-card"
                     data-bs-toggle="modal"
                     data-bs-target="#patientModal"
                     data-id="{{ $p->patient_id }}"
                     data-name="{{ $p->patient_first_name }} {{ $p->patient_last_name }}">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle bg-light me-3" style="width:60px;height:60px;"></div>
                        <div>
                            <div class="fw-semibold">{{ $p->patient_first_name }} {{ $p->patient_last_name }}</div>
                            <small class="text-muted">P-{{ $p->patient_id }}</small><br>
                     <span class="badge bg-primary">
  {{ $p->service_assignments_count + $p->prescriptions_count }} Orders
</span>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $patients->links() }}</div>
</div>

<div class="modal fade" id="patientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle" class="fw-bold mb-0"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <p class="text-muted">Loading…</p>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const baseUrl = "{{ url('/doctor/orders') }}/";   // ➜ "/doctor/orders/"

  document.querySelectorAll('.patient-card').forEach(card => {
    card.addEventListener('click', e => {
      const id   = e.currentTarget.dataset.id
      const name = e.currentTarget.dataset.name
      document.getElementById('modalTitle').textContent =
        `${name} (P-${id})`

      document.getElementById('modalBody').innerHTML =
        '<p class="text-muted">Loading…</p>';

      fetch(baseUrl + id)                       // ← matches the route above
        .then(r => r.text())
        .then(html => document.getElementById('modalBody').innerHTML = html)
        .catch(() => {
          document.getElementById('modalBody').innerHTML =
            '<p class="text-danger">Unable to load orders.</p>';
        });
    });
  });
});
</script>
@endpush

@if(session('show_patient'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const pid   = "{{ session('show_patient') }}";
  const card  = document.querySelector(`.patient-card[data-id='${pid}']`);
  if (card) card.click();   // triggers the same fetch + modal open
});
</script>
@endpush
@endif


