@extends('layouts.home-layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Statement of account</div>

                <div class="card-body">


                    <table style="width: 80%">
                        <tr>
                            <th style="border: 2px solid #dddddd">DATE TIME</th>
                            <th style="border: 2px solid #dddddd">AMOUNT</th>
                            <th style="border: 2px solid #dddddd">TYPE</th>
                            <th style="border: 2px solid #dddddd">DETAILS</th>
                            <th style="border: 2px solid #dddddd">BALANCE</th>
                            <th style="border: 2px solid #dddddd"></th>

                        </tr>
                        @foreach ($transactionDetails as $transactionDetail )
                        <tr>
                            <td style="border: 2px solid #dddddd">{{$transactionDetail->created_at}}</td>
                            <td style="border: 2px solid #dddddd">{{$transactionDetail->amount}}</td>
                            <td style="border: 2px solid #dddddd">{{$transactionDetail->type}}</td>
                            <td style="border: 2px solid #dddddd">{{$transactionDetail->category}}</td>
                            <td style="border: 2px solid #dddddd">{{$transactionDetail->balance}}</td>
                        </tr>
                        @endforeach
                    </table>

                    {{-- @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }} --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
