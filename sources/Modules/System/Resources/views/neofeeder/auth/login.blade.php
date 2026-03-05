@extends('system::template/admin/header')
@section('title', 'Login Feeder Area')

@section('content')
    {{--
        SECTION CONTENT
        Kita pakai Flexbox 'd-flex' dengan tinggi 80vh.
        Ini akan memaksa konten berada persis di tengah vertikal & horizontal.
    --}}
    <section class="content">
        <div class="container-fluid">

            <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
                <div class="col-md-4 col-sm-6 col-11">

                    {{-- CARD LOGIN --}}
                    <div class="card card-outline card-navy elevation-3 border-0" style="border-radius: 20px;">

                        {{-- HEADER --}}
                        <div class="card-header text-center bg-white border-0 pt-4">
                            <div class="mb-2">
                                {{-- Animasi Pulse dikit biar keren --}}
                                <div class="d-inline-block rounded-circle bg-light p-3">
                                    <i class="fas fa-lock text-warning" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                            <h4 class="font-weight-bold text-dark mt-2">AKSES NEO FEEDER</h4>
                            <p class="text-muted text-sm">Gerbang Integrasi PDDIKTI</p>
                        </div>

                        {{-- BODY --}}
                        <div class="card-body px-4 pb-4">

                            {{-- ERROR ALERTS --}}
                            @if(session('error'))
                                <div class="alert alert-danger text-sm shadow-sm rounded-lg border-0">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                                </div>
                            @endif

                            @if(session('warning'))
                                <div class="alert alert-warning text-sm shadow-sm rounded-lg border-0">
                                    <i class="fas fa-clock mr-1"></i> {{ session('warning') }}
                                </div>
                            @endif

                            <form action="{{ route('neo_feeder.login.post') }}" method="POST">
                                @csrf

                                <div class="form-group mb-3">
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 rounded-left" style="border-radius: 10px 0 0 10px;">
                                                <i class="fas fa-user text-muted text-sm"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="username" class="form-control bg-light border-left-0" placeholder="Email Admin PT" required style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0 rounded-left" style="border-radius: 10px 0 0 10px;">
                                                <i class="fas fa-key text-muted text-sm"></i>
                                            </span>
                                        </div>
                                        <input type="password" name="password" class="form-control bg-light border-left-0" placeholder="Password Feeder" required style="border-radius: 0 10px 10px 0; font-size: 0.9rem;">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-navy btn-block btn-lg shadow-sm" style="background-color: #001f3f; color: white; border-radius: 10px; font-weight: 600;">
                                    Buka Koneksi <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </button>
                            </form>
                        </div>

                        {{-- FOOTER --}}
                        <div class="card-footer text-center bg-white border-top-0 pb-4">
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="fas fa-shield-alt text-success mr-1"></i> Koneksi Terenkripsi & Aman
                            </small>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection
