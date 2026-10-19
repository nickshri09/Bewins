@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-normal text-secondary">{{ ucfirst($type) }} Expected Return List</h4>
            <a href="{{ route('investment.expected-return.create', ['type' => $type]) }}" class="button2">
                <i class="fa fa-plus mr-2"></i> Add New {{ ucfirst($type) }} %
            </a>
            <style>
                /* From Uiverse.io by shah1345 */
                .button2 {
                    display: inline-block;
                    transition: all 0.2s ease-in;
                    position: relative;
                    overflow: hidden;
                    z-index: 1;
                    color: #090909;
                    padding: 0.4em 1.2em;
                    cursor: pointer;
                    font-size: 16px;
                    border-radius: 0.5em;
                    background: #e8e8e8;
                    border: 1px solid #e8e8e8;
                    box-shadow: 6px 6px 12px #c5c5c5, -6px -6px 12px #ffffff;
                }

                .button2:active {
                    color: #666;
                    box-shadow: inset 4px 4px 12px #c5c5c5, inset -4px -4px 12px #ffffff;
                }

                .button2:before {
                    content: "";
                    position: absolute;
                    left: 50%;
                    transform: translateX(-50%) scaleY(1) scaleX(1.25);
                    top: 100%;
                    width: 140%;
                    height: 180%;
                    background-color: rgba(0, 0, 0, 0.05);
                    border-radius: 50%;
                    display: block;
                    transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
                    z-index: -1;
                }

                .button2:after {
                    content: "";
                    position: absolute;
                    left: 55%;
                    transform: translateX(-50%) scaleY(1) scaleX(1.45);
                    top: 180%;
                    width: 160%;
                    height: 190%;
                    background-color: #009087;
                    border-radius: 50%;
                    display: block;
                    transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
                    z-index: -1;
                }

                .button2:hover {
                    color: #ffffff;
                    border: 1px solid #009087;
                }

                .button2:hover:before {
                    top: -35%;
                    background-color: #009087;
                    transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
                }

                .button2:hover:after {
                    top: -45%;
                    background-color: #10cabeb0;
                    transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
                }
            </style>
        </div>

        {{-- Navigation Tabs --}}
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ $type == 'investment' ? 'active' : '' }}"
                    href="{{ route('investment.expected-return.index', ['type' => 'investment']) }}">
                    Investment Returns
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type == 'fd' ? 'active' : '' }}"
                    href="{{ route('investment.expected-return.index', ['type' => 'fd']) }}">
                    FD Returns
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $type == 'loan' ? 'active' : '' }}"
                    href="{{ route('investment.expected-return.index', ['type' => 'loan']) }}">
                    Loan Returns
                </a>
            </li>
        </ul>

        {{-- MAIN LIST --}}
        <div class="card mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Expected Return (%)</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->expected_return }}%</td>
                                    <td>
                                        <span
                                            class="badge {{ $row->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                            {{ $row->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $row->updated_at->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{-- Edit Button --}}
                                            <a href="{{ route('investment.expected-return.edit', $row->id) }}"
                                                class="btn btn-sm" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>

                                            {{-- Toggle Switch --}}
                                            <a href="{{ route('investment.expected-return.toggle-status', $row->id) }}"
                                                class="toggle-switch {{ $row->status ? 'active' : '' }}"
                                                title="Toggle Status">
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No records found for
                                        {{ $type }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- HISTORY SECTION --}}
        <h4 class="fw-normal text-secondary mb-3">{{ ucfirst($type) }} Return History</h4>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Historical Value (%)</th>
                                <th>New Value (%)</th>
                                <th>Changed By</th>
                                <th>Changed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sno = 1; @endphp
                            @forelse($data as $row)
                                @foreach ($row->histories as $history)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td>{{ $history->old_value ? $history->old_value . '%' : 'N/A' }}</td>
                                        <td>{{ $history->new_value }}%</td>
                                        <td>
                                            {{ \App\Models\User::find($history->changed_by)->name ?? 'System' }}
                                        </td>
                                        <td>{{ $history->created_at->format('d-m-Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No History Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    /* Typography */
    body {
        font-size: 0.9rem;
    }

    h4 {
        font-size: 1.25rem;
    }

    /* Card styling */
    .card {
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-radius: 0.375rem;
    }

    /* Table styling */
    .table {
        font-size: 0.875rem;
        margin-bottom: 0;
    }

    .table th {
        font-weight: 500;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
        padding: 0.75rem;
    }

    .table td {
        padding: 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }

    .table tbody tr:hover {
        background-color: rgba(248, 249, 250, 0.5);
    }

    /* Badge styling */
    .badge {
        font-weight: 400;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    /* Button styling */
    .btn-outline-primary {
        border-color: #86b7fe;
        color: #0d6efd;
    }

    .btn-outline-primary:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-outline-secondary {
        border-color: #ced4da;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    /* Toggle switch */
    .toggle-switch {
        width: 40px;
        height: 20px;
        background: #adb5bd;
        border-radius: 20px;
        position: relative;
        display: inline-block;
        transition: background 0.3s ease;
    }

    .toggle-switch.active {
        background: #6ea8fe;
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 16px;
        height: 16px;
        background: white;
        border-radius: 50%;
        transition: transform 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch.active::after {
        transform: translateX(20px);
    }

    /* Navigation tabs */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.5rem 1rem;
        font-weight: 400;
        transition: all 0.2s;
    }

    .nav-tabs .nav-link:hover {
        color: #495057;
        border-bottom-color: #e9ecef;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border: none;
        border-bottom: 2px solid #0d6efd;
        background: transparent;
        font-weight: 500;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table {
            font-size: 0.8rem;
        }

        .table th,
        .table td {
            padding: 0.5rem;
        }
    }
</style>
