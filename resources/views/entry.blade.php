{{-- resources/views/entry.blade.php --}}
@extends('layouts.app')

@section('content')
    {{-- ===== Hero Section ===== --}}
    <section class="hero-section position-relative" style="height: 100vh; overflow: hidden;">
        {{-- Background Image --}}
        <div
          class="position-absolute top-0 start-0 w-100 h-100"
          style="
            background: url('{{ asset('images/hero-bg.png') }}') center center no-repeat;
            background-size: cover;
            filter: brightness(0.6);
            z-index: 0;
          "
        ></div>

        {{-- Overlay Content --}}
        <div class="container h-100 d-flex flex-column justify-content-center align-items-start position-relative" style="z-index: 1;">
            <div class="row">
                <div class="col-lg-6 text-white">
                    <h1 class="display-4 fw-bold">Welcome to PatientCare</h1>
                    <p class="lead mt-3">
                        PatientCare is a patient-first billing portal designed to make hospital billing simpler, clearer, and fairer. built by people, for the people.
                    </p>
                    <a href="#services" class="btn btn-primary btn-lg mt-4">
                        Read More &nbsp;
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="col-lg-6 d-none d-lg-flex justify-content-center">
                    {{-- Optional: A transparent version of your logo or illustration --}}
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Example Services Section Anchor ===== --}}
    <section id="services" class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Our Services</h2>
            <p>…brief overview of what the portal offers…</p>
        </div>
    </section>

    {{-- ===== Example Contact Section Anchor ===== --}}
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2 class="fw-bold mb-4">Contact Us</h2>
            <p>…contact form or contact details…</p>
        </div>
    </section>
@endsection
