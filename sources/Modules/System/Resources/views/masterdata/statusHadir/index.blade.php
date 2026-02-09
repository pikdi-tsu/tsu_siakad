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
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Status Hadir
                                </h5>
                                <form id="form-status">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_status" id="kode_status"
                                                    class="form-control" placeholder="H / A" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Status Hadir <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_status" id="nama_status"
                                                    class="form-control" placeholder="Hadir / Alfa" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-2">
                                            <label>Pengaturan:</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group clearfix">
                                                        <div class="icheck-success d-inline">
                                                            <input type="checkbox" id="is_hitung_hadir"
                                                                name="is_hitung_hadir">
                                                            <label for="is_hitung_hadir"
                                                                style="font-weight: normal;">Terhitung Hadir?</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group clearfix">
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" id="is_untuk_dosen" name="is_untuk_dosen"
                                                                checked>
                                                            <label for="is_untuk_dosen" style="font-weight: normal;">Berlaku
                                                                Dosen?</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group clearfix">
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" id="is_untuk_mahasiswa"
                                                                name="is_untuk_mahasiswa" checked>
                                                            <label for="is_untuk_mahasiswa"
                                                                style="font-weight: normal;">Berlaku Mhs?</label>
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
                                            <th>Nama Status Hadir</th>
                                            <th class="text-center">Terhitung Hadir?</th>
                                            <th class="text-center">Berlaku Dosen?</th>
                                            <th class="text-center">Berlaku Mhs?</th>
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
                ajax: "{{ route('perkuliahan.status_hadir.index') }}",
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
                        data: 'is_hitung_hadir',
                        name: 'is_hitung_hadir',
                        className: 'text-center'
                    },
                    {
                        data: 'is_untuk_dosen',
                        name: 'is_untuk_dosen',
                        className: 'text-center'
                    },
                    {
                        data: 'is_untuk_mahasiswa',
                        name: 'is_untuk_mahasiswa',
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

                // Fix Checkbox: Pastikan terkirim 0 jika uncheck
                if (!$('#is_hitung_hadir').is(':checked')) formData.append('is_hitung_hadir', 0);
                if (!$('#is_untuk_dosen').is(':checked')) formData.append('is_untuk_dosen', 0);
                if (!$('#is_untuk_mahasiswa').is(':checked')) formData.append('is_untuk_mahasiswa', 0);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('perkuliahan.status_hadir.store') }}",
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
                $.get("{{ route('perkuliahan.status_hadir.edit') }}" + '/' + id, function(res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#kode_status').val(res.data.kode_status);
                        $('#nama_status').val(res.data.nama_status);

                        // Set Checkbox
                        $('#is_hitung_hadir').prop('checked', res.data.is_hitung_hadir);
                        $('#is_untuk_dosen').prop('checked', res.data.is_untuk_dosen);
                        $('#is_untuk_mahasiswa').prop('checked', res.data.is_untuk_mahasiswa);

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
                            url: "{{ route('perkuliahan.status_hadir.delete') }}" + '/' +
                                id,
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
                // Reset checkbox ke default
                $('#is_hitung_hadir').prop('checked', false);
                $('#is_untuk_dosen').prop('checked', true);
                $('#is_untuk_mahasiswa').prop('checked', true);

                $('#form-title').html('<i class="fas fa-plus"></i> Input Status Hadir');
            }
        });
    </script>
@endsection
