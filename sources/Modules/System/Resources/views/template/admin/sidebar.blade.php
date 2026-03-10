<style>
    /* KUSTOM SCROLLBAR SIDEBAR BIAR GANTENG */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }
    .sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 3px;
    }
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    .nav-sidebar .nav-treeview {
        padding-left: 0; margin-left: 0;
    }

    /* Styling indikator */
    .nav-indicator {
        font-size: 0.75rem;
        width: 1rem;
        text-align: center;
        transition: transform 0.3s ease;
        color: #adb5bd; /* Warna abu-abu */
    }

    /* Strip (-) */
    .nav-indicator.fa-minus {
        font-size: 0.6rem;
        opacity: 0.7;
    }

    /* Menu Open */
    .nav-item.menu-open > .nav-link {
        color: yellow !important;

        .nav-indicator.fa-chevron-right {
            transform: rotate(90deg);
            color: yellow;
        }
    }

    /* Active State */
    .nav-link.active {
        background-color: teal !important;
        color: yellow !important;
    }
    .nav-link.active > .nav-indicator {
        color: yellow !important;
        opacity: 1;
    }

    .nav-sidebar .nav-link > .nav-icon {
        margin-left: 0 !important;
        margin-right: 0.6rem !important;
        font-size: 1rem;
        width: 1.2rem;
        text-align: center;
    }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('public/assetsku/img/logotsu.png') }}" alt="TSU Logo" class="brand-image"
             style="opacity: .8">
        <span class="brand-text font-weight-light" style="font-size: 18px;font-weight: bold;">Tiga Serangkai University</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img src="{{ Auth::user()->profile_photo_url }}"
                     class="img-circle elevation-2"
                     style="width: 2.1rem; height: 2.1rem; object-fit: cover;"
                     alt="User Image">
            </div>
            <div class="info w-100 overflow-hidden">
                <a href="javascript:void(0)" class="d-block text-truncate">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column text-sm" data-widget="treeview" role="menu" data-accordion="false">
                {{-- MENU UTAMA (DINAMIS) --}}
                <li class="nav-header">Main Navigation</li>
                <x-layouts.sidebar mode="main" />

                {{-- Pemisah Visual --}}
                <div class="user-panel mt-2 pb-2 mb-2 d-flex border-bottom-0"></div>

                {{-- INTEGRASI PDDIKTI (Area Bawah) --}}
                <li class="nav-header mt-3 border-top pt-3">Integrasi PDDIKTI</li>

                @if(!session()->has('neofeeder_token'))
                    {{-- KONDISI: BELUM LOGIN (Tombol Kuning) --}}
                    <li class="nav-item mb-5">
                        <a href="{{ route('neo_feeder.login') }}" class="nav-link" style="background-color: #ffc107; color: #1f2d3d;">
                            <i class="nav-icon fas fa-key"></i>
                            <p><b>Buka Akses Feeder</b></p>
                        </a>
                    </li>
                @else
                    {{-- KONDISI: SUDAH LOGIN (Status Hijau) --}}
                    {{-- A. RENDER MENU NEO FEEDER DISINI --}}
                    {{-- PANGGILAN 2: Mode 'feeder' (Render cuma anak-anak Feeder) --}}
                    <x-layouts.sidebar mode="feeder" />

                    {{-- B. TOMBOL LOGOUT + INDIKATOR --}}
                    <li class="nav-item mt-2">
                        <form action="{{ route('neo_feeder.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link btn-block text-left" style="background-color: #e74c3c; color: white; border: none;">
                                <i class="nav-icon fas fa-power-off"></i>
                                <p>
                                    Putus Koneksi
                                </p>
                            </button>
                        </form>
                    </li>

                    {{-- Info user kecil di bawah tombol --}}
                    <div class="text-center mt-2">
                        <small class="text-muted" style="font-size: 0.7rem;">
                            <i class="fas fa-user-circle mr-1"></i> {{ session('neofeeder_username') }}
                        </small>
                    </div>
                @endif
            </ul>
        </nav>
    </div>
</aside>
