{{--
    FILE: Modules/System/Resources/views/layouts/sidebar.blade.php
    Logic Rendering Menu dengan Atribut Detektor
--}}

@php
    // 🕵️‍♂️ DETEKSI MODE (LOGIC BARU)
    // 1. Cek apakah ada variable $mode?
    $currentMode = $mode ?? 'main';

    // 2. Kalau tidak ada, cek apakah terselip di $attributes?
    // (Ini yang terjadi pada case Komandan)
    if(isset($attributes) && $attributes->has('mode')) {
        $currentMode = $attributes->get('mode');
    }
@endphp

@foreach ($menus as $menu)

    @php
        // Cek Nama Menu (Case Insensitive biar aman)
        $isFeeder = strtolower($menu->name) === 'neo feeder';
    @endphp

    {{-- ======================================================= --}}
    {{-- LOGIC A: MODE 'MAIN' (Tampil di Atas) --}}
    {{-- Syarat: Tampilkan SEMUA menu, KECUALI Neo Feeder --}}
    {{-- ======================================================= --}}
    @if($currentMode === 'main')

        @if(!$isFeeder)
            {{-- Render Menu Normal (Dashboard, Master Data, dll) --}}
            @include('system::components.sidebar-item', ['menu' => $menu, 'level' => 0])
        @endif

        {{-- ======================================================= --}}
        {{-- LOGIC B: MODE 'FEEDER' (Tampil di Bawah) --}}
        {{-- Syarat: HANYA Cari Neo Feeder, lalu ambil ANAKNYA --}}
        {{-- ======================================================= --}}
    @elseif($currentMode === 'feeder')

        @if($isFeeder)
            {{-- Ketemu Bapaknya (Neo Feeder)! --}}
            {{-- Render ANAK-ANAKNYA saja (Mahasiswa, Perkuliahan) --}}
            @foreach($menu->children as $child)
                @include('system::components.sidebar-item', ['menu' => $child, 'level' => 0])
            @endforeach
        @endif

    @endif

@endforeach
