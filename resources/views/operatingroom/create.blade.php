{{-- resources/views/operatingroom/create.blade.php --}}
@extends('layouts.operatingroom')

@section('content')
<div class="container-fluid py-4">
  <h1 class="h4 mb-3">Add New Operating-Room Charge</h1>

  <form method="POST" action="{{ route('operating.store') }}" id="or-form">
    @csrf
    <div class="mb-3">
  <label class="form-label">Ordering Doctor</label>
  <select name="doctor_id"
          class="form-select @error('doctor_id') is-invalid @enderror" required>
    <option value="">Select doctor…</option>
    @foreach($doctors as $doc)
      <option value="{{ $doc->doctor_id }}"
        @selected(old('doctor_id') == $doc->doctor_id)>
        Dr. {{ $doc->doctor_name }}
      </option>
    @endforeach
  </select>
  @error('doctor_id')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
    {{-- Patient Select --}}
    <div class="mb-3">
      <label class="form-label">Patient</label>
      <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
        <option value="">Select patient…</option>
        @foreach($patients as $p)
          <option value="{{ $p->patient_id }}" @selected(old('patient_id') == $p->patient_id)>
            {{ $p->patient_first_name }} {{ $p->patient_last_name }}
          </option>
        @endforeach
      </select>
      @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Dynamic Procedures --}}
    <div id="procedure-list">
      <div class="procedure-item mb-3">
        <h6>Procedure #1</h6>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Procedure</label>
            <select name="misc_item[0][service_id]"
                    class="form-select @error('misc_item.0.service_id') is-invalid @enderror" required>
              <option value="">Select procedure…</option>
              @foreach($services as $s)
                <option value="{{ $s->service_id }}" data-price="{{ $s->price }}">
                  {{ $s->service_name }} – ₱{{ number_format($s->price,2) }}
                </option>
              @endforeach
            </select>
            @error('misc_item.0.service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-2">
            <label class="form-label">OR No.</label>
            <input type="number" name="misc_item[0][orNumber]" min="1"
                   class="form-control @error('misc_item.0.orNumber') is-invalid @enderror">
            @error('misc_item.0.orNumber')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-2">
            <label class="form-label">Unit Price</label>
            <input type="text" readonly class="form-control-plaintext border border-info unit-price">
          </div>
          <div class="col-md-2">
            <label class="form-label">Total</label>
            <input type="text" readonly class="form-control-plaintext border border-warning total-price">
          </div>
        </div>
      </div>
    </div>

    <button type="button" id="add-procedure" class="btn btn-sm btn-outline-secondary mb-4">
      + Add Procedure
    </button>

    {{-- Notes --}}
    <div class="mb-3">
      <label class="form-label">Notes (Optional)</label>
      <textarea name="notes" rows="3"
                class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
      @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Total Display --}}
    <div class="mb-4 text-end">
      <strong>Total Amount:</strong> ₱<span id="grand-total">0.00</span>
    </div>

    <div class="text-end">
      <a href="{{ route('operating.dashboard') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Charge</button>
    </div>
  </form>
</div>

{{-- =======================  ADD‑CHARGE MODAL  ======================= --}}
<div class="modal fade" id="addChargeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form id="add-charge-form" method="POST" action="{{ route('operating.store') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Add OR Charge</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="patient_id" id="modal-patient-id">
        <div class="row g-3">
          {{-- patient label (readonly) --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">Patient</label>
            <input id="modal-patient-name" class="form-control-plaintext" readonly>
          </div>

          {{-- doctor selector --}}
          <div class="col-md-6">
            <label for="doctor_id" class="form-label fw-semibold">Doctor</label>
            <select name="doctor_id" id="doctor_id" class="form-select" required>
              <option value="">Choose…</option>
              @foreach($doctors as $doc)
                <option value="{{ $doc->doctor_id }}"
                  @selected(optional(Auth::user()->doctor)->doctor_id === $doc->doctor_id)>
                  {{ $doc->doctor_name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- service selector --}}
          <div class="col-md-8">
            <label class="form-label fw-semibold">Procedure / Service</label>
            <select name="misc_item[0][service_id]" class="form-select" required>
              <option value="">Choose…</option>
              @foreach($services as $svc)
                <option value="{{ $svc->service_id }}">
                  {{ $svc->service_name }} — ₱{{ number_format($svc->price,2) }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- OR number --}}
          <div class="col-md-4">
            <label class="form-label fw-semibold">OR No.</label>
            <input type="number" name="misc_item[0][orNumber]" class="form-control" min="1">
          </div>

          {{-- notes --}}
          <div class="col-12">
            <label class="form-label fw-semibold">Notes</label>
            <textarea name="notes" rows="2" class="form-control"></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Charge</button>
      </div>
    </form>
  </div>
</div>
{{-- ====================  / END ADD‑CHARGE MODAL  ==================== --}}

@endsection
@push('scripts')
<script>
(function () {

  // idx will be used both for naming indexes and for OR numbers
  let idx = 1;
   const list = document.getElementById('procedure-list');

  //─── initialize first OR No. ─────────────────────────────────────
  const firstOrInput = document.querySelector('input[name="misc_item[0][orNumber]"]');
  if (firstOrInput) {
    firstOrInput.value = 1;
  }

   // Add new procedure block
   document.getElementById('add-procedure').addEventListener('click', () => {
     const tpl = list.querySelector('.procedure-item').cloneNode(true);
     tpl.querySelector('h6').textContent = `Procedure #${idx + 1}`;

     // Re-index name attributes
     tpl.querySelectorAll('[name]').forEach(el => {
       el.name = el.name.replace(/\[\d+\]/, `[${idx}]`);
       if (!el.matches('select')) el.value = '';
     });

    //─── auto-set OR No. to idx+1 ─────────────────────────────────────
    const orInput = tpl.querySelector('input[name*="[orNumber]"]');
    if (orInput) {
      orInput.value = idx + 1;
    }

     // Clear price fields
     tpl.querySelector('.unit-price').value = '';
     tpl.querySelector('.total-price').value = '';

     list.append(tpl);
     idx++;
   });

   // Recalculate line + grand total
   function updateLine(item) {
     const sel   = item.querySelector('select[name*="[service_id]"]');
     const unit  = item.querySelector('.unit-price');
     const total = item.querySelector('.total-price');

     const price = parseFloat(sel.selectedOptions[0]?.dataset.price || 0);
     unit.value  = price.toFixed(2);
     total.value = price.toFixed(2);

     // grand total
     let gt = 0;
     document.querySelectorAll('.total-price').forEach(el => {
       gt += parseFloat(el.value) || 0;
     });
     document.getElementById('grand-total').textContent = gt.toFixed(2);
   }

   // Delegate change events
   list.addEventListener('change', e => {
     if (e.target.matches('select[name*="[service_id]"]')) {
       updateLine(e.target.closest('.procedure-item'));
     }
   });
})();
</script>
@endpush
