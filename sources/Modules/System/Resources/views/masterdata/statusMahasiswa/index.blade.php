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
                        <li class="breadcrumb-item">Mahasiswa</li>
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
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Status
                                    Mahasiswa</h5>
                                <form id="form-status">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_status" id="kode_status"
                                                    class="form-control" placeholder="A" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Status Mahasiswa <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_status" id="nama_status"
                                                    class="form-control" placeholder="Aktif / Cuti" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-2">
                                            <label>Pengaturan:</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group clearfix">
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" id="is_pengajuan_mhs"
                                                                name="is_pengajuan_mhs">
                                                            <label for="is_pengajuan_mhs"
                                                                style="font-weight: normal;">Diajukan Mhs?</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group clearfix">
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" id="is_aktif" name="is_aktif" checked>
                                                            <label for="is_aktif" style="font-weight: normal;">Status
                                                                Aktif?</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group clearfix">
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" id="is_sks" name="is_sks" checked>
                                                            <label for="is_sks" style="font-weight: normal;">Bisa
                                                                Kuliah?</label>
                                                        </div>
                                                    </div>
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
                                <table id="table-status" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead style="background-color: #003366; color: white;">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="10%">Kode</th>
                                            <th>Nama Status Mahasiswa</th>
                                            <th class="text-center">Diajukan Mahasiswa</th>
                                            <th class="text-center">Aktif</th>
                                            <th class="text-center">Kuliah</th>
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
            var table = $('#table-status').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('mahasiswa.status_mahasiswa.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_status',
                        name: 'kode_status'
                    },
                    {
                        data: 'nama_status',
                        name: 'nama_status'
                    },
                    {
                        data: 'is_pengajuan_mhs',
                        name: 'is_pengajuan_mhs',
                        className: 'text-center'
                    },
                    {
                        data: 'is_aktif',
                        name: 'is_aktif',
                        className: 'text-center'
                    },
                    {
                        data: 'is_sks',
                        name: 'is_sks',
                        className: 'text-center'
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
                $('#kode_status').focus();
            });

            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });

            // 3. SUBMIT FORM
            $('#form-status').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                // Fix Checkbox
                if (!$('#is_pengajuan_mhs').is(':checked')) formData.append('is_pengajuan_mhs', 0);
                if (!$('#is_aktif').is(':checked')) formData.append('is_aktif', 0);
                if (!$('#is_sks').is(':checked')) formData.append('is_sks', 0);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('mahasiswa.status_mahasiswa.store') }}",
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
                $.get("{{ route('mahasiswa.status_mahasiswa.edit', ':id') }}".replace(':id', id), function(
                    res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#kode_status').val(res.data.kode_status);
                        $('#nama_status').val(res.data.nama_status);

                        // Set Checkbox
                        $('#is_pengajuan_mhs').prop('checked', res.data.is_pengajuan_mhs);
                        $('#is_aktif').prop('checked', res.data.is_aktif);
                        $('#is_sks').prop('checked', res.data.is_sks);

                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Status');
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
                            url: "{{ route('mahasiswa.status_mahasiswa.delete', ':id') }}"
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
                $('#form-status')[0].reset();
                $('#id').val('');
                // Default Checkbox
                $('#is_pengajuan_mhs').prop('checked', false);
                $('#is_aktif').prop('checked', false);
                $('#is_sks').prop('checked', false);

                $('#form-title').html('<i class="fas fa-plus"></i> Input Status Mahasiswa');
            }
        });
    </script>
@endsection
