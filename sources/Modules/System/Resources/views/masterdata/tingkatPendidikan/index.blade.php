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
                            @can('system:master_contactperson:create')
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
                                    <i class="fas fa-plus"></i> Input Tingkat Pendidikan
                                </h5>

                                <form id="form-tingkat-pendidikan">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Jenjang <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="jenjang" id="jenjang"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Nama Jenjang Pendidikan</label>
                                                <input type="text" class="form-control" name="nama_jenjang_pendidikan"
                                                    id="nama_jenjang_pendidikan">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Nama Jenjang Pendidikan EN</label>
                                                <input type="text" class="form-control" name="nama_jenjang_pendidikan_en"
                                                    id="nama_jenjang_pendidikan_en">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Urutan Jenjang Pendidikan</label>
                                                <input type="text" class="form-control" name="urutan_jenjang_pendidikan"
                                                    id="urutan_jenjang_pendidikan">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Perguruan Tinggi</label>
                                                <input type="text" class="form-control" name="perguruan_tinggi"
                                                    id="perguruan_tinggi">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Pasca Sarjana</label>
                                                <input type="text" class="form-control" name="pasca_sarjana"
                                                    id="pasca_sarjana">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Jenjang RPL</label>
                                                <input type="text" class="form-control" name="jenjang_rpl"
                                                    id="jenjang_rpl">
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
                                <table id="table-tingkat-pendidikan" class="table table-bordered table-striped"
                                    style="width:100%">
                                    <thead style="background:#003366;color:white">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Jenjang</th>
                                            <th>Nama Jenjang Pendidikan</th>
                                            <th>Nama Jenjang Pendidikan EN</th>
                                            <th>Urutan</th>
                                            <th>Perguruan Tinggi</th>
                                            <th>Pasca Sarjana</th>
                                            <th>Jenjang RPL</th>
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

            let table = $('#table-tingkat-pendidikan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('perguruan_tinggi.tingkat_pendidikan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenjang',
                        name: 'jenjang'
                    },
                    {
                        data: 'nama_jenjang_pendidikan',
                        name: 'nama_jenjang_pendidikan'
                    },
                    {
                        data: 'nama_jenjang_pendidikan_en',
                        name: 'nama_jenjang_pendidikan_en'
                    },
                    {
                        data: 'urutan_jenjang_pendidikan',
                        name: 'urutan_jenjang_pendidikan'
                    },
                    {
                        data: 'perguruan_tinggi',
                        name: 'perguruan_tinggi'
                    },
                    {
                        data: 'pasca_sarjana',
                        name: 'pasca_sarjana'
                    },
                    {
                        data: 'jenjang_rpl',
                        name: 'jenjang_rpl'
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
                $('#jenjang').focus();
            });
            $('#btn-cancel').click(function() {
                resetForm();
                $('#form-container').slideUp();
            });

            $('#form-tingkat-pendidikan').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('perguruan_tinggi.tingkat_pendidikan.store') }}", $(this).serialize(),
                    function(res) {
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
                $.get("{{ route('perguruan_tinggi.tingkat_pendidikan.edit', ':id') }}".replace(':id', id),
                    function(res) {
                        if (res.status === 'success') {
                            $('#id').val(res.data.id);
                            $('#jenjang').val(res.data.jenjang);
                            $('#nama_jenjang_pendidikan').val(res.data.nama_jenjang_pendidikan);
                            $('#nama_jenjang_pendidikan_en').val(res.data.nama_jenjang_pendidikan_en);
                            $('#urutan_jenjang_pendidikan').val(res.data.urutan_jenjang_pendidikan);
                            $('#perguruan_tinggi').val(res.data.perguruan_tinggi);
                            $('#pasca_sarjana').val(res.data.pasca_sarjana);
                            $('#jenjang_rpl').val(res.data.jenjang_rpl);
                            $('#form-title').html(
                                '<i class="fas fa-edit"></i> Edit Tingkat Pendidikan');
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
                            url: "{{ route('perguruan_tinggi.tingkat_pendidikan.delete', ':id') }}"
                                .replace(
                                    ':id', id),
                            success: function(res) {
                                Swal.fire('Terhapus', res.message, 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-tingkat-pendidikan')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Tingkat Pendidikan');
            }

        });
    </script>
@endsection
