<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = Auth::id();
            $pesanan_user = DB::table('ta_final_booking')
                ->where('id_user', $userId)
                ->orderBy('created_at', 'DESC')
                ->get();

            return Datatables::of($pesanan_user)
                ->addIndexColumn()
                ->addColumn('status_pesanan', function ($item) {
                    if ($item->status_final == 0) {
                        $status = '<button type="button" class="btn rounded-pill btn-outline-youtube waves-effect btn-xs"> <i class="tf-icons mdi mdi-close-circle me-1"></i>Belum Bayar</button>';
                    } else if ($item->status_final == 1) {
                        $status = '<button type="button" class="btn btn-outline-twitter waves-effect btn-xs"> <i class="tf-icons mdi mdi-check-decagram me-1">Pembayaran Diproses</i></button>';
                    } else if ($item->status_final == 2) {
                        $status = '<button type="button" class="btn btn-outline-whatsapp waves-effect btn-xs"> <i class="tf-icons mdi mdi-check-decagram me-1">Pembayaran Diterima</i></button>';
                    }
                    return $status;
                })
                ->addColumn('lama_inap', function ($item) {
                    $lama_inap = $item->total_menginap . ' Malam';
                    return $lama_inap;
                })
                ->addColumn('total_biaya', function ($item) {
                    if ($item->status_final == 0) {
                        $total = '<button type="button" class="btn btn-text-danger text-bold waves-effect waves-light">Rp. ' . number_format($item->final_biaya) . '</button>';
                    } else if ($item->status_final == 1) {
                        $total = '<button type="button" class="btn btn-text-warning text-bold waves-effect waves-light">Rp. ' . number_format($item->final_biaya) . '</button>';
                    } else if ($item->status_final == 2) {
                        $total = '<button type="button" class="btn btn-text-success text-bold waves-effect waves-light">Rp. ' . number_format($item->final_biaya) . '</button>';
                    }
                    return $total;
                })
                ->addColumn('action', function ($item) {
                    if ($item->status_final == 0) {
                        $btn = '<button type="button" data-id="' . $item->id_final_booking . '" data-no_booking="' . $item->no_booking . '" title="Detail data" class="btn btn-icon btn-primary waves-effect waves-light" id="BtnDetail"><span class="fa-solid fa-circle-info"></span></button>
                        <button type="button" data-id="' . $item->id_final_booking . '" data-no_booking="' . $item->no_booking . '" title="Upload Bukti Pembayaran" class="btn btn-icon btn-info waves-effect waves-light" id="BtnUploadPembayaran"><span class="mdi mdi-upload"></span></button>

                        ';
                    } else if ($item->status_final == 1) {
                        $total = '<button type="button" class="btn btn-text-warning text-bold waves-effect waves-light">Rp. ' . number_format($item->final_biaya) . '</button>';
                    } else if ($item->status_final == 2) {
                        $total = '<button type="button" class="btn btn-text-success text-bold waves-effect waves-light">Rp. ' . number_format($item->final_biaya) . '</button>';
                    }
                    // $btn = '<button type="button" data-id="' . $item->id_final_booking . '" data-no_booking="' . $item->no_booking . '" title="Detail data" class="btn btn-icon btn-primary waves-effect waves-light" id="BtnDetail"><span class="fa-solid fa-circle-info"></span></button>
                    // <button type="button" data-id="' . $item->id_final_booking . '" title="Hapus data" class="btn btn-icon btn-danger waves-effect waves-light" id="BtnHapus"><span class="fa-regular fa-trash-can"></span></button>
                    // ';
                    return $btn;
                })
                ->rawColumns(['action', 'status_pesanan', 'lama_inap', 'total_biaya'])
                ->make(true);
        }
        return view('user.pesanan.index', [
            // 'kabkota' => $city
        ]);
    }


    public function get_detail_pesanan(Request $request)
    {
        $data_booking = DB::table('ta_final_booking')
            ->join('users', 'users.id', '=', 'ta_final_booking.id_user')
            ->where('ta_final_booking.id_final_booking', $request->id)
            ->get();
        return response()->json($data_booking, 200);
    }

    public function list_booking(Request $request)
    {
        $no_booking = $request->no_booking;
        $booking =  DB::table('ta_booking as A')
            ->join('ms_kavling as B', 'A.id_kavling', '=', 'B.id_kavling')
            ->where('A.no_booking', '=', $no_booking)
            ->orderBy('A.id_booking', 'DESC')
            ->get();
        return response()->json($booking, 200);
    }
}
