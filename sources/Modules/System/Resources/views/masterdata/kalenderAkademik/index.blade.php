@extends('system::template/admin/header')
@section('title', $title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $menu }} <small class="text-muted" style="font-size: 14px;">Daftar Kegiatan</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $menu }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="card card-outline card-warning">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-orange">Periode Akademik</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2" id="filter_periode">
                                        <option value="">-- Semua Periode --</option>
                                        @foreach($list_periode as $key => $val)
                                            <option value="{{ $key }}" {{ $key == '20251' ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-orange">Libur Akademik</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="filter_libur_akademik">
                                        <label for="filter_libur_akademik"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-orange">Jenis Kegiatan</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2" id="filter_kegiatan">
                                        <option value="">-- Semua Kegiatan --</option>
                                        @foreach($list_kegiatan as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kegiatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-orange">Libur Nasional</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="filter_libur_nasional">
                                        <label for="filter_libur_nasional"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0 d-inline-block">Daftar Kalender</h5>
                            <button class="btn btn-success float-right" id="btn-tambah">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>

                        <div class="card-body">
                            <div id="form-container" style="display: none;" class="mb-4 p-3 border rounded bg-light">
                                <h5 class="text-primary mb-3" id="form-title"><i class="fas fa-edit"></i> Input Jadwal</h5>
                                <form id="form-kalender">
                                    @csrf
                                    <input type="hidden" id="id" name="id">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Periode <span class="text-danger">*</span></label>
                                                <select name="id_periode" id="id_periode" class="form-control" required>
                                                    @foreach($list_periode as $key => $val)
                                                        <option value="{{ $key }}">{{ $val }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Jenis Kegiatan <span class="text-danger">*</span></label>
                                                <select name="id_kegiatan" id="id_kegiatan" class="form-control" required>
                                                    <option value="">Pilih...</option>
                                                    @foreach($list_kegiatan as $k)
                                                        <option value="{{ $k->id }}">{{ $k->nama_kegiatan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tgl Mulai <span class="text-danger">*</span></label>
                                                <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tgl Selesai <span class="text-danger">*</span></label>
                                                <input type="date" name="tgl_selesai" id="tgl_selesai" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Keterangan <span class="text-danger">*</span></label>
                                                <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Contoh: Upacara Bendera" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pt-4">
                                            <div class="form-group clearfix">
                                                <div class="icheck-danger d-inline mr-3">
                                                    <input type="checkbox" id="is_libur_nasional" name="is_libur_nasional">
                                                    <label for="is_libur_nasional">Libur Nasional</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pt-4">
                                            <div class="form-group clearfix">
                                                <div class="icheck-warning d-inline">
                                                    <input type="checkbox" id="is_libur_akademik" name="is_libur_akademik">
                                                    <label for="is_libur_akademik">Libur Akademik</label>
                                                </div>
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
                                <table id="table-kalender" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead style="background-color: #003366; color: white;">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Tgl Mulai</th>
                                        <th>Tgl Selesai</th>
                                        <th>Jenis Kegiatan</th>
                                        <th>Keterangan</th>
                                        <th>Libur</th>
                                        <th width="10%">Aksi</th>
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
            var table = $('#table-kalender').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kalender_akademik.index') }}",
                    data: function (d) {
                        d.periode = $('#filter_periode').val();
                        d.kegiatan = $('#filter_kegiatan').val();
                        d.libur_nasional = $('#filter_libur_nasional').is(':checked');
                        d.libur_akademik = $('#filter_libur_akademik').is(':checked');
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tgl_mulai', name: 'tgl_mulai' },
                    { data: 'tgl_selesai', name: 'tgl_selesai' },
                    { data: 'nama_kegiatan', name: 'kegiatan.nama_kegiatan' }, // Relasi search
                    { data: 'keterangan', name: 'keterangan' },
                    { data: 'status_libur', name: 'is_libur_nasional' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center' },
                ],
                order: [[1, 'asc']]
            });

            // 2. TRIGGER FILTERS
            $('#filter_periode, #filter_kegiatan, #filter_libur_nasional, #filter_libur_akademik').change(function() {
                table.draw();
            });

            // 3. FORM ACTIONS
            $('#btn-tambah').click(function() {
                resetForm();
                $('#form-container').slideDown();
            });
            $('#btn-cancel').click(function() {
                $('#form-container').slideUp();
            });

            // 4. SUBMIT FORM
            $('#form-kalender').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                // Fix Checkbox not sending '0' if unchecked
                if(!$('#is_libur_nasional').is(':checked')) formData.append('is_libur_nasional', 0);
                if(!$('#is_libur_akademik').is(':checked')) formData.append('is_libur_akademik', 0);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('kalender_akademik.store') }}",
                    data: formData,
                    contentType: false, processData: false,
                    success: function(res) {
                        res.status == 'success' ? Swal.fire('Berhasil', res.message, 'success') : Swal.fire('Gagal', res.message, 'error');
                        if(res.status == 'success') { table.ajax.reload(); $('#form-container').slideUp(); resetForm(); }
                    }
                });
            });

            // 5. EDIT DATA
            $('body').on('click', '.btn_edit', function() {
                var id = $(this).data('id');
                $.get("{{ route('kalender_akademik.index') }}" + '/edit/' + id, function(res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#id_periode').val(res.data.id_periode);
                        $('#id_kegiatan').val(res.data.id_kegiatan);

                        // Format Tanggal: YYYY-MM-DD
                        $('#tgl_mulai').val(res.data.tgl_mulai.substring(0, 10));
                        $('#tgl_selesai').val(res.data.tgl_selesai.substring(0, 10));

                        $('#keterangan').val(res.data.keterangan);

                        // Set Checkbox
                        $('#is_libur_nasional').prop('checked', res.data.is_libur_nasional);
                        $('#is_libur_akademik').prop('checked', res.data.is_libur_akademik);

                        $('#form-title').html('<i class="fas fa-edit"></i> Edit Jadwal');
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
                            url: "{{ route('kalender_akademik.index') }}" + '/delete/' + id,
                            success: function(res) {
                                res.status == 'success' ? Swal.fire('Terhapus', res.message, 'success') : Swal.fire('Gagal', res.message, 'error');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function resetForm() {
                $('#form-kalender')[0].reset();
                $('#id').val('');
                $('#form-title').html('<i class="fas fa-plus"></i> Input Jadwal');
            }
        });
    </script>
@endsection
