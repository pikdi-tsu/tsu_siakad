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
                                                    maxlength="10" placeholder="Contoh: S1, S2, D3, Prof" required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Nama Jenjang Pendidikan <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nama_jenjang_pendidikan"
                                                    id="nama_jenjang_pendidikan" maxlength="100"
                                                    placeholder="Contoh: Strata 1" required>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Nama Jenjang Pendidikan (EN)</label>
                                                <input type="text" class="form-control" name="nama_jenjang_pendidikan_en"
                                                    id="nama_jenjang_pendidikan_en" maxlength="100"
                                                    placeholder="Example: Bachelor Degree">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Urutan Jenjang <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="urutan_jenjang_pendidikan"
                                                    id="urutan_jenjang_pendidikan" min="1" max="99"
                                                    placeholder="Urutan sorting (misal: 1)" required>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Perguruan Tinggi <span class="text-danger">*</span></label>
                                                <select class="form-control" name="perguruan_tinggi" id="perguruan_tinggi"
                                                    required>
                                                    <option value="" disabled selected>-- Pilih --</option>
                                                    <option value="1">Ya</option>
                                                    <option value="0">Tidak</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Pasca Sarjana <span class="text-danger">*</span></label>
                                                <select class="form-control" name="pasca_sarjana" id="pasca_sarjana"
                                                    required>
                                                    <option value="" disabled selected>-- Pilih --</option>
                                                    <option value="1">Ya</option>
                                                    <option value="0">Tidak</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Jenjang RPL <span class="text-danger">*</span></label>
                                                <select class="form-control" name="jenjang_rpl" id="jenjang_rpl" required>
                                                    <option value="" disabled selected>-- Pilih --</option>
                                                    <option value="1">Ya</option>
                                                    <option value="0">Tidak</option>
                                                </select>
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
                                <table id="table-tingkat-pendidikan" class="table table-bordered table-striped">
                                    <thead style="background:#003366;color:white">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Jenjang</th>
                                            <th>Nama</th>
                                            <th>EN</th>
                                            <th>Urutan</th>
                                            <th>Perguruan Tinggi</th>
                                            <th>Pasca Sarjana</th>
                                            <th>RPL</th>
                                            <th width="15%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
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

            // Auto uppercase jenjang
            $('#jenjang').on('input', function() {
                this.value = this.value.toUpperCase();
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
                        data: 'jenjang'
                    },
                    {
                        data: 'nama_jenjang_pendidikan'
                    },
                    {
                        data: 'nama_jenjang_pendidikan_en'
                    },
                    {
                        data: 'urutan_jenjang_pendidikan'
                    },
                    {
                        data: 'perguruan_tinggi',
                        render: data => data == 1 ?
                            '<span class="badge badge-success">Ya</span>' :
                            '<span class="badge badge-secondary">Tidak</span>'
                    },
                    {
                        data: 'pasca_sarjana',
                        render: data => data == 1 ?
                            '<span class="badge badge-info">Ya</span>' :
                            '<span class="badge badge-secondary">Tidak</span>'
                    },
                    {
                        data: 'jenjang_rpl',
                        render: data => data == 1 ?
                            '<span class="badge badge-warning">Ya</span>' :
                            '<span class="badge badge-secondary">Tidak</span>'
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

                $.post("{{ route('perguruan_tinggi.tingkat_pendidikan.store') }}",
                    $(this).serialize(),
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
                $.get("{{ route('perguruan_tinggi.tingkat_pendidikan.edit', ':id') }}"
                    .replace(':id', id),
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
                $('#form-tingkat-pendidikan')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Tingkat Pendidikan');
            }

        });
    </script>
@endsection
