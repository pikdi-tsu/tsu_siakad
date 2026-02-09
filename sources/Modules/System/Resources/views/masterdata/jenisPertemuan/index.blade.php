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
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Jenis
                                    Pertemuan</h5>
                                <form id="form-jenis">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_jenis" id="kode_jenis" class="form-control"
                                                    placeholder="K" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Jenis Pertemuan <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_jenis" id="nama_jenis" class="form-control"
                                                    placeholder="Kuliah / Praktikum" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Nama Singkat <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_singkat" id="nama_singkat"
                                                    class="form-control" placeholder="Kuliah / Prak" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kelompok (Jenis)</label>
                                                <input type="text" name="kelompok_jenis" id="kelompok_jenis"
                                                    class="form-control" placeholder="Opsional (misal: UTS)">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 pt-2">
                                            <label>Pengaturan:</label>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group clearfix">
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="is_hitung_presensi" name="is_hitung_presensi"
                                                        checked>
                                                    <label for="is_hitung_presensi" style="font-weight: normal;">Masuk
                                                        Persentase Presensi?</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group clearfix">
                                                <div class="icheck-primary d-inline">
                                                    <input type="checkbox" id="is_ujian" name="is_ujian">
                                                    <label for="is_ujian" style="font-weight: normal;">Termasuk
                                                        Ujian?</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                            Simpan</button>
                                        <button type="button" class="btn btn-secondary" id="btn-cancel">Batal</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table id="table-jenis" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead style="background-color: #003366; color: white;">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="10%">Kode</th>
                                            <th>Nama Jenis Pertemuan</th>
                                            <th>Nama Singkat</th>
                                            <th class="text-center">Masuk Persentase?</th>
                                            <th class="text-center">Termasuk Ujian?</th>
                                            <th>Jenis</th>
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

            // 1. INIT DATATABLE
            var table = $('#table-jenis').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('perkuliahan.jenis_pertemuan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_jenis',
                        name: 'kode_jenis'
                    },
                    {
                        data: 'nama_jenis',
                        name: 'nama_jenis'
                    },
                    {
                        data: 'nama_singkat',
                        name: 'nama_singkat'
                    },
                    {
                        data: 'is_hitung_presensi',
                        name: 'is_hitung_presensi',
                        className: 'text-center'
                    },
                    {
                        data: 'is_ujian',
                        name: 'is_ujian',
                        className: 'text-center'
                    },
                    {
                        data: 'kelompok_jenis',
                        name: 'kelompok_jenis'
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

            // 2. FORM ACTIONS
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#kode_jenis').focus();
            });

            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });

            // 3. SUBMIT FORM
            $('#form-jenis').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                // Fix Checkbox
                if (!$('#is_hitung_presensi').is(':checked')) formData.append('is_hitung_presensi', 0);
                if (!$('#is_ujian').is(':checked')) formData.append('is_ujian', 0);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('perkuliahan.jenis_pertemuan.store') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        res.status == 'success' ? Swal.fire('Berhasil', res.message,
                            'success') : Swal.fire('Gagal', res.message, 'error');
                        if (res.status == 'success') {
                            table.ajax.reload();
                            $('#form-container').slideUp();
                            resetForm();
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
                    }
                });
            });

            // 4. EDIT DATA
            $('body').on('click', '.btn_edit', function() {
                var id = $(this).data('id');
                $.get("{{ route('perkuliahan.jenis_pertemuan.edit', ':id') }}".replace(':id', id), function(
                    res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#kode_jenis').val(res.data.kode_jenis);
                        $('#nama_jenis').val(res.data.nama_jenis);
                        $('#nama_singkat').val(res.data.nama_singkat);
                        $('#kelompok_jenis').val(res.data.kelompok_jenis);

                        // Set Checkbox
                        $('#is_hitung_presensi').prop('checked', res.data.is_hitung_presensi);
                        $('#is_ujian').prop('checked', res.data.is_ujian);

                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Jenis Pertemuan');
                        $('#form-container').slideDown();
                        $('html, body').animate({
                            scrollTop: $('#form-container').offset().top - 100
                        }, 'slow');
                    }
                });
            });

            // 5. DELETE DATA
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
                            url: "{{ route('perkuliahan.jenis_pertemuan.delete', ':id') }}"
                                .replace(':id', id),
                            success: function(res) {
                                res.status == 'success' ? Swal.fire('Terhapus', res
                                    .message, 'success') : Swal.fire('Gagal', res
                                    .message, 'error');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-jenis')[0].reset();
                $('#id').val('');
                // Default Checkbox
                $('#is_hitung_presensi').prop('checked', true);
                $('#is_ujian').prop('checked', false);

                $('#form-title').html('<i class="fas fa-plus"></i> Input Jenis Pertemuan');
            }
        });
    </script>
@endsection
