<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MetamaskController extends Controller
{
//      @return void
    public function index()
    {
        $response['transactions'] = Transaction::all();
        return view('metamask')->with($response);
    }
//      @param  mixed $request
//      @return void
    public function create(Request $request)
    {
        return  Transaction::create([
            "txHash" => $request->txHash,
            "amount" => $request->amount,
        ]);
    }
}
