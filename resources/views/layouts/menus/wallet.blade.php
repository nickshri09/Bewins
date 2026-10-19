@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-body">
            
            {{-- Wallet Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Wallet</h4>
                <div>
                    <a href="{{ route('request_withdraw') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-coins me-1"></i> Request Withdraw
                    </a>
                    <a href="{{ route('add_money') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Money to Deposit Wallet
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Main Wallet Balance --}}
                <div class="col-md-12">
                    <div class="mb-3 text-center  fw-bold fs-4 ">
                        <h5 class="mb-1">Main Wallet Balance:</h5>
                        <span class="badge bg-success fs-5 py-2 px-3 text-white">₹ {{ number_format($walletAmount, 2) }}</span>
                    </div>

                    {{-- Transactions Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th>Type</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $txn)
                                    <tr>
                                        <td>
                                            {{ $txn->created_at->format('d M Y') }}<br>
                                            <small class="text-muted">{{ $txn->created_at->format('h:i:s A') }}</small>
                                        </td>
                                        <td style="text-align: center; color:antiquewhite">
                                            @if($txn->type == 'credit')
                                                <span class="badge bg-success">Credit</span>
                                            @elseif($txn->type == 'debit')
                                                <span class="badge bg-danger">Debit</span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $txn->note ?? 'N/A' }}</td>
                                        <td>
                                            @if($txn->type == 'debit')
                                                <span class="text-danger fw-bold">₹ {{ number_format($txn->amount, 2) }} DR</span><br>
                                            @else
                                                <span class="text-success fw-bold">₹ {{ number_format($txn->amount, 2) }}</span>
                                                   <span  style="color:green">CR</span>
                                                <br>
                                            @endif
                                           
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
