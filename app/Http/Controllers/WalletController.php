<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function fundWallet(Request $request)
    {
        $user = Auth::user();
        $amount = $request->validate([
            'amount' => 'required|integer'
        ]);

        $data = [
            'email' => $user->email,
            'amount' => $amount['amount']*100
        ];

        $token = env('PAYSTACK_SECRET_TOKEN');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize',$data);

        return $response;
    }

    public function checkBalance()
    {
        $user = Auth::user();

        $balance = $user->wallet->balance;

        return [
            'balance' => $balance
        ];
    }

    public function payBills(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'number' => 'required|min:11|max:11',
            'amount' => 'required'
        ]);

        $wallet = Wallet::where('user_id',$user->id)->first();

        if($wallet->balance <= $data['amount']){
            return response()->json(['status' => 'Insufficient Funds']);
        } else {
            $transactionData = [
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'type' => 'bills',
                'reference' => Str::random(8),
                'narration' => 'Airtime purchase'
            ];

          $transaction =  Transaction::create($transactionData);

                $wallet->balance = $wallet->balance - $data['amount'];
                $wallet->save();

        }

        return [
            'status' => 'airtime purchase successful'
        ];

    }

    public function getTransactions()
    {
       $user = Auth::user();
       $transaction =  Transaction::where('user_id',$user->id)->get();

       return TransactionResource::collection($transaction)->paginate(10);

      //*-- return $transaction;

    }

    public function verifyFunding($reference)
    {
        $user = Auth::user();

    $token =  env('PAYSTACK_SECRET_TOKEN');

    $check = Transaction::where('reference',$reference)->first();
    if ($check) {
        throw new Exception("Cannot process this request", 1);

    }else {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get("https://api.paystack.co/transaction/verify/$reference");

        if($response["data"]["status"] == "success"){

            $data = [
                'user_id' => $user->id,
                'amount' => $response['data']['amount']/100,
                'type' => 'funding',
                'reference' => $response['data']['reference'],
                'narration' => 'Wallet Funding'
            ];

          $transaction =  Transaction::create($data);

            $wallet = Wallet::where('user_id',$user->id)->first();
             if($wallet){
                $wallet->balance = $wallet->balance + $data['amount'];
                $wallet->save();
             }

             return [
                response()->json(['status' => 'verified successfully']),
                'transaction' => $transaction,
                ];

        }else {
            return $response->status();
        }
    }

    }
}
