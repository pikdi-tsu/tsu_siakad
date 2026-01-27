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
                        <li class="breadcrumb-item">Biodata</li>
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

                            <div id="form-container" class="mb-4 p-3 border rounded bg-light" style="display:none">

                                <h5 class="text-primary mb-3" id="form-title">
                                    <i class="fas fa-plus"></i> Input Kelompok Perkuliahan
                                </h5>

                                <form id="form-kelompok-perkuliahan">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Kelompok Perkuliahan <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nama_kelompok_perkuliahan"
                                                    id="nama_kelompok_perkuliahan" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Urutan</label>
                                                <input type="text" class="form-control" name="urutan" id="urutan">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                        <button type="button" id="btn-cancel" class="btn btn-secondary btn-sm">
                                            Batal / Tutup Form
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table id="table-kelompok-perkuliahan" class="table table-bordered table-striped"
                                    style="width:100%">
                                    <thead style="background:#003366;color:white">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Kelompok Perkuliahan</th>
                                            <th>Urutan</th>
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let table = $('#table-kelompok-perkuliahan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kelompok_perkuliahan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_kelompok_perkuliahan',
                        name: 'nama_kelompok_perkuliahan'
                    },
                    {
                        data: 'urutan',
                        name: 'urutan'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#nama_kelompok_perkuliahan').focus();
            });
            $('#btn-cancel').click(function() {
                resetForm();
                $('#form-container').slideUp();
            });

            $('#form-kelompok-perkuliahan').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('kelompok_perkuliahan.store') }}", $(this).serialize(), function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Berhasil', res.message, 'success');
                        table.ajax.reload();
                        resetForm();
                        $('#form-container').slideUp();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                });
            });

            $('body').on('click', '.btn_edit', function() {
                let id = $(this).data('id');
                $.get("{{ route('kelompok_perkuliahan.edit', ':id') }}".replace(':id', id), function(res) {
                    if (res.status === 'success') {
                        $('#id').val(res.data.id);
                        $('#nama_kelompok_perkuliahan').val(res.data.nama_kelompok_perkuliahan);
                        $('#urutan').val(res.data.urutan);
                        $('#form-title').html(
                            '<i class="fas fa-edit"></i> Edit Kelompok Perkuliahan');
                        $('#form-container').slideDown();
                    }
                });
            });

            $('body').on('click', '.btn_hapus', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('kelompok_perkuliahan.delete', ':id') }}"
                                .replace(':id', id),
                            success: function(res) {
                                Swal.fire('Terhapus', res.message, 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-kelompok-perkuliahan')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Kelompok Perkuliahan');
            }

        });
    </script>
@endsection
