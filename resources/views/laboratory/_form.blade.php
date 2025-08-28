@csrf

{{-- Patient (searchable) --}}
<div class="mb-3">
  <label class="form-label">Patient</label>
  <input
    list="patients"
    name="search_patient"
    class="form-control @error('search_patient') is-invalid @enderror"
    placeholder="Type name or ID…"
    value="{{ old('search_patient') }}"
    required
  >
  <datalist id="patients">
    @foreach($patients as $p)
      <option value="{{ $p->patient_id }}">
        {{ $p->patient_first_name }} {{ $p->patient_last_name }}
      </option>
    @endforeach
  </datalist>
  @error('search_patient')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Ordering Doctor --}}
<div class="mb-3">
  <label class="form-label">Ordering Doctor</label>
  <select
    name="doctor_id"
    class="form-select @error('doctor_id') is-invalid @enderror"
    required
  >
    <option value="">Select doctor…</option>
    @foreach($doctors as $doc)
      <option
        value="{{ $doc->doctor_id }}"
        @selected(old('doctor_id') == $doc->doctor_id)
      >
        {{ $doc->doctor_name }}
      </option>
    @endforeach
  </select>
  @error('doctor_id')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Dynamic Lab Tests --}}
<div id="lab-list">
  <div class="lab-item mb-3">
    <h6>Lab Test #1</h6>
    <div class="row g-2">
      <div class="col-md-7">
        <label class="form-label">Test</label>
        <select
          name="charges[0][service_id]"
          class="form-select @error('charges.0.service_id') is-invalid @enderror"
          required
        >
          <option value="">Select test…</option>
          @foreach($services as $s)
            <option
              value="{{ $s->service_id }}"
              data-price="{{ $s->price }}"
              @selected(old('charges.0.service_id') == $s->service_id)
            >
              {{ $s->service_name }} – ₱{{ number_format($s->price,2) }}
            </option>
          @endforeach
        </select>
        @error('charges.0.service_id')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-md-2">
        <label class="form-label">Unit Price</label>
        <input
          type="text"
          readonly
          class="form-control-plaintext border border-info unit-price"
          value="{{ old('charges.0.amount') ? number_format(old('charges.0.amount'),2) : '' }}"
        >
      </div>
      <div class="col-md-2">
        <label class="form-label">Total</label>
        <input
          type="text"
          readonly
          class="form-control-plaintext border border-warning total-price"
          value="{{ old('charges.0.amount') ? number_format(old('charges.0.amount'),2) : '' }}"
        >
        <input
          type="hidden"
          name="charges[0][amount]"
          class="amount-field"
          value="{{ old('charges.0.amount') ?: '' }}"
        >
      </div>
    </div>
  </div>
</div>

<button
  type="button"
  id="add-test"
  class="btn btn-sm btn-outline-secondary mb-4"
>
  + Add Test
</button>

{{-- Notes --}}
<div class="mb-3">
  <label class="form-label">Notes (Optional)</label>
  <textarea
    name="notes"
    rows="3"
    class="form-control @error('notes') is-invalid @enderror"
  >{{ old('notes') }}</textarea>
  @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Total Display --}}
<div class="mb-4 text-end">
  <strong>Total Amount:</strong>
  ₱<span id="grand-total">
    {{ old('charges') ? collect(old('charges'))->sum('amount') : '0.00' }}
  </span>
</div>

@push('scripts')
<script>
(function () {
  // start index based on old input count, or 1
  let idx = {{ old('charges') ? count(old('charges')) : 1 }};
  const list = document.getElementById('lab-list');

  // Add new row
  document.getElementById('add-test').addEventListener('click', () => {
    const tpl = list.querySelector('.lab-item').cloneNode(true);
    tpl.querySelector('h6').textContent = `Lab Test #${idx + 1}`;

    tpl.querySelectorAll('[name]').forEach(el => {
      el.name = el.name.replace(/\[\d+\]/, `[${idx}]`);
      if (!el.matches('select')) el.value = '';
    });
    tpl.querySelector('.unit-price').value = '';
    tpl.querySelector('.total-price').value = '';
    tpl.querySelector('.amount-field').value = '';
    list.append(tpl);
    idx++;
  });

  // Recalculate line & grand totals
  function updateLine(item) {
    const sel = item.querySelector('select[name*="[service_id]"]');
    const unit = item.querySelector('.unit-price');
    const total = item.querySelector('.total-price');
    const amount = item.querySelector('.amount-field');

    const price = parseFloat(sel.selectedOptions[0]?.dataset.price || 0);
    unit.value = price.toFixed(2);
    total.value = price.toFixed(2);
    amount.value = price.toFixed(2);

    let gt = 0;
    document.querySelectorAll('.total-price').forEach(el => {
      gt += parseFloat(el.value) || 0;
    });
    document.getElementById('grand-total').textContent = gt.toFixed(2);
  }

  list.addEventListener('change', e => {
    if (e.target.matches('select[name*="[service_id]"]')) {
      updateLine(e.target.closest('.lab-item'));
    }
  });
})();
</script>
@endpush
