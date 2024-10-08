<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StokProduk;

class PenerimaanStokController extends Controller
{

    public function index(){
        return view('mart.stockinput.index');
    }


    public function createStok(){
        $dataProducts = Product::all();

        return view('mart.stockinput.add', compact('dataProducts'));
    }

    public function storeData(Request $request){
        if($request->ajax()){
            $typeOfStock = $request->typeOfStock;
            $currentStockProduct = StokProduk::create([
                'statusenabled' => true,
                'product_id' => $request->productId,
                'keterangan' => generate_keterangan_stok($typeOfStock == "new" ? "init" : "addtion_stock"),
                'stokawal' => $typeOfStock == "new" ? 0 : StokProduk::where("product_id", $request->product_id)->latest()->value('stok_akhir'),
                'qtyin' => $request->quantityIn,
                'qtyout' => 0,
                'stok_akhir' => $typeOfStock == "new" ? $request->quantityIn : StokProduk::where("product_id", $request->product_id)->latest()->value('stok_akhir') + $request->quantityIn,
            ]);

            return response()->json([
                "message" => "success, get data!",
                "data" => $currentStockProduct
            ],200);
        }
    }

    public function showStok(string $id){
        try {
            // Ambil stok terbaru untuk produk berdasarkan ID
            $currentStok = StokProduk::with('product')
                            ->where('product_id', $id)
                            ->orderBy('created_at', 'desc')
                            ->first(); // Mengambil stok terbaru

            // Jika stok tidak ditemukan
            if (!$currentStok) {
                return response()->json([
                    "message" => "Stok not found",
                ], 404);
            }

            // Jika stok ditemukan, kirimkan data stok
            return response()->json([
                "message" => "success, get data!",
                "data" => $currentStok
            ], 200);

        } catch (\Exception $e) {
            // Tangani kesalahan yang tidak terduga
            return response()->json([
                "message" => "An error occurred",
                "error" => $e->getMessage()
            ], 500);
        }
    }


}