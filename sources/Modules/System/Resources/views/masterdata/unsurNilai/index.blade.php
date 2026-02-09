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
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Unsur Nilai
                                </h5>
                                <form id="form-unsur">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_unsur" id="kode_unsur" class="form-control"
                                                    placeholder="Contoh: 1 / UTS" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Unsur <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_unsur" id="nama_unsur" class="form-control"
                                                    placeholder="Contoh: UJIAN TENGAH SEMESTER" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Nama Singkat <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_singkat" id="nama_singkat"
                                                    class="form-control" placeholder="Contoh: UTS" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Kelompok Unsur</label>
                                                <input type="text" name="kelompok_unsur" id="kelompok_unsur"
                                                    class="form-control" placeholder="Opsional">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Metode Evaluasi</label>
                                                <input type="text" name="metode_evaluasi" id="metode_evaluasi"
                                                    class="form-control" placeholder="Contoh: Kognitif/Pengetahuan">
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
                                <table id="table-unsur" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead style="background-color: #003366; color: white;">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="10%">Kode</th>
                                            <th>Nama Unsur</th>
                                            <th>Nama Singkat</th>
                                            <th>Kelompok Unsur</th>
                                            <th>Metode Evaluasi</th>
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
            var table = $('#table-unsur').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('perkuliahan.unsur_nilai.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_unsur',
                        name: 'kode_unsur'
                    },
                    {
                        data: 'nama_unsur',
                        name: 'nama_unsur'
                    },
                    {
                        data: 'nama_singkat',
                        name: 'nama_singkat'
                    },
                    {
                        data: 'kelompok_unsur',
                        name: 'kelompok_unsur'
                    },
                    {
                        data: 'metode_evaluasi',
                        name: 'metode_evaluasi'
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
                $('#kode_unsur').focus();
            });

            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });

            // 3. SUBMIT FORM
            $('#form-unsur').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('perkuliahan.unsur_nilai.store') }}",
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
                $.get("{{ route('perkuliahan.unsur_nilai.index') }}" + '/edit/' + id, function(res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#kode_unsur').val(res.data.kode_unsur);
                        $('#nama_unsur').val(res.data.nama_unsur);
                        $('#nama_singkat').val(res.data.nama_singkat);
                        $('#kelompok_unsur').val(res.data.kelompok_unsur);
                        $('#metode_evaluasi').val(res.data.metode_evaluasi);

                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Unsur Nilai');
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
                            url: "{{ route('perkuliahan.unsur_nilai.index') }}" +
                                '/delete/' + id,
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
                $('#form-unsur')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Unsur Nilai');
            }
        });
    </script>
@endsection
