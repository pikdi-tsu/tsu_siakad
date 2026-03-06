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
                                <button class="btn btn-info" id="btn-tambah"><i class="fas fa-plus"></i> Tambah</button>
                                <button id="btn-batal" class="btn btn-warning float-right" style="display: none;"><i class="fas fa-undo"></i>Batal</button>
                            </div>
                        </div>

                        <div class="card-body">

                            <div id="form-container" style="display: none;">
                                <h5 class="mb-3" id="form-title"><i class="fas fa-pencil"></i> Input Periode</h5>
                                <form id="form-periode" action="#" method="POST">
                                    @csrf
                                    <input type="hidden" id="IdPeriode" name="IdPeriode">
                                    <table class="table">
                                        <tr>
                                            <th class="text-info" width="25%">Kode Periode</th>
                                            <th width="25%">
                                                <input type="text" name="kode_periode" id="kode_periode" class="form-control" placeholder="Otomatis Generate" readonly>
                                            </th>
                                            <th width="25%" class="text-info">Tanggal Akhir UTS</th>
                                            <th width="25%">
                                                <input type="date" name="tglakhir_uts" id="tglakhir_uts" class="form-control">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-info">Tahun Ajaran<code>*</code></th>
                                            <th>
                                                <select class="form-control select2" id="tahun_ajaran" name="tahun_ajaran" width="100%">
                                                    <option value="" selected disabled>-- Pilih Tahun Ajaran --</option>
                                                    @foreach ($ta as $p)
                                                        <option value="{{ $p->id }}">{{$p->tahun_mulai.'/'.$p->tahun_selesai}}</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                            <th class="text-info">Tanggal Awal UAS</th>
                                            <th>
                                                <input type="date" name="tglawal_uas" id="tglawal_uas" class="form-control">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-info">Semester<code>*</code></th>
                                            <th>
                                                <select class="form-control select2" id="semester" name="semester" width="100%">
                                                    <option value="" selected disabled>-- Pilih Semester --</option>
                                                    <option value="1">Ganjil</option>
                                                    <option value="2">Genap</option>
                                                    <option value="3">Pendek</option>
                                                </select>
                                            </th>
                                            <th class="text-info">Tanggal Akhir UAS</th>
                                            <th>
                                                <input type="date" name="tglakhir_uas" id="tglakhir_uas" class="form-control">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-info">Nama Periode<code>*</code></th>
                                            <th>
                                                <input type="text" name="nama_periode" id="nama_periode" class="form-control" placeholder="Isian Maksimal 100 Karakter">
                                            </th>
                                            <th class="text-info">Ketua Ujian</th>
                                            <th>
                                                <select class="form-control select2" id="ketua_ujian" name="ketua_ujian">

                                                </select>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-info">Nama Singkat<code>*</code></th>
                                            <th>
                                                <input type="text" name="nama_singkat" id="nama_singkat" class="form-control" placeholder="Isian Maksimal 50 Karakter">
                                            </th>
                                            <th class="text-info">Jumlah Pertemuan Kuliah<code>*</code></th>
                                            <th>
                                                <input type="number" name="jumlah_pertemuan_kuliah" id="jumlah_pertemuan_kuliah" class="form-control" min="1" max="100">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-info">Tanggal Awal Kuliah<code>*</code></th>
                                            <th>
                                                <input type="date" name="tglawal_kuliah" id="tglawal_kuliah" class="form-control">
                                            </th>
                                            <th class="text-info">Minimal Presensi(%)<code>*</code></th>
                                            <th>
                                                <input type="number" name="minimal_presensi" id="minimal_presensi" class="form-control" min="1" max="100">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-info">Tanggal Akhir Kuliah<code>*</code></th>
                                            <th>
                                                <input type="date" name="tglakhir_kuliah" id="tglakhir_kuliah" class="form-control">
                                            </th>
                                            <th class="text-info">Kuisioner Layanan</th>
                                            <th>
                                                <input type="text" placeholder="Isian Belum Tersedia" class="form-control" disabled>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-info">Tanggal Awal UTS</th>
                                            <th>
                                                <input type="date" name="tglawal_uts" id="tglawal_uts" class="form-control">
                                            </th>
                                            <th class="text-info">Aktif?</th>
                                            <th id="aktif">
                                                <div class="text-danger"><i class="fas fa-times"></i></div>
                                            </th>
                                        </tr>
                                    </table>
                                    <div class="text-right">
                                        <button id="submit-periode" type="button" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                                    </div>
                                </form>
                            </div>

                            <div id="form-detailperiode" style="display: none;">
                                <h5 class="mb-3"><i class="fas fa-eye"></i> Detail Periode

                                </h5>

                                <table class="table">
                                    <tr>
                                        <th class="text-info" width="25%">Kode Periode</th>
                                        <th width="25%" id="o_kode_periode" class="dp"></th>
                                        <th width="25%" class="text-info">Tanggal Akhir UTS</th>
                                        <th width="25%" id="o_tglakhir_uts" class="dp"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-info">Tahun Ajaran</th>
                                        <th id="o_tahun_ajaran" class="dp"></th>
                                        <th class="text-info">Tanggal Awal UAS</th>
                                        <th id="o_tglawal_uas" class="dp"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-info">Semester</th>
                                        <th id="o_semester" class="dp"></th>
                                        <th class="text-info">Tanggal Akhir UAS</th>
                                        <th id="o_tglakhir_uas" class="dp"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-info">Nama Periode</th>
                                        <th id="o_nama_periode" class="dp"></th>
                                        <th class="text-info">Ketua Ujian</th>
                                        <th id="o_ketua_ujian" class="dp"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-info">Nama Singkat</th>
                                        <th id="o_nama_singkat" class="dp"></th>
                                        <th class="text-info">Jumlah Pertemuan Kuliah</th>
                                        <th id="o_jumlah_pertemuan_kuliah" class="dp"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-info">Tanggal Awal Kuliah</th>
                                        <th id="o_tglawal_kuliah" class="dp"></th>
                                        <th class="text-info">Minimal Presensi(%)</th>
                                        <th id="o_minimal_presensi" class="dp"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-info">Tanggal Akhir Kuliah</th>
                                        <th id="o_tglakhir_kuliah" class="dp"></th>
                                        <th class="text-info">Kuisioner Layanan</th>
                                        <th>-</th>
                                    </tr>
                                    <tr>
                                        <th class="text-info">Tanggal Awal UTS</th>
                                        <th id="o_tglawal_uts" class="dp"></th>
                                        <th class="text-info">Aktif?</th>
                                        <th id="o_aktif" class="dp"></th>
                                    </tr>
                                </table>
                                <div class="text-right">
                                    <button type="button" id="btn-closedetail" class="btn btn-danger">Close Detail</button>
                                </div>
                            </div>

                            <div class="table-responsive" style="margin-top: 20px;">
                                <table id="table-periode" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead class="bg-info">
                                        <tr>
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

            $('.select2').select2()

            loadEvent()

            function loadEvent()
            {
                add()
                batal()
                change_KodePeriode()
                change_ketuaujian()
                tabel()
                save_periode()
                closedetail()
            }

            function tabel()
            {
                let otable = $('#table-periode').DataTable({
                    destroy: true,
                    processing: true,
                    paging: false,
                    scrollX: true,
                    scrollY: '500px',
                    scrollCollapse: true,
                    serverSide: true,
                    searchDelay: 500,
                    responsive: true,
                    order: [],
                    ajax: {
                        url: '{!! route('setting.periode_akademik.tabel') !!}',
                        type: 'GET',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'kode'
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'mulai_kuliah'
                        },
                        {
                            data: 'akhir_kuliah'
                        },
                        {
                            data: 'awal_uts'
                        },
                        {
                            data: 'awal_uas'
                        },
                        {
                            data: 'aktif',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    language: {
                        processing: '<i class="fa fa-spinner fa-lg fa-spin"></i>'
                    },
                    drawCallback: function(settings) {
                        edit()
                        detail()
                        setAktif()
                        destroy()
                    }
                });

                otable.on('draw', function(event) {
                    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
                    $('[data-tooltip="tooltip"]').tooltip({trigger: "hover"});

                });
            }

            function add()
            {
                $('#btn-tambah').click(function (e) {
                    e.preventDefault();
                    $(this).hide()
                    $('#btn-batal').show()
                    $('#form-title').html('<i class="fas fa-pencil"></i> Input Periode');
                    $('#aktif').html('<div class="text-danger"><i class="fas fa-times"></i></div>')
                    $('#form-container').slideDown('slow');
                    $('#form-detailperiode').slideUp('slow')
                });
            }

            function batal()
            {
                $('#btn-batal').click(function (e) {
                    e.preventDefault();
                    $(this).hide()
                    $('#btn-tambah').show()
                    $('#form-container').slideUp('slow');
                    $('#form-detailperiode').slideUp('slow')
                    resetForm()
                });
            }

            function change_KodePeriode()
            {
                $('#semester').change(function (e) {
                    e.preventDefault();

                    let tahunajaran = $('#tahun_ajaran option:selected').text()
                    let semester    = $(this).val()
                    let kodeperiode = ''
                    if(tahunajaran!=null){
                        let tahun = tahunajaran.split('/')
                        kodeperiode = tahun[0]+semester;
                    }
                    $('#kode_periode').val(kodeperiode);
                });
            }

            function change_ketuaujian() {
                $('#ketua_ujian').select2({
                    width: '100%',
                    placeholder: 'Cari NIP/Nama Ketua ujian',
                    minimumInputLength: 2,
                    ajax: {
                        url: "{{ route('setting.periode_akademik.cariketua') }}",
                        dataType: 'json',
                        delay: 300,
                        data: params => ({
                            q: params.term
                        }),
                        processResults: data => ({
                            results: data
                        })
                    }
                });
            }

            function validasi_periode()
            {
                let tahun_ajaran = $('#tahun_ajaran').val()
                let semester = $('#semester').val()
                let nama_periode = $('#nama_periode').val()
                let nama_singkat = $('#nama_singkat').val()
                let tglawal_kuliah = $('#tglawal_kuliah').val()
                let tglakhir_kuliah = $('#tglakhir_kuliah').val()
                let jumlah_pertemuan = $('#jumlah_pertemuan_kuliah').val()
                let minim_presensi = $('#minimal_presensi').val()
                let notif = ''
                if(tahun_ajaran==''||tahun_ajaran==null){
                    notif = 'Tahun Ajaran Harus diisi'
                }else if(semester==''||semester==null){
                    notif = 'Semester harus diisi'
                }else if(nama_periode==''||nama_periode==null){
                    notif = 'Nama Periode Harus diisi'
                }else if(nama_singkat==''||nama_singkat==null){
                    notif = 'Nama Singkat Periode harus diisi'
                }else if(tglawal_kuliah==''||tglawal_kuliah==null){
                    notif = 'Tanggal Awal Kuliah harus diisi'
                }else if(tglakhir_kuliah==''||tglakhir_kuliah==null){
                    notif = 'Tanggal Akhir Kuliah harus diisi'
                }else if(jumlah_pertemuan==''||jumlah_pertemuan==null||jumlah_pertemuan==0){
                    notif = 'Jumlah Pertemuan Kuliah harus diisi atau tidak boleh <b>0</b>'
                }else if(minim_presensi==''||minim_presensi==null||minim_presensi==0){
                    notif = 'Minimal Presensi harus diisi atau tidak boleh <b>0</b>'
                }else{
                    notif = 'ok';
                }
                return notif;
            }

            function save_periode()
            {
                $('#submit-periode').click(function (e) {
                    e.preventDefault();
                    let validasiku = validasi_periode()
                    if(validasiku!='ok'){
                        notifalert('Perhatian',validasiku,'warning')
                    }else{
                        let dataku = $('#form-periode').serialize()
                        Swal.fire({
                            title: "Information",
                            text: "Apakah Data Periode Akademik Sudah Benar ?",
                            icon: "question",
                            showConfirmButton: true,
                            showCancelButton: true,
                        }).then((result) => {
                           if(result.value){
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('setting.periode_akademik.store')}}",
                                    data: dataku,
                                    dataType: "JSON",
                                    beforeSend: function(response) {
                                        $('#loading').show()
                                    },
                                    success: function(data) {
                                        $('#loading').hide()
                                        Swal.fire({
                                            title: data.title,
                                            text: data.message,
                                            icon: data.status
                                        }).then((result) => {
                                            if(data.status=='success'){
                                                $('#btn-batal').trigger('click');
                                                $('#table-periode').DataTable().ajax.reload();
                                            }
                                        });
                                        return;
                                    },
                                    error: function(xhr, status, error) {
                                        $('#loading').hide()
                                        Swal.fire({
                                            title: 'Unsuccessfully Saved Data',
                                            text: 'Check Your Data',
                                            icon: 'error'
                                        }).then((result) => {
                                            // $('#table-periode').DataTable().ajax.reload();
                                        });
                                        return;
                                    }
                                });
                            }else{
                                return false;
                            }
                        });
                    }
                });
            }

            function edit()
            {
                $('.btn_edit').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id')
                    let urlku = "{{ route('setting.periode_akademik.edit', [':id',':status']) }}"
                    urlku = urlku.replace(':id',params).replace(':status','edit')
                    $.ajax({
                        type: "GET",
                        url: urlku,
                        dataType: "JSON",
                        beforeSend: function(response) {
                            $('#loading').show()
                        },
                        success: function(data) {
                            $('#loading').hide()
                            if(data.hasil==0){
                                notifalert('Information', 'Data Periode Akademik Tidak Ditemukan','error')
                            }else{
                                $('#IdPeriode').val(data.periode.id)
                                $('#kode_periode').val(data.periode.kode_periode)
                                $('#tahun_ajaran').val(data.periode.id_tahunajaran).trigger('change')
                                $('#semester').val(data.periode.semester).trigger('change')
                                $('#nama_periode').val(data.periode.nama_periode)
                                $('#nama_singkat').val(data.periode.nama_singkat)
                                $('#tglawal_kuliah').val(data.periode.tgl_awal_kuliah)
                                $('#tglakhir_kuliah').val(data.periode.tgl_akhir_kuliah)
                                $('#tglawal_uts').val(data.periode.tgl_awal_uts)
                                $('#tglakhir_uts').val(data.periode.tgl_akhir_uts)
                                $('#tglawal_uas').val(data.periode.tgl_awal_uas)
                                $('#tglakhir_uas').val(data.periode.tgl_akhir_uas)
                                // $('#ketua_ujian').val(data.periode.ketua_ujian)
                                if(data.periode.ketua_ujian){
                                    let option = new Option(
                                        data.ketuaujian.text,
                                        data.ketuaujian.id,
                                        true,
                                        true
                                    );
                                    $('#ketua_ujian').append(option).trigger('change');
                                }
                                $('#jumlah_pertemuan_kuliah').val(data.periode.jumlah_pertemuan)
                                $('#minimal_presensi').val(data.periode.minimal_presensi)
                                $('#form-title').html('<i class="fas fa-edit"></i> Edit Periode');
                                $('#btn-tambah').trigger('click')
                                let aktif = data.periode.aktif=='1' ? '<div class="text-success"><i class="fas fa-check"></i></div>' : '<div class="text-danger"><i class="fas fa-times"></i></div>'
                                $('#aktif').html(aktif)
                                $('#form-title').html('<i class="fas fa-edit"></i> Edit Periode');
                            }
                        },
                        error: function(data) {
                            $('#loading').hide()
                            Swal.fire({
                                title: 'Gagal Edit Periode Akademik !',
                                text: 'Silahkan Hubungi PIKDI',
                                icon: 'error'
                            }).then((result) => {

                            });
                            return;
                        }
                    });
                    return false;
                    // $('#btn-tambah').trigger('click')
                    // $('#form-title').html('<i class="fas fa-edit"></i> Edit Periode');
                });
            }

            function detail()
            {
                $('.btn_detail').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id')
                    let urlku = "{{ route('setting.periode_akademik.edit', [':id',':status']) }}"
                    urlku = urlku.replace(':id',params).replace(':status','detail')
                    $.ajax({
                        type: "GET",
                        url: urlku,
                        dataType: "JSON",
                        beforeSend: function(response) {
                            $('#loading').show()
                        },
                        success: function(data) {
                            $('#loading').hide()
                            if(data.hasil==0){
                                notifalert('Information', 'Data Periode Akademik Tidak Ditemukan','error')
                            }else{
                                $('#o_kode_periode').html(data.periode.kode_periode)
                                $('#o_tahun_ajaran').html(data.periode.tahun_ajaran.nama_tahun_ajaran)
                                let semester = '';
                                if(data.periode.semester=='1'){
                                    semester = 'Ganjil';
                                }else if(data.periode.semester=='2'){
                                    semester = 'Genap';
                                }else{
                                    semester = 'Pendek';
                                }
                                $('#o_semester').html(semester)
                                $('#o_nama_periode').html(data.periode.nama_periode)
                                $('#o_nama_singkat').html(data.periode.nama_singkat)
                                $('#o_tglawal_kuliah').html(tglIndonesia(data.periode.tgl_awal_kuliah))
                                $('#o_tglakhir_kuliah').html(tglIndonesia(data.periode.tgl_akhir_kuliah))
                                $('#o_tglawal_uts').html(tglIndonesia(data.periode.tgl_awal_uts))
                                $('#o_tglakhir_uts').html(tglIndonesia(data.periode.tgl_akhir_uts))
                                $('#o_tglawal_uas').html(tglIndonesia(data.periode.tgl_awal_uas))
                                $('#o_tglakhir_uas').html(tglIndonesia(data.periode.tgl_akhir_uas))
                                $('#o_ketua_ujian').html(data.ketuaujian)
                                $('#o_jumlah_pertemuan_kuliah').html(data.periode.jumlah_pertemuan)
                                $('#o_minimal_presensi').html(data.periode.minimal_presensi)
                                let aktif = data.periode.aktif=='1' ? '<div class="text-success"><i class="fas fa-check"></i></div>' : '<div class="text-danger"><i class="fas fa-times"></i></div>'
                                $('#o_aktif').html(aktif)
                                $('#form-container').slideUp('slow')
                                $('#form-detailperiode').slideDown('slow')
                            }
                        },
                        error: function(data) {
                            $('#loading').hide()
                            Swal.fire({
                                title: 'Internal Server Error',
                                text: 'Silahkan Hubungi PIKDI',
                                icon: 'error'
                            }).then((result) => {

                            });
                            return;
                        }
                    });
                    return false;
                });
            }

            function setAktif()
            {
                $('.btn_aktif').click(function (e) {
                    e.preventDefault();
                    let params1 = $(this).data('id')
                    let params2 = $(this).data('aktif')
                    let urlku = "{{ route('setting.periode_akademik.active', [':id', ':status']) }}";
                    urlku = urlku.replace(':id', params1).replace(':status', params2);
                    let text = params2=='1' ? 'Apakah anda yakin akan mengaktifkan periode ?' : 'Apakah anda yakin akan menonaktifkan periode ?'
                    Swal.fire({
                        title: "Information",
                        text: text,
                        icon: "question",
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if(result.value){
                            $.ajax({
                                type: "GET",
                                url: urlku,
                                dataType: "JSON",
                                beforeSend: function(response) {
                                    $('#loading').show()
                                },
                                success: function(data) {
                                    $('#loading').hide()
                                    notifalert(data.title,data.message,data.status)
                                    if(data.status=='success'){
                                        $('#table-periode').DataTable().ajax.reload();
                                    }
                                },
                                error: function(data) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Silahkan Hubungi PIKDI',
                                        icon: 'error'
                                    }).then((result) => {

                                    });
                                    return;
                                }
                            });
                        }else{
                            return false;
                        }
                    });

                    return false;
                });
            }

            function destroy()
            {
                $('.btn_hapus').click(function (e) {
                    e.preventDefault();
                    let params1 = $(this).data('id')
                    let urlku = "{{ route('setting.periode_akademik.delete', ':id') }}";
                    urlku = urlku.replace(':id', params1);
                    Swal.fire({
                        title: "Information",
                        text: "Apakah Anda Akan Menghapus Data Periode Akademik ?",
                        icon: "question",
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if(result.value){
                            $.ajax({
                                type: "GET",
                                url: urlku,
                                dataType: "JSON",
                                beforeSend: function(response) {
                                    $('#loading').show()
                                },
                                success: function(data) {
                                    $('#loading').hide()
                                    notifalert(data.title,data.message,data.status)
                                    if(data.status=='success'){
                                        $('#table-periode').DataTable().ajax.reload();
                                    }
                                },
                                error: function(data) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Silahkan Hubungi PIKDI',
                                        icon: 'error'
                                    }).then((result) => {

                                    });
                                    return;
                                }
                            });
                        }else{
                            return false;
                        }
                    });

                    return false;
                });
            }

            function closedetail()
            {
                $('#btn-closedetail').click(function (e) {
                    e.preventDefault();
                    $('.dp').text('')
                    $('#form-detailperiode').slideUp('slow')
                });
            }

            function resetForm()
            {
                $('#form-periode')[0].reset();
                $('IdPeriode').val(null);
                $('#form-title').html('<i class="fas fa-pencil"></i> Input Periode');
                $('#form-periode').find('select').each(function() {
                    $(this).val($(this).data('default') ?? '').trigger('change');
                    $('#kode_periode').val(null)
                });
            }

        });
    </script>
@endsection
