<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('public/assetsku/img/logotsu.png') }}" alt="AdminLTE Logo" class="brand-image"
            style="opacity: .8">
        <span class="brand-text font-weight-light" style="font-size: 18px;font-weight: bold;">Tiga Serangkai
            University</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::user()->profile_photo_url }}"
                    class="img-circle elevation-2"
                    style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #adb5bd;" alt="User Image">
            </div>
            <div class="info text-sm">
                <a href="javascript:void(0)" class="d-block">{{ Auth::user()->name }}</a>
                {{-- <a href="#"><i class="fa fa-circle text-success"></i> Online</a> --}}
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column text-sm" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-header">Main Navigation</li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @if (checkmenu('SDM','Data Mahasiswa')>0)
                <li class="nav-item">
                    <a href="{{ route('home.mahasiswa') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Mahasiswa</p>
                    </a>
                </li>
                @endif
                @if (checkmenu('SDM','Data Pegawai')>0)
                <li class="nav-item">
                    <a href="{{ route('home.dosen') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Pegawai</p>
                    </a>
                </li>
                @endif
                <li class="nav-item" style="display: none;">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                Master A
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                Master B
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                Master C
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                    $fakultas = checkmenu('Master Data', 'Master Fakultas');
                    $jurusan = checkmenu('Master Data', 'Master Jurusan');
                    $jenjang = checkmenu('Master Data', 'Master Jenjang Pendidikan');
                    $batch = checkmenu('Master Data', 'Master Batch Pendaftaran');
                    $ukt = checkmenu('Master Data', 'Master Tarif UKT');
                    $provinsi = checkmenu('Master Data', 'Master Provinsi');
                    $kabupaten = checkmenu('Master Data', 'Master Kabupaten Kota');
                    $kecamatan = checkmenu('Master Data', 'Master kecamatan');
                    $kelurahan = checkmenu('Master Data', 'Master Kelurahan');
                @endphp
                @if($fakultas+$jurusan+$jenjang+$batch+$ukt+$provinsi+$kabupaten+$kecamatan+$kelurahan>0)
                    <li class="nav-item"> {{-- menu-open --}}
                        <a href="#" class="nav-link"> {{-- active --}}
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Master Data
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link"> {{-- active --}}
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>Perguruan Tinggi
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('admin.fakultas.show')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Fakultas
                                            </a>
                                        </li>
                                    @endif
                                    @if ($jurusan > 0)
                                        <li class="nav-item">
                                            <a href="{{route('admin.Jurusan.show')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Program Studi
                                            </a>
                                        </li>
                                    @endif
                                    @if ($jenjang > 0)
                                        <li class="nav-item">
                                            <a href="{{route('admin.Jenjang.show')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Jenjang Pendidikan Universitas
                                            </a>
                                        </li>
                                    @endif
                                    @if ($jenjang > 0)
                                        <li class="nav-item">
                                            <a href="{{route('sistem_kuliah.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Sistem Kuliah
                                            </a>
                                        </li>
                                    @endif
                                    @if ($jenjang > 0)
                                        <li class="nav-item">
                                            <a href="{{route('ruang_kuliah.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Ruang Kuliah
                                            </a>
                                        </li>
                                    @endif
                                    @if ($jenjang > 0)
                                        <li class="nav-item">
                                            <a href="{{route('kegiatan_akademik.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kegiatan Akademik
                                            </a>
                                        </li>
                                    @endif
                                    @if ($jenjang > 0)
                                        <li class="nav-item">
                                            <a href="{{route('kalender_akademik.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kalender Akademik
                                            </a>
                                        </li>
                                    @endif
                                    @if ($ukt > 0)
                                        <li class="nav-item">
                                            <a href="{{route('admin.TarifUKT.show')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Tarif UKT
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"> {{-- active --}}
                                    <i class="nav-icon fas fa-person-booth"></i>
                                    <p>Perkuliahan
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('jenis_matakuliah.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Jenis Matakuliah
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('kelompok_matakuliah.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kelompok Matakuliah
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('bidang_ilmu.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Bidang Ilmu
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('unsur_nilai.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Unsur Nilai
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('kelas_perkuliahan.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kelas Perkuliahan
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('slot_waktu.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Slot Waktu
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('status_hadir.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Status Hadir
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('jenis_pertemuan.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Jenis Pertemuan
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('jenis_modul_mata_kuliah.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Jenis Modul Mata Kuliah
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('jenis_kegiatan_pendukung.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Jenis Kegiatan Pendukung
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"> {{-- active --}}
                                    <i class="nav-icon fas fa-id-card"></i>
                                    <p>Biodata
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('agama.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Agama
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('pekerjaan.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Pekerjaan
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('penghasilan.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Penghasilan
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('jas_almamater.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Jas Almamater
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"> {{-- active --}}
                                    <i class="nav-icon fas fa-child"></i>
                                    <p>Mahasiswa
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('status_mahasiswa.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Status Mahasiswa
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('jenis_tinggal.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Jenis Tinggal
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('transportasi.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Transportasi
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('kebutuhan_khusus.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kebutuhan Khusus
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"> {{-- active --}}
                                    <i class="nav-icon fas fa-map"></i>
                                    <p>Wilayah
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if ($provinsi > 0)
                                        <li class="nav-item">
                                            <a href="{{route('admin.Provinsi.show')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Provinsi
                                            </a>
                                        </li>
                                    @endif
                                    @if ($kabupaten > 0)
                                        <li class="nav-item">
                                            <a href="{{route('admin.Kabupaten.show')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kabupaten/Kota
                                            </a>
                                        </li>
                                    @endif
                                    @if ($kecamatan > 0)
                                        <li class="nav-item">
                                            <a href="{{route('admin.Kecamatan.show')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kecamatan
                                            </a>
                                        </li>
                                    @endif
                                    @if ($kelurahan > 0)
                                        <li class="nav-item">
                                            <a href="{{route('admin.Kelurahan.show')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kelurahan
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"> {{-- active --}}
                                    <i class="nav-icon fas fa-users-cog"></i>
                                    <p>Settings
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('kategori_kuesioner_layanan.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Kuesioner Layanan
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('setting.prodi.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Setting Prodi
                                            </a>
                                        </li>
                                    @endif
                                    @if ($fakultas > 0)
                                        <li class="nav-item">
                                            <a href="{{route('periode_akademik.index')}}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                Periode Akademik
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif
                @php
                    $changepassword = checkmenu('Tools','Change Password');
                    $listmenu = checkmenu('Tools','List Menu');
                    $groupuser = checkmenu('Tools','Group User');
                    $usermanagement = checkmenu('Tools','User Management');
                    $userreset = checkmenu('Tools','User Reset');
                    // dd($changepassword,$listmenu);
                @endphp
                @if ($changepassword>0)
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Tools
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if ($changepassword>0)
                        <li class="nav-item">
                            <a href="{{ route('show.changepassword') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                Change Password
                            </a>
                        </li>
                        @endif
                        @if($listmenu+$groupuser>0)
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p> Management Menu
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if($listmenu>0)
                                <li class="nav-item">
                                    <a href="{{route('menu.show')}}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>List Menu</p>
                                    </a>
                                </li>
                                @endif
                                @if($groupuser>0)
                                <li class="nav-item">
                                    <a href="{{route('gruopuser.show')}}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Group User</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if($usermanagement+$userreset>0)
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon far fa-circle text-danger"></i>
                                <p> Management User
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if($usermanagement>0)
                                <li class="nav-item">
                                    <a href="{{ route('show.userManagement') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>User Management</p>
                                    </a>
                                </li>
                                @endif
                                @if($userreset>0)
                                <li class="nav-item">
                                    <a href="{{route('UserReset.show')}}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>User Reset</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<style>
    .user-panel .info a {
        white-space: normal !important;
        word-break: break-word;
        display: block;
        max-width: 150px;
    }
</style>
<!-- /.Main Sidebar Container -->
