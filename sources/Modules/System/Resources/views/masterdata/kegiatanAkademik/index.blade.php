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
                        <li class="breadcrumb-item">Portal</li>
                        <li class="breadcrumb-item">Kegiatan</li>
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
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Kegiatan
                                    Akademik</h5>
                                <form id="form-kegiatan">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_kegiatan" id="kode_kegiatan"
                                                    class="form-control" placeholder="01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Nama Kegiatan Akademik <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_kegiatan" id="nama_kegiatan"
                                                    class="form-control" placeholder="Contoh: KKN / Wisuda" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Warna Background</label>
                                                <div class="input-group">
                                                    <input type="text" name="warna_background" id="warna_background"
                                                        class="form-control" placeholder="#RRGGBB" value="#ffffff">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text p-1">
                                                            <input type="color" id="color_picker" value="#ffffff"
                                                                style="border: none; background: none; height: 25px; width: 30px; cursor: pointer;">
                                                        </span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Klik kotak warna untuk memilih.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center pt-3">
                                            <div class="form-group w-100">
                                                <button type="submit" class="btn btn-primary btn-block"><i
                                                        class="fas fa-save"></i> Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel">Batal /
                                                Tutup Form</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table id="table-kegiatan" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead style="background-color: #003366; color: white;">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="10%">Kode</th>
                                            <th>Nama Kegiatan Akademik</th>
                                            <th width="15%">Background</th>
                                            <th width="10%" class="text-center">Aksi</th>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 1. SYNC COLOR PICKER & TEXT INPUT
            // Kalau color picker diganti, update text
            $('#color_picker').on('input', function() {
                $('#warna_background').val(this.value.toUpperCase());
            });
            // Kalau text diganti manual, update color picker
            $('#warna_background').on('input', function() {
                $('#color_picker').val(this.value);
            });

            // 2. INIT DATATABLE
            var table = $('#table-kegiatan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('perguruan_tinggi.kegiatan_akademik.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_kegiatan',
                        name: 'kode_kegiatan'
                    },
                    {
                        data: 'nama_kegiatan',
                        name: 'nama_kegiatan'
                    },
                    {
                        data: 'warna_background',
                        name: 'warna_background'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });

            // 3. FORM CONTROLS
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#kode_kegiatan').focus();
            });

            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });

            // 4. SUBMIT FORM
            $('#form-kegiatan').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('perguruan_tinggi.kegiatan_akademik.store') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status == 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                            $('#form-container').slideUp();
                            resetForm();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
                    }
                });
            });

            // 5. EDIT DATA
            $('body').on('click', '.btn_edit', function() {
                var id = $(this).data('id');
                $.get("{{ route('perguruan_tinggi.kegiatan_akademik.edit', ':id') }}"
                    .replace(':id', id),
                    function(res) {

                        if (res.status == 'success') {
                            $('#id').val(res.data.id);
                            $('#kode_kegiatan').val(res.data.kode_kegiatan);
                            $('#nama_kegiatan').val(res.data.nama_kegiatan);

                            // Set Warna
                            let warna = res.data.warna_background || '#ffffff';
                            $('#warna_background').val(warna);
                            $('#color_picker').val(warna);

                            $('#form-title').html(
                                '<i class="fas fa-edit"></i> Edit Kegiatan');
                            $('#form-container').slideDown();
                            $('html, body').animate({
                                scrollTop: $('#form-container').offset().top - 100
                            }, 'slow');
                        }
                    });
            });

            // 6. DELETE DATA
            $('body').on('click', '.btn_hapus', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('perguruan_tinggi.kegiatan_akademik.delete', ':id') }}"
                                .replace(':id', id),
                            success: function(res) {
                                res.status == 'success' ? Swal.fire(
                                    'Terhapus', res
                                    .message, 'success') : Swal.fire(
                                    'Gagal', res
                                    .message, 'error');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-kegiatan')[0].reset();
                $('#id').val('');
                $('#warna_background').val('#ffffff');
                $('#color_picker').val('#ffffff');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Kegiatan Akademik');
            }
        });
    </script>
@endsection
