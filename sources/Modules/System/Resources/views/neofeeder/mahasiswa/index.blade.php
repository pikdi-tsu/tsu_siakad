@extends('system::template/admin/header')
@section('title', 'Sinkronisasi Mahasiswa')

@section('content')
    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            {{-- NAVIGASI TABS --}}
            <ul class="nav nav-tabs" id="mahasiswa-tab" role="tablist">
                {{-- TAB 1: SEMUA DATA TSU (BARU) --}}
                <li class="nav-item">
                    <a class="nav-link active" id="tab-lokal-link" data-toggle="pill" href="#tab-lokal" role="tab">
                        <i class="fas fa-users text-primary mr-1"></i>
                        Data Mahasiswa TSU
                    </a>
                </li>

                {{-- TAB 2: BELUM SINKRON --}}
                <li class="nav-item">
                    <a class="nav-link" id="tab-sync-link" data-toggle="pill" href="#tab-sync" role="tab">
                        <i class="fas fa-exclamation-circle text-warning mr-1"></i>
                        Belum Sinkron
                    </a>
                </li>

                {{-- TAB 3: DATA FEEDER --}}
                <li class="nav-item">
                    <a class="nav-link" id="tab-feeder-link" data-toggle="pill" href="#tab-feeder" role="tab">
                        <i class="fas fa-server text-success mr-1"></i>
                        Data Neo Feeder
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="mahasiswa-tabContent">

                {{-- Tabel Lokal --}}
                <div class="tab-pane fade show active" id="tab-lokal" role="tabpanel">
                    <div class="callout callout-info mt-3">
                        <h5><i class="fas fa-info"></i> Master Data TSU</h5>
                        Ini adalah murni seluruh data mahasiswa yang tercatat di database lokal TSU Siakad.
                    </div>
                    <table class="table table-bordered table-striped" id="table-lokal" style="width:100%">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Prodi</th>
                            <th>Status Sync</th>
                        </tr>
                        </thead>
                    </table>
                </div>

                {{-- Tabel Belum Sinkron --}}
                <div class="tab-pane fade" id="tab-sync" role="tabpanel">
                    <div class="row mt-3 mb-2">
                        <div class="col-md-8">
                            <div class="callout callout-warning py-2 mb-0">
                                <small><i class="fas fa-exclamation-triangle"></i> Data mahasiswa lokal (TSU Siakad) yang belum ada di Neo Feeder.</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-right align-self-center">
                            <button class="btn btn-primary btn-sm" id="btn-push-massal">
                                <i class="fas fa-cloud-upload-alt mr-1"></i> Push ke Neo Feeder
                            </button>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="table-sync" style="width:100%">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Prodi</th>
                        </tr>
                        </thead>
                    </table>
                </div>

                {{-- Tabel Feeder --}}
                <div class="tab-pane fade" id="tab-feeder" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="callout callout-success py-2">
                                <small><i class="fas fa-database"></i> Data Mirroring (Disimpan Lokal)</small>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            {{-- TOMBOL SAKTI --}}
                            <button class="btn btn-success btn-sm" id="btn-pull-feeder">
                                <i class="fas fa-download mr-1"></i> Tarik Data Neo Feeder
                            </button>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="table-feeder" style="width:100%">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama (Feeder)</th>
                            <th>NIM (Feeder)</th>
                            <th>L/P</th>
                            <th>Tgl Lahir</th>
                            <th>Ket. Status</th>
                        </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            // INIT TAB 1: TABEL LOKAL
            var tableLokal = $('#table-lokal').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('neo_feeder.mahasiswa.json.lokal') }}",
                columns: [
                    { data: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'nim', name: 'nim' },
                    { data: 'nama_mahasiswa', name: 'nama_mahasiswa' },
                    { data: 'id_prodi', name: 'id_prodi' },
                    { data: 'status_sync', name: 'status_sync', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // INIT TAB 2: BELUM SINKRON (Lazy Load)
            var tableBelumSinkronInit = false;
            $('a[href="#tab-sync"]').on('shown.bs.tab', function (e) {
                if (!tableBelumSinkronInit) {
                    $('#table-sync').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('neo_feeder.mahasiswa.json.sync') }}",
                        columns: [
                            { data: 'DT_RowIndex', searchable: false, orderable: false },
                            { data: 'nama_mahasiswa', name: 'nama_mahasiswa' },
                            { data: 'nim', name: 'nim' },
                            { data: 'id_prodi', name: 'id_prodi' }
                        ]
                    });
                    tableBelumSinkronInit = true;
                }
            });

            // INIT TAB 3: DATA FEEDER (Lazy Load)
            var tableFeederInitialized = false;
            $('a[href="#tab-feeder"]').on('shown.bs.tab', function (e) {
                if (!tableFeederInitialized) {
                    $('#table-feeder').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('neo_feeder.mahasiswa.json.feeder') }}",
                        columns: [
                            { data: 'DT_RowIndex', searchable: false, orderable: false },
                            { data: 'nama_mahasiswa' },
                            { data: 'nim' },
                            { data: 'jenis_kelamin' },
                            { data: 'tanggal_lahir' },
                            { data: 'nama_status_mahasiswa' }
                        ]
                    });
                    tableFeederInitialized = true;
                }
            });

            // Sync Config
            const SyncConfig = {
                url: {
                    init: "{{ route('neo_feeder.mahasiswa.sync.init') }}",
                    exec: "{{ route('neo_feeder.mahasiswa.sync.exec') }}"
                },
                token: "{{ csrf_token() }}",
                btn: '#btn-pull-feeder',
                table: '#table-feeder'
            };

            // State Global
            let totalData = 0;
            let processedData = 0;

            // Sync Event Listener
            $(SyncConfig.btn).click(function() {
                Swal.fire({
                    title: 'Tarik Data Feeder?',
                    text: "Data akan disalin dari Feeder ke Database Lokal. Proses ini mungkin memakan waktu.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Mulai Sync!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        startSync();
                    }
                });
            });

            // Logic Sync
            function startSync() {
                // Loading
                Swal.fire({
                    title: 'Menghubungkan...',
                    html: 'Sedang mengecek data di server Feeder...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });

                // Hit API Init
                $.post(SyncConfig.url.init, { _token: SyncConfig.token })
                    .done(function(res) {
                        if (res.status === 'success') {
                            totalData = res.total;
                            processedData = 0;

                            if (totalData > 0) {
                                loopSync(0);
                            } else {
                                // Kalau kosong, pakai notifalert (Toast) aja cukup
                                if(typeof notifalert === 'function') {
                                    notifalert('Info', 'Data Feeder Kosong', 'info');
                                } else {
                                    Swal.fire('Info', 'Data Feeder Kosong', 'info');
                                }
                            }
                        } else {
                            handleError('Gagal memulai sinkronisasi.', res.error);
                        }
                    })
                    .fail(function(xhr) {
                        handleError('Gagal terhubung ke server.', xhr.responseText);
                    });
            }

            function loopSync(offset) {
                // Update Progress Bar
                let percent = Math.floor((processedData / totalData) * 100);

                Swal.update({
                    title: 'Sinkronisasi Berjalan...',
                    showConfirmButton: false,
                    html: `
                    <div class="progress mt-3 mb-2" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                             role="progressbar" style="width: ${percent}%">
                             ${percent}%
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Memproses ${processedData} dari ${totalData} data...</p>
                `
                });

                Swal.hideLoading();

                // Hit API Exec
                $.post(SyncConfig.url.exec, { _token: SyncConfig.token, offset: offset })
                    .done(function(res) {
                        if (res.status === 'success') {
                            processedData += res.fetched;

                            if (res.fetched > 0 && processedData < totalData) {
                                loopSync(res.next_offset);
                            } else {
                                finishSync();
                            }
                        } else {
                            handleError('Proses terhenti di tengah jalan.', res.error);
                        }
                    })
                    .fail(function(xhr) {
                        handleError('Koneksi terputus saat mengambil data.', xhr.responseText);
                    });
            }

            function finishSync() {
                // Reload Tabel
                $(SyncConfig.table).DataTable().ajax.reload();

                // Tampilkan Pesan Sukses
                Swal.fire({
                    title: 'Selesai!',
                    text: `Berhasil menarik ${processedData} data mahasiswa.`,
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });
            }

            // Tombol Push Neo Feeder
            const PushConfig = {
                url: {
                    init: "{{ route('neo_feeder.mahasiswa.push.init') }}",
                    exec: "{{ route('neo_feeder.mahasiswa.push.exec') }}"
                },
                token: "{{ csrf_token() }}",
                btn: '#btn-push-massal'
            };

            let pushTotalData = 0;
            let pushProcessedData = 0;

            // 1. Klik Tombol Nuklir
            $(PushConfig.btn).click(function() {
                Swal.fire({
                    title: 'Kirim Pasukan ke PDDIKTI?',
                    text: "Seluruh mahasiswa lokal yang belum tersinkronisasi akan dikirim ke server pusat Neo Feeder. Proses ini butuh waktu.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Luncurkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        startPush();
                    }
                });
            });

            // 2. Hitung Jumlah Pasukan
            function startPush() {
                Swal.fire({
                    title: 'Radar Aktif...',
                    html: 'Menghitung total mahasiswa yang akan dikirim...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });

                $.post(PushConfig.url.init, { _token: PushConfig.token })
                    .done(function(res) {
                        if (res.status === 'success') {
                            pushTotalData = res.total;
                            pushProcessedData = 0;

                            if (pushTotalData > 0) {
                                loopPush();
                            } else {
                                Swal.fire('Info', 'Pasukan kosong! Semua mahasiswa sudah ada di Feeder.', 'info');
                            }
                        } else {
                            Swal.fire('Error', res.error, 'error');
                        }
                    })
                    .fail(function() { Swal.fire('Error', 'Gagal menghubungi server.', 'error'); });
            }

            // 3. Eksekusi Tembakan Beruntun (Batch)
            function loopPush() {
                let percent = Math.floor((pushProcessedData / pushTotalData) * 100);

                Swal.update({
                    title: 'Peluncuran Berjalan...',
                    showConfirmButton: false,
                    html: `
                    <div class="progress mt-3 mb-2" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                             role="progressbar" style="width: ${percent}%">
                             ${percent}%
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Berhasil mengirim ${pushProcessedData} dari ${pushTotalData} data...</p>
                    `
                });
                Swal.hideLoading();

                $.post(PushConfig.url.exec, { _token: PushConfig.token })
                    .done(function(res) {
                        if (res.status === 'success') {
                            pushProcessedData += res.processed;

                            if (pushProcessedData < pushTotalData) {
                                loopPush(); // Tembak lagi batch selanjutnya
                            } else {
                                finishPush(); // Selesai
                            }
                        } else {
                            Swal.fire('Error', res.error, 'error');
                        }
                    })
                    .fail(function() { Swal.fire('Error', 'Koneksi terputus saat Push data.', 'error'); });
            }

            // 4. Selebrasi
            function finishPush() {
                // Refresh semua tabel agar statusnya berubah jadi hijau
                $('#table-lokal').DataTable().ajax.reload(null, false);
                $('#table-semua-lokal').DataTable().ajax.reload(null, false);
                $('#table-feeder').DataTable().ajax.reload(null, false);

                Swal.fire({
                    title: 'Operasi Sukses!',
                    text: `Data mahasiswa berhasil dikirim ke Neo Feeder. Silakan cek tabel!`,
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });
            }

            // Error Handling
            function handleError(userMessage, technicalError) {
                console.error("SYNC NEO FEEDER ERROR:", technicalError);

                Swal.fire({
                    title: 'Terjadi Kesalahan',
                    text: userMessage + " Silahkan hubungi PIKDI untuk tindak lanjut!",
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        });
    </script>
@endsection
