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
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" id="btn-pull-batch-bio">
                            <i class="fas fa-magic"></i> Lengkapi Biodata (Batch)
                        </button>
                    </div>
                    <table class="table table-bordered table-striped" id="table-lokal" style="width:100%">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Prodi</th>
                            <th>Status Sync</th>
                            <th>Actions</th>
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
                    { data: 'prodi', name: 'prodi.nama_prodi' },
                    { data: 'status_sync', name: 'status_sync', orderable: false, searchable: false },
                    { data: 'action', name: 'actions', searchable: false, orderable: false, className: 'text-center align-middle' },
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
                            { data: 'nim', name: 'siakad_data_mahasiswas.nim' },
                            { data: 'nama_mahasiswa', name: 'nama_mahasiswa', searchable: false },
                            { data: 'prodi', name: 'prodi.nama_prodi' },
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

            // Fungsi tarik biodata per data dan batch
            let totalLokal = 0; // Total data mahasiswa lokal
            let processedLokal = 0;
            let stats = {
                totalDicek: 0,
                totalSukses: 0,
                totalNoId: 0
            };

            $('.btn-pull-single-bio').on('click', function() {
                let idLokal = $(this).data('id');

                Swal.fire({
                    title: 'Tarik Biodata Feeder',
                    html: `
                            <div class="text-left mt-2" style="font-size: 0.9em;">
                                <p class="mb-3 text-bold">Pilih metode sinkronisasi data untuk mahasiswa ini:</p>

                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio" id="modeFill" name="syncMode" class="custom-control-input" value="fill" checked>
                                    <label class="custom-control-label" for="modeFill" style="cursor: pointer;">
                                        <strong class="text-success"><i class="fas fa-shield-alt"></i> Lengkapi Data Kosong (Aman)</strong><br>
                                        <span class="text-muted small">Hanya mengisi kolom lokal yang belum ada datanya.</span>
                                    </label>
                                </div>

                                <div class="custom-control custom-radio">
                                    <input type="radio" id="modeOverwrite" name="syncMode" class="custom-control-input" value="overwrite">
                                    <label class="custom-control-label" for="modeOverwrite" style="cursor: pointer;">
                                        <strong class="text-danger"><i class="fas fa-exclamation-triangle"></i> Timpa Data Feeder (Force Update)</strong><br>
                                        <span class="text-muted small">Awas! Menghapus data lokal & menggantinya dengan data pusat.</span>
                                    </label>
                                </div>
                            </div>
                        `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#17a2b8',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-cloud-download-alt"></i> Eksekusi!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Get pull mode
                        let selectedMode = document.querySelector('input[name="syncMode"]:checked').value;

                        Swal.fire({
                            title: 'Menarik Data...',
                            html: 'Mengkoneksikan ke Neo Feeder',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading() }
                        });

                        $.post('{{ route("neo_feeder.mahasiswa.pull-biodata.single") }}', {
                            _token: '{{ csrf_token() }}',
                            id: idLokal,
                            mode: selectedMode
                        })
                            .done(function(res) {
                                if (res.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        html: res.message
                                    }).then(() => {
                                        // Refresh Datatable
                                        $('#table-lokal').DataTable().ajax.reload(null, false);
                                    });
                                } else if (res.status === 'warning') {
                                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: res.message });
                                }
                            })
                            .fail(function(xhr) {
                                let res = xhr.responseJSON;
                                if (res && res.html_error) {
                                    Swal.fire({ icon: 'error', title: 'Sistem Terhenti', html: res.html_error });
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Koneksi Terputus', text: 'Gagal menghubungi server TSU.' });
                                }
                            });
                    }
                });
            });

            $('#btn-pull-batch-bio').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Mulai Eksekusi Batch Fill?',
                    html: `
                            <div class="text-left mt-2">
                                <p>Sistem akan melengkapi biodata <b>seluruh mahasiswa lokal</b> yang masih kosong secara otomatis dari data Neo Feeder Lokal.</p>
                                <div class="alert alert-success mt-3 mb-0" style="font-size: 0.9em;">
                                    <i class="fas fa-note-sticky"></i> <b>Catatan:</b><br>
                                    Data lokal TSU yang sudah ada isinya <b>TIDAK AKAN</b> ditimpa!
                                </div>
                            </div>
                        `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check-circle"></i> Ya, Saya Mengerti!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processedLokal = 0;
                        loopBatchPullBiodata(0);
                    }
                });
            });

            function loopBatchPullBiodata(offset) {
                Swal.fire({
                    title: 'Checking Data...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    html: `
                                <div class="mt-3 mb-4">
                                    <i class="fas fa-cog fa-spin fa-3x text-info"></i>
                                </div>
                                <p class="text-bold" style="font-size: 1.1em;">Memproses <span class="text-primary">${stats.totalDicek}</span> data lokal...</p>
                                <p class="small text-muted">Sedang Mengisi biodata yang kosong.</p>
                                <p class="small text-danger mt-2"><i class="fas fa-exclamation-triangle"></i> Mohon tunggu dan jangan tutup halaman ini!</p>
                            `
                });

                $.post('{{ route("neo_feeder.mahasiswa.pull-biodata.batch") }}', {
                    _token: '{{ csrf_token() }}',
                    offset: offset
                }).done(function(res) {
                        if (res.status === 'success') {
                            if (!res.finished) {
                                processedLokal += res.fetched;
                                stats.totalDicek += res.fetched;
                                stats.totalSukses += res.updated;
                                stats.totalNoId += res.no_id;

                                loopBatchPullBiodata(res.next_offset);
                            } else {
                                $('#table-lokal').DataTable().ajax.reload(null, false);

                                // Hitung sisa yang tidak berubah
                                let sisaAman = stats.totalDicek - stats.totalSukses - stats.totalNoId;

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Laporan Pengisian Biodata Lokal',
                                    width: 500,
                                    html: `
                                            <div class="text-left mt-3" style="font-size: 0.95em;">
                                                <p>Proses sinkronisasi massal telah selesai dengan rincian:</p>
                                                <ul class="list-group list-group-flush mb-3">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span><i class="fas fa-users text-primary"></i> Total Diperiksa</span>
                                                        <span class="badge badge-primary badge-pill" style="font-size:1em;">${stats.totalDicek}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span><i class="fas fa-check-circle text-success"></i> Berhasil Dilengkapi</span>
                                                        <span class="badge badge-success badge-pill" style="font-size:1em;">${stats.totalSukses}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span><i class="fas fa-shield-alt text-info"></i> Aman / Sudah Lengkap</span>
                                                        <span class="badge badge-info badge-pill" style="font-size:1em;">${sisaAman}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span><i class="fas fa-exclamation-circle text-warning"></i> Dilewati (Belum ada di Neo Feeder lokal)</span>
                                                        <span class="badge badge-warning badge-pill" style="font-size:1em;">${stats.totalNoId}</span>
                                                    </li>
                                                </ul>
                                                <div class="alert alert-warning p-2 small text-center mb-0">
                                                    <b>Tips:</b> Tarik List Mahasiswa dari Feeder terlebih dahulu untuk menekan angka data yang dilewati.
                                                </div>
                                            </div>
                                        `,
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: 'Selesai & Tutup'
                                });
                            }
                        }
                    })
                    .fail(function(xhr) {
                        let res = xhr.responseJSON;
                        if (res && res.html_error) {
                            Swal.fire({ icon: 'error', title: 'Mesin Berhenti!', html: res.html_error });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Koneksi Terputus!', text: 'Gagal menghubungi server TSU.' });
                        }
                    });
            }

            // Sync Config
            let totalData = 0;
            let processedData = 0;

            const SyncConfig = {
                url: {
                    init: "{{ route('neo_feeder.mahasiswa.sync.init') }}",
                    exec: "{{ route('neo_feeder.mahasiswa.sync.exec') }}"
                },
                token: "{{ csrf_token() }}",
                btn: '#btn-pull-feeder',
                table: '#table-feeder'
            };

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

            // Push data mahasiswa ke Neo Feeder
            let pushTotalData = 0;
            let pushProcessedData = 0;

            const PushConfig = {
                url: {
                    init: "{{ route('neo_feeder.mahasiswa.push.init') }}",
                    exec: "{{ route('neo_feeder.mahasiswa.push.exec') }}"
                },
                token: "{{ csrf_token() }}",
                btn: '#btn-push-massal'
            };

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

            function startPush() {
                Swal.fire({
                    title: 'Inisiasi Aktif...',
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
