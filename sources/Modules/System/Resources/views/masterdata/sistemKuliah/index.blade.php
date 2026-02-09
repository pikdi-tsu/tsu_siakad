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
                        <li class="breadcrumb-item">Perguruan Tinggi</li>
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
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Sistem
                                    Kuliah</h5>
                                <form id="form-waktu-kuliah">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Kode (ID)</label>
                                                <input type="text" class="form-control" placeholder="Auto" readonly>
                                                <small class="text-muted">Kode otomatis dari sistem</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Sistem Kuliah (Waktu) <span class="text-danger">*</span></label>
                                                <input type="text" name="waktu" id="waktu" class="form-control"
                                                    placeholder="Contoh: PAGI / SORE / KARYAWAN" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Status Aktif</label>
                                                <select name="isactive" id="isactive" class="form-control">
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Tidak Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i>
                                            Simpan</button>
                                        <button type="button" class="btn btn-secondary" id="btn-cancel">Batal</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table id="table-waktu-kuliah" class="table table-bordered table-striped"
                                    style="width: 100%;">
                                    <thead style="background-color: #003366; color: white;">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="10%">Kode</th>
                                            <th>Nama Sistem Kuliah</th>
                                            <th width="15%" class="text-center">Status</th>
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
            // Setup CSRF Token untuk semua request AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 1. INIT DATATABLE
            var table = $('#table-waktu-kuliah').DataTable({
                processing: true,
                serverSide: true,
                // Pastikan route ini sesuai dengan Controller yang Anda punya
                ajax: "{{ route('perguruan_tinggi.sistem_kuliah.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
                    }, // Kode diambil dari ID Auto Increment
                    {
                        data: 'waktu',
                        name: 'waktu'
                    }, // Sesuai nama kolom DB: 'waktu'
                    {
                        data: 'isactive',
                        name: 'isactive',
                        className: 'text-center', // Sesuai kolom DB: 'isactive'
                        render: function(data) {
                            return data == '1' ?
                                '<span class="badge badge-success">Aktif</span>' :
                                '<span class="badge badge-danger">Non-Aktif</span>';
                        }
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

            // 2. TOMBOL TAMBAH (Reset Form & Tampilkan)
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#waktu').focus();
            });

            // 3. TOMBOL BATAL (Sembunyikan Form)
            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });

            // 4. SUBMIT FORM (SIMPAN DATA)
            $('#form-waktu-kuliah').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    // Pastikan route store sesuai
                    url: "{{ route('perguruan_tinggi.sistem_kuliah.store') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status == 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload(); // Refresh Tabel
                            $('#form-container').slideUp(); // Tutup Form
                            resetForm();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            });

            // 5. EDIT DATA (AMBIL DATA KE FORM)
            $('body').on('click', '.btn_edit', function() {
                var id = $(this).data('id');
                // Pastikan route edit sesuai
                $.get("{{ route('perguruan_tinggi.sistem_kuliah.edit') }}" + '/' + id, function(res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#waktu').val(res.data.waktu); // Isi input waktu
                        $('#isactive').val(res.data.isactive); // Isi select isactive

                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Sistem Kuliah');
                        $('#form-container').slideDown();
                        $('html, body').animate({
                            scrollTop: $('#form-container').offset().top - 100
                        }, 'slow');
                    }
                });
            });

            // 6. DELETE DATA
            $('body').on('click', '.btn_hapus', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data ini?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            // Pastikan route delete sesuai
                            url: "{{ route('perguruan_tinggi.sistem_kuliah.index') }}" +
                                '/delete/' + id,
                            success: function(res) {
                                if (res.status == 'success') {
                                    Swal.fire('Terhapus', res.message, 'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            }
                        });
                    }
                });
            });

            // Fungsi Reset Form
            function resetForm() {
                $('#form-waktu-kuliah')[0].reset();
                $('#id').val('');
                $('#isactive').val('1');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Sistem Kuliah');
            }
        });
    </script>
@endsection
