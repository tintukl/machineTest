<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function signup(){
        return view('register');
    }
    public function index()
    {
        return view('home');
    }
    public function deposit()
    {
        return view('deposit');
    }
    public function withdraw()
    {
        return view('withdraw');
    }
    public function transfer()
    {
        return view('transfer');
    }
    public function statement()
    {
        return view('statement');
    }

    public function depositMoney(Request $request)
    {

        $this->validate($request,[
            'amount'=>'required',
        ]);
        $userId = Auth::id();
        $userEmail = User::select('email')->where('id',$userId)->first();;
        $transactionDetail = new TransactionDetail();
        $transactionDetail->amount = $request->amount;
        $transactionDetail->type = 'Credit';
        $transactionDetail->category = 'Deposit';
        $query = TransactionDetail::where('user_id',Auth::id())->get();
        if(empty($query))
        {
            $transactionDetail->balance = $request->amount;
        }
        else{
            $deposit = TransactionDetail::select(DB::raw("SUM(amount) as deposit_total"))
            ->where('user_id',Auth::id())
            ->where('category','Deposit')->pluck('deposit_total')->toArray();

            $withdraw = TransactionDetail::select(DB::raw("SUM(amount) as withdraw_total"))
                    ->where('user_id',Auth::id())
                    ->where('category','Withdraw')->pluck('withdraw_total')->toArray();
            $transfer = TransactionDetail::select(DB::raw("SUM(amount) as transfer_total"))
                    ->where('user_id',Auth::id())
                    ->where('category','Transfer')->pluck('transfer_total')->toArray();

            $credit = TransactionDetail::select(DB::raw("SUM(amount) as credit_total"))
                    ->where('transfer_to',$userEmail['email'])
                    ->where('category','Transfer')->pluck('credit_total')->toArray();
            $newBalance = ($deposit[0] + $credit[0]) - ($withdraw[0] + $transfer[0]);
            $transactionDetail->balance = $newBalance + $request->amount;
        }

        $transactionDetail->user_id = Auth::id();
        $transactionDetail->save();
        return redirect('/deposit');
    }

    public function withdrawMoney(Request $request)
    {
        $this->validate($request,[
            'amount'=>'required',
        ]);
        $userId = Auth::id();
        $userEmail = User::select('email')->where('id',$userId)->first();;
        $transactionDetail = new TransactionDetail();
        $transactionDetail->amount = $request->amount;
        $transactionDetail->type = 'Debit';
        $transactionDetail->category = 'Withdraw';
        $query = TransactionDetail::where('user_id',Auth::id())->latest();
        $creditedUser = User::select('id')->where('email',$request->email)->first();
        dd($creditedUser);
        if(empty($query))
        {
            $transactionDetail->balance = $request->amount;
        }
        else{
            $deposit = TransactionDetail::select(DB::raw("SUM(amount) as deposit_total"))
            ->where('user_id',Auth::id())
            ->where('category','Deposit')->pluck('deposit_total')->toArray();

            $withdraw = TransactionDetail::select(DB::raw("SUM(amount) as withdraw_total"))
                    ->where('user_id',Auth::id())
                    ->where('category','Withdraw')->pluck('withdraw_total')->toArray();
            $transfer = TransactionDetail::select(DB::raw("SUM(amount) as transfer_total"))
                    ->where('user_id',Auth::id())
                    ->where('category','Transfer')->pluck('transfer_total')->toArray();

            $credit = TransactionDetail::select(DB::raw("SUM(amount) as credit_total"))
                    ->where('transfer_to',$userEmail['email'])
                    ->where('category','Transfer')->pluck('credit_total')->toArray();
            $newBalance = ($deposit[0] + $credit[0]) - ($withdraw[0] + $transfer[0]);
            $transactionDetail->balance = $newBalance - $request->amount;
        }
        $transactionDetail->user_id = Auth::id();
        $transactionDetail->save();
        return view('withdraw');
    }


    public function transferMoney(Request $request)
    {
        $this->validate($request,[
            'amount'=>'required',
            'email'=> 'required|exists:users,email'
        ]);
        $userId = Auth::id();
        $userEmail = User::select('email')->where('id',$userId)->first();
        $transactionDetail = new TransactionDetail();
        $transactionDetail->amount = $request->amount;
        $transactionDetail->transfer_to = $request->email;
        $transactionDetail->type = 'Debit';
        $transactionDetail->category = 'Transfer';

       // dd($creditedUser['id']);

        if(empty($query))
        {
            $transactionDetail->balance = $request->amount;
        }
        else{
            $deposit = TransactionDetail::select(DB::raw("SUM(amount) as deposit_total"))
            ->where('user_id',Auth::id())
            ->where('category','Deposit')->pluck('deposit_total')->toArray();

            $withdraw = TransactionDetail::select(DB::raw("SUM(amount) as withdraw_total"))
                    ->where('user_id',Auth::id())
                    ->where('category','Withdraw')->pluck('withdraw_total')->toArray();
            $transfer = TransactionDetail::select(DB::raw("SUM(amount) as transfer_total"))
                    ->where('user_id',Auth::id())
                    ->where('category','Transfer')->pluck('transfer_total')->toArray();

            $credit = TransactionDetail::select(DB::raw("SUM(amount) as credit_total"))
                    ->where('transfer_to',$userEmail['email'])
                    ->where('category','Transfer')->pluck('credit_total')->toArray();
            $newBalance = ($deposit[0] + $credit[0]) - ($withdraw[0] + $transfer[0]);
            $transactionDetail->balance = $newBalance - $request->amount;


            //transfer credit
           /* $creditedUser = User::select('id')->where('email',$request->email)->first();

            */
        }
        $transactionDetail->user_id = Auth::id();
        $transactionDetail->save();
        if($transactionDetail){
            $creditedUser = User::select('id')->where('email',$request->email)->first();
            //dd($creditedUser['id']);
            $creditTransaction = new TransactionDetail();
            $creditTransaction->amount = $request->amount;
            $creditTransaction->type = 'Credit';
            $creditTransaction->category = 'Transfer';
            $creditTransaction->transfer_from = $userEmail['email'];

            $deposit = TransactionDetail::select(DB::raw("SUM(amount) as deposit_total"))
            ->where('user_id',$creditedUser['id'])
            ->where('category','Deposit')->pluck('deposit_total')->toArray();

            $withdraw = TransactionDetail::select(DB::raw("SUM(amount) as withdraw_total"))
                    ->where('user_id',$creditedUser['id'])
                    ->where('category','Withdraw')->pluck('withdraw_total')->toArray();
            $transfer = TransactionDetail::select(DB::raw("SUM(amount) as transfer_total"))
                    ->where('user_id',$creditedUser['id'])
                    ->where('category','Transfer')->pluck('transfer_total')->toArray();

            $credit = TransactionDetail::select(DB::raw("SUM(amount) as credit_total"))
                    ->where('transfer_to',$creditedUser['id'])
                    ->where('category','Transfer')->pluck('credit_total')->toArray();
            $newBalance = ($deposit[0] + $credit[0]) - ($withdraw[0] + $transfer[0]);
            $creditTransaction->balance = $newBalance + ($request->amount);
            $creditTransaction->user_id = $creditedUser['id'];
            $creditTransaction->save();

        }
      //  $creditTransaction->save();

        return view('transfer');
    }

    public function moneyStatement()
    {
        $userEmail = User::select('email')->where('id',Auth::id())->first();
        $transactionDetail = TransactionDetail::select('*')->where('user_id',Auth::id())
                            ->orWhere('transfer_to',$userEmail['email'])->get();
        $transactionDetail= array($transactionDetail);

      $transactionDetails = $transactionDetail['0'];
      //dd($transactionDetails);
    //   foreach($data['0'] as $data)
    //   {
    //       dd($data);
    //   }
        return view('statement', compact('transactionDetails'));
        //return view('acc-statement');
    }
}
