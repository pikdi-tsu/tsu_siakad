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
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
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

                            {{-- FORM --}}
                            <div id="form-container" class="mb-4 p-3 border rounded bg-light" style="display:none">

                                <h5 class="text-primary mb-3" id="form-title">
                                    <i class="fas fa-plus"></i> Input Tingkat Pendidikan Universitas
                                </h5>

                                <form id="form-tp-universitas">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Jenjang <span class="text-danger">*</span></label>
                                                <select class="form-control" name="jenjang" id="jenjang" required>
                                                    <option value="">-- Pilih Jenjang --</option>
                                                    <option value="D3 - Diploma 3">D3 - Diploma 3</option>
                                                    <option value="D4 - Diploma 4">D4 - Diploma 4</option>
                                                    <option value="S1 - Strata 1">S1 - Strata 1</option>
                                                    <option value="S2 - Strata 2">S2 - Strata 2</option>
                                                    <option value="S3 - Strata 3">S3 - Strata 3</option>
                                                    <option value="Profesi">Profesi</option>
                                                    <option value="Spesialis">Spesialis</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Masa Studi <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="masa_studi" id="masa_studi"
                                                    placeholder="Dalam semester (contoh: 4)" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Max Cuti</label>
                                                <input type="number" class="form-control" name="max_cuti" id="max_cuti"
                                                    placeholder="Dalam semester (contoh: 1)">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Max Studi</label>
                                                <input type="number" class="form-control" name="max_studi" id="max_studi"
                                                    placeholder="Dalam semester (contoh: 7)">
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
                            {{-- END FORM --}}

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <table id="table-tp-universitas" class="table table-bordered table-striped"
                                    style="width:100%">
                                    <thead style="background:#003366;color:white">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Jenjang</th>
                                            <th>Masa Studi (smt)</th>
                                            <th>Max Cuti (smt)</th>
                                            <th>Max Studi (smt)</th>
                                            <th width="15%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            {{-- END TABLE --}}

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

            let table = $('#table-tp-universitas').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('perguruan_tinggi.tingkat_pendidikan_univ.index') }}",
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
                        data: 'masa_studi',
                        name: 'masa_studi'
                    },
                    {
                        data: 'max_cuti',
                        name: 'max_cuti'
                    },
                    {
                        data: 'max_studi',
                        name: 'max_studi'
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

            $('#form-tp-universitas').submit(function(e) {
                e.preventDefault();

                $.post("{{ route('perguruan_tinggi.tingkat_pendidikan_univ.store') }}", $(this).serialize(),
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

                $.get("{{ route('perguruan_tinggi.tingkat_pendidikan_univ.edit', ':id') }}".replace(':id',
                    id), function(
                    res) {
                    if (res.status === 'success') {
                        $('#id').val(res.data.id);
                        $('#jenjang').val(res.data.jenjang);
                        $('#masa_studi').val(res.data.masa_studi);
                        $('#max_cuti').val(res.data.max_cuti);
                        $('#max_studi').val(res.data.max_studi);

                        $('#form-title').html(
                            '<i class="fas fa-edit"></i> Edit Tingkat Pendidikan Universitas');
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
                            url: "{{ route('perguruan_tinggi.tingkat_pendidikan_univ.delete', ':id') }}"
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
                $('#form-tp-universitas')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Tingkat Pendidikan Universitas');
            }

        });
    </script>
@endsection
