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
                        <li class="breadcrumb-item">Perkuliahan</li>
                        <li class="breadcrumb-item active">{{ $menu }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <div class="card card-outline card-success shadow-sm">
                        <div class="card-body py-3">
                            <form id="form-waktu" class="form-inline justify-content-center">
                                @csrf
                                <input type="hidden" id="id" name="id">

                                <label class="mr-2 font-weight-bold">Waktu :</label>
                                <input type="time" name="waktu" id="waktu" class="form-control mr-2" required
                                    style="width: 150px;">

                                <button type="submit" class="btn btn-success" id="btn-submit">
                                    <i class="fas fa-plus"></i> <span id="btn-text">Tambah</span>
                                </button>
                                <button type="button" class="btn btn-secondary ml-1" id="btn-reset" style="display: none;">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header bg-navy text-white text-center">
                            <h5 class="card-title m-0 w-100">Waktu Pagi (06:00-12:00)</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <tbody>
                                    @forelse($pagi as $p)
                                        <tr>
                                            <td class="pl-4 font-weight-bold align-middle">{{ $p->waktu_formatted }}</td>
                                            <td class="text-right pr-3">
                                                <button class="btn btn-xs btn-primary btn_edit"
                                                    data-id="{{ $p->id }}"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-xs btn-danger btn_hapus"
                                                    data-id="{{ $p->id }}"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header bg-navy text-white text-center">
                            <h5 class="card-title m-0 w-100">Waktu Siang (12:00-18:00)</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <tbody>
                                    @forelse($siang as $s)
                                        <tr>
                                            <td class="pl-4 font-weight-bold align-middle">{{ $s->waktu_formatted }}</td>
                                            <td class="text-right pr-3">
                                                <button class="btn btn-xs btn-primary btn_edit"
                                                    data-id="{{ $s->id }}"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-xs btn-danger btn_hapus"
                                                    data-id="{{ $s->id }}"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header bg-navy text-white text-center">
                            <h5 class="card-title m-0 w-100">Waktu Malam (18:00-24:00)</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover mb-0">
                                <tbody>
                                    @forelse($malam as $m)
                                        <tr>
                                            <td class="pl-4 font-weight-bold align-middle">{{ $m->waktu_formatted }}</td>
                                            <td class="text-right pr-3">
                                                <button class="btn btn-xs btn-primary btn_edit"
                                                    data-id="{{ $m->id }}"><i
                                                        class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-xs btn-danger btn_hapus"
                                                    data-id="{{ $m->id }}"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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

            // 1. SUBMIT FORM (CREATE/UPDATE)
            $('#form-waktu').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('perkuliahan.slot_waktu.store') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status == 'success') {
                            Swal.fire({
                                title: 'Berhasil',
                                text: res.message,
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                location
                                    .reload(); // Reload untuk refresh grouping kolom
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
                    }
                });
            });

            // 2. EDIT DATA
            $('.btn_edit').click(function() {
                var id = $(this).data('id');
                $.get("{{ route('perkuliahan.slot_waktu.index') }}" + '/edit/' + id, function(res) {
                    if (res.status == 'success') {
                        $('#id').val(res.data.id);
                        $('#waktu').val(res.data.waktu_input); // Format H:i

                        // Ubah UI Form jadi Mode Edit
                        $('#btn-text').text('Update');
                        $('#btn-submit').removeClass('btn-success').addClass('btn-warning');
                        $('#btn-reset').show();

                        // Focus ke input atas
                        $('html, body').animate({
                            scrollTop: 0
                        }, 'fast');
                        $('#waktu').focus();
                    }
                });
            });

            // 3. RESET FORM
            $('#btn-reset').click(function() {
                $('#form-waktu')[0].reset();
                $('#id').val('');

                // Balik ke Mode Tambah
                $('#btn-text').text('Tambah');
                $('#btn-submit').removeClass('btn-warning').addClass('btn-success');
                $(this).hide();
            });

            // 4. HAPUS DATA
            $('.btn_hapus').click(function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Waktu ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('perkuliahan.slot_waktu.index') }}" +
                                '/delete/' + id,
                            success: function(res) {
                                if (res.status == 'success') {
                                    Swal.fire('Terhapus', res.message, 'success').then(
                                        () => location.reload());
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
