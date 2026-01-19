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

            <div class="card card-outline card-warning">
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-orange">Periode</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" name="periode" onchange="this.form.submit()">
                                            @foreach($list_periode as $k => $v)
                                                <option value="{{ $k }}" {{ $id_periode_selected == $k ? 'selected' : '' }}>{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-orange">Unit/Prodi</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2">
                                            <option>Universitas Tiga Serangkai</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered table-hover table-sm" style="width: 100%; font-size: 14px;">
                        <thead class="bg-navy text-center">
                        <tr>
                            <th class="align-middle" style="min-width: 250px;">Program Studi</th>
                            <th class="align-middle" style="width: 120px;">Kurikulum Maba</th>
                            <th class="align-middle" style="width: 60px;">Biodata</th>
                            <th class="align-middle" style="width: 50px;">KRS</th>
                            <th class="align-middle" style="width: 60px;">Val KRS</th>
                            <th class="align-middle" style="width: 60px;">Cetak</th>
                            <th class="align-middle" style="width: 50px;">KHS</th>
                            <th class="align-middle" style="width: 60px;">Nilai</th>
                            <th class="align-middle" style="width: 60px;">Kuesioner</th>
                            <th class="align-middle" style="width: 60px;">Gen. Pertemuan</th>
                            <th class="align-middle" style="width: 50px;">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($fakultas_data as $fak)
                            <tr style="background-color: #f4f6f9;">
                                <td colspan="11" class="font-weight-bold pl-3">{{ $fak->nama_fakultas }}</td>
                            </tr>

                            @foreach($fak->prodi as $prodi)
                                @php
                                    // Cek apakah ada settingan untuk prodi ini
                                    $set = $settings_map[$prodi->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="pl-4 align-middle">{{ $prodi->nama_prodi }}</td>

                                    <td class="text-center p-1">
                                        <select class="form-control form-control-sm auto-save-select" data-id="{{ $prodi->id }}" data-field="id_kurikulum_maba">
                                            <option value="">- Pilih -</option>
                                            <option value="2024" {{ ($set && $set->id_kurikulum_maba == '2024') ? 'selected' : '' }}>2024</option>
                                            <option value="2025" {{ ($set && $set->id_kurikulum_maba == '2025') ? 'selected' : '' }}>2025</option>
                                        </select>
                                    </td>

                                    @foreach(['is_biodata', 'is_krs', 'is_validasi_krs', 'is_cetak_krs', 'is_khs', 'is_nilai', 'is_kuesioner', 'is_generate_pertemuan'] as $field)
                                        <td class="text-center align-middle">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="{{ $field }}_{{ $prodi->id }}"
                                                       class="auto-save-check"
                                                       data-id="{{ $prodi->id }}"
                                                       data-field="{{ $field }}"
                                                    {{ ($set && $set->$field) ? 'checked' : '' }}>
                                                <label for="{{ $field }}_{{ $prodi->id }}"></label>
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="text-center align-middle">
                                        <button class="btn btn-xs btn-info"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            const periode_id = "{{ $id_periode_selected }}";

            // Fungsi Auto Save
            function saveData(prodi_id, field, value) {
                // Tampilkan Toast Loading (Optional)
                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 1000
                });

                $.ajax({
                    type: 'POST',
                    url: "{{ route('setting.prodi.update') }}", // Pastikan route ini ada
                    data: {
                        id_periode: periode_id,
                        id_prodi: prodi_id,
                        field: field,
                        value: value
                    },
                    success: function(res) {
                        if(res.status == 'success') {
                            // Feedback Visual Sukses Kecil
                            console.log('Saved: ' + field);
                        } else {
                            Toast.fire({icon: 'error', title: 'Gagal Simpan'});
                        }
                    },
                    error: function() {
                        Toast.fire({icon: 'error', title: 'Error Server'});
                    }
                });
            }

            // 1. Listener Checkbox
            $('.auto-save-check').change(function() {
                let prodi_id = $(this).data('id');
                let field = $(this).data('field');
                let value = $(this).is(':checked') ? 1 : 0;
                saveData(prodi_id, field, value);
            });

            // 2. Listener Dropdown
            $('.auto-save-select').change(function() {
                let prodi_id = $(this).data('id');
                let field = $(this).data('field');
                let value = $(this).val();
                saveData(prodi_id, field, value);
            });
        });
    </script>
@endsection
