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

                            {{-- FORM --}}
                            <div id="form-container" style="display:none" class="mb-4 p-3 border rounded bg-light">

                                <h5 class="text-primary mb-3" id="form-title">
                                    <i class="fas fa-plus"></i> Input Jenis Perguruan Tinggi
                                </h5>

                                <form id="form-jenis-pt">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Jenis Perguruan Tinggi <span class="text-danger">*</span></label>
                                                <input type="text" name="jenis_pt" id="jenis_pt" class="form-control"
                                                    placeholder="Contoh: Universitas" required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="isactive" id="isactive" class="form-control">
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Nonaktif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-10 text-right">
                                            <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel">
                                                Batal
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <table id="table-jenis-pt" class="table table-bordered table-striped">
                                    <thead style="background:#003366;color:white">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Jenis Perguruan Tinggi</th>
                                            <th width="15%">Status</th>
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
        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // =====================
            // DATATABLE
            // =====================
            let table = $('#table-jenis-pt').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('jenis_pt.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_pt',
                        name: 'jenis_pt'
                    },
                    {
                        data: 'isactive',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });

            // =====================
            // TAMBAH
            // =====================
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#jenis_pt').focus();
            });

            // =====================
            // BATAL
            // =====================
            $('#btn-cancel').click(function() {
                resetForm();
                $('#form-container').slideUp();
            });

            // =====================
            // SIMPAN
            // =====================
            $('#form-jenis-pt').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('jenis_pt.store') }}",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        Swal.fire(
                            res.status === 'success' ? 'Berhasil' : 'Gagal',
                            res.message,
                            res.status
                        );

                        if (res.status === 'success') {
                            table.ajax.reload();
                            $('#form-container').slideUp();
                            resetForm();
                        }
                    }
                });
            });

            // =====================
            // EDIT
            // =====================
            $('body').on('click', '.btn_edit', function() {
                let id = $(this).data('id');

                $.get("{{ route('jenis_pt.edit', ':id') }}".replace(':id', id), function(res) {
                    if (res.status === 'success') {
                        $('#id').val(res.data.id);
                        $('#jenis_pt').val(res.data.jenis_pt);
                        $('#isactive').val(res.data.isactive);

                        $('#form-title').html(
                            '<i class="fas fa-edit"></i> Edit Jenis Perguruan Tinggi'
                        );
                        $('#form-container').slideDown();
                    }
                });
            });

            // =====================
            // HAPUS
            // =====================
            $('body').on('click', '.btn_hapus', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('jenis_pt.delete', ':id') }}".replace(':id', id),
                            success: function(res) {
                                Swal.fire(
                                    res.status === 'success' ? 'Terhapus' : 'Gagal',
                                    res.message,
                                    res.status
                                );
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-jenis-pt')[0].reset();
                $('#id').val('');
                $('#form-title').html(
                    '<i class="fas fa-plus"></i> Input Jenis Perguruan Tinggi'
                );
            }

        });
    </script>
@endsection
