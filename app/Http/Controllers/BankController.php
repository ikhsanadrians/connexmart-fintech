<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function index(){

        $topups = TopUp::latest()->paginate(5);
        return view("bank.index",compact('topups'));
    }

    public function topUp(Request $request){
        $topups = TopUp::query();

        if ($request->sort) {
            $sortOrder = $request->sort === "oldfirst" ? "asc" : "desc";
        } else {
            $sortOrder = "desc";
        }

        if ($request->show === "all") {
            $topups = $topups->orderBy("created_at", $sortOrder)->get();
        } else {
            $topups = $topups->orderBy("created_at", $sortOrder)->paginate($request->show ?? 50);
        }


        $count_topups = count(TopUp::all());
        $users = User::all();
        return view('bank.topup', compact('topups', 'users','count_topups'));
    }


    public function topupconfirm(Request $request){
        $topUp = TopUp::where('unique_code',$request->unique_code)->first();
        $userWallet = Wallet::where('user_id',$request->user_id)->first();
        $sumTopUp = $userWallet->credit += $request->nominals;

        $userWallet->update([
            "credit" => $sumTopUp
        ]);

        $topUp->update([
            "status" => "confirmed"
        ]);

        alert()->success('Success','Success Confirm Top Up!');

        return redirect()->back();

    }

    public function topupreject(Request $request){
        $topUp = TopUp::where('unique_code',$request->unique_code);
        $topUp->update([
            "status" => "rejected"
        ]);

        alert()->success("Success","Success Rejected Top Up!");

        return redirect()->back();
    }

    public function clientindex(){
        $clients = User::where('role_id',4)->with('wallet')->get();
        return view('bank.client',compact('clients'));
    }

    public function auth(){
        return view("bank.login");
    }

    public function auth_proceed(Request $request){
        $credentials = [
            "name" => $request->username,
            "password" => $request->password
        ];

        $checkRoles =  User::where('name',$credentials['name'])->first();

        if($checkRoles->role_id != 2) return redirect()->back();

        if(Auth::attempt($credentials)) return redirect()->route('bank.index');

        return redirect()->back();
    }

    public function transaction(){
        $transactions = Transaction::all();
        return view('bank.transaction',compact('transactions'));
    }

    public function banklogout(){
        Auth::logout();

        request()->session()->invalidate();

        return redirect()->route('auth');

    }

}
