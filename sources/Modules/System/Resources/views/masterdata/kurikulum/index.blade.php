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
                        <li class="breadcrumb-item">Master Data</li>
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
                            <div id="form-container" style="display:none;" class="mb-4 p-3 border rounded bg-light">
                                <h5 class="text-primary mb-3" id="form-title">
                                    <i class="fas fa-plus"></i> Input Kurikulum
                                </h5>

                                <form id="form-kurikulum">
                                    @csrf
                                    <input type="hidden" name="id" id="id">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Kurikulum <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_kurikulum" id="nama_kurikulum"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Status <span class="text-danger">*</span></label>
                                                <select name="isactive" id="isactive" class="form-control">
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Nonaktif</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 d-flex align-items-end">
                                            <button class="btn btn-primary btn-block">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Deskripsi</label>
                                                <textarea name="deskripsi" id="deskripsi" rows="2" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel">
                                            Tutup Form
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <table id="table-kurikulum" class="table table-bordered table-striped w-100">
                                    <thead style="background:#003366;color:white;">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nama Kurikulum</th>
                                            <th width="25%">Deskripsi</th>
                                            <th width="10%">Status</th>
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

            // DATATABLE
            var table = $('#table-kurikulum').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('perkuliahan.kurikulum.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_kurikulum'
                    },
                    {
                        data: 'deskripsi'
                    },
                    {
                        data: 'isactive',
                        className: 'text-center'
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


            // SHOW FORM
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#nama_kurikulum').focus();
            });

            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });


            // SUBMIT
            $('#form-kurikulum').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('perkuliahan.kurikulum.store') }}",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status == 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                            $('#form-container').slideUp();
                            resetForm();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Server error', 'error');
                    }
                });
            });


            // EDIT
            $('body').on('click', '.btn_edit', function() {
                var id = $(this).data('id');

                $.get("{{ route('perkuliahan.kurikulum.edit', ':id') }}".replace(':id', id),
                    function(res) {
                        if (res.status == 'success') {
                            $('#id').val(res.data.id);
                            $('#nama_kurikulum').val(res.data.nama_kurikulum);
                            $('#deskripsi').val(res.data.deskripsi);
                            $('#isactive').val(res.data.isactive);

                            $('#form-title').html(
                                '<i class="fas fa-edit"></i> Edit Kurikulum');
                            $('#form-container').slideDown();

                            $('html,body').animate({
                                scrollTop: $('#form-container').offset().top - 100
                            }, 'slow');
                        }
                    });
            });


            // DELETE
            $('body').on('click', '.btn_hapus', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('perkuliahan.kurikulum.delete', ':id') }}"
                                .replace(':id', id),
                            success: function(res) {
                                if (res.status == 'success') {
                                    Swal.fire('Terhapus', res.message,
                                        'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Gagal', res.message,
                                        'error');
                                }
                            }
                        });
                    }
                });
            });


            function resetForm() {
                $('#form-kurikulum')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Kurikulum');
            }

        });
    </script>
@endsection
