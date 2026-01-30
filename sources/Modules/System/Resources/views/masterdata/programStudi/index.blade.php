@extends('system::template/admin/header')
@section('title', $title)

@section('content')

    {{-- HEADER --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $menu }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Data Pelengkap</li>
                        <li class="breadcrumb-item active">{{ $menu }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card card-primary card-outline">

                        {{-- CARD HEADER --}}
                        <div class="card-header">
                            <h5 class="m-0 d-inline-block">Daftar {{ $menu }}</h5>
                            <button class="btn btn-success float-right" id="btn-tambah">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>

                        {{-- CARD BODY --}}
                        <div class="card-body">

                            {{-- FORM CREATE / EDIT --}}
                            <div id="form-container" class="mb-4 p-3 border rounded bg-light" style="display:none">

                                <h5 class="text-primary mb-3" id="form-title">
                                    <i class="fas fa-plus"></i> Input Program Studi
                                </h5>

                                <form id="form-prodi">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kode Prodi <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="kode_prodi" id="kode_prodi"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Prodi <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nama_prodi" id="nama_prodi"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Ketua Prodi</label>
                                                <input type="text" class="form-control" name="ketua_prodi"
                                                    id="ketua_prodi">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fakultas ID</label>
                                                <input type="text" class="form-control" name="fakultas_id"
                                                    id="fakultas_id">
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
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                        <button type="button" id="btn-cancel" class="btn btn-secondary">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <table id="table-prodi" class="table table-bordered table-striped" width="100%">
                                    <thead style="background:#003366;color:white">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Kode Prodi</th>
                                            <th>Nama Prodi</th>
                                            <th>Ketua Prodi</th>
                                            <th>Fakultas ID</th>
                                            <th>Status</th>
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

            let table = $('#table-prodi').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('program_studi.index') }}",
                order: [
                    [2, 'asc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_prodi',
                        name: 'kode_prodi'
                    },
                    {
                        data: 'nama_prodi',
                        name: 'nama_prodi'
                    },
                    {
                        data: 'ketua_prodi',
                        name: 'ketua_prodi'
                    },
                    {
                        data: 'fakultas_id',
                        name: 'fakultas_id'
                    },
                    {
                        data: 'status_prodi',
                        name: 'status_prodi',
                        render: function(data) {
                            let map = {
                                'Aktif': 'success',
                                'Tidak Aktif': 'warning',
                                'Tutup': 'danger'
                            };
                            return `<span class="badge badge-${map[data] ?? 'secondary'}">${data}</span>`;
                        }
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
                $('#kode_prodi').focus();
            });

            $('#btn-cancel').click(function() {
                resetForm();
                $('#form-container').slideUp();
            });

            $('#form-prodi').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('program_studi.store') }}", $(this).serialize(), function(res) {
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
                $.get("{{ route('program_studi.edit', ':id') }}".replace(':id', id), function(res) {
                    if (res.status === 'success') {
                        $('#id').val(res.data.id);
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
                Swal.fire({
                    title: 'Hapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                }).then(r => {
                    if (r.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('program_studi.delete', ':id') }}".replace(':id',
                                id),
                            success: function(res) {
                                Swal.fire('Terhapus', res.message, 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-prodi')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Program Studi');
            }

        });
    </script>
@endsection
