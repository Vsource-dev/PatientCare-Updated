{{-- resources/views/supplies/create.blade.php --}}

@extends('layouts.supplies')

@section('content')
<div class="container-fluid py-4 border h-100 d-flex flex-column" style="background-color: #fafafa">

    {{-- Top --}}
    <div>
        <h1 class="hdng">Add New Miscellaneous Charge</h1>
        <p>Add new manual charge to patients</p>
    </div>

    <form method="POST" action="{{ route('supplies.store') }}" id="misc-form">
        @csrf

        {{-- Select Patient --}}
        <div class="mb-3">
            <label class="form-label">Patient</label>
            <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                <option value="">Select patient...</option>
                @foreach ($patients as $p)
                    <option value="{{ $p->patient_id }}" @selected(old('patient_id') == $p->patient_id)>
                        {{ $p->patient_first_name }} {{ $p->patient_last_name }}
                    </option>
                @endforeach
            </select>
            @error('patient_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Dynamic Items List --}}
        <div class="border p-3 my-3 flex-grow-1 d-flex flex-column" style="max-height: 350px; overflow-y: auto;">
            <div id="misc-list">
                <div class="misc-item mt-1 border p-3">
                    <h6>Item #1</h6>
                    <div class="row gx-4">
                        <div class="col-md-5">
                            <label class="form-label">Item</label>
                 
<select name="misc_item[0][service_id]"
        class="form-select @error('misc_item.0.service_id') is-invalid @enderror"
        required>
    <option value="">Select Item</option>
    @foreach ($services as $s)
        <option value="{{ $s->service_id }}"
                data-price="{{ $s->price }}">
            {{ $s->service_name }}
            <small class="text-muted">({{ $s->department->department_name }})</small>
            – ₱ {{ number_format($s->price,2) }}
        </option>
    @endforeach
</select>

                            @error('misc_item.0.service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="misc_item[0][quantity]" min="1"
                                   class="form-control @error('misc_item.0.quantity') is-invalid @enderror">
                            @error('misc_item.0.quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unit Price</label>
                            <input type="text" readonly
                                   class="form-control-plaintext border border-info unit-price">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Total</label>
                            <input type="text" readonly
                                   class="form-control-plaintext border border-warning line-total">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-supply">
                                <i class="fa-solid fa-xmark me-2"></i>Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Add Item Button --}}
            <button type="button" id="add-item" class="btn btn-sm btn-outline-secondary mt-2">+ Add Item</button>
        </div>

        {{-- Notes --}}
        <div class="mb-3">
            <label class="form-label">Notes (Optional)</label>
            <textarea name="notes" rows="3"
                      class="form-control @error('notes') is-invalid @enderror"
                      style="resize: none;">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Total Display --}}
        <div class="text-end mb-3">
            <strong>Total Amount:</strong> ₱<span id="grand-total">0.00</span>
        </div>

        {{-- Actions --}}
        <div class="text-end">
            <a href="{{ route('supplies.dashboard') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let idx = 1;
    const addBtn = document.getElementById('add-item');
    const miscList = document.getElementById('misc-list');

    function updateLineTotals(item) {
        const select    = item.querySelector('select[name^="misc_item"]');
        const qtyInput  = item.querySelector('input[type="number"]');
        const unitPrice = parseFloat(select.selectedOptions[0].dataset.price || 0);
        const qty       = parseInt(qtyInput.value) || 0;
        const unitEl    = item.querySelector('.unit-price');
        const lineEl    = item.querySelector('.line-total');

        unitEl.value = unitPrice.toFixed(2);
        lineEl.value = (unitPrice * qty).toFixed(2);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        miscList.querySelectorAll('.line-total').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('grand-total').textContent = total.toFixed(2);
    }

    // Recalculate on change
    miscList.addEventListener('change', e => {
        const item = e.target.closest('.misc-item');
        if (item) updateLineTotals(item);
    });

    // Add new item
    addBtn.addEventListener('click', () => {
        const tpl = miscList.querySelector('.misc-item').cloneNode(true);
        idx++;
        tpl.querySelectorAll('input, select').forEach(el => {
            if (el.name) el.name = el.name.replace(/\[\d+\]/, `[${idx-1}]`);
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
            else el.value = '';
            el.classList.remove('is-invalid');
        });
        tpl.querySelectorAll('.invalid-feedback').forEach(f => f.remove());
        tpl.querySelector('h6').textContent = `Item #${idx}`;
        miscList.appendChild(tpl);
    });

    // Remove item
    miscList.addEventListener('click', e => {
        if (e.target.closest('.remove-supply')) {
            const items = miscList.querySelectorAll('.misc-item');
            if (items.length > 1) {
                e.target.closest('.misc-item').remove();
                // re-label
                let i = 1;
                miscList.querySelectorAll('.misc-item').forEach(it => {
                    it.querySelector('h6').textContent = `Item #${i++}`;
                });
                idx = i - 1;
                updateGrandTotal();
            }
        }
    });
</script>
@endpush
