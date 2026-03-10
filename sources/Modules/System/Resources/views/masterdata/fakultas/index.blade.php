@extends('system::template/admin/header')
@section('title', $title)
@section('link_href')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $menu }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Master Data</li>
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
                            <h5 class="m-0">{{ $menu }}</h5>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="fakultasTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="lokal-tab" data-toggle="tab" href="#tab-lokal" role="tab">
                                        <i class="fas fa-database text-primary mr-1"></i> Data Fakultas Lokal (PMB)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="feeder-tab" data-toggle="tab" href="#tab-feeder" role="tab">
                                        <i class="fas fa-cloud text-success mr-1"></i> Data Neo Feeder
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-4" id="fakultasTabContent">

                                {{-- =================================== --}}
                                {{-- TAB 1: DATA LOKAL --}}
                                {{-- =================================== --}}
                                <div class="tab-pane fade show active" id="tab-lokal" role="tabpanel">
                                    <div class="row justify-content-center mb-4">
                                        <div class="col-md-6">
                                            <form id="form-fakultas" method="POST" action="{{route('perguruan_tinggi.fakultas.store')}}">
                                                @csrf
                                                <input type="hidden" id="IdFakultas" name="IdFakultas" value="">
                                                <div class="form-group mb-3">
                                                    <label>Kode Fakultas <code>*Otomatis</code></label>
                                                    <input type="text" id="kdfakultas" name="kdfakultas" class="form-control" value="{{$kdfakultas}}" readonly required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Nama Fakultas</label>
                                                    <input type="text" id="namafakultas" name="namafakultas" class="form-control" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Singkatan</label>
                                                    <input type="text" id="singkatanfakultas" name="singkatanfakultas" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-success float-right ml-2"><i class="fas fa-save"></i> Submit</button>
                                                    <button id="btn-reset" class="btn btn-warning float-right">Reset</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <table id="table-lokal" class="table table-bordered table-striped" style="width: 100%;">
                                        <thead class="bg-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama Fakultas</th>
                                            <th>Singkatan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>

                                {{-- TAB 2: DATA NEO FEEDER --}}
                                <div class="tab-pane fade" id="tab-feeder" role="tabpanel">
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <div class="callout callout-info py-2 mb-0">
                                                <small><i class="fas fa-info-circle"></i> Ini adalah data referensi Fakultas resmi dari PDDIKTI. Data ini ditarik secara satu arah (Pull).</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-right align-self-center">
                                            <button class="btn btn-info btn-sm" id="btn-sync-feeder">
                                                <i class="fas fa-cloud-download-alt mr-1"></i> Tarik Data Feeder
                                            </button>
                                        </div>
                                    </div>

                                    <table id="table-feeder" class="table table-bordered table-striped" style="width: 100%;">
                                        <thead class="bg-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Fakultas</th>
                                            <th>Jenjang</th>
                                            <th>Status</th>
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
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            setTimeout(() => { loadEvent() }, 500);

            function loadEvent(){
                tabelFakultas();
                btn_reset();
            }

            function EditFakultas(){
                $('.btn_edit').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id')
                    $.ajax({
                        type: "GET",
                        url:  '{!! url('/MasterData/Fakultas/EditFakultas') !!}' + '/' + params,
                        dataType: "JSON",
                        success: function(data) {
                            if(data.hasil===0){
                                Swal.fire('Error', 'Data Fakultas Tidak Ditemukan', 'error');
                            }else{
                                $('#IdFakultas').val(data.IdFakultas)
                                $('#kdfakultas').val(data.fakultas.KodeFakultas)
                                $('#namafakultas').val(data.fakultas.namafakultas)
                                $('#singkatanfakultas').val(data.fakultas.singkatan)
                            }
                        }
                    });
                });
            }

            function btn_reset(){
                $('#btn-reset').click(function (e) {
                    e.preventDefault();
                    $('#IdFakultas').val(null);
                    $('#kdfakultas').val('{{$kdfakultas}}');
                    $('#namafakultas').val(null);
                    $('#singkatanfakultas').val(null);
                });
            }

            // INIT TABEL LOKAL
            $('#table-lokal').DataTable({
                processing: true, serverSide: true,
                ajax: '{!! route('perguruan_tinggi.fakultas.table_lokal') !!}', // Route lokal Komandan
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode' }, { data: 'nama' }, { data: 'singkatan' },
                    { data: 'aktif' }, { data: 'action', orderable: false, searchable: false }
                ]
            });

            // INIT TABEL FEEDER (Lazy Load)
            var tableFeederInit = false;
            $('a[href="#tab-feeder"]').on('shown.bs.tab', function (e) {
                if (!tableFeederInit) {
                    $('#table-feeder').DataTable({
                        processing: true, serverSide: true,
                        ajax: '{!! route('perguruan_tinggi.fakultas.json_feeder') !!}',
                        columns: [
                            { data: 'DT_RowIndex', orderable: false, searchable: false },
                            { data: 'nama_fakultas' },
                            { data: 'jenjang' },
                            { data: 'status' }
                        ]
                    });
                    tableFeederInit = true;
                }
            });

            // TOMBOL SYNC FEEDER
            $('#btn-sync-feeder').click(function() {
                Swal.fire({
                    title: 'Tarik Referensi Fakultas?',
                    text: "Sistem akan menyalin data Fakultas terbaru dari server PDDIKTI.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tarik Sekarang!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Mengambil Data...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                        $.post("{{ route('perguruan_tinggi.fakultas.sync_feeder') }}", { _token: "{{ csrf_token() }}" })
                            .done(function(res) {
                                Swal.close();

                                if (res.status === 'success') {
                                    $('#table-feeder').DataTable().ajax.reload(null, false);
                                    notifalert('Sukses!', res.message, 'success');
                                } else {
                                    notifalert('Gagal!', res.message, 'error');
                                }
                            })
                            .fail(function() {
                                Swal.close();
                                notifalert('Error!', 'Koneksi terputus saat mencoba menarik data.', 'error');
                            });
                    }
                });
            });

            // 🔥 OPERASI PENYEDOTAN DATA NEO FEEDER
            $('#btn-sync-fakultas').click(function() {
                Swal.fire({
                    title: 'Tarik Data Fakultas?',
                    text: "Sistem akan mengambil referensi Fakultas resmi dari server pusat Neo Feeder PDDIKTI.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tarik Sekarang!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Berkomunikasi dengan PDDIKTI...',
                            html: 'Mohon tunggu, sedang menyedot data...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        $.post("{{ route('perguruan_tinggi.fakultas.sync_feeder') }}")
                            .done(function(res) {
                                if (res.status === 'success') {
                                    $('#example2').DataTable().ajax.reload(null, false);
                                    Swal.fire('Operasi Sukses!', res.message, 'success');
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            })
                            .fail(function() {
                                Swal.fire('Error', 'Koneksi terputus saat mencoba menarik data.', 'error');
                            });
                    }
                });
            });

        });
    </script>
@endsection
