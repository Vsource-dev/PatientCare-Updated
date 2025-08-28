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
