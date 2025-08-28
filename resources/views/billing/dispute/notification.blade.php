{{-- resources/views/billing/notification.blade.php --}}

@extends('layouts.billing')

@section('content')
    <div class="container-fluid p-4">

        <!-- Header -->
        <div class="mb-3">
            <h5 class="hdng">Notifications</h5>
            <p class="text-muted">Stay updated with all recent actions and alerts.</p>
        </div>

        <!-- Important Alert -->
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <strong>Important!</strong> Do not share this information with anyone else.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Notifications Card -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-bell me-2"></i>Latest Notifications</span>
                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Mark All as Read</button>
                </form>
            </div>

            <div class="card-body table-responsive" style="height: 70vh;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Time Ago</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notif)
                            <tr class="{{ $notif->read ? '' : 'table-warning' }}">
                                <td><i class="fa-solid fa-circle-dot text-primary me-1"></i> {{ $notif->type ?? 'General' }}</td>
                                <td>{{ $notif->title ?? 'No message' }}</td>
                                <td>{{ $notif->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $notif->created_at->diffForHumans() }}</td>
                                <td>
                                    <span class="badge {{ $notif->read ? 'bg-success' : 'bg-danger' }}">
                                        {{ $notif->read ? 'Read' : 'Unread' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No notifications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-muted text-end">
                Last updated: {{ now()->format('F j, Y - g:i A') }}
            </div>
        </div>

    </div>
@endsection


{{-- resources/views/billing/dispute/show.blade.php --}}

@extends('layouts.billing')

@section('content')
<main class="p-4" style="margin-left: 240px;">
    <div class="container-fluid min-vh-100 p-4 d-flex flex-column" style="background-color: #fafafa;">

      
        <div class="mb-3">
            <a href="dispute-dash.html" class="btn btn-primary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back
            </a>
        </div>

        
        <div class="card border mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-bed me-2"></i>Patient Information</h5>
            </div>

            <div class="card-body d-flex align-items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="avatar rounded-circle bg-light border" style="width: 64px; height: 64px; background-image: url('{{ asset('images/default-avatar.png') }}'); background-size: cover;"></div>
                </div>

                <div>
                    <strong class="fs-5">Stanley Gonzales</strong>
                    <div class="text-muted small">Patient ID: 1234</div>
                    <div class="text-muted small">Room: 201-B</div>
                    <div class="text-muted small">Admitted: June 25, 2025</div>
                </div>
            </div>
        </div>


        <div class="card border shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-comment me-2"></i>Dispute Details</h5>
                <span class="badge bg-warning text-dark">Pending Review</span>
            </div>

            <div class="card-body d-flex flex-column gap-3">


                <div>
                    <label class="form-label fw-bold">Submitted Evidence</label><br>
                    <img src="{{ asset('storage/evidence_images/sample.jpg') }}" alt="Evidence Image" class="img-fluid rounded border" style="max-height: 250px;">
                </div>

          
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Item</label>
                        <div class="text-muted">Ceftriaxone IV</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Department</label>
                        <div class="text-muted">Pharmacy</div>
                    </div>
                </div>

                <div>
                    <label class="form-label fw-bold">Reason</label>
                    <div class="text-muted">Charged but not administered</div>
                </div>

                <div>
                    <label class="form-label fw-bold">Description</label>
                    <div class="text-muted">
                        Patient claims the medication was logged but never received due to early discharge before scheduled dose. Evidence uploaded includes discharge slip and timestamp.
                    </div>
                </div>

                <div>
                    <label class="form-label fw-bold">Amount Demanded</label>
                    <div class="fs-5 text-danger fw-semibold">₱1,250.00</div>
                </div>

                <div class="small text-muted">
                    Submitted by <strong>Stanley Gonzales</strong> on June 27, 2025 • 03:42 PM
                </div>
            </div>
        </div>

 
        <div class="text-end mt-2">
            <a href="#" class="btn btn-outline-danger me-2 decideBtn">
                <i class="fa-solid fa-circle-xmark me-2"></i>Reject
            </a>
            <a href="#" class="btn btn-success decideBtn">
                <i class="fa-solid fa-circle-check me-2"></i>Approve
            </a>
        </div>

    </div>
</main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const decideButtons = document.querySelectorAll('.decideBtn');

            decideButtons.forEach(button => {
                button.addEventListener('click', function () {

                    const isApprove = this.classList.contains('btn-success');
                    const actionText = isApprove ? 'approve' : 'reject';
                    const icon = isApprove ? 'success' : 'error';

                    Swal.fire({
                        title: `Are you sure you want to ${actionText} this?`,
                        text: "This action cannot be undone.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: isApprove ? "#28a745" : "#dc3545",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: `Yes, ${actionText} it!`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: `${isApprove ? 'Approved' : 'Rejected'}!`,
                                text: `The dispute has been ${actionText}d.`,
                                icon: icon
                            });

                           {{-- --}}
                        }
                    });

                });
            });

        });
    </script>
@endsection