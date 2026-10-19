@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header d-flex align-items-center">
                    <h4 class="header-title">{{ _lang('User List') }}</h4>
                    <a class="btn btn-primary btn-xs ml-auto" href="{{ route('users.create') }}"><i
                            class="ti-plus"></i>&nbsp;{{ _lang('Add New') }}</a>
                </div>

                <div class="card-body">

                    {{-- 🔎 Search + Branch Filter --}}
                    <div class="p-3 bg-light rounded mb-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <input type="text" id="search" class="form-control form-control-sm"
                                    placeholder="Search by name, email, mobile...">
                            </div>

                            <div class="col-md-4">
                                <select id="branch_id" class="form-control form-control-sm select2">
                                    <option value="">All Branches</option>
                                    @foreach ($branches as $b)
                                        <option value="{{ $b->id }}"
                                            {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-white btn-sm border w-100" onclick="clearFilters()">
                                    <i class="ti-reload"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <table id="users_table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>{{ _lang('Profile ID') }}</th>
                                <th>{{ _lang('Referral Code') }}</th>
                                <th>{{ _lang('Name') }}</th>
                                <th>{{ _lang('Email') }}</th>
                                <th>{{ _lang('Wallet Amount') }}</th>
                                <th>{{ _lang('percent(%)') }}</th>
                                <th>{{ _lang('Mobile Number') }}</th>
                                <th> Branch </th>
                                <th>{{ _lang('User Type') }}</th>
                                <th>{{ _lang('Role') }}</th>
                                <th>{{ _lang('Status') }}</th>
                                <th>{{ _lang('Assigned RM') }}</th>
                                <th class="text-center">{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr data-id="row_{{ $user->id }}">
                                    <td class='profile_picture text-center'><img
                                            src="{{ profile_picture($user->profile_picture) }}"
                                            class="thumb-sm img-thumbnail">
                                    </td>
                                    <td class='name'>{{ $user->user_id }}</td>
                                    <td>{{ $user->referral_code ?? '-' }}</td>
                                    <td class='name'>{{ $user->name }}</td>
                                    <td class='email'>{{ $user->email }}</td>
                                    <td class='wallet text-center'>{{ $user->wallet_amount ? $user->wallet_amount : 0 }}
                                    </td>
                                    <td class='percent text-center'>{{ $user->percent ? $user->percent : 0 }}</td>
                                    <td class='mobile_number'>{{ $user->mobile_number }}</td>
                                    <td>{{ $user->branch->name ?? '-' }}</td>
                                    <td class='user_type'>{{ strtoupper($user->user_type) }}</td>
                                    <td class='role_id'>{{ strtolower((string) $user->user_type) === 'admin' ? _lang('Admin') : $user->role->name }}</td>
                                    <td class='status'>{!! xss_clean(user_status($user->status)) !!}</td>

                                    <td>
                                        {{ $user->assignedManagerDetails->name ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        <span class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle btn-xs" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown">
                                                {{ _lang('Action') }}
                                            </button>

                                            <form action="{{ route('users.destroy', $user['id']) }}" method="post">
                                                {{ csrf_field() }}
                                                <input name="_method" type="hidden" value="DELETE">

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a href="{{ route('users.edit', $user['id']) }}" class="dropdown-item">
                                                        <i class="ti-pencil-alt"></i> {{ _lang('Edit') }}
                                                    </a>

                                                    <a href="{{ route('users.show', $user['id']) }}"
                                                        data-title="{{ _lang('User Details') }}"
                                                        class="dropdown-item ajax-modal">
                                                        <i class="ti-eye"></i> {{ _lang('View') }}
                                                    </a>
                                                </div>
                                            </form>
                                        </span>
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

@section('js-script')
    <script>
        $(document).ready(function() {
            // Check if DataTable is already initialized and destroy it
            if ($.fn.DataTable.isDataTable('#users_table')) {
                $('#users_table').DataTable().destroy();
            }

            // Initialize DataTable with custom configuration
            var table = $('#users_table').DataTable({
                "searching": true, // Enable searching functionality
                "dom": 'lrtip', // Hide default search box (removed 'f')
                "pageLength": 25, // Show 25 entries per page
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ], // Entries dropdown options
                "ordering": true, // Enable column sorting
                "info": true, // Show "Showing 1 to X of Y entries"
                "autoWidth": false, // Disable auto width calculation
                "responsive": true, // Enable responsive design
                "language": {
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ users",
                    "infoEmpty": "No users found",
                    "infoFiltered": "(filtered from _MAX_ total users)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });

            // Custom search function using our search box
            $('#search').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Custom branch filter - SERVER SIDE RELOAD
            $('#branch_id').on('change', function() {
                var branchId = $(this).val();
                var url = new URL(window.location.href);

                if (branchId) {
                    url.searchParams.set('branch_id', branchId);
                } else {
                    url.searchParams.delete('branch_id');
                }

                window.location.href = url.toString();
            });

            // Reset filters function
            window.clearFilters = function() {
                var url = new URL(window.location.href);
                url.searchParams.delete('branch_id');
                $('#search').val('');
                window.location.href = url.toString();
            };
        });
    </script>
@endsection
