@extends('system::template/admin/header')
@section('title', $title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Setting</li>
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
                            <h5 class="m-0 d-inline-block">Daftar Periode Akademik</h5>
                            <div class="float-right">
                                <button class="btn btn-success" id="btn-tambah"><i class="fas fa-plus"></i> Tambah</button>
                                <button class="btn btn-danger" id="btn-hapus-bulk"><i class="fas fa-trash"></i> Hapus</button>
                            </div>
                        </div>

                        <div class="card-body">

                            <div id="form-container" style="display: none;" class="mb-4 p-3 border rounded bg-light">
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Periode</h5>
                                <form id="form-periode">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_periode" id="kode_periode" class="form-control" placeholder="20251" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Nama Periode <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_periode" id="nama_periode" class="form-control" placeholder="2025 Ganjil" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tgl Awal Kuliah <span class="text-danger">*</span></label>
                                                <input type="date" name="tgl_awal_kuliah" id="tgl_awal_kuliah" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tgl Akhir Kuliah <span class="text-danger">*</span></label>
                                                <input type="date" name="tgl_akhir_kuliah" id="tgl_akhir_kuliah" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tgl Awal UTS</label>
                                                <input type="date" name="tgl_awal_uts" id="tgl_awal_uts" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tgl Awal UAS</label>
                                                <input type="date" name="tgl_awal_uas" id="tgl_awal_uas" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                        <button type="button" class="btn btn-secondary" id="btn-cancel">Batal</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table id="table-periode" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead class="bg-navy">
                                    <tr>
                                        <th width="3%"><input type="checkbox" id="check-all"></th>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Nama Periode</th>
                                        <th>Tgl. Awal Kuliah</th>
                                        <th>Tgl. Akhir Kuliah</th>
                                        <th>Tgl. Awal UTS</th>
                                        <th>Tgl. Awal UAS</th>
                                        <th class="text-center">Aktif?</th>
                                        <th width="12%" class="text-center">Aksi</th>
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
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            // 1. INIT DATATABLE
            var table = $('#table-periode').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('periode_akademik.index') }}",
                columns: [
                    { data: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode_periode', name: 'kode_periode' },
                    { data: 'nama_periode', name: 'nama_periode' },
                    { data: 'tgl_awal_kuliah', name: 'tgl_awal_kuliah' },
                    { data: 'tgl_akhir_kuliah', name: 'tgl_akhir_kuliah' },
                    { data: 'tgl_awal_uts', name: 'tgl_awal_uts', defaultContent: '-' },
                    { data: 'tgl_awal_uas', name: 'tgl_awal_uas', defaultContent: '-' },
                    { data: 'is_active', name: 'is_active', className: 'text-center' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center' },
                ],
                order: [[2, 'desc']] // Urut Kode Periode Descending
            });

            // 2. LOGIC SET ACTIVE (PENTING)
            window.setActive = function(id) {
                Swal.fire({
                    title: 'Aktifkan Periode ini?',
                    text: "Periode lain akan otomatis dinonaktifkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ya, Aktifkan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ url('setting/periode/set-active') }}/" + id,
                            success: function(res) {
                                if(res.status == 'success'){
                                    Swal.fire('Berhasil', res.message, 'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            }
                        });
                    }
                });
            }

            // 3. FORM ACTIONS
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#kode_periode').focus();
            });

            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });

            // 4. SUBMIT FORM
            $('#form-periode').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('periode_akademik.store') }}",
                    data: formData,
                    contentType: false, processData: false,
                    success: function(res) {
                        res.status == 'success' ? Swal.fire('Berhasil', res.message, 'success') : Swal.fire('Gagal', res.message, 'error');
                        if(res.status == 'success') { table.ajax.reload(); $('#form-container').slideUp(); resetForm(); }
                    },
                    error: function() { Swal.fire('Error', 'Terjadi kesalahan server.', 'error'); }
                });
            });

            // 5. EDIT DATA
            $('body').on('click', '.btn_edit', function() {
                var id = $(this).data('id');
                $.get("{{ route('periode_akademik.index') }}" + '/edit/' + id, function(res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#kode_periode').val(res.data.kode_periode);
                        $('#nama_periode').val(res.data.nama_periode);
                        $('#tgl_awal_kuliah').val(res.data.tgl_awal_kuliah);
                        $('#tgl_akhir_kuliah').val(res.data.tgl_akhir_kuliah);
                        $('#tgl_awal_uts').val(res.data.tgl_awal_uts);
                        $('#tgl_awal_uas').val(res.data.tgl_awal_uas);

                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Periode');
                        $('#form-container').slideDown();
                        $('html, body').animate({ scrollTop: $('#form-container').offset().top - 100 }, 'slow');
                    }
                });
            });

            // 6. DELETE DATA
            $('body').on('click', '.btn_hapus', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data ini?', icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('periode_akademik.index') }}" + '/delete/' + id,
                            success: function(res) {
                                res.status == 'success' ? Swal.fire('Terhapus', res.message, 'success') : Swal.fire('Gagal', res.message, 'error');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-periode')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Periode');
            }
        });
    </script>
@endsection
