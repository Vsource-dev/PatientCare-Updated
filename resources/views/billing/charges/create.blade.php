{{-- resources/views/billing/charges/create.blade.php --}}
@extends('layouts.billing')

@section('content')
<div class="container-fluid p-4">
  <h4 class="mb-4">Post Manual Charges</h4>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('billing.charges.store') }}">
    @csrf

    {{-- Select Patient --}}
    <div class="mb-4">
      <label for="patient_id" class="form-label">Patient</label>
      <select
        id="patient_id"
        name="patient_id"
        class="form-select @error('patient_id') is-invalid @enderror"
        required
      >
        <option value="">— select patient —</option>
        @foreach($patients as $p)
          <option
            value="{{ $p->patient_id }}"
            {{ old('patient_id') == $p->patient_id ? 'selected' : '' }}
          >
            {{ $p->patient_last_name }}, {{ $p->patient_first_name }}
            (ID: {{ str_pad($p->patient_id, 6, '0', STR_PAD_LEFT) }})
          </option>
        @endforeach
      </select>
      @error('patient_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span>Charges</span>
        <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn">
          <i class="fa-solid fa-plus me-1"></i> Add Row
        </button>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0" id="chargesTable">
            <thead class="table-light">
              <tr>
                <th>Item</th>
                <th style="width: 120px;" class="text-end">Unit ₱</th>
                <th style="width: 100px;">Qty</th>
                <th style="width: 140px;" class="text-end">Subtotal ₱</th>
                <th style="width: 60px;"></th>
              </tr>
            </thead>
            <tbody>
              @php $grouped = $services->groupBy('service_type'); @endphp
              <tr class="charge-row">
                <td>
                  <select name="charges[0][service_id]"
                          class="form-select service-select @error('charges.0.service_id') is-invalid @enderror"
                          required>
                    <option value="">— select item —</option>
                    @foreach($grouped as $type => $group)
                      <optgroup label="{{ ucfirst($type) }}">
                        @foreach($group as $s)
                          <option
                            value="{{ $s->service_id }}"
                            data-price="{{ $s->price }}"
                          >
                            {{ $s->service_name }} — ₱{{ number_format($s->price,2) }}
                          </option>
                        @endforeach
                      </optgroup>
                    @endforeach
                  </select>
                </td>
                <td class="text-end align-middle">
                  <input type="text"
                         class="form-control-plaintext unit-price"
                         readonly
                         value="₱0.00">
                </td>
                <td class="align-middle">
                  <input type="number"
                         name="charges[0][quantity]"
                         class="form-control quantity-input @error('charges.0.quantity') is-invalid @enderror"
                         min="1"
                         value="1"
                         required>
                </td>
                <td class="text-end align-middle">
                  <input type="text"
                         class="form-control-plaintext line-subtotal"
                         readonly
                         value="₱0.00">
                </td>
                <td class="text-center align-middle">
                  <button type="button" class="btn btn-sm btn-outline-danger removeRow">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
            <tfoot class="table-light">
              <tr>
                <th colspan="3" class="text-end">Grand Total</th>
                <th class="text-end" id="grandTotal">₱0.00</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-paper-plane me-1"></i> Submit Charges
        </button>
        <a href="{{ route('billing.charges.index') }}" class="btn btn-secondary ms-2">
          Cancel
        </a>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  let idx = 1;
  const tbody      = document.querySelector('#chargesTable tbody');
  const grandTotal = document.getElementById('grandTotal');

  document.getElementById('addRowBtn').addEventListener('click', () => {
    const row = tbody.querySelector('.charge-row').cloneNode(true);

    // update the name attributes
    row.querySelector('.service-select').name  = `charges[${idx}][service_id]`;
    row.querySelector('.quantity-input').name = `charges[${idx}][quantity]`;

    // reset values
    row.querySelector('.service-select').selectedIndex = 0;
    row.querySelector('.unit-price').value    = '₱0.00';
    row.querySelector('.quantity-input').value = 1;
    row.querySelector('.line-subtotal').value = '₱0.00';

    tbody.appendChild(row);
    idx++;
  });

  // delegate change and remove events
  tbody.addEventListener('change', e => {
    if (e.target.matches('.service-select') || e.target.matches('.quantity-input')) {
      const tr = e.target.closest('tr');
      const price = parseFloat(tr.querySelector('.service-select')
                        .selectedOptions[0]?.dataset.price || 0);
      const qty   = parseInt(tr.querySelector('.quantity-input').value) || 0;

      tr.querySelector('.unit-price').value    = `₱${price.toFixed(2)}`;
      tr.querySelector('.line-subtotal').value = `₱${(price * qty).toFixed(2)}`;
      recalc();
    }
  });

  tbody.addEventListener('click', e => {
    if (e.target.closest('.removeRow')) {
      const rows = tbody.querySelectorAll('tr');
      if (rows.length > 1) {
        e.target.closest('tr').remove();
        recalc();
      }
    }
  });

  function recalc() {
    let total = 0;
    tbody.querySelectorAll('.line-subtotal').forEach(inp => {
      total += parseFloat(inp.value.replace(/[^0-9.-]+/g,'')) || 0;
    });
    grandTotal.textContent = `₱${total.toFixed(2)}`;
  }
});
</script>
@endpush
