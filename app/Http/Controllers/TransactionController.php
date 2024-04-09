<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Wallet;
use App\Models\TopUp;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\UserCheckout;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TransactionController extends Controller
{
    public function index()
    {
        $product_count = 0;
        $total_prices = 0;
        $carts = Transaction::with('product')->where('user_id', Auth::user()->id)->where('status', 'not_paid')->orderBy('created_at', 'desc')->get();

        foreach ($carts as $product_cart) {
            $product_count += $product_cart->quantity;
        }

        foreach ($carts as $product_cart) {
            $total_prices += $product_cart->price;
        }

        return view('cart', compact('carts', 'product_count', 'total_prices'));
    }

    public function payCart(Request $request)
    {
        if ($request->ajax()) {

            $total_prices = $request->total_prices;
            $wallets = Wallet::where('user_id', Auth::user()->id)->first();

            if ($total_prices > $wallets->credit) {
                return response()->json([
                    "message" => "Your Balance Is Not Enough",
                ],406);

            } else {

                $current_debit = $wallets->debit;
                $current_credit = $wallets->credit;

                $carts = Transaction::with('product')->where('user_id', Auth::user()->id)->where('status', 'not_paid')->orderBy('created_at', 'desc')->get();


                foreach ($carts as $cr) {
                    if($cr->product->deleted_at == ""){
                        $cr->update([
                            "status" => "paid"
                        ]);
                    }
                }

                $wallets->update([
                    "debit" => ($current_debit += $total_prices),
                    "credit" => ($current_credit -= $total_prices)
                ]);


                session(['total_price' => $total_prices]);


                return response()->json([
                    "status" => "success",
                ]);

            }

        }

    }

    public function sentToCart(Request $request)
    {

        if ($request->ajax()) {
            $product = Product::find($request->product_id);
            $productPrice = $product->price;
            $productSummaryPrice = ($productPrice * $request->quantity);

            $sameTransaction = Transaction::where('product_id', $request->product_id)
                ->where('user_id', Auth::user()->id)
                ->where('status', 'not_paid')
                ->first();

            if($product->stock < $request->quantity){
                return response()->json([
                    "message" => "failed, product stock is not enough"
                ], 401);
            } else {
                if ($sameTransaction) {
                    $sumQuantity = $sameTransaction->quantity += $request->quantity;
                    $sumPrice = $sumQuantity * $product->price;
                    $sameTransaction->update([
                        'quantity' => $sumQuantity,
                        'price' => $sumPrice
                    ]);
                } else {
                    Transaction::create([
                        "user_id" => Auth::user()->id,
                        "product_id" => $product->id,
                        "status" => "not_paid",
                        "order_id" => "INV-" . Auth::user()->id . now()->format('dmYHis'),
                        "quantity" => $request->quantity,
                        "price" => $productSummaryPrice
                    ]);
                }

                return response()->json([
                    "message" => "success",
                    "data" => $product
                ]);
            }

        }
    }

    public function cart_delete(Request $request)
    {
        if ($request->ajax()) {
            $TransactionToDelete = Transaction::find($request->product_id);

            $delete = $TransactionToDelete->delete();

            return response()->json([
                "message" => "success delete transaction",
            ]);
        }
    }

    public function updateQuantity(Request $request)
    {

        if ($request->ajax()) {

            $quantityToUpdate = Transaction::where('id', $request->transaction_id)->where('user_id', Auth::user()->id)->first();

            if($request->quantity >= $quantityToUpdate->product->stock){
                return response()->json([
                    "message" => "error, stock outdated!",
                ],401);
            }

            $quantityToUpdate->update([
                'quantity' => $request->quantity
            ]);

            return response()->json([
                "message" => "success, update quantity",
                "data" => $quantityToUpdate
            ]);
        }

    }


    public function topUp()
    {
        return view('topup');
    }

    public function topUpProceed(Request $request)
    {
        if ($request->ajax()) {
            $data = TopUp::create([
                "user_id" => Auth::user()->id,
                "nominals" => $request->nominals,
                "status" => "unconfirmed",
                "unique_code" => "TU-" . Auth::user()->id . now()->format('dmYHis')
            ]);

            $data->user = User::find(Auth::user()->id);

            return response()->json([
                "message" => "Success! Add Top Up",
                "data" => $data
            ]);
        }
    }

    public function receipt(Request $request)
    {
        $currentTopUp = TopUp::where('unique_code', $request->unique_code)->first();

        $data = QrCode::size(200)->generate(
            $currentTopUp->unique_code,
        );

        $currentTopUp->qr_code = $data;

        return view('receipt', compact('currentTopUp'));
    }

    public function cart_receipt()
    {

        $total_prices = 0;
        $currentTransactions = Transaction::where('status', 'paid')->where('user_id', Auth::user()->id)->get();

        $qrcode = $currentTransactions[0];

        foreach ($currentTransactions as $transaction) {
            $total_prices += $transaction->price;
        }

        $data = QrCode::size(100)->generate(
            $qrcode->order_id,
        );

        $currentTransactions->qr_code = $data;
        $currentTransactions->total_prices = session('total_price');


        return view('receiptcart', compact('currentTransactions'));

    }

    public function cart_take(Request $request)
    {
        if ($request->ajax()) {
            $currentTransactions = Transaction::where('status', 'paid')->where('user_id', Auth::user()->id)->get();

            foreach ($currentTransactions as $transaction) {
                $transaction->update([
                    'status' => 'taken'
                ]);
            }
        }

        return response()->json([
            "message" => "Berhasil Update"
        ]);
    }


    public function checkout(string $checkout_code){
        $checkouts = UserCheckout::where("checkout_code", $checkout_code)->first();
        $product_list = json_decode($checkouts->product_list);
        $transactions = Transaction::whereIn("id", $product_list)->get();

        foreach($transactions as $transaction){
            $transaction->totalPricePerTransaction = ($transaction->price * $transaction->quantity);
        }

        return view("checkout", compact("transactions", "checkouts"));
    }

    public function handleCheckout(Request $request){
        if($request->ajax()){

            $checkout_code = now()->format('dmYHis') . Auth::user()->id . substr(uniqid(), 0, 3);
            $data = UserCheckout::create([
                "checkout_code" => $checkout_code,
                "user_id" => Auth::user()->id,
                "product_list" => json_encode($request->product_list),
                "total_quantity" => $request->total_quantity,
                "total_price" => $request->total_price
            ]);

            return response()->json([
                "message" => "success, checkout user",
                "checkout_code" => $data->checkout_code
            ]);
        }
    }
}