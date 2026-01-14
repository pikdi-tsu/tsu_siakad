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
                        <li class="breadcrumb-item">Perguruan Tinggi</li>
                        <li class="breadcrumb-item active">{{ $menu }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body py-2"> <div class="row align-items-center">
                        <label class="col-sm-1 col-form-label text-danger">Unit</label> <div class="col-sm-4">
                            <select class="form-control select2" id="filter_unit">
                                <option value="">-- Semua --</option>
                                @foreach($list_unit as $u)
                                    <option value="{{ $u }}">{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

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
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Ruang Kuliah</h5>
                                <form id="form-ruang">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Kode Ruang <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_ruang" id="kode_ruang" class="form-control" placeholder="Contoh: B13" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Ruang <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_ruang" id="nama_ruang" class="form-control" placeholder="Contoh: Lab Komputer 1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Unit/Gedung <span class="text-danger">*</span></label>
                                                <select name="unit" id="unit" class="form-control">
                                                    @foreach($list_unit as $u)
                                                        <option value="{{ $u }}">{{ $u }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Lokasi Detail</label>
                                                <input type="text" name="lokasi" id="lokasi" class="form-control" placeholder="Lantai 1, Sayap Kiri">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Kapasitas (Org)</label>
                                                <input type="number" name="kapasitas" id="kapasitas" class="form-control" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="is_active" id="is_active" class="form-control">
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Non-Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <div class="form-group w-100">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                                <button type="button" class="btn btn-secondary ml-1" id="btn-cancel">Batal</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table id="table-ruang" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead style="background-color: #003366; color: white;">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Nama Ruang</th>
                                        <th>Unit</th>
                                        <th>Lokasi</th>
                                        <th>Kap.</th>
                                        <th class="text-center">Aktif</th>
                                        <th class="text-center" width="10%">Aksi</th>
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
            var table = $('#table-ruang').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('ruang_kuliah.index') }}",
                    data: function (d) {
                        d.filter_unit = $('#filter_unit').val(); // Kirim parameter filter
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode_ruang', name: 'kode_ruang' },
                    { data: 'nama_ruang', name: 'nama_ruang' },
                    { data: 'unit', name: 'unit' },
                    { data: 'lokasi', name: 'lokasi' },
                    { data: 'kapasitas', name: 'kapasitas' },
                    { data: 'is_active', name: 'is_active', className: 'text-center' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center' },
                ]
            });

            // 2. FILTER UNIT TRIGGER
            $('#filter_unit').change(function() {
                table.draw();
            });

            // 3. TOMBOL TAMBAH (BUKA FORM)
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
                $('#kode_ruang').focus();
            });

            // 4. TOMBOL BATAL (TUTUP FORM)
            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
                resetForm();
            });

            // 5. SUBMIT FORM
            $('#form-ruang').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('ruang_kuliah.store') }}",
                    data: formData,
                    contentType: false, processData: false,
                    success: function(res) {
                        if(res.status == 'success') {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                            $('#form-container').slideUp();
                            resetForm();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function() { Swal.fire('Error', 'Terjadi kesalahan server.', 'error'); }
                });
            });

            // 6. EDIT DATA
            $('body').on('click', '.btn_edit', function() {
                var id = $(this).data('id');
                $.get("{{ route('ruang_kuliah.index') }}" + '/edit/' + id, function(res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#kode_ruang').val(res.data.kode_ruang);
                        $('#nama_ruang').val(res.data.nama_ruang);
                        $('#unit').val(res.data.unit);
                        $('#lokasi').val(res.data.lokasi);
                        $('#kapasitas').val(res.data.kapasitas);
                        $('#is_active').val(res.data.is_active ? 1 : 0);

                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Ruang Kuliah');
                        $('#form-container').slideDown();
                        $('html, body').animate({ scrollTop: $('#form-container').offset().top - 100 }, 'slow');
                    }
                });
            });

            // 7. HAPUS DATA
            $('body').on('click', '.btn_hapus', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data ini?', icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('ruang_kuliah.index') }}" + '/delete/' + id,
                            success: function(res) {
                                res.status == 'success' ? Swal.fire('Terhapus', res.message, 'success') : Swal.fire('Gagal', res.message, 'error');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-ruang')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Ruang Kuliah');
            }
        });
    </script>
@endsection
