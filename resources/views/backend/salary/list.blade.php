@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card no-export">
                <div class="card-header d-flex align-items-center">
                    <span class="panel-title">{{ _lang('Staff Salaries') }}</span>
                    <a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Pay Salary') }}"
                        href="{{ route('salaries.create') }}"><i class="ti-plus"></i>&nbsp;{{ _lang('Pay Salary') }}</a>
                </div>
                <div class="card-body">
                    <table id="salaries_table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ _lang('Employee') }}</th>
                                <th>{{ _lang('Month') }}</th>
                                <th>{{ _lang('Year') }}</th>
                                <th>{{ _lang('Amount') }}</th>
                                <th>{{ _lang('Status') }}</th>
                                <th>{{ _lang('Date') }}</th>
                                <th class="text-center">{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salaries as $salary)
                                <tr id="row_{{ $salary->id }}">
                                    <td class="employee_name">{{ $salary->user->name }}</td>
                                    <td class="month">{{ $salary->month }}</td>
                                    <td class="year">{{ $salary->year }}</td>
                                    <td class="salary_amount">
                                        {{ decimalPlace($salary->amount, currency(get_base_currency())) }}</td>
                                    <td class="status">{{ $salary->status }}</td>
                                    <td class="payment_date">{{ $salary->payment_date }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-xs dropdown-toggle" type="button"
                                                data-toggle="dropdown">
                                                {{ _lang('Action') }} &nbsp;
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item ajax-modal"
                                                    href="{{ route('salaries.edit', $salary->id) }}"
                                                    data-title="{{ _lang('Update Salary') }}">
                                                    <i class="ti-pencil-alt"></i> {{ _lang('Edit') }}
                                                </a>
                                                <a class="dropdown-item ajax-modal"
                                                    href="{{ route('salaries.show', $salary->id) }}"
                                                    data-title="{{ _lang('Salary Details') }}">
                                                    <i class="ti-eye"></i> {{ _lang('View') }}
                                                </a>
                                                <form action="{{ route('salaries.destroy', $salary->id) }}" method="post"
                                                    class="d-inline">
                                                    {{ csrf_field() }}
                                                    <input name="_method" type="hidden" value="DELETE">
                                                    <button class="dropdown-item btn-remove" type="submit">
                                                        <i class="ti-trash"></i> {{ _lang('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
