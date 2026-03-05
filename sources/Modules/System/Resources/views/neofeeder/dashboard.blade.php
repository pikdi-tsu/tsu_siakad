@extends('system::template/admin/header')
@section('title', 'Dashboard Integrasi')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-network-wired mr-2 text-primary"></i> Hub Integrasi PDDIKTI</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- INFO STATUS --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success shadow-sm border-0">
                        <h5><i class="icon fas fa-check-circle"></i> Koneksi Terhubung!</h5>
                        Saat ini Anda telah terhubung dengan Server Neo Feeder. Sesi Anda aktif.
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- CARD 1: STATUS SERVER --}}
                <div class="col-md-4">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Status Koneksi</h3>
                        </div>
                        <div class="card-body text-center py-4">
                            <div class="display-4 text-success mb-2"><i class="fas fa-wifi"></i></div>
                            <h5 class="font-weight-bold">ONLINE</h5>
                            <p class="text-muted text-sm">Token Valid</p>
                            <hr>
                            <div class="text-left">
                                <small class="text-muted d-block">User: <b>{{ session('neofeeder_username') }}</b></small>
                                <small class="text-muted d-block">Login: <b>{{ now()->format('H:i d/m/Y') }}</b></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: MENU CEPAT --}}
                <div class="col-md-8">
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Aksi Cepat</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('neo_feeder.mahasiswa.index') }}" class="btn btn-app bg-white border w-100 py-3 shadow-sm text-left pl-4" style="height: auto; text-align: left;">
                                        <span class="badge bg-danger float-right">Priority</span>
                                        <i class="fas fa-user-graduate text-primary" style="font-size: 2rem;"></i>
                                        <h5 class="mt-2 mb-0 font-weight-bold text-dark">Mahasiswa</h5>
                                        <small class="text-muted">Sinkronisasi Biodata & History</small>
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="#" class="btn btn-app bg-white border w-100 py-3 shadow-sm text-left pl-4" style="height: auto; text-align: left;">
                                        <i class="fas fa-chalkboard-teacher text-success" style="font-size: 2rem;"></i>
                                        <h5 class="mt-2 mb-0 font-weight-bold text-dark">Perkuliahan</h5>
                                        <small class="text-muted">Kelas, KRS, & Nilai (Coming Soon)</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
