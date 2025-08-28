{{-- resources/views/patients/_form.blade.php --}}
@php
  // ensure $patient is always defined
  $patient   = $patient ?? null;
  $admission = optional($patient)->admissionDetail;
  $medical   = optional($patient)->medicalDetail;
  $billing   = optional($patient)->billingInformation;
@endphp

@csrf
@isset($patient)
  @method('PUT')
@endisset

<style>
  /* mark completed tabs with a green check */
  .nav-tabs .nav-link.completed {
    color: #28a745;
  }
  .nav-tabs .nav-link.completed::after {
    content: ' ✓';
    color: #28a745;
    font-weight: bold;
  }
</style>

<ul class="nav nav-tabs mb-3" id="patientTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
            type="button" role="tab">Personal Information</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical"
            type="button" role="tab">Medical Details</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="admission-tab" data-bs-toggle="tab" data-bs-target="#admission"
            type="button" role="tab">Admission Details</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="billing-tab" data-bs-toggle="tab" data-bs-target="#billing"
            type="button" role="tab">Billing Details</button>
  </li>
</ul>

<div class="tab-content" id="patientTabsContent">
  {{-- PERSONAL --}}
  <div class="tab-pane fade show active" id="personal" role="tabpanel">
    <div class="card mb-4">
      <div class="card-header"><strong>Personal Information</strong></div>
      <div class="card-body">
        <div class="row g-3">
          {{-- First / Last / Birthday / Sex / Civil / Phone / Address --}}
          <div class="col-md-4">
            <label class="form-label">First Name <span class="text-danger">*</span></label>
            <input type="text" name="patient_first_name"
                   value="{{ old('patient_first_name', $patient->patient_first_name ?? '') }}"
                   class="form-control @error('patient_first_name') is-invalid @enderror" required>
            @error('patient_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Last Name <span class="text-danger">*</span></label>
            <input type="text" name="patient_last_name"
                   value="{{ old('patient_last_name', $patient->patient_last_name ?? '') }}"
                   class="form-control @error('patient_last_name') is-invalid @enderror" required>
            @error('patient_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Birthday</label>
            <input type="date" name="patient_birthday"
                   value="{{ old('patient_birthday', optional($patient)->patient_birthday?->format('Y-m-d')) }}"
                   class="form-control @error('patient_birthday') is-invalid @enderror">
            @error('patient_birthday')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Sex <span class="text-danger">*</span></label>
            <select name="sex" class="form-select @error('sex') is-invalid @enderror" required>
              <option value="">Choose…</option>
              @foreach(['Male','Female'] as $opt)
                <option value="{{ $opt }}"
                  {{ old('sex', $patient->sex ?? '') === $opt ? 'selected' : '' }}>
                  {{ $opt }}
                </option>
              @endforeach
            </select>
            @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Civil Status <span class="text-danger">*</span></label>
            <select name="civil_status" class="form-select @error('civil_status') is-invalid @enderror" required>
              <option value="">Choose…</option>
              @foreach(['Single','Married','Divorced','Widowed','Separated'] as $status)
                <option value="{{ $status }}"
                  {{ old('civil_status', $patient->civil_status ?? '') === $status ? 'selected' : '' }}>
                  {{ $status }}
                </option>
              @endforeach
            </select>
            @error('civil_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Phone Number</label>
            <div class="input-group">
              <span class="input-group-text">(+63)</span>
              <input type="text" name="phone_number"
                     value="{{ old('phone_number', $patient->phone_number ?? '') }}"
                     class="form-control @error('phone_number') is-invalid @enderror">
            </div>
            @error('phone_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" rows="2"
                      class="form-control @error('address') is-invalid @enderror"
                      placeholder="Street, Barangay, City, Zip Code">{{ old('address', $patient->address ?? '') }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
      <div class="card-footer text-end">
        <button type="button" class="btn btn-primary step-next"
                data-current="personal" data-next="medical">
          Next
        </button>
      </div>
    </div>
  </div>

  {{-- MEDICAL --}}
  <div class="tab-pane fade" id="medical" role="tabpanel">
    <div class="card mb-4">
      <div class="card-header"><strong>Medical Details</strong></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Primary Reason</label>
            <input type="text" name="primary_reason"
                   value="{{ old('primary_reason', $medical->primary_reason ?? '') }}"
                   class="form-control @error('primary_reason') is-invalid @enderror">
            @error('primary_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Weight (KG)</label>
            <input type="number" step="0.1" name="weight"
                   value="{{ old('weight', $medical->weight ?? '') }}"
                   class="form-control @error('weight') is-invalid @enderror">
            @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Height (cm)</label>
            <input type="number" step="0.1" name="height"
                   value="{{ old('height', $medical->height ?? '') }}"
                   class="form-control @error('height') is-invalid @enderror">
            @error('height')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Temperature (°F)</label>
            <input type="number" step="0.1" name="temperature"
                   value="{{ old('temperature', $medical->temperature ?? '') }}"
                   class="form-control @error('temperature') is-invalid @enderror">
            @error('temperature')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Blood Pressure</label>
            <input type="text" name="blood_pressure"
                   value="{{ old('blood_pressure', $medical->blood_pressure ?? '') }}"
                   class="form-control @error('blood_pressure') is-invalid @enderror"
                   placeholder="e.g. 120/80">
            @error('blood_pressure')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Heart Rate (BPM)</label>
            <input type="number" name="heart_rate"
                   value="{{ old('heart_rate', $medical->heart_rate ?? '') }}"
                   class="form-control @error('heart_rate') is-invalid @enderror">
            @error('heart_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- Medical History --}}
          <div class="col-12 mt-3">
            <label class="form-label">Medical History</label>
            <div class="row">
              <div class="col-md-3">
                @foreach(['hypertension','heart_disease','copd'] as $h)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="history_{{ $h }}" id="history_{{ $h }}"
                           {{ old("history_$h", $medical->{'medical_history'}[$h] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="history_{{ $h }}">
                      {{ ucwords(str_replace('_',' ',$h)) }}
                    </label>
                  </div>
                @endforeach
              </div>
              <div class="col-md-3">
                @foreach(['diabetes','asthma','kidney_disease'] as $h)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="history_{{ $h }}" id="history_{{ $h }}"
                           {{ old("history_$h", $medical->{'medical_history'}[$h] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="history_{{ $h }}">
                      {{ ucwords(str_replace('_',' ',$h)) }}
                    </label>
                  </div>
                @endforeach
              </div>
              <div class="col-md-6">
                <label class="form-label mt-2">Others</label>
                <input type="text" name="history_others"
                       value="{{ old('history_others', $medical->{'medical_history'}['others'] ?? '') }}"
                       class="form-control @error('history_others') is-invalid @enderror"
                       placeholder="Specify if not listed">
                @error('history_others')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          {{-- Allergies --}}
          <div class="col-12 mt-4">
            <label class="form-label">Allergies</label>
            <div class="row">
              <div class="col-md-3">
                @foreach(['penicillin','nsaids','contrast_dye'] as $a)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="allergy_{{ $a }}" id="allergy_{{ $a }}"
                           {{ old("allergy_$a", $medical->{'allergies'}[$a] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="allergy_{{ $a }}">
                      {{ ucwords(str_replace('_',' ',$a)) }}
                    </label>
                  </div>
                @endforeach
              </div>
              <div class="col-md-3">
                @foreach(['sulfa','latex','none'] as $a)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="allergy_{{ $a }}" id="allergy_{{ $a }}"
                           {{ old("allergy_$a", $medical->{'allergies'}[$a] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="allergy_{{ $a }}">  
                      {{ $a==='none' ? 'No Known Allergies' : ucwords($a) }}
                    </label>
                  </div>
                @endforeach
              </div>
              <div class="col-md-6">
                <label class="form-label mt-2">Others</label>
                <input type="text" name="allergy_others"
                       value="{{ old('allergy_others', $medical->{'allergies'}['others'] ?? '') }}"
                       class="form-control @error('allergy_others') is-invalid @enderror"
                       placeholder="Specify if not listed">
                @error('allergy_others')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer d-flex justify-content-between">
        <button type="button" class="btn btn-secondary step-prev"
                data-current="medical" data-prev="personal">
          Previous
        </button>
        <button type="button" class="btn btn-primary step-next"
                data-current="medical" data-next="admission">
          Next
        </button>
      </div>
    </div>
  </div>

  {{-- ADMISSION --}}
  <div class="tab-pane fade" id="admission" role="tabpanel">
    <div class="card mb-4">
      <div class="card-header"><strong>Admission Details</strong></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
      <input type="datetime-local"
       class="form-control"
       name="admission_date"
       value="{{ old('admission_date', now()->format('Y-m-d\TH:i')) }}">

            @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Admission Type <span class="text-danger">*</span></label>
            <input type="text" name="admission_type"
                   value="{{ old('admission_type', $admission->admission_type ?? '') }}"
                   class="form-control @error('admission_type') is-invalid @enderror" required>
            @error('admission_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Admission Source</label>
            <input type="text" name="admission_source"
                   value="{{ old('admission_source', $admission->admission_source ?? '') }}"
                   class="form-control @error('admission_source') is-invalid @enderror">
            @error('admission_source')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Department <span class="text-danger">*</span></label>
            <select id="department" name="department_id"
                    class="form-select @error('department_id') is-invalid @enderror" required>
              <option value="">Choose…</option>
              @foreach($departments as $d)
                <option value="{{ $d->department_id }}"
                  {{ old('department_id', $admission->department_id ?? '') == $d->department_id ? 'selected':''}}>
                  {{ $d->department_name }}
                </option>
              @endforeach
            </select>
            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Attending Doctor <span class="text-danger">*</span></label>
            <select id="doctor" name="doctor_id"
                    class="form-select @error('doctor_id') is-invalid @enderror" required>
              <option value="">Choose department first…</option>
              @foreach($doctors as $doc)
                @php
                  $limit = 10;
                  $load  = $doc->today_load ?? $doc->todaysLoad();
                  $full  = $load >= $limit;
                  $statusText = $full ? 'Unavailable' : 'Available';
                @endphp
                <option value="{{ $doc->doctor_id }}"
                        {{ old('doctor_id', $admission->doctor_id ?? '') == $doc->doctor_id ? 'selected' : '' }}
                        {{ $full ? 'disabled' : '' }}>
                  {{ $doc->doctor_name }} ({{ $statusText }})
                </option>
              @endforeach
            </select>
            @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Room <span class="text-danger">*</span></label>
            <select id="room" name="room_id"
                    class="form-select @error('room_id') is-invalid @enderror" required>
              <option value="">Choose department first…</option>
              @foreach($rooms as $room)
                @php
                  $occupied = $room->occupiedCount();
                  $full     = $room->isFull();
                @endphp
                <option value="{{ $room->room_id }}"
                        {{ old('room_id', $admission->room_id ?? '') == $room->room_id ? 'selected' : '' }}
                        {{ $full ? 'disabled' : '' }}>
                  {{ $room->room_number }} ({{ $occupied }}/{{ $room->capacity }})
                </option>
              @endforeach
            </select>
            @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Bed</label>
            <select id="bed" name="bed_id"
                    class="form-select @error('bed_id') is-invalid @enderror">
              <option value="">Choose room first…</option>
              @foreach($beds as $bd)
                @php
                  $occupied = $bd->isOccupied();
                  $text     = $bd->bed_number . ' (' . ($occupied ? 'Occupied' : 'Free') . ')';
                @endphp
                <option value="{{ $bd->bed_id }}"
                        {{ old('bed_id', $admission->bed_id ?? '') == $bd->bed_id ? 'selected' : '' }}
                        {{ $occupied ? 'disabled' : '' }}>
                  {{ $text }}
                </option>
              @endforeach
            </select>
            @error('bed_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label">Admission Notes</label>
            <textarea name="admission_notes" rows="3"
                      class="form-control @error('admission_notes') is-invalid @enderror"
                      placeholder="Any special instructions…">{{ old('admission_notes', $admission->admission_notes ?? '') }}</textarea>
            @error('admission_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
      <div class="card-footer d-flex justify-content-between">
        <button type="button" class="btn btn-secondary step-prev"
                data-current="admission" data-prev="medical">
          Previous
        </button>
        <button type="button" class="btn btn-primary step-next"
                data-current="admission" data-next="billing">
          Next
        </button>
      </div>
    </div>
  </div>

  {{-- BILLING --}}
  <div class="tab-pane fade" id="billing" role="tabpanel">
    <div class="card mb-4">
      <div class="card-header"><strong>Billing Details</strong></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Guarantor Name <span class="text-danger">*</span></label>
            <input type="text" name="guarantor_name"
                   value="{{ old('guarantor_name', optional($billing)->guarantor_name ?? '') }}"
                   class="form-control @error('guarantor_name') is-invalid @enderror" required>
            @error('guarantor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Relationship to Guarantor <span class="text-danger">*</span></label>
            <input type="text" name="guarantor_relationship"
                   value="{{ old('guarantor_relationship', optional($billing)->guarantor_relationship ?? '') }}"
                   class="form-control @error('guarantor_relationship') is-invalid @enderror" required>
            @error('guarantor_relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            
          </div>
          

        </div>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-secondary step-prev"
                data-current="billing" data-prev="admission">
          Previous
        </button>
        {{-- Final submit button should be in your parent create/edit template --}}
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // dynamic fetch logic
  const deptSelect = document.getElementById('department');
  const docSelect  = document.getElementById('doctor');
  const roomSelect = document.getElementById('room');
  const bedSelect  = document.getElementById('bed');

  deptSelect?.addEventListener('change', async () => {
    const depId = deptSelect.value;
    if (!depId) return;
    // fetch doctors
    const doctors = await fetch(`/admission/departments/${depId}/doctors`).then(r => r.json());
    docSelect.innerHTML = `<option value="">Choose doctor…</option>`;
    doctors.forEach(d => {
      docSelect.innerHTML += `<option value="${d.doctor_id}">${d.doctor_name}</option>`;
    });
    // fetch rooms
    const rooms = await fetch(`/admission/departments/${depId}/rooms`).then(r => r.json());
    roomSelect.innerHTML = `<option value="">Choose room…</option>`;
    rooms.forEach(rm => {
      roomSelect.innerHTML += `<option value="${rm.room_id}">${rm.room_number}</option>`;
    });
    // clear beds
    bedSelect.innerHTML = `<option value="">Choose room first…</option>`;
  });

  roomSelect?.addEventListener('change', async () => {
    const roomId = roomSelect.value;
    if (!roomId) return;
    const beds = await fetch(`/admission/rooms/${roomId}/beds`).then(r => r.json());
    bedSelect.innerHTML = `<option value="">Choose bed…</option>`;
    beds.forEach(b => {
      bedSelect.innerHTML += `<option value="${b.bed_id}">${b.bed_number}</option>`;
    });
  });

  // restore on edit
  if (deptSelect?.value) deptSelect.dispatchEvent(new Event('change'));
  if (roomSelect?.value) roomSelect.dispatchEvent(new Event('change'));

  // step navigation
  document.querySelectorAll('.step-next').forEach(btn => {
    btn.addEventListener('click', () => {
      const current = btn.dataset.current;
      const next    = btn.dataset.next;
      document.getElementById(`${current}-tab`).classList.add('completed');
      new bootstrap.Tab(document.getElementById(`${next}-tab`)).show();
    });
  });

  document.querySelectorAll('.step-prev').forEach(btn => {
    btn.addEventListener('click', () => {
      const current = btn.dataset.current;
      const prev    = btn.dataset.prev;
      document.getElementById(`${current}-tab`).classList.remove('completed');
      new bootstrap.Tab(document.getElementById(`${prev}-tab`)).show();
    });
  });
});
</script>
