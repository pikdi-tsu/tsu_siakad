@extends('system::template/admin/header')
@section('title', $title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>{{ $menu }}</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Data Pelengkap</li>
                        <li class="breadcrumb-item active">{{ $menu }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0 d-inline-block">Daftar {{ $menu }}</h5>
                            @can('system:master_contactperson:create')
                                <button class="btn btn-success float-right" id="btn-tambah"><i class="fas fa-plus"></i> Tambah</button>
                            @else
                                <span class="badge badge-secondary p-2 float-right shadow-sm" style="cursor: not-allowed; opacity: 0.7;" title="No Access"><i class="fas fa-lock mr-1"></i> Tambah (No Access)</span>
                            @endcan
                        </div>

                        <div class="card-body">
                            <ul class="nav nav-tabs mb-4" id="prodiTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="lokal-tab" data-toggle="tab" href="#tab-lokal" role="tab"><i class="fas fa-database text-primary mr-1"></i> Data Prodi Lokal</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="feeder-tab" data-toggle="tab" href="#tab-feeder" role="tab"><i class="fas fa-cloud text-success mr-1"></i> Data Neo Feeder</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="prodiTabContent">

                                {{-- TAB 1: DATA LOKAL --}}
                                <div class="tab-pane fade show active" id="tab-lokal" role="tabpanel">
                                    {{-- FORM CREATE / EDIT --}}
                                    <div id="form-container" class="mb-4 p-3 border rounded bg-light" style="display:none">
                                        <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-plus"></i> Input Program Studi</h5>
                                        <form id="form-prodi">
                                            @csrf
                                            <input type="hidden" name="id" id="id">

                                            <div class="row mb-2">
                                                <div class="col-md-12">
                                                    <div class="alert alert-info py-2 mb-2">
                                                        <small><i class="fas fa-link"></i> <b>Mapping Feeder:</b> Pilih Prodi dari Neo Feeder di bawah ini agar Mahasiswa dari prodi ini bisa dikirim ke PDDIKTI. (Kosongkan jika tidak disinkronkan).</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Pilih Prodi Neo Feeder (Mapping)</label>
                                                        <select class="form-control" name="id_prodi_feeder" id="id_prodi_feeder">
                                                            <option value="">-- Tidak Terhubung ke Feeder --</option>
                                                            @foreach($feeder_prodis as $fp)
                                                                <option value="{{ $fp->id_prodi }}">[{{ $fp->kode_program_studi }}] {{ $fp->nama_jenjang_pendidikan }} - {{ $fp->nama_program_studi }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Kode Prodi Lokal <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="kode_prodi" id="kode_prodi" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nama Prodi Lokal <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="nama_prodi" id="nama_prodi" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Ketua Prodi</label>
                                                        <input type="text" class="form-control" name="ketua_prodi" id="ketua_prodi">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Fakultas ID</label>
                                                        <input type="text" class="form-control" name="fakultas_id" id="fakultas_id">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Status Prodi <span class="text-danger">*</span></label>
                                                        <select class="form-control" name="status_prodi" id="status_prodi" required>
                                                            <option value="">-- Pilih Status --</option>
                                                            <option value="Aktif">Aktif</option>
                                                            <option value="Tidak Aktif">Tidak Aktif</option>
                                                            <option value="Tutup">Tutup</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                                <button type="button" id="btn-cancel" class="btn btn-secondary">Batal</button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="table-prodi" class="table table-bordered table-striped" width="100%">
                                            <thead style="background:#003366;color:white">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Kode</th>
                                                <th>Nama Prodi</th>
                                                <th>Ketua Prodi</th>
                                                <th>Status</th>
                                                <th width="15%" class="text-center">Aksi</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- TAB 2: DATA NEO FEEDER --}}
                                <div class="tab-pane fade" id="tab-feeder" role="tabpanel">
                                    <div class="row mb-3">
                                        <div class="col-md-8 align-self-center">
                                            <span class="text-muted"><i class="fas fa-info-circle"></i> Referensi Program Studi dari PDDIKTI.</span>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <button class="btn btn-info btn-sm" id="btn-sync-feeder">
                                                <i class="fas fa-cloud-download-alt mr-1"></i> Tarik Data Feeder
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="table-feeder" class="table table-bordered table-striped" width="100%">
                                            <thead style="background:#003366;color:white">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Kode Feeder</th>
                                                <th>Nama Prodi</th>
                                                <th>Jenjang</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            // INIT TABEL LOKAL
            let table = $('#table-prodi').DataTable({
                processing: true, serverSide: true,
                ajax: "{{ route('perguruan_tinggi.program_studi.index') }}",
                order: [[2, 'asc']],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode_prodi', name: 'kode_prodi' },
                    { data: 'nama_prodi', name: 'nama_prodi' },
                    { data: 'ketua_prodi', name: 'ketua_prodi' },
                    { data: 'status_prodi', name: 'status_prodi',
                        render: function(data) {
                            let map = { 'Aktif': 'success', 'Tidak Aktif': 'warning', 'Tutup': 'danger' };
                            return `<span class="badge badge-${map[data] ?? 'secondary'}">${data}</span>`;
                        }
                    },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // INIT TABEL FEEDER
            var tableFeederInit = false;
            $('a[href="#tab-feeder"]').on('shown.bs.tab', function (e) {
                if (!tableFeederInit) {
                    $('#table-feeder').DataTable({
                        processing: true, serverSide: true,
                        ajax: '{!! route('perguruan_tinggi.program_studi.json_feeder') !!}',
                        columns: [
                            { data: 'DT_RowIndex', orderable: false, searchable: false },
                            { data: 'kode_prodi' },
                            { data: 'nama_prodi' },
                            { data: 'jenjang' },
                            { data: 'status' }
                        ]
                    });
                    tableFeederInit = true;
                }
            });

            // TOMBOL SYNC FEEDER (DENGAN ALERT GLOBAL)
            $('#btn-sync-feeder').click(function() {
                Swal.fire({
                    title: 'Tarik Referensi Prodi?',
                    text: "Sistem akan mengambil referensi Program Studi resmi dari server PDDIKTI.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tarik Sekarang!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Mengambil Data...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                        $.post("{{ route('perguruan_tinggi.program_studi.sync_feeder') }}")
                            .done(function(res) {
                                Swal.close();
                                if (res.status === 'success') {
                                    $('#table-feeder').DataTable().ajax.reload(null, false);
                                    notifalert('Sukses!', res.message, 'success');
                                    // Reload halaman agar Dropdown mapping ikut ter-update datanya
                                    setTimeout(() => location.reload(), 2000);
                                } else {
                                    notifalert('Gagal!', res.message, 'error');
                                }
                            }).fail(function() {
                            Swal.close();
                            notifalert('Error!', 'Koneksi terputus saat menarik data.', 'error');
                        });
                    }
                });
            });

            // OPERASI FORM LOKAL
            $('#btn-tambah').click(function() { resetForm(); $('#form-container').slideDown(); $('#kode_prodi').focus(); });
            $('#btn-cancel').click(function() { resetForm(); $('#form-container').slideUp(); });

            $('#form-prodi').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('perguruan_tinggi.program_studi.store') }}", $(this).serialize(), function(res) {
                    if (res.status === 'success') {
                        notifalert('Berhasil', res.message, 'success');
                        table.ajax.reload();
                        resetForm();
                        $('#form-container').slideUp();
                    } else {
                        notifalert('Gagal', res.message, 'error');
                    }
                });
            });

            $('body').on('click', '.btn_edit', function() {
                let id = $(this).data('id');
                $.get("{{ route('perguruan_tinggi.program_studi.edit', ':id') }}".replace(':id', id), function(res) {
                    if (res.status === 'success') {
                        $('#id').val(res.data.id);
                        $('#id_prodi_feeder').val(res.data.id_prodi_feeder); // 🔥 Set nilai mapping saat Edit
                        $('#kode_prodi').val(res.data.kode_prodi);
                        $('#nama_prodi').val(res.data.nama_prodi);
                        $('#ketua_prodi').val(res.data.ketua_prodi);
                        $('#fakultas_id').val(res.data.fakultas_id);
                        $('#status_prodi').val(res.data.status_prodi);
                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Program Studi');
                        $('#form-container').slideDown();
                    }
                });
            });

            $('body').on('click', '.btn_hapus', function() {
                let id = $(this).data('id');
                Swal.fire({ title: 'Hapus data ini?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, hapus'
                }).then(r => {
                    if (r.isConfirmed) {
                        $.ajax({
                            type: 'DELETE', url: "{{ route('perguruan_tinggi.program_studi.delete', ':id') }}".replace(':id', id),
                            success: function(res) {
                                notifalert('Terhapus', res.message, 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-prodi')[0].reset();
                $('#id').val('');
                $('#id_prodi_feeder').val(''); // Reset mapping
                $('#form-title').html('<i class="fas fa-plus"></i> Input Program Studi');
            }
        });
    </script>
@endsection
