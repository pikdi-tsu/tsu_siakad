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
                        <li class="breadcrumb-item">Master Data</li>
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

                        {{-- CARD HEADER --}}
                        <div class="card-header">
                            <h5 class="m-0 d-inline-block">Universitas Tiga Serangkai</h5>
                            <button class="btn btn-warning btn-sm float-right" id="btn-batal" style="display: none;">
                                <i class="fas fa-refresh"></i> Batal
                            </button>
                            <button class="btn btn-success btn-sm float-right" id="btn-simpan"
                                style="margin-right: 5px;display: none;">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan
                            </button>
                            <button class="btn btn-warning btn-sm float-right" id="btn-edit">
                                <i class="fas fa-pencil"></i> Edit
                            </button>
                        </div>

                        {{-- CARD BODY --}}
                        <div class="card-body">

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <form action="{{ route('perguruan_tinggi.perguruan_tinggi.save') }}" id="form-edit-pt" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="idpt" id="idpt" value="{{ $pt ? $pt->id : null }}">
                                    <table class="table">
                                        <tr>
                                            <th class="text-primary">Kode Unit<span class="editpt"
                                                    style="display: none;"><code>*</code></span></th>
                                            <td>
                                                <span class="showpt">{{ $pt == null ? '-' : $pt->kode_unit }}</span>
                                                <input type="text" class="form-control editpt" name="kodeunit"
                                                    id="kodeunit" style="display:none;"
                                                    value="{{ $pt == null ? null : $pt->kode_unit }}"
                                                    title="Isian Maksimal 10 Karakter">
                                            </td>
                                            <th class="text-primary">Lembaga Naungan<span class="editpt"
                                                    style="display: none;"><code>*</code></span></th>
                                            <td>
                                                <span class="showpt">{{ $pt == null ? '-' : $pt->lembaga_naungan }}</span>
                                                <select class="form-control select2 editpt" id="lembaga_naungan"
                                                    name="lembaga_naungan" style="display: none;">
                                                    <option value="" selected disabled>-- Pilih Lembaga Naungan --
                                                    </option>
                                                    @foreach ($lembaga as $p)
                                                        @php
                                                            $select1 = '';
                                                            if ($pt) {
                                                                $select1 =
                                                                    $p->id == $pt->lembaga_naungan ? 'selected' : '';
                                                            }
                                                        @endphp
                                                        <option value="{{ encrypt($p->id) }}"{{ $select1 }}>
                                                            {{ $p->nama_lembaga }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Nama Unit<span class="editpt"
                                                    style="display: none;"><code>*</code></span></th>
                                            <td>
                                                <span class="showpt">{{ $pt == null ? '-' : $pt->nama_unit }}</span>
                                                <input type="text" class="form-control editpt" name="namaunit"
                                                    id="namaunit" value="{{ $pt == null ? null : $pt->nama_unit }}"
                                                    style="display:none;" title="Isian Maksimal 100 Karakter">
                                            </td>
                                            <th class="text-primary">Unit/Satuan Kerja</th>
                                            <td>
                                                <span class="showpt">{{ $pt == null ? '-' : $pt->unit_satuan_kerja }}</span>
                                                <select class="form-control select2 editpt" id="unitsatuankerja"
                                                    name="unitsatuankerja" style="display: none;">
                                                    <option value="" selected disabled>-- Pilih Unit Satuan Kerja --
                                                    </option>
                                                    @php
                                                        $select4 = '';
                                                        if ($pt) {
                                                            $select4 =
                                                                $pt->unit_satuan_kerja == 'Universitas Tiga Serangkai'
                                                                    ? 'selected'
                                                                    : '';
                                                        }
                                                    @endphp
                                                    <option value="Universitas Tiga Serangkai" {{ $select4 }}>
                                                        Universitas Tiga Serangkai</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Nama Unit (EN)<span class="editpt"
                                                    style="display: none;"><code>*</code></span></th>
                                            <td>
                                                <span class="showpt">{{ $pt == null ? '-' : $pt->nama_unit_en }}</span>
                                                <input type="text" class="form-control editpt" name="namaunit_en"
                                                    id="namaunit_en" value="{{ $pt == null ? null : $pt->nama_unit_en }}"
                                                    style="display:none;" title="Isian Maksimal 50 Karakter">
                                            </td>
                                            <th class="text-primary">Periode Berdiri</th>
                                            <td>
                                                <span class="showpt">{{ $pt == null ? '-' : $pt->periode_berdiri }}</span>
                                                <select class="form-control select2 editpt" id="periode_berdiri"
                                                    name="periode_berdiri" style="display: none;">
                                                    <option value="" selected disabled>-- Pilih Periode Berdiri --
                                                    </option>
                                                    @php
                                                        $select5 = '';
                                                        $select6 = '';
                                                        if ($pt) {
                                                            $select5 =
                                                                $pt->periode_berdiri == '2024 Genap' ? 'selected' : '';
                                                        }
                                                        if ($pt) {
                                                            $select6 =
                                                                $pt->periode_berdiri == '2024 Ganjil' ? 'selected' : '';
                                                        }
                                                    @endphp
                                                    <option value="2024 Genap" {{ $select5 }}>2024 Genap</option>
                                                    <option value="2024 Ganjil" {{ $select6 }}>2024 Ganjil</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Nama Singkat<span class="editpt"
                                                    style="display: none;"><code>*</code></span></th>
                                            <td>
                                                <span class="showpt">{{ $pt == null ? '-' : $pt->nama_singkat }}</span>
                                                <input type="text" class="form-control editpt" name="namasingkat"
                                                    value="{{ $pt == null ? null : $pt->nama_singkat }}" id="namasingkat"
                                                    style="display:none;" title="Isian Maksimal 50 Karakter">
                                            </td>
                                            <th class="text-primary">No. SK Pendirian</th>
                                            <td>
                                                <span class="showpt">{{ $pt == null ? '-' : $pt->no_sk_pendirian }}</span>
                                                <input type="text" class="form-control editpt" name="noskpendirian"
                                                    id="noskpendirian"
                                                    value="{{ $pt == null ? null : $pt->no_sk_pendirian }}"
                                                    style="display:none;" title="Isian Maksimal 100 Karakter">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Jenis Perguruan Tinggi<span class="editpt"
                                                    style="display: none;"><code>*</code></span></th>
                                            <td>
                                                <span
                                                    class="showpt">{{ $pt == null ? '-' : $pt->jenis_perguruan_tinggi }}</span>
                                                <select class="form-control select2 editpt" id="jenis_pt"
                                                    name="jenis_pt" style="display: none;">
                                                    <option value="" selected disabled>-- Pilih Jenis Perguruan
                                                        Tinggi --</option>
                                                    @foreach ($jenispt as $p)
                                                        @php
                                                            $select2 = '';
                                                            if ($pt) {
                                                                $select2 =
                                                                    $p->id == $pt->jenis_perguruan_tinggi
                                                                        ? 'selected'
                                                                        : '';
                                                            }
                                                        @endphp
                                                        <option value="{{ encrypt($p->id) }}"{{ $select2 }}>
                                                            {{ $p->jenis_pt }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <th class="text-primary">Tanggal SK Pendirian</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : (($pt->tanggal_sk_pendirian) ? tglIndo($pt->tanggal_sk_pendirian) : '')}}</span>
                                                <input type="date" class="form-control editpt" name="tglskpendirian" value="{{ $pt==null ? null : $pt->tanggal_sk_pendirian }}" id="tglskpendirian" style="display:none;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-success" colspan="4" style="font-size: 20px;">Pejabat
                                                Universitas Tiga Serangkai</th>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Rektor</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : $pt->rektor.' - '.namapegawai($pt->rektor)}}</span>
                                                <select class="form-control select2 editpt" id="rektor" name="rektor"style="display:none;">

                                                </select>
                                            </td>
                                            <th class="text-primary">Wakil Rektor 3</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : $pt->wakil_rektor3.' - '.namapegawai($pt->wakil_rektor3)}}</span>
                                                <select class="form-control select2 editpt" id="wr3" name="wr3"style="display:none;">

                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Wakil Rektor 1</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : $pt->wakil_rektor1.' - '.namapegawai($pt->wakil_rektor1)}}</span>
                                                <select class="form-control select2 editpt" id="wr1" name="wr1"style="display:none;">

                                                </select>
                                            </td>
                                            <th class="text-primary">Wakil Rektor 4</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : $pt->wakil_rektor4.' - '.namapegawai($pt->wakil_rektor4)}}</span>
                                                <select class="form-control select2 editpt" id="wr4" name="wr4"style="display:none;">

                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Wakil Rektor 2</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : $pt->wakil_rektor2.' - '.namapegawai($pt->wakil_rektor2)}}</span>
                                                <select class="form-control select2 editpt" id="wr2" name="wr2"style="display:none;">

                                                </select>
                                            </td>
                                            <th class="text-primary"></th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th class="text-success" colspan="4" style="font-size: 20px;">Akreditasi
                                                Universitas Tiga Serangkai</th>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Lembaga Akreditasi</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : $pt->lembaga_akreditasi}}</span>
                                                <input type="text" class="form-control editpt" value="{{$pt==null ? null : $pt->lembaga_akreditasi}}" name="lembaga_akreditasi" id="lembaga_akreditasi" style="display:none;">
                                            </td>
                                            <th class="text-primary">Tgl SK Akreditasi</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : (($pt->tanggal_sk_akreditasi) ? tglIndo($pt->tanggal_sk_akreditasi) : '')}}</span>
                                                <input type="date" class="form-control editpt" value="{{$pt==null ? null : $pt->tanggal_sk_akreditasi}}" name="tglskakreditasi" id="tglskakreditasi" style="display:none;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Peringkat Akreditasi</th>
                                            <td>
                                                <span
                                                    class="showpt">{{ $pt == null ? '-' : $pt->peringkat_akreditasi }}</span>
                                                <select class="form-control select2 editpt" id="peringkat_akreditasi"
                                                    name="peringkat_akreditasi" style="display: none;">
                                                    <option value="" selected disabled>-- Pilih Peringkat Akreditasi
                                                        --</option>
                                                    @foreach ($peringkatAK as $p)
                                                        @php
                                                            $select3 = '';
                                                            if ($pt) {
                                                                $select3 =
                                                                    $p->id == $pt->peringkat_akreditasi
                                                                        ? 'selected'
                                                                        : '';
                                                            }
                                                        @endphp
                                                        <option value="{{ encrypt($p->id) }}"{{ $select3 }}>
                                                            {{ $p->peringkat_akreditasi }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <th class="text-primary">Tgl Berlaku Akreditasi</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : (($pt->tanggal_berlaku_akreditasi) ? tglIndo($pt->tanggal_berlaku_akreditasi) : '')}}</span>
                                                <input type="date" class="form-control editpt" value="{{$pt==null ? null : $pt->tanggal_berlaku_akreditasi}}" name="tglberlakuakreditasi" id="tglberlakuakreditasi" style="display:none;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Nilai Akreditasi</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : $pt->nilai_akreditasi}}</span>
                                                <input type="number" class="form-control editpt" value="{{$pt==null ? null : $pt->nilai_akreditasi}}" min="0" max="999" name="nilai_akreditasi" id="nilai_akreditasi" title="Isian maksimal 3 karakter, isian harus angka" style="display: none;">
                                            </td>
                                            <th class="text-primary">Tgl Berakhir Akreditasi</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : (($pt->tanggal_berakhir_akreditasi) ? tglIndo($pt->tanggal_berakhir_akreditasi) : '')}}</span>
                                                <input type="date" class="form-control editpt" value="{{$pt==null ? null : $pt->tanggal_berakhir_akreditasi}}" name="tglberakhirakreditasi" id="tglberakhirakreditasi" style="display:none;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">No. SK Akreditasi</th>
                                            <td>
                                                <span class="showpt">{{$pt==null ? '-' : $pt->no_sk_akreditasi}}</span>
                                                <input type="text" class="form-control editpt" name="nosk_akreditasi" value="{{$pt==null ? null : $pt->no_sk_akreditasi}}" id="nosk_akreditasi" title="Isian maksimal 100 karakter" style="display:none;">
                                            </td>
                                            <th class="text-primary">File Sertifikasi Akreditasi</th>
                                            <td>
                                                <span class="showpt">
                                                    <a href="{{ $file_akred }}" target="_blank" class="text-success">{{$pt==null ? '-' : $pt->file_sertifikat_akreditasi}}</a>
                                                </span>
                                                <div class="editpt" style="display: none;">
                                                    <span class="text-success">
                                                        @if($pt)
                                                            @if($pt->file_sertifikat_akreditasi)
                                                                <a href="{{ $file_akred }}" target="_blank" class="text-success">{{$pt->file_sertifikat_akreditasi}}</a>
                                                            @endif
                                                        @endif
                                                    </span>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                id="file_akreditasi" name="file_akreditasi"
                                                                accept=".pdf,.jpg,.jpeg">
                                                            <label class="custom-file-label" for="file_akreditasi">Choose
                                                                file</label>
                                                        </div>
                                                    </div>
                                                    <span class="text-primary">jpg,jpeg,pdf (maxsize: 2 MB)</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-success" colspan="4" style="font-size: 20px;">Informasi
                                                Universitas Tiga Serangkai</th>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Visi</th>
                                            <td colspan="3">
                                                <span class="showpt">{!! ($pt==null) ? '-' : $pt->visi !!}</span>
                                                <textarea class="summernote editpt" id="visi" name="visi" style="display: none;">
                                                    {!! ($pt==null) ? null : $pt->visi !!}
                                                </textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Misi</th>
                                            <td colspan="3">
                                                <span class="showpt">{!! ($pt==null) ? '-' : $pt->misi !!}</span>
                                                <textarea class="summernote editpt" id="misi" name="misi" style="display: none;">
                                                    {!! ($pt==null) ? null : $pt->misi !!}
                                                </textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-success" colspan="4" style="font-size: 20px;">Kontak
                                                Universitas Tiga Serangkai</th>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Alamat</th>
                                            <td colspan="3">
                                                <span class="showpt">{{$pt==null ? '-' : $pt->alamat}}</span>
                                                <input type="text" class="form-control editpt" value="{{$pt==null ? null : $pt->alamat}}" name="alamat" id="alamat" title="Isian maksimal 100 karakter" style="display:none;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Telepon</th>
                                            <td colspan="3">
                                                <span class="showpt">{{$pt==null ? '-' : $pt->telepon}}</span>
                                                <input type="text" class="form-control editpt" value="{{$pt==null ? null : $pt->telepon}}" name="telp" id="telp" title="Isian maksimal 20 karakter" style="display:none;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Alamat Email</th>
                                            <td colspan="3">
                                                <span class="showpt">{{$pt==null ? '-' : $pt->alamat_email}}</span>
                                                <input type="text" class="form-control editpt" value="{{$pt==null ? null : $pt->alamat_email}}" name="alamatemail" id="alamatemail" title="Isian maksimal 100 karakter" style="display:none;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Alamat Website</th>
                                            <td colspan="3">
                                                <span class="showpt">{{$pt==null ? '-' : $pt->alamat_website}}</span>
                                                <input type="text" class="form-control editpt" value="{{$pt==null ? null : $pt->alamat_website}}" name="alamatweb" id="alamatweb" title="Isian maksimal 100 karakter" style="display:none;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-primary">Fax</th>
                                            <td colspan="3">
                                                <span class="showpt">{{$pt==null ? '-' : $pt->fax}}</span>
                                                <input type="text" class="form-control editpt" value="{{$pt==null ? null : $pt->fax}}" name="fax" id="fax" title="Isian maksimal 100 karakter" style="display:none;">
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-4"><label>Website</label><input disabled name="website" id="website"
                                        class="form-control form-input"></div>
                            </div>

                            <hr>

                            <div class="text-right d-none" id="form-action">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <button type="button" class="btn btn-secondary" id="btn-cancel">Batal</button>
                            </div>

                            </form>
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

                    let selectedRektor = @json($rektor);
                    let selectedWR1 = @json($wr1);
                    let selectedWR2 = @json($wr2);
                    let selectedWR3 = @json($wr3);
                    let selectedWR4 = @json($wr4);
                    // notifalert('success','Berhasil save','success')

                    // $('#loading').show()

                    loadEvent()

                    function loadEvent() {
                        edit()
                        batal()
                        save()
                    }

                    function edit() {
                        $('#btn-edit').click(function(e) {
                            e.preventDefault();
                            $('#btn-batal').show()
                            $('#btn-simpan').show()
                            $(this).hide()
                            $('.editpt').show()
                            $('.showpt').hide()

                            initSelect2Statis()

                            initRektorSelect2();
                            setSelectedRektor()

                            initWR1Select2();
                            setSelectedWR1()

                            initWR2Select2();
                            setSelectedWR2()

                            initWR3Select2();
                            setSelectedWR3()

                            initWR4Select2();
                            setSelectedWR4()

                            $('.summernote').each(function() {
                                if (!$(this).next('.note-editor').length) {
                                    $(this).summernote({
                                        height: 200
                                    });
                                }
                            });
                        });

                    }

                    function initSelect2Statis() {
                        $('.select2').not('#rektor').each(function() {
                            if (!$(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2({
                                    width: '100%'
                                    // minimumResultsForSearch: 0 // 🔥 paksa ada search
                                });
                            }
                        });
                    }

                    function initRektorSelect2() {
                        $('#rektor').select2({
                            width: '100%',
                            placeholder: 'Cari NIP/Nama Rektor',
                            minimumInputLength: 2,
                            ajax: {
                                url: "{{ route('perguruan_tinggi.perguruan_tinggi.caripegawai') }}",
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

                    function setSelectedRektor() {
                        if (!selectedRektor) return;

                        let option = new Option(
                            selectedRektor.text,
                            selectedRektor.id,
                            true,
                            true
                        );
                        $('#rektor').append(option).trigger('change');
                    }

                    function initWR1Select2() {
                        $('#wr1').select2({
                            width: '100%',
                            placeholder: 'Cari NIP/Nama Wakil Rektor 1',
                            minimumInputLength: 2,
                            ajax: {
                                url: "{{ route('perguruan_tinggi.perguruan_tinggi.caripegawai') }}",
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

                    function setSelectedWR1() {
                        if (!selectedWR1) return;

                        let option = new Option(
                            selectedWR1.text,
                            selectedWR1.id,
                            true,
                            true
                        );
                        $('#wr1').append(option).trigger('change');
                    }

                    function initWR2Select2() {
                        $('#wr2').select2({
                            width: '100%',
                            placeholder: 'Cari NIP/Nama Wakil Rektor 2',
                            minimumInputLength: 2,
                            ajax: {
                                url: "{{ route('perguruan_tinggi.perguruan_tinggi.caripegawai') }}",
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

                    function setSelectedWR2() {
                        if (!selectedWR2) return;

                let option = new Option(
                    selectedWR2.text,
                    selectedWR2.id,
                    true,
                    true
                );
                $('#wr2').append(option).trigger('change');
            }

                    function initWR3Select2() {
                        $('#wr3').select2({
                            width: '100%',
                            placeholder: 'Cari NIP/Nama Wakil Rektor 3',
                            minimumInputLength: 2,
                            ajax: {
                                url: "{{ route('perguruan_tinggi.perguruan_tinggi.caripegawai') }}",
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

                    function setSelectedWR3() {
                        if (!selectedWR3) return;

                let option = new Option(
                    selectedWR3.text,
                    selectedWR3.id,
                    true,
                    true
                );
                $('#wr3').append(option).trigger('change');
            }

                    function initWR4Select2() {
                        $('#wr4').select2({
                            width: '100%',
                            placeholder: 'Cari NIP/Nama Wakil Rektor 4',
                            minimumInputLength: 2,
                            ajax: {
                                url: "{{ route('perguruan_tinggi.perguruan_tinggi.caripegawai') }}",
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

                    function setSelectedWR4() {
                        if (!selectedWR4) return;

                let option = new Option(
                    selectedWR4.text,
                    selectedWR4.id,
                    true,
                    true
                );
                $('#wr4').append(option).trigger('change');
            }

                    function batal() {
                        $('#btn-batal').click(function(e) {
                            e.preventDefault();
                            $('.select2').each(function() {
                                if ($(this).hasClass('select2-hidden-accessible')) {
                                    $(this).select2('destroy');
                                }
                            });
                            if ($('#rektor').hasClass('select2-hidden-accessible')) {
                                $('#rektor').val(null).trigger('change');
                                $('#rektor option').not(':first').remove();
                                $('#rektor').select2('destroy');
                            }
                            if ($('#wr1').hasClass('select2-hidden-accessible')) {
                                $('#wr1').val(null).trigger('change');
                                $('#wr1 option').not(':first').remove();
                                $('#wr1').select2('destroy');
                            }
                            if ($('#wr2').hasClass('select2-hidden-accessible')) {
                                $('#wr2').val(null).trigger('change');
                                $('#wr2 option').not(':first').remove();
                                $('#wr2').select2('destroy');
                            }
                            if ($('#wr3').hasClass('select2-hidden-accessible')) {
                                $('#wr3').val(null).trigger('change');
                                $('#wr3 option').not(':first').remove();
                                $('#wr3').select2('destroy');
                            }
                            if ($('#wr4').hasClass('select2-hidden-accessible')) {
                                $('#wr4').val(null).trigger('change');
                                $('#wr4 option').not(':first').remove();
                                $('#wr4').select2('destroy');
                            }
                            $('.summernote').each(function() {
                                if ($(this).next('.note-editor').length) {
                                    $(this).summernote('destroy');
                                }
                            });
                            $('.editpt').hide()
                            $('.showpt').show()
                            $(this).hide()
                            $('#btn-simpan').hide()
                            $('#btn-edit').show()
                        });
                    }

                    function validasi() {
                        let kodeunit = $('#kodeunit').val()
                        let namaunit = $('#namaunit').val()
                        let namauniten = $('#namaunit_en').val()
                        let namasingkat = $('#namasingkat').val()
                        let jenispt = $('#jenis_pt').val()
                        let naungan = $('#lembaga_naungan').val()
                        let fileku = $('#file_akreditasi').prop('files')[0];

                        let notif = '';
                        let fileSize = 0
                        if (fileku) {
                            fileSize = fileku.size
                        }
                        if (kodeunit == '' || kodeunit == null) {
                            notif = 'Kode Unit Harus diisi !';
                        } else if (namaunit == '' || namaunit == null) {
                            notif = 'Nama Unit Harus diisi !';
                        } else if (namauniten == '' || namauniten == null) {
                            notif = 'Nama Unit (EN) Harus diisi !';
                        } else if (namasingkat == '' || namasingkat == null) {
                            notif = 'Nama Singkat Harus diisi !';
                        } else if (jenispt == '' || jenispt == null) {
                            notif = 'Jenis Perguruan Tinggi Harus diisi !';
                        } else if (naungan == '' || naungan == null) {
                            notif = 'Lembaga Naungan Harus diisi !';
                        } else if (fileSize > 2 * 1024 * 1024) {
                            notif = 'Ukuran File Sertifikasi Akreditasi tidak boleh lebih dari 2MB !'
                        } else {
                            notif = 'ok';
                        }

                        return notif;

                    }

                    function save() {
                        $('#btn-simpan').click(function(e) {
                            e.preventDefault();
                            let validasiku = validasi()
                            if (validasiku != 'ok') {
                                notifalert('Information', validasiku, 'warning')
                            } else {
                                Swal.fire({
                                    title: "Information",
                                    text: "Apakah Data Perguruan Tinggi Sudah Benar ?",
                                    icon: "question",
                                    showConfirmButton: true,
                                    showCancelButton: true,
                                }).then((result) => {
                                    if (result.value) {
                                        $(this).prop('disabled', true)
                                        $('#loading').show()
                                        $('#form-edit-pt').submit();
                                    } else {
                                        return false;
                                    }
                                });
                            }
                        });
                    }

                    function resetForm() {
                        $('#form-perguruan-tinggi')[0].reset();
                    }

                });
            </script>
        @endsection
