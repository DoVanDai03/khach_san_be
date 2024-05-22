<?php

namespace App\Http\Controllers;

use App\Models\ChiTietThuePhong;
use App\Models\LoaiPhong;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChiTietThuePhongController extends Controller
{
    public function createData(Request $request)
    {
        $today  =   Carbon::today();

        $ngayCuoiNam    = Carbon::now()->endOfMonth();

        while($today <= $ngayCuoiNam) {
            ChiTietThuePhong::firstOrCreate(
                [
                    'id_phong'      =>  $request->id,
                    'ngay_thue'     =>  $today,
                ],
                [
                    'id_phong'      =>  $request->id,
                    'gia_thue'      =>  $request->gia_mac_dinh,
                    'tinh_trang'    =>  1,
                    'ngay_thue'     =>  $today,
                ]
            );
            $today->addDay();
        }

        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo chi tiết phòng thành công!',
        ]);
    }
    public function getData()
    {

        $data = ChiTietThuePhong::get();
        return response()->json([
            'data' => $data
        ]);
    }
    public function UpdateData(Request $request)
    {
        $data   = $request->all();

        ChiTietThuePhong::find($request->id)->update($data);

        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã cập nhật phòng thành công!'
        ]);
    }

    public function data()
    {
        $data = ChiTietThuePhong::select('ngay_thue', 'tinh_trang', DB::raw("COUNT(id) as tong"))
                                ->groupBy('ngay_thue', 'tinh_trang')
                                ->orderBy('ngay_thue')
                                ->get();

        $newData = ChiTietThuePhong::select('ngay_thue')
                                   ->groupBy('ngay_thue')
                                   ->orderBy('ngay_thue')
                                   ->get();

        foreach($newData as $k1 => $v1) {
            foreach($data as $k2 => $v2) {
                if($v1->ngay_thue == $v2->ngay_thue && $v2->tinh_trang == 0) {
                    $v1->phong_sua = $v2->tong;
                } else if($v1->ngay_thue == $v2->ngay_thue && $v2->tinh_trang == 1) {
                    $v1->phong_trong = $v2->tong;
                } else if($v1->ngay_thue == $v2->ngay_thue && $v2->tinh_trang == 2) {
                    $v1->phong_coc = $v2->tong;
                } else if($v1->ngay_thue == $v2->ngay_thue && $v2->tinh_trang == 3) {
                    $v1->phong_co_nguoi = $v2->tong;
                }
            }
        }

        return response()->json([
            'data' => $newData
        ]);
    }

    public function danhSachHienThi(Request $request)
    {
        $xxx = ChiTietThuePhong::join('phongs', 'chi_tiet_thue_phongs.id_phong', 'phongs.id')
                                ->join('loai_phongs', 'phongs.id_loai_phong', 'loai_phongs.id')
                                ->where('chi_tiet_thue_phongs.tinh_trang', 1)
                                ->where('loai_phongs.tinh_trang', 1)
                                ->whereDate('chi_tiet_thue_phongs.ngay_thue', '<=', $request->ngay_di)
                                ->whereDate('chi_tiet_thue_phongs.ngay_thue', '>=', $request->ngay_den)
                                ->select(
                                        'loai_phongs.id',
                                        'loai_phongs.ten_loai_phong',
                                        'loai_phongs.so_giuong',
                                        'loai_phongs.so_nguoi_lon',
                                        'loai_phongs.so_tre_em',
                                        'loai_phongs.dien_tich',
                                        'loai_phongs.hinh_anh',
                                        'loai_phongs.tien_ich',
                                        'chi_tiet_thue_phongs.ngay_thue',
                                        DB::raw("COUNT(chi_tiet_thue_phongs.id) as so_phong_trong"),
                                    )
                                ->groupBy(
                                        'loai_phongs.id',
                                        'loai_phongs.ten_loai_phong',
                                        'loai_phongs.so_giuong',
                                        'loai_phongs.so_nguoi_lon',
                                        'loai_phongs.so_tre_em',
                                        'loai_phongs.dien_tich',
                                        'loai_phongs.hinh_anh',
                                        'loai_phongs.tien_ich',
                                        'chi_tiet_thue_phongs.ngay_thue'
                                )
                                ->get();
        $yyy    =   ChiTietThuePhong::join('phongs', 'chi_tiet_thue_phongs.id_phong', 'phongs.id')
                                    ->join('loai_phongs', 'phongs.id_loai_phong', 'loai_phongs.id')
                                    ->where('chi_tiet_thue_phongs.tinh_trang', 1)
                                    ->where('loai_phongs.tinh_trang', 1)
                                    ->whereDate('chi_tiet_thue_phongs.ngay_thue', '<=', $request->ngay_di)
                                    ->whereDate('chi_tiet_thue_phongs.ngay_thue', '>=', $request->ngay_den)
                                    ->select(
                                            'loai_phongs.id',
                                            'loai_phongs.ten_loai_phong',
                                            'loai_phongs.so_giuong',
                                            'loai_phongs.so_nguoi_lon',
                                            'loai_phongs.so_tre_em',
                                            'loai_phongs.dien_tich',
                                            'loai_phongs.hinh_anh',
                                            'loai_phongs.tien_ich',
                                            DB::raw("AVG(chi_tiet_thue_phongs.gia_thue) as gia_trung_binh"),
                                        )
                                    ->groupBy(
                                            'loai_phongs.id',
                                            'loai_phongs.ten_loai_phong',
                                            'loai_phongs.so_giuong',
                                            'loai_phongs.so_nguoi_lon',
                                            'loai_phongs.so_tre_em',
                                            'loai_phongs.dien_tich',
                                            'loai_phongs.hinh_anh',
                                            'loai_phongs.tien_ich',
                                    )
                                    ->get();


        $data   = LoaiPhong::get();

        foreach ($data as $key => $value) {
            $value->so_phong_trong = 99999999;
            foreach($xxx as $key_1 => $value_1) {
                if($value->id == $value_1->id) {
                    $value->so_phong_trong = min($value->so_phong_trong, $value_1->so_phong_trong);
                }
            }
            foreach($yyy as $key_2 => $value_2) {
                if($value->id == $value_2->id) {
                    $value->gia_trung_binh = number_format($value_2->gia_trung_binh, 0);
                }
            }
        }

        return response()->json([
            'data'  => $data,
            'xxx'   => $xxx
        ]);
    }

}
