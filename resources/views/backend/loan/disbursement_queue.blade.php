
@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <span class="panel-title">
                    <i class="fas fa-money-check-alt mr-1"></i>
                    {{ _lang('Disbursement Queue') }}
                </span>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ _lang('Loan ID') }}</th>
                                <th>{{ _lang('Borrower') }}</th>
                                <th>{{ _lang('Loan Product') }}</th>
                                <th>{{ _lang('Release Date') }}</th>
                                <th>{{ _lang('Approved Amount') }}</th>
                                <th>{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loans as $loan)
                                <tr>
                                    <td>{{ $loan->loan_id }}</td>
                                    <td>
                                        {{ optional($loan->borrower)->first_name }}
                                        {{ optional($loan->borrower)->last_name }}
                                    </td>
                                    <td>{{ optional($loan->loan_product)->name ?? '-' }}</td>
                                    <td>{{ $loan->getRawOriginal('release_date') ?? '-' }}</td>
                                    <td>
                                        {{ decimalPlace($loan->applied_amount, currency(optional($loan->currency)->name)) }}
                                    </td>
                                    <td>
                                        <a href="{{ route('loans.show', $loan->id) }}"
                                           class="btn btn-primary btn-sm">
                                            {{ _lang('View') }}
                                        </a>

                                        <form method="POST"
                                              action="{{ route('loans.record_disbursement', $loan->id) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Confirm that the actual loan payment has been released?');">
                                            @csrf
                                            <input type="hidden" name="confirm_disbursement" value="1">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                {{ _lang('Record Disbursement') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        {{ _lang('No approved loans are awaiting disbursement.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
