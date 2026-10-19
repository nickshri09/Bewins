@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><span class="panel-title">{{ _lang('Commission History') }}</span></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>{{ _lang('Date') }}</th><th>{{ _lang('Investment') }}</th><th>{{ _lang('Source Client') }}</th><th>{{ _lang('Recipient') }}</th><th>{{ _lang('Level') }}</th><th>{{ _lang('Base Amount') }}</th><th>{{ _lang('Rate') }}</th><th>{{ _lang('Commission') }}</th></tr></thead>
            <tbody>
            @forelse($records as $record)
                @php($recipient = $record->recipient_type === 'user' ? $users->get($record->recipient_id) : $members->get($record->recipient_id))
                <tr>
                    <td>{{ $record->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $record->investment?->investment_code ?: '#'.$record->investment_id }}</td>
                    <td>{{ $record->sourceMember?->first_name }} {{ $record->sourceMember?->last_name }}</td>
                    <td>{{ $recipient?->name ?: ($recipient?->first_name.' '.$recipient?->last_name) }}</td>
                    <td>{{ $record->level === 0 ? _lang('Direct Agent') : 'Level '.$record->level }}</td>
                    <td>{{ number_format($record->base_amount, 2) }}</td>
                    <td>{{ number_format($record->rate_percent, 4) }}%</td>
                    <td>{{ number_format($record->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">{{ _lang('No commission records yet') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $records->links() }}
    </div>
</div>
@endsection
