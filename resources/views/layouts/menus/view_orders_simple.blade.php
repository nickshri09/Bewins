@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Filters</h4>
                <a href="{{ route('orders.index.simple') }}" class="btn btn-success">Clear Filter</a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('orders.index.simple') }}">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Lead Id</label>
                            <input type="text" class="form-control" name="lead_id"
                                placeholder="Enter Lead ID like 001 or 010" value="{{ request('lead_id') }}">

                        </div>
                        <div class="col-md-3">
                            <label>Order Id</label>
                            <input type="text" class="form-control" placeholder="Order id" name="order_id"
                                value="{{ request('order_id') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Lead Status</label>
                            <select class="form-control" name="lead_status">
                                <option>All</option>
                                <option>Fresh</option>
                                <option>In Process</option>
                                <option>Completed</option>
                                <option>Refunded</option>
                                <option>Cancelled</option>
                                <option>Document Pending</option>
                                <option>Document Requested</option>
                                <option>Document Received</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Order Status</label>
                            <select class="form-control" name="order_status">
                                <option>All</option>
                                <option>New case</option>
                                <option>Process</option>
                                <option>Completed</option>
                                <option>Refund initiated</option>
                                <option>(POG) Pending From Government side</option>
                                <option>Contact to Connector/RM</option>
                                <option>Audit and Certification</option>
                                <option>Evp till 06 Aug 2024</option>
                                <option>Not Linked or Wrong Order or Tax payable</option>
                                <option>Chalan/DSC Payment Pendency</option>
                                <option>Approval (Pending)</option>
                                <option>Approval (Approved)</option>
                                <option>Approval (Rejected) </option>
                                <option> Refund Approved</option>
                                <option> Refund Rejected</option>
                                <option>Partially Refunded </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Applicant</label>
                            <input type="text" class="form-control" placeholder="Applicant" name="applicant"
                                value="{{ request('applicant') }}">
                            <small>(Name | Email | Mobile Number)</small>
                        </div>
                        <div class="col-md-3">
                            <label>Extended Subscriber</label>
                            <input type="text" class="form-control" placeholder="subas" name="subscriber"
                                value="{{ request('subscriber') }}">
                            <small>(Id | Name | Email | Mobile Number)</small>
                        </div>
                        <div class="col-md-3">
                            <label>Percent (%)</label>
                            <input type="number" class="form-control" name="percent" placeholder="e.g. 10"value="{{ request('percent') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                        </div>

                        @php
                            $isAdmin = auth()->user()->user_type === 'admin';
                        @endphp

                        @if($isAdmin)
                        <div class="col-md-3">
                            <label>User</label>
                            <select name="user_id" class="form-control">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <label>Product Category</label>
                            <select class="form-control" name="product_category">
                                <option>All</option>
                                <option>Income Tax Related Services (Itr & Other Direct Tax compliance)</option>
                                <option>Company and LLB and NGO Incorporation</option>
                                <option>Import - Export Services (IEC)</option>
                                <option>Msme or Udyog Aadhar or Udyami Services</option>
                                <option>Trade/Shop licence</option>
                                <option>ROC Event compliance</option>
                                <option>ROC Annual complaints</option>
                                <option>Pan And Tan Card</option>
                                <option>Project Report and Cma Report</option>
                                <option>GST (Goods and Service tax)</option>
                                <option>Partnership Services</option>
                                <option>Miscellaneous Services</option>
                                <option>Other services</option>
                                <option>PF and ESI and Payroll Services</option>
                                <option>Gem portal services</option>
                                <option>Website development And Hosting Services</option>
                                <option>Net Worth Statement</option>
                                <option>Startup Indian Services</option>
                                <option>IRCTC</option>
                                <option>FSSAI AND FOSCOS</option>
                                <option>Government Fees</option>
                                <option>DCS (digital signature certificate)</option>
                                <option>ISO certificate and surveillance</option>
                                <option>Income tax related services(Itr & Other Direct Tax Compliance)</option>
                                <option>Variable Product and Services </option>
                                <option>Trademark and Intellectual Right</option>
                                <option>Miscellaneous services</option>
                                <option>Insurance Company and LLB Registration</option>


                            </select>
                        </div>
                        {{-- <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div> --}}
                        <div class="col-md-3 d-flex justify-content-end align-items-center">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Taxation & Compliance Leads</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped m-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#Lead No.</th>
                            <th>Applicant</th>
                            <th>Subscriber</th>
                            <th>Review</th>
                            <th>Service fee</th>
                            <th>Precent %</th>
                            <th>RM</th>
                            <th>Status</th>
                            {{-- <th>Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>

                                <td>
                                    {{ $order->member->name ?? 'N/A' }}<br>
                                    <small>Applied On {{ $order->created_at->format('d/m/Y') }}</small>
                                </td>

                                <td>
                                    <strong>
                                        @if ($order->user_id == auth()->id())
                                            Your Client
                                        @else
                                            Added by: {{ $order->user->name ?? 'Another User' }}
                                        @endif
                                    </strong>
                                </td>



                                <td class="text-danger">{{ $order->review }}</td>
                                 <td>
                                    {{$order->main_service_fee}}
                                 </td>
                                <td>
                                    {{$order->user->percent ?? 'N/A'}}
                                </td>

                                <td>
                                    <div class="rm-info mb-2 text-center">
                                        <!--   <span>-->
                                        <!--       @if (Auth::user()->assignedManager)
    -->
                                        <!--      {{ Auth::user()->assignedManager->name ?? '-' }}         -->
                                    <!--  @else-->
                                        <!--  N/A-->
                                        <!--
    @endif-->
                                        <!--</span>-->
                                        @if ($order->user && $order->user->assignedManager)
                                            <!--<strong>RM(ID): </strong> {{ $order->user->assignedManager->user_id ?? 'N/A' }}<br>-->
                                            {{ $order->user->assignedManager->name ?? 'N/A' }}
                                            <!--<strong>Mobile:</strong>-->
                                            <!--{{ $order->user->assignedManager->mobile_number ? '+91' . $order->user->assignedManager->mobile_number : 'N/A' }}-->
                                        @else
                                            N/A
                                        @endif

                                    </div>
                                </td>




                                {{-- <td class="text-center">
                                    <small>0/{{ $order->service->maximum_tat ?? 'N/A' }} </small>
                                <td class="text-center text-danger">
                                    {{ $order->documents_count }}
                                </td>
                                </td> --}}
                                <td>
                                    Lead: <span class="badge badge-warning">{{ $order->lead_status }}</span><br>
                                    <span class="badge badge-success mt-1">{{ $order->order_status }}</span>
                                </td>


                                {{-- <td>
                                    <a href="{{ route('orders.show', $order->id) }}"
                                        class="btn btn-xs btn-outline-info me-1" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('edit.show', $order->id) }}"
                                        class="btn btn-xs btn-outline-success me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!--<form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this order?');">-->
                                    <!--    @csrf-->
                                    <!--     @method('DELETE')-->
                                    <!--   <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete">-->
                                    <!--   <i class="fas fa-trash"></i>-->
                                    <!--    </button>-->
                                    <!-- </form>-->
                                </td> --}}



                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                </div> --}}

                <div class="card-footer bg-light py-2">
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="pagination-container mb-0">
                            {{ $orders->appends(request()->except('page'))->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>

                <style>
                    /* Pagination ka design aur slim look improve */
                    .pagination {
                        margin-bottom: 0;
                        font-size: 0.85rem;
                        gap: 4px !important; /* 👈 buttons ke beech gap */
                    }
                    .pagination .page-link {
                        border-radius: 6px !important;
                        padding: 2px 10px !important;
                        color: #495057;
                    }
                    .pagination .page-item.active .page-link {
                        background-color: #1d81ec;
                        border-color: #147ae7;
                        color: #fff;
                    }
                    .pagination .page-link:hover {
                        background-color: #e9ecef;
                    }
                </style>


            </div>
        </div>
    </div>
@endsection
