<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_DataPerguruanTinggi;
use App\Models\MasterData\Master_JenisPerguruanTinggi;
use App\Models\MasterData\Master_LembagaNaungan;
use App\Models\MasterData\Master_PeringkatAkreditasi;
use App\Models\PegawaiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DataPerguruanTinggiController extends Controller
{
    public function index()
    {
        $pt = Master_DataPerguruanTinggi::first();
        $lembaga = Master_LembagaNaungan::where('isactive',1)->get();
        $jenispt = Master_JenisPerguruanTinggi::where('isactive',1)->get();
        $peringkatAK = Master_PeringkatAkreditasi::where('isactive',1)->get();
        $rektor = null;
        $wr1 = null;
        $wr2 = null;
        $wr3 = null;
        $wr4 = null;
        $linkfileakreditasi = null;

        if($pt){
            $cekrektor = PegawaiModel::where('nip',$pt->rektor)->select('nip','nama')->first();
            if($cekrektor){
                $rektor = array(
                    'id'   => $cekrektor->nip,
                    'text' => $cekrektor->nip.' - '.$cekrektor->nama
                );
            }

            $cekwr1 = PegawaiModel::where('nip',$pt->wakil_rektor1)->select('nip','nama')->first();
            if($cekwr1){
                $wr1 = array(
                    'id'   => $cekwr1->nip,
                    'text' => $cekwr1->nip.' - '.$cekwr1->nama
                );
            }

            $cekwr2 = PegawaiModel::where('nip',$pt->wakil_rektor2)->select('nip','nama')->first();
            if($cekwr2){
                $wr2 = array(
                    'id'   => $cekwr2->nip,
                    'text' => $cekwr2->nip.' - '.$cekwr2->nama
                );
            }

            $cekwr3 = PegawaiModel::where('nip',$pt->wakil_rektor3)->select('nip','nama')->first();
            if($cekwr3){
                $wr3 = array(
                    'id'   => $cekwr3->nip,
                    'text' => $cekwr3->nip.' - '.$cekrektor->nama
                );
            }

            $cekwr4 = PegawaiModel::where('nip',$pt->wakil_rektor4)->select('nip','nama')->first();
            if($cekwr4){
                $wr4 = array(
                    'id'   => $cekwr4->nip,
                    'text' => $cekwr4->nip.' - '.$cekrektor->nama
                );
            }

            if($pt->file_sertifikat_akreditasi){
                $linkfileakreditasi = asset('sources/storage/app/public/FILE_AKREDITASI/'.$pt->file_sertifikat_akreditasi);
            }
        }
        $data = array(
            'title' => 'Master Data Perguruan Tinggi',
            'menu'  => 'Perguruan Tinggi',
            'pt'    => $pt,
            'lembaga' => $lembaga,
            'peringkatAK' => $peringkatAK,
            'jenispt' => $jenispt,
            'rektor' => $rektor,
            'wr1' => $wr1,
            'wr2' => $wr2,
            'wr3' => $wr3,
            'wr4' => $wr4,
            'file_akred' => $linkfileakreditasi
        );
        // dd($data);
        return view('system::masterdata.dataPerguruanTinggi.index', $data);
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

    public function save(Request $post)
    {
        $jenispt = decrypt($post->jenis_pt);
        $naungan = decrypt($post->lembaga_naungan);

        $filename = null;
        if($post->hasFile('file_akreditasi')){
            $file = $post->file('file_akreditasi');
            $ext = $file->getClientOriginalExtension();
            $filename = 'DOKUMEN_AKREDITASI_TSU_'.date('YmdHis').'.'.$ext;
            // $file->storeAs('FILE_AKREDITASI', $filename,'public');
            $destinationPath = base_path('storage/app/public/FILE_AKREDITASI');

            $file->move($destinationPath, $filename);
        }else{
            $cekfile = Master_DataPerguruanTinggi::where('id',$post->idpt)->select('id','file_sertifikat_akreditasi')->first();
            $filename = $cekfile->file_sertifikat_akreditasi;
        }

        $array = array(
            'kode_unit' => $post->kodeunit,
            'nama_unit' => $post->namaunit,
            'nama_unit_en' => $post->namaunit_en,
            'nama_singkat' => $post->namasingkat,
            'jenis_perguruan_tinggi' => $jenispt,
            'lembaga_naungan' => $naungan,
            'unit_satuan_kerja' => $post->unitsatuankerja,
            'periode_berdiri' => $post->periode_berdiri,
            'no_sk_pendirian' => $post->noskpendirian,
            'tanggal_sk_pendirian' => $post->tglskpendirian,
            'rektor' => $post->rektor ? $post->rektor : null,
            'wakil_rektor1' => $post->wr1 ? $post->wr1 : null,
            'wakil_rektor2' => $post->wr2 ? $post->wr2 : null,
            'wakil_rektor3' => $post->wr3 ? $post->wr3 : null,
            'wakil_rektor4' => $post->wr4 ? $post->wr4 : null,
            'lembaga_akreditasi' => $post->lembaga_akreditasi,
            'peringkat_akreditasi' => $post->peringkat_akreditasi ? decrypt($post->peringkat_akreditasi) : null,
            'nilai_akreditasi' => $post->nilai_akreditasi,
            'no_sk_akreditasi' => $post->nosk_akreditasi,
            'tanggal_sk_akreditasi' => $post->tglskakreditasi,
            'tanggal_berlaku_akreditasi' => $post->tglberlakuakreditasi,
            'tanggal_berakhir_akreditasi' => $post->tglberakhirakreditasi,
            'file_sertifikat_akreditasi' => $filename,
            'visi' => $post->visi,
            'misi' => $post->misi,
            'alamat' => $post->alamat,
            'telepon' => $post->telp,
            'alamat_email' => $post->alamatemail,
            'alamat_website' => $post->alamatweb,
            'fax' => $post->fax,
            'created_at' => now(),
            'created_by' => session('active_identity'),
            'updated_at' => now(),
            'updated_by' => session('active_identity'),
        );

        $save = null;
        if($post->idpt){
            $save = Master_DataPerguruanTinggi::where('id',$post->idpt)->update($array);
        }else{
            $save = Master_DataPerguruanTinggi::create($array);
        }

        if($save){
            $alert = ['title' => 'Berhasil', 'message' => 'Data Perguruan Tinggi Berhasil diperbarui', 'status' => 'success'];
        }else{
            $alert = ['title' => 'Gagal', 'message' => 'Data Perguruan Tinggi Gagal diperbarui', 'status' => 'error'];
        }

        return redirect()->back()->with('alert',$alert);
    }
}
