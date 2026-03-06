<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_TahunAjaran;
use App\Models\MasterData\Master_PeriodeAkademik;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Tahun Ajaran',
            'menu' => 'Tahun Ajaran',
        );
        return view('system::masterdata.tahunajaran.index', $data);
    }

    public function tabel()
    {
        $query = Master_TahunAjaran::where('isactive',1)->orderBy('created_at', 'desc')->get();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama', function($q){
                    return $q->nama_tahun_ajaran;
                })
                ->editColumn('mulai', function ($q) {
                    return $q->tahun_mulai;
                })
                ->editColumn('selesai', function ($q) {
                    return $q->tahun_selesai;
                })
                ->editColumn('aktif', function ($q) {
                    $aktif = $q->aktif==1 ? '<div class="text-center text-success"><i class="fas fa-check"></i></div>' : '<div class="text-center text-danger"><i class="fas fa-times"></i></div>';
                    return $aktif;
                })
                ->addColumn('action', function ($q) {
                    $id = encrypt($q->id);
                    $btn = '';
                    // Tombol Set Aktif (Hijau)
                    if($q->aktif==0){
                        $btn .= '<button class="btn btn-success btn-sm btn_aktif" data-aktif="1" data-id="'.$id.'" title="Set Aktif"><i class="fas fa-check"></i></button>';
                    }
                    // Tombol Set Tidak Aktif (merah)
                    if($q->aktif==1){
                        $btn .= '<button class="btn btn-danger btn-sm btn_aktif" data-aktif="0" data-id="'.$id.'" title="Set Non Aktif"><i class="fas fa-times"></i></button>';
                    }
                    // Tombol Edit/Detail (Biru)
                    $btn .= ' <button class="btn btn-warning btn-sm btn_edit" data-id="'.$id.'" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    // Tombol Hapus (Merah)
                    $btn .= ' <button type="button" class="btn btn-danger btn-sm btn_hapus" data-id="'.$id.'" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['aktif', 'action'])
                ->make(true);
    }

    public function create(Request $post)
    {
        $id = $post->IdTahunAjaran;

        $save = false;
        DB::beginTransaction();
        if($id){
            $up = array(
                'nama_tahun_ajaran' => $post->nama_tahunajaran,
                'tahun_mulai' => $post->tahunmulai,
                'tahun_selesai' => $post->tahunselesai,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('active_identity')
            );
            $save = Master_TahunAjaran::where('id',$id)->update($up);
        }else{
            $up = array(
                'nama_tahun_ajaran' => $post->nama_tahunajaran,
                'tahun_mulai' => $post->tahunmulai,
                'tahun_selesai' => $post->tahunselesai,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => session('active_identity')
            );
            $save = Master_TahunAjaran::create($up);
        }

        if($save){
            DB::commit();
            $data['title'] = 'Berhasil';
            $data['message'] = $id ? 'Data Tahun Ajaran berhasil diperbarui':'Data Tahun Ajaran berhasil disimpan';
            $data['status'] = 'success';
        }else{
            DB::rollback();
            $data['title'] = 'Gagal';
            $data['message'] = $id ? 'Data Tahun Ajaran Gagal diperbarui !':'Data Tahun Ajaran Gagal disimpan !';
            $data['status'] = 'error';
        }

        return response()->json($data, Response::HTTP_OK);
    }

    public function show($params)
    {
        $id = decrypt($params);
        $cek = Master_TahunAjaran::where('id',$id)->where('isactive',1)->first();
        if($cek){
            $data['hasil'] = 1;
            $data['tahunajaran'] = $cek;
        }else{
            $data['hasil'] = 0;
            $data['tahunajaran'] = $cek;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function aktifnonaktif($params1,$params2)
    {
        $id = decrypt($params1);

        $cek = Master_TahunAjaran::where('id',$id)->where('isactive',1)->exists();
        if($cek){
            $update = false;
            DB::beginTransaction();
            $array = array(
                'aktif' => $params2,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('active_identity')
            );
            $update = Master_TahunAjaran::where('id',$id)->update($array);
            if($params2==1){
                Master_TahunAjaran::where('id','!=',$id)->where('isactive',1)->where('aktif',1)->update([
                    'aktif' => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => session('active_identity')
                ]);
            }

            if($update){
                DB::commit();
                $data['title'] = 'Berhasil';
                $data['message'] = $params2==1 ? 'Data Tahun Ajaran berhasil diaktifkan':'Data Tahun Ajaran berhasil dinonaktifkan';
                $data['status'] = 'success';
            }else{
                DB::rollBack();
                $data['title'] = 'Gagal';
                $data['message'] = $params2==1 ? 'Data Tahun Ajaran gagal diaktifkan':'Data Tahun Ajaran gagal dinonaktifkan';
                $data['status'] = 'error';
            }
        }else{
            $data['title'] = 'Gagal';
            $data['message'] = 'Data Tahun Ajaran tidak ada ! Silahkan Hubungi PIKDI';
            $data['status'] = 'success';
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function delete($params)
    {
        $id = decrypt($params);
        $cek = Master_TahunAjaran::where('id',$id)->where('isactive',1)->first();
        if($cek){
            $cekperiode = Master_PeriodeAkademik::where('id_tahunajaran',$id)->exists();
            if($cekperiode){
                $data['title'] = 'Information';
                $data['message'] = 'Data Tahun Ajaran Sudah Ada Periode Akademik !';
                $data['status'] = 'warning';
            }else{
                $update = false;
                DB::beginTransaction();
                $array = array(
                    'isactive' => '0',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => session('active_identity')
                );
                $update = Master_TahunAjaran::where('id',$id)->where('isactive',1)->update($array);
                if($update){
                    DB::commit();
                    $data['title'] = 'Berhasil';
                    $data['message'] = 'Data Tahun Ajaran berhasil dihapus';
                    $data['status'] = 'success';
                }else{
                    DB::rollBack();
                    $data['title'] = 'Gagal';
                    $data['message'] = 'Data Tahun Ajaran Gagal dihapus';
                    $data['status'] = 'error';
                }
            }
        }else{
            $data['title'] = 'Gagal';
            $data['message'] = 'Data Tahun Ajaran tidak ada ! Silahkan Hubungi PIKDI';
            $data['status'] = 'success';
        }
        return response()->json($data, Response::HTTP_OK);
    }
}
