@extends('system::template/admin/header')
@section('title', $title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $menu }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Master Data</li>
                        <li class="breadcrumb-item active">{{ $menu }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="m-0 d-inline-block">Data {{ $menu }}</h5>
                    <div class="float-right">
                        <button class="btn btn-success btn-sm" id="btn-create">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                        <button class="btn btn-warning btn-sm d-none" id="btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>

                <div class="card-body">

                    <form id="form-pt">
                        @csrf
                        <input type="hidden" name="id" id="id">

                        {{-- IDENTITAS --}}
                        <div class="row">
                            <div class="col-md-3">
                                <label>Kode Unit *</label>
                                <input type="text" name="kode_unit" id="kode_unit" class="form-control form-input"
                                    disabled>
                            </div>
                            <div class="col-md-6">
                                <label>Nama Perguruan Tinggi *</label>
                                <input type="text" name="nama_unit" id="nama_unit" class="form-control form-input"
                                    disabled>
                            </div>
                            <div class="col-md-6">
                                <label>Nama Perguruan Tinggi EN*</label>
                                <input type="text" name="nama_unit_en" id="nama_unit_en" class="form-control form-input"
                                    disabled>
                            </div>
                            <div class="col-md-3">
                                <label>Nama Singkat</label>
                                <input type="text" name="nama_singkat" id="nama_singkat" class="form-control form-input"
                                    disabled>
                            </div>
                        </div>

                        <hr>

                        {{-- KLASIFIKASI --}}
                        <div class="row">
                            <div class="col-md-4">
                                <label>Jenis Perguruan Tinggi</label>
                                <input type="text" name="jenis_perguruan_tinggi" id="jenis_perguruan_tinggi"
                                    class="form-control form-input" disabled>
                            </div>
                            <div class="col-md-4">
                                <label>Lembaga Naungan</label>
                                <input type="text" name="lembaga_naungan" id="lembaga_naungan"
                                    class="form-control form-input" disabled>
                            </div>
                            <div class="col-md-4">
                                <label>Unit Satuan Kerja</label>
                                <input type="text" name="unit_satuan_kerja" id="unit_satuan_kerja"
                                    class="form-control form-input" disabled>
                            </div>
                        </div>

                        <hr>

                        {{-- LEGALITAS --}}
                        <div class="row">
                            <div class="col-md-3">
                                <label>Periode Berdiri</label>
                                <input type="number" name="periode_berdiri" id="periode_berdiri"
                                    class="form-control form-input" disabled>
                            </div>
                            <div class="col-md-3">
                                <label>No SK Pendirian</label>
                                <input type="text" name="no_sk_pendirian" id="no_sk_pendirian"
                                    class="form-control form-input" disabled>
                            </div>
                            <div class="col-md-3">
                                <label>Tgl SK Pendirian</label>
                                <input type="date" name="tanggal_sk_pendirian" id="tanggal_sk_pendirian"
                                    class="form-control form-input" disabled>
                            </div>
                            <div class="col-md-3">
                                <label>Rektor</label>
                                <input type="text" name="rektor" id="rektor" class="form-control form-input"
                                    disabled>
                            </div>
                        </div>

                        <hr>

                        {{-- PIMPINAN --}}
                        <div class="row">
                            <div class="col-md-3"><label>WR I</label><input disabled name="wr1" id="wr1"
                                    class="form-control form-input"></div>
                            <div class="col-md-3"><label>WR II</label><input disabled name="wr2" id="wr2"
                                    class="form-control form-input"></div>
                            <div class="col-md-3"><label>WR III</label><input disabled name="wr3" id="wr3"
                                    class="form-control form-input"></div>
                            <div class="col-md-3"><label>WR IV</label><input disabled name="wr4" id="wr4"
                                    class="form-control form-input"></div>
                        </div>

                        <hr>

                        {{-- AKREDITASI --}}
                        <div class="row">
                            <div class="col-md-4"><label>Lembaga Akreditasi</label><input disabled
                                    name="lembaga_akreditasi" id="lembaga_akreditasi" class="form-control form-input">
                            </div>
                            <div class="col-md-2"><label>Peringkat</label><input disabled name="peringkat_akreditasi"
                                    id="peringkat_akreditasi" class="form-control form-input"></div>
                            <div class="col-md-2"><label>Nilai</label><input disabled name="nilai_akreditasi"
                                    id="nilai_akreditasi" class="form-control form-input"></div>
                            <div class="col-md-4"><label>No SK Akreditasi</label><input disabled name="no_sk_akreditasi"
                                    id="no_sk_akreditasi" class="form-control form-input"></div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4"><label>Tgl SK Akreditasi</label><input type="date" disabled
                                    name="tanggal_sk_akreditasi" id="tanggal_sk_akreditasi"
                                    class="form-control form-input">
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4"><label>Tgl SK Akreditasi</label><input type="date" disabled
                                        name="tanggal_berlaku_akreditasi" id="tanggal_berlaku_akreditasi"
                                        class="form-control form-input">
                                </div>

                                <div class="col-md-4"><label>Berakhir</label><input type="date" disabled
                                        name="tgl_berakhir_akreditasi" id="tgl_berakhir_akreditasi"
                                        class="form-control form-input"></div>
                            </div>

                            <hr>

                            {{-- VISI MISI --}}
                            <div class="row">
                                <div class="col-md-6"><label>Visi</label>
                                    <textarea disabled name="visi" id="visi" rows="3" class="form-control form-input"></textarea>
                                </div>
                                <div class="col-md-6"><label>Misi</label>
                                    <textarea disabled name="misi" id="misi" rows="3" class="form-control form-input"></textarea>
                                </div>
                            </div>

                            <hr>

                            {{-- KONTAK --}}
                            <div class="row">
                                <div class="col-md-6"><label>Alamat</label>
                                    <textarea disabled name="alamat" id="alamat" rows="2" class="form-control form-input"></textarea>
                                </div>
                                <div class="col-md-2"><label>Telepon</label><input disabled name="telepon" id="telepon"
                                        class="form-control form-input"></div>
                                <div class="col-md-2"><label>Fax</label><input disabled name="fax" id="fax"
                                        class="form-control form-input"></div>
                                <div class="col-md-2"><label>Email</label><input disabled name="email" id="email"
                                        class="form-control form-input"></div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-4"><label>Website</label><input disabled name="website" id="website"
                                        class="form-control form-input"></div>
                            </div>

                            <hr>

                            <div class="text-right d-none" id="form-action">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <button type="button" class="btn btn-secondary" id="btn-cancel">Batal</button>
                            </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function() {

            function setMode(mode) {
                let edit = mode === 'edit';

                $('.form-input').prop('disabled', !edit);
                $('#form-action').toggleClass('d-none', !edit);
                $('#btn-create').toggleClass('d-none', edit);
                $('#btn-edit').toggleClass('d-none', edit);
            }

            setMode('view');

            $('#btn-create').click(function() {
                $('#form-pt')[0].reset();
                $('#id').val('');
                setMode('edit');
            });

            $('#btn-edit').click(function() {
                setMode('edit');
            });

            $('#btn-cancel').click(function() {
                setMode('view');
            });

            $('#form-pt').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('perguruan_tinggi.store') }}", $(this).serialize(), function(res) {
                    Swal.fire(res.status === 'success' ? 'Berhasil' : 'Gagal', res.message, res
                        .status);
                    if (res.status === 'success') {
                        setMode('view');
                    }
                });
            });

        });
    </script>
@endsection
