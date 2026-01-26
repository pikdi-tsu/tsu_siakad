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
                        <li class="breadcrumb-item">Biodata</li>
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

                            {{-- FORM --}}
                            <div id="form-container" class="mb-4 p-3 border rounded bg-light" style="display:none">

                                <h5 class="text-primary mb-3" id="form-title">
                                    <i class="fas fa-plus"></i> Input Suku
                                </h5>

                                <form id="form-suku">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label>Nama Suku
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="nama_suku" id="nama_suku"
                                                    placeholder="Contoh: Jawa" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center pt-3">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="button" id="btn-cancel" class="btn btn-secondary btn-sm">
                                            Batal / Tutup Form
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <table id="table-suku" class="table table-bordered table-striped" style="width:100%">
                                    <thead style="background:#003366;color:white">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Suku</th>
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

            // ================= CSRF =================
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ================= DATATABLE =================
            let table = $('#table-suku').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('suku.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_suku',
                        name: 'nama_suku'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            // ================= TAMBAH =================
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#nama_suku').focus();
            });

            $('#btn-cancel').click(function() {
                resetForm();
                $('#form-container').slideUp();
            });

            // ================= SIMPAN =================
            $('#form-suku').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('suku.store') }}",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                            resetForm();
                            $('#form-container').slideUp();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    }
                });
            });

            // ================= EDIT =================
            $('body').on('click', '.btn_edit', function() {
                let id = $(this).data('id');

                $.get("{{ route('suku.edit', ':id') }}".replace(':id', id), function(res) {
                    if (res.status === 'success') {
                        $('#id').val(res.data.id);
                        $('#nama_suku').val(res.data.nama_suku);
                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Suku');
                        $('#form-container').slideDown();
                    }
                });
            });

            // ================= DELETE =================
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
                            url: "{{ route('suku.delete', ':id') }}".replace(':id', id),
                            success: function(res) {
                                Swal.fire('Terhapus', res.message, 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            // ================= RESET =================
            function resetForm() {
                $('#form-suku')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Suku');
            }

        });
    </script>
@endsection
