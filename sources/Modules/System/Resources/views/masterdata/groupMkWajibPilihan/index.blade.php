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
                            @can('system:master_golonganpangkat:create')
                                <button class="btn btn-success float-right" id="btn-tambah">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            @else
                                <span class="badge badge-secondary p-2 shadow-sm" style="cursor: not-allowed; opacity: 0.7;"
                                    title="Anda tidak memiliki akses ke action ini">
                                    <i class="fas fa-lock mr-1"></i> Tambah (No Access)
                                </span>
                            @endcan
                        </div>

                        <div class="card-body">

                            <div id="form-container" class="mb-4 p-3 border rounded bg-light" style="display:none">

                                <h5 class="text-primary mb-3" id="form-title">
                                    <i class="fas fa-plus"></i> Input Group MK Wajib/Pilihan
                                </h5>

                                <form id="form-group-mk">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Group MK <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nama_group_mk"
                                                    id="nama_group_mk" required>
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
                                <table id="table-group-mk" class="table table-bordered table-striped" style="width:100%">
                                    <thead style="background:#003366;color:white">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Group MK</th>
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

            let table = $('#table-group-mk').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('perkuliahan.group_mk.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_group_mk',
                        name: 'nama_group_mk'
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
                $('#nama_group_mk').focus();
            });
            $('#btn-cancel').click(function() {
                resetForm();
                $('#form-container').slideUp();
            });

            $('#form-group-mk').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('perkuliahan.group_mk.store') }}", $(this).serialize(), function(res) {
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
                $.get("{{ route('perkuliahan.group_mk.edit', ':id') }}".replace(':id', id), function(res) {
                    if (res.status === 'success') {
                        $('#id').val(res.data.id);
                        $('#nama_group_mk').val(res.data.nama_group_mk);
                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Group MK');
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
                            url: "{{ route('perkuliahan.group_mk.delete', ':id') }}"
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
                $('#form-group-mk')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Group MK');
            }

        });
    </script>
@endsection
