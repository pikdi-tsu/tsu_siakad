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
                        <li class="breadcrumb-item">Data Pelengkap</li>
                        <li class="breadcrumb-item">Perkuliahan</li>
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
                            <button class="btn btn-success float-right" id="btn-tambah">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>

                        <div class="card-body">

                            <div id="form-container" style="display: none;" class="mb-4 p-3 border rounded bg-light">
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Jenis Modul</h5>
                                <form id="form-modul">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_modul" id="kode_modul" class="form-control" placeholder="Contoh: P" required>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label>Nama Jenis Modul <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_modul" id="nama_modul" class="form-control" placeholder="Contoh: Praktikum" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center pt-3">
                                            <div class="form-group w-100">
                                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel">Batal / Tutup Form</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table id="table-modul" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead style="background-color: #003366; color: white;">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Kode</th>
                                        <th>Nama Jenis Modul</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
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
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            // 1. INIT DATATABLE
            var table = $('#table-modul').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('jenis_modul_mata_kuliah.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode_modul', name: 'kode_modul' },
                    { data: 'nama_modul', name: 'nama_modul' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center' },
                ],
                order: [[1, 'asc']]
            });

            // 2. FORM ACTIONS
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#kode_modul').focus();
            });

            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });

            // 3. SUBMIT FORM
            $('#form-modul').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('jenis_modul_mata_kuliah.store') }}",
                    data: formData,
                    contentType: false, processData: false,
                    success: function(res) {
                        res.status == 'success' ? Swal.fire('Berhasil', res.message, 'success') : Swal.fire('Gagal', res.message, 'error');
                        if(res.status == 'success') { table.ajax.reload(); $('#form-container').slideUp(); resetForm(); }
                    },
                    error: function() { Swal.fire('Error', 'Terjadi kesalahan server.', 'error'); }
                });
            });

            // 4. EDIT DATA
            $('body').on('click', '.btn_edit', function() {
                var id = $(this).data('id');
                $.get("{{ route('jenis_modul_mata_kuliah.index') }}" + '/edit/' + id, function(res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#kode_modul').val(res.data.kode_modul);
                        $('#nama_modul').val(res.data.nama_modul);

                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Jenis Modul');
                        $('#form-container').slideDown();
                        $('html, body').animate({ scrollTop: $('#form-container').offset().top - 100 }, 'slow');
                    }
                });
            });

            // 5. DELETE DATA
            $('body').on('click', '.btn_hapus', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data ini?', icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('jenis_modul_mata_kuliah.index') }}" + '/delete/' + id,
                            success: function(res) {
                                res.status == 'success' ? Swal.fire('Terhapus', res.message, 'success') : Swal.fire('Gagal', res.message, 'error');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-modul')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Jenis Modul');
            }
        });
    </script>
@endsection
