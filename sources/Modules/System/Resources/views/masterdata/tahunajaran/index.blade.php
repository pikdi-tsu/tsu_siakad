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
                            <h5 class="m-0 d-inline-block">Daftar {{ $menu }}</h5>
                            <div class="float-right">
                                <button class="btn btn-primary" id="btn-tambah"><i class="fas fa-plus"></i> Tambah</button>
                                <button id="btn-batal" class="btn btn-warning float-right" style="display: none;"><i class="fas fa-undo"></i>Batal</button>

                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row justify-content-center" id="slide-tahunajaran" style="display: none;">
                                <div class="col-md-6"> <!-- Ubah lebar form di sini -->
                                    <form id="form-tahunajaran" method="POST" action="#">
                                        @csrf
                                        <input type="hidden" id="IdTahunAjaran" name="IdTahunAjaran" value="">
                                        <div class="form-group mb-3">
                                            <label for="batch">Nama Tahun Ajaran<code>*</code></label>
                                            <input type="text" class="form-control" name="nama_tahunajaran" id="nama_tahunajaran" placeholder="Nama Tahun Ajaran">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="tahunlulus">Tahun Mulai<code>*</code></label>
                                            <select class="form-control select2" id="tahunmulai" name="tahunmulai">
                                                <option value="" selected disabled>-- Pilih Tahun --</option>
                                                    @php
                                                    $currentYear = date('Y')+5;
                                                @endphp
                                                @for ($i = 0; $i < 15; $i++)
                                                    <option value="{{ $currentYear - $i }}">{{ $currentYear - $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="tahunlulus">Tahun Selesai<code>*</code></label>
                                            <select class="form-control select2" id="tahunselesai" name="tahunselesai">
                                                <option value="" selected disabled>-- Pilih Tahun --</option>
                                                @php
                                                    $currentYear = date('Y')+5;
                                                @endphp
                                                @for ($i = 0; $i < 15; $i++)
                                                    <option value="{{ $currentYear - $i }}">{{ $currentYear - $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <button type="button" id="submit-tahunajaran" class="btn btn-success float-right"><i class="fas fa-save"></i> Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive"  style="margin-top: 20px;">
                                <table id="table-tahunajaran" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead class="bg-info">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Tahun Ajaran</th>
                                            <th>Tahun Mulai</th>
                                            <th>Tahun Selesai</th>
                                            <th class="text-center">Aktif</th>
                                            <th class="text-center">Action</th>
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
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2()

            loadEvent()

            function loadEvent()
            {
                tabel_tahunajaran()
                add_tahunajaran()
                batal()
                save_tahunajaran()
            }

            function tabel_tahunajaran()
            {
                let otable = $('#table-tahunajaran').DataTable({
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
                        url: '{!! route('setting.tahun_ajaran.tabel') !!}',
                        type: 'GET',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'mulai'
                        },
                        {
                            data: 'selesai'
                        },
                        {
                            data: 'aktif'
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
                        edit_tahunajaran()
                        aktif_tahunajaran()
                        hapus_tahunajaran()
                    }
                });

                otable.on('draw', function(event) {
                    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
                    $('[data-tooltip="tooltip"]').tooltip({trigger: "hover"});

                });
            }

            function add_tahunajaran()
            {
                $('#btn-tambah').click(function (e) {
                    e.preventDefault();
                    $('#slide-tahunajaran').slideDown('slow');
                    $(this).hide()
                    $('#btn-batal').show()
                });
            }

            function batal()
            {
                $('#btn-batal').click(function (e) {
                    e.preventDefault();
                    $('#slide-tahunajaran').slideUp('slow');
                    $(this).hide()
                    $('#btn-tambah').show()
                    $('#form-tahunajaran')[0].reset();
                    $('#form-tahunajaran').find('select').each(function() {
                        $(this).val($(this).data('default') ?? '').trigger('change');
                    });
                });
            }

            function validasi_ajaran()
            {
                let nama = $('#nama_tahunajaran').val()
                let tahunmulai = $('#tahunmulai').val()
                let tahunselesai = $('#tahunselesai').val()

                let notif = ''

                if(nama==''||nama==null){
                    notif = 'Nama Tahun Ajaran tidak boleh kosong'
                }else if(tahunmulai==''||tahunmulai==null){
                    notif = 'Tahun Mulai Ajaran tidak boleh kosong'
                }else if(tahunselesai==''||tahunmulai==null){
                    notif = 'Tahun Selesai Ajaran tidak boleh kosong'
                }else{
                    notif = 'ok'
                }
                return notif;
            }

            function save_tahunajaran()
            {
                $('#submit-tahunajaran').click(function (e) {
                    e.preventDefault();
                    let validasiku = validasi_ajaran()
                    if(validasiku!='ok'){
                        notifalert('Perhatian',validasiku,'warning')
                    }else{
                        let dataku = $('#form-tahunajaran').serialize()
                        Swal.fire({
                            title: "Information",
                            text: "Apakah Data Tahun Ajaran Sudah Benar ?",
                            icon: "question",
                            showConfirmButton: true,
                            showCancelButton: true,
                        }).then((result) => {
                            if(result.value){
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('setting.tahun_ajaran.save')}}",
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
                                            $('#btn-batal').trigger('click');
                                            $('#table-tahunajaran').DataTable().ajax.reload();
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
                                            $('#table-tahunajaran').DataTable().ajax.reload();
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

            function edit_tahunajaran()
            {
                $('.btn_edit').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id')
                    $.ajax({
                        type: "GET",
                        url: '{{ route('setting.tahun_ajaran.edit', ':id') }}'.replace(':id', params),
                        dataType: "JSON",
                        beforeSend: function(response) {
                            $('#loading').show()
                        },
                        success: function(data) {
                            $('#loading').hide()
                            if(data.hasil==0){
                                notifalert('Information', 'Data Tahun Ajaran Tidak Ditemukan','error')
                            }else{
                                $('#IdTahunAjaran').val(data.tahunajaran.id)
                                $('#nama_tahunajaran').val(data.tahunajaran.nama_tahun_ajaran)
                                $('#tahunmulai').val(data.tahunajaran.tahun_mulai).trigger('change')
                                $('#tahunselesai').val(data.tahunajaran.tahun_selesai).trigger('change')
                                $('#btn-tambah').trigger('click')
                            }
                        },
                        error: function(data) {
                            $('#loading').hide()
                            Swal.fire({
                                title: 'Gagal Edit Tahun Ajaran !',
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

            function aktif_tahunajaran()
            {
                $('.btn_aktif').click(function (e) {
                    e.preventDefault();
                    let params1 = $(this).data('id')
                    let params2 = $(this).data('aktif')
                    let urlku = "{{ route('setting.tahun_ajaran.aktif', [':id', ':status']) }}";
                    urlku = urlku.replace(':id', params1).replace(':status', params2);
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
                            $('#table-tahunajaran').DataTable().ajax.reload();
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
                    return false;
                });
            }

            function hapus_tahunajaran()
            {
                $('.btn_hapus').click(function(e) {
                    // e.preventDefault()
                    let params = $(this).data('id')

                    Swal.fire({
                        title: 'Information',
                        text: 'Apakah Anda yakin akan menghapus data tahun ajaran ?',
                        icon: 'question',
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "GET",
                                url: '{{ route('setting.tahun_ajaran.hapus', ':id') }}'.replace(':id', params),
                                dataType: "JSON",
                                beforeSend: function(response) {
                                    $('#loading').show()
                                },
                                success: function(data) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: 'Information',
                                        text: data.message,
                                        icon: data.status
                                    }).then(() => {
                                        $("#table-tahunajaran").DataTable().ajax.reload();
                                    })
                                },
                                error: function(data) {
                                    $('#loading').hide()
                                    console.log(0)
                                }
                            });
                            return false;
                        }else{
                            return false;
                        }
                    })
                })
            }

        });
    </script>
@endsection
