<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_ContactPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ContactPersonController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Contact Person";
        $data['menu']  = "Contact Person";

        if ($request->ajax()) {
            $query = Master_ContactPerson::query()->orderBy('nama', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn  = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-sm btn_edit" title="Edit">';
                    $btn .= '<i class="fas fa-pencil-alt"></i></button> ';
                    $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn_hapus" title="Hapus">';
                    $btn .= '<i class="fas fa-trash"></i></button>';

                    return '<div class="text-center">' . $btn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.contact_person.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:150',
            'jenis_kelamin' => 'required|string|max:20',
            'no_telepon'    => 'required|string|max:30',
            'alamat_email' => 'required|email|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_ContactPerson::updateOrCreate(
            ['id' => $request->id],
            [
                'nama'          => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_telepon'    => $request->no_telepon,
                'alamat_email' => $request->alamat_email,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Contact Person berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_ContactPerson::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_ContactPerson::find($id);

        if ($data) {
            $data->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus!'
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }
}
