<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_PeriodeAkademik;
use App\Models\MasterData\Master_TahunAjaran;
use App\Models\PegawaiModel;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PeriodeAkademikController extends Controller
{
    public function index(Request $request)
    {

        $ta = Master_TahunAjaran::where('aktif',1)->where('isactive',1)->get();

        $data = array(
            'title' => 'Periode Akademik',
            'menu'  => 'Periode Akademik',
            'ta'    => $ta
        );
        return view('system::masterdata.periodeAkademik.index', $data);
    }

    function tabel()
    {
        $query = Master_PeriodeAkademik::where('isactive',1)->orderBy('kode_periode', 'desc'); // Biasanya periode terbaru di atas

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('kode', function ($q) {
                return $q->kode_periode;
            })
            ->editColumn('nama', function ($q) {
                return $q->nama_periode;
            })
            ->editColumn('mulai_kuliah', function ($q) {
                return tglIndo($q->tgl_awal_kuliah);
            })
            ->editColumn('akhir_kuliah', function ($q) {
                return tglIndo($q->tgl_akhir_kuliah);
            })
            ->editColumn('awal_uts', function ($q) {
                return tglIndo($q->tgl_awal_uts);
            })
            ->editColumn('awal_uas', function ($q) {
                return tglIndo($q->tgl_akhir_uts);
            })
            ->editColumn('aktif', function ($q) {
                return $q->aktif==1
                    ? '<div class="text-center text-success" title="Aktif"><i class="fas fa-check"></i></div>'
                    : '<div class="text-center text-danger" title="Non Aktif"><i class="fas fa-times"></i></div>';
            })
            ->addColumn('action', function ($q) {
                $id = $q->id;
                if($q->aktif==1){
                    // Tombol Set Aktif (merah)
                    $btn = '<button class="btn btn-danger btn-sm btn_aktif" data-id="'.$id.'" data-aktif="0" title="Set Non Aktif"><i class="fas fa-times"></i></button>';
                }else{
                    // Tombol Set Aktif (Hijau)
                    $btn = '<button class="btn btn-success btn-sm btn_aktif" data-id="'.$id.'" data-aktif="1" title="Set Aktif"><i class="fas fa-check"></i></button>';
                }
                // Tombol Detail (Biru)
                $btn .= ' <button class="btn btn-info btn-sm btn_detail" data-id="'.$id.'" data-status="detail" title="Detail"><i class="fas fa-eye"></i></button>';
                // Tombol Edit (kuning)
                $btn .= ' <button class="btn btn-warning btn-sm btn_edit" data-id="'.$id.'" data-status="edit" title="Edit"><i class="fas fa-edit"></i></button>';
                // Tombol Hapus (Merah)
                $btn .= ' <button class="btn btn-danger btn-sm btn_hapus" data-id="'.$id.'" title="Hapus"><i class="fas fa-trash"></i></button>';
                return '<div class="text-center">'.$btn.'</div>';
            })
            ->rawColumns(['aktif', 'action'])
            ->make(true);
    }

    public function search(Request $request)
    {
        $q = $request->q;

        $data = PegawaiModel::where('nama', 'LIKE', "%{$q}%")
            ->orWhere('nip', 'LIKE', "%{$q}%")
            ->limit(10)
            ->get();

        return response()->json(
            $data->map(function ($row) {
                return [
                    'id' => $row->nip,
                    'text' => $row->nip.' - '.$row->nama
                ];
            })
        );
    }

    public function store(Request $post)
    {
        $id = $post->IdPeriode;
        $save = null;

        DB::beginTransaction();
        if($id){
            $save = $this->update($post);
        }else{
            $save = $this->save($post);
        }

        return response()->json($save, Response::HTTP_OK);
    }

    public function save($post)
    {
        $cekdate = Master_PeriodeAkademik::whereBetween('tgl_awal_kuliah',[$post->tglawal_kuliah,$post->tglakhir_kuliah])->where('isactive',1)->exists();
        $save = false;
        if($cekdate){
            $save = false;
            $data['title'] = 'Information';
            $data['message'] = 'Tanggal Awal Kuliah Sudah digunakan';
            $data['status'] = 'warning';
        }else{
            $array = array(
                'id_tahunajaran' => $post->tahun_ajaran,
                'semester' => $post->semester,
                'kode_periode' => $post->kode_periode,
                'nama_periode' => $post->nama_periode,
                'tgl_awal_kuliah' => $post->tglawal_kuliah,
                'tgl_akhir_kuliah' => $post->tglakhir_kuliah,
                'nama_singkat' => $post->nama_singkat,
                'tgl_awal_uts' => $post->tglawal_uts,
                'tgl_akhir_uts' => $post->tglakhir_uts,
                'tgl_awal_uas' => $post->tglawal_uas,
                'tgl_akhir_uas' => $post->tglakhir_uas,
                'ketua_ujian' => $post->ketua_ujian,
                'jumlah_pertemuan' => $post->jumlah_pertemuan_kuliah,
                'minimal_presensi' => $post->minimal_presensi,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => session('active_identity')
            );
            $save = Master_PeriodeAkademik::create($array);
        }

        if($save){
            DB::commit();
            $data['title'] = 'Berhasil';
            $data['message'] = 'Data Periode Berhasil Ditambahkan';
            $data['status'] = 'success';
        }else{
            DB::rollback();
            $data['title'] = 'Gagal';
            $data['message'] = 'Data Periode Gagal Ditambahkan';
            $data['status'] = 'error';
        }

        return $data;

    }

    public function update($post)
    {
        $id = $post->IdPeriode;
        $cekdate = Master_PeriodeAkademik::where('id','!=',$id)->whereBetween('tgl_awal_kuliah',[$post->tglawal_kuliah,$post->tglakhir_kuliah])->exists();
        $save = false;
        if($cekdate){
            $save = false;
            $data['title'] = 'Information';
            $data['message'] = 'Tanggal Awal Kuliah Sudah digunakan';
            $data['status'] = 'warning';
        }else{
            $array = array(
                'id_tahunajaran' => $post->tahun_ajaran,
                'semester' => $post->semester,
                'kode_periode' => $post->kode_periode,
                'nama_periode' => $post->nama_periode,
                'tgl_awal_kuliah' => $post->tglawal_kuliah,
                'tgl_akhir_kuliah' => $post->tglakhir_kuliah,
                'nama_singkat' => $post->nama_singkat,
                'tgl_awal_uts' => $post->tglawal_uts,
                'tgl_akhir_uts' => $post->tglakhir_uts,
                'tgl_awal_uas' => $post->tglawal_uas,
                'tgl_akhir_uas' => $post->tglakhir_uas,
                'ketua_ujian' => $post->ketua_ujian,
                'jumlah_pertemuan' => $post->jumlah_pertemuan_kuliah,
                'minimal_presensi' => $post->minimal_presensi,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('active_identity')
            );
            $save = Master_PeriodeAkademik::where('id',$id)->update($array);
        }
        if($save){
            DB::commit();
            $data['title'] = 'Berhasil';
            $data['message'] = 'Data Periode Berhasil Diperbarui';
            $data['status'] = 'success';
        }else{
            DB::rollback();
            $data['title'] = 'Gagal';
            $data['message'] = 'Data Periode Gagal Diperbarui';
            $data['status'] = 'error';
        }

        return $data;
    }

    public function edit($params,$status)
    {
        $cek = Master_PeriodeAkademik::with('tahun_ajaran')->find($params);
        if($cek){
            $data['hasil'] = 1;
            if($status=='edit'){
                $data['periode'] = $cek;
                $data['ketuaujian'] = $cek->ketua_ujian ? array(
                    'id'   => $cek->ketua_ujian,
                    'text' => $cek->ketua_ujian.' - '.namapegawai($cek->ketua_ujian)
                ) : null;
            }else{
                $data['periode'] = $cek;
                $data['ketuaujian'] = $cek->ketua_ujian ? $cek->ketua_ujian.' - '.namapegawai($cek->ketua_ujian) : null;
            }
        }else{
            $data['hasil'] = 0;
            $data['periode'] = $cek;
        }
        // dd($data);
        return response()->json($data, Response::HTTP_OK);
    }

    // LOGIC PENTING: SET AKTIF
    public function setActive($params,$status)
    {
        DB::beginTransaction();

        try {
            $periode = Master_PeriodeAkademik::find($params);
            $periode->aktif = $status;
            $periode->updated_at = date('Y-m-d H:i:s');
            $periode->updated_by = session('active_identity');
            $periode->save();

            DB::commit();
            $data['title'] = 'Berhasil';
            $data['message'] = $status=='0' ? 'Periode '.$periode->nama_periode.' Berhasil di Nonaktifkan' : 'Periode '.$periode->nama_periode.' Berhasil Diaktifkan';
            $data['status'] = 'success';
            return response()->json($data,Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            $data['title'] = 'Gagal';
            $data['message'] = $status=='0' ? 'Periode '.$periode->nama_periode.' Gagal di Nonaktifkan' : 'Periode '.$periode->nama_periode.' Gagal Diaktifkan';
            $data['status'] = 'error';
            return response()->json($data,Response::HTTP_OK);
        }
    }

    public function destroy($params)
    {
        $data = Master_PeriodeAkademik::find($params);
        if($data->aktif=='1'){
            $data['title'] = 'Gagal';
            $data['message'] = 'Tidak boleh menghapus Periode yang sedang AKTIF!';
            $data['status'] = 'warning';
        }else{
            $array = array(
                'aktif' => 0,
                'isactive' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('active_identity')
            );
            $up = Master_PeriodeAkademik::where('id',$params)->update($array);
            if($up){
                $data['title'] = 'Berhasil';
                $data['message'] = 'Data periode Berhasil Dihapus';
                $data['status'] = 'success';
            }else{
                $data['title'] = 'Gagal';
                $data['message'] = 'Data Periode Gagal dihapus';
                $data['status'] = 'error';
            }
        }
        return response()->json($data,Response::HTTP_OK);
    }
}
