<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderChat;
use App\Models\Service;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderPlacedController extends Controller
{
    public function create()
    {
        $amendService = Service::where('service_name', 'Basic Licence - Amendment')->first();
        $scopeServices = Service::where('main_service_id', $amendService->id ?? null)->get();
        $members = Member::all();
        return view('layouts.menus.create_order', compact('amendService', 'scopeServices', 'members'));
    }

    // public function index()
    // {
    //     $orders = Order::with(['member', 'service'])
    //                   ->withCount('documents')
    //                   ->latest()
    //                   ->get();

    //     return view('layouts.menus.view_orders', compact('orders'));
    // }

    // public function index()
    // {
    //     $query = Order::with(['member', 'service','user'])
    //                   ->withCount('documents')
    //                   ->latest();

    //     dd($query);
    //     //   direct code
    //     // if (auth()->user()->user_type != 'admin') {
    //     //     $query->where('user_id', auth()->id());
    //     // }

    //     // readabale code
    //      $user_type =Auth::user()->user_type;
    //     if($user_type != 'admin'){
    //         $query->where('user_id', auth()->id());
    //     }
    //     //dd($user_type);
    //     $orders = $query->get();
    //     // dd($orders);
    //     dd($orders->pluck('user.user_id'));

    //     return view('layouts.menus.view_orders', compact('orders'));
    // }

    public function index()
    {
        $query = Order::with(['member', 'service', 'user'])
            ->withCount('documents')
            ->latest();

        $user_type = Auth::user()->user_type;

        if ($user_type != 'admin') {
            $query->where('user_id', auth()->id());
        }

        if (request('branch_id')) {
            $query->whereHas('user', function ($q) {
                $q->where('branch_id', request('branch_id'));
            });
        }

        $orders = $query->get();

        // ✅ Get all mobile numbers of related users
        // dd($orders->pluck('user.mobile_number'));
        // dd($orders->pluck('user.account_number'));

        return view('layouts.menus.view_orders', compact('orders'));
    }

    public function dropdownindexSimple(Request $request)
    {
        // dd('done');
        $query = Order::query();

        // Apply same filters as dropdownindex
        if ($request->filled('lead_id')) {
            $numericId = ltrim($request->lead_id, '0');
            $query->where('id', $numericId);
        }

        if ($request->filled('order_id')) {
            $query->where('id', 'like', '%' . $request->order_id . '%');
        }

        if ($request->filled('lead_status') && $request->lead_status !== 'All') {
            $query->where('lead_status', $request->lead_status);
        }

        if ($request->filled('order_status') && $request->order_status !== 'All') {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('applicant')) {
            $query->whereHas('member', function ($q) use ($request) {
                $q
                    ->where('first_name', 'like', '%' . $request->applicant . '%')
                    ->orWhere('email', 'like', '%' . $request->applicant . '%')
                    ->orWhere('mobile', 'like', '%' . $request->applicant . '%');
            });
        }

        if ($request->filled('subscriber')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q
                    ->where('name', 'like', '%' . $request->subscriber . '%')
                    ->orWhere('email', 'like', '%' . $request->subscriber . '%')
                    ->orWhere('id', $request->subscriber);
            });
        }

        if ($request->filled('product_category') && $request->product_category !== 'All') {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('product_category', $request->product_category);
            });
        }

        if ($request->filled('percent')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('percent', $request->percent);
            });
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Eager load relationships (same as before)
        // $orders = $query->with(['member', 'user', 'service'])
        //                 ->latest()
        //                 ->paginate(10);

        if (auth()->user()->user_type !== 'admin') {
            // Normal user: sirf apne orders
            $query->where('user_id', auth()->id());
        } else {
            // Admin: optional user filter
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Eager load relationships (same as before)
        $orders = $query
            ->with(['member', 'user', 'service'])
            ->latest()
            ->paginate(10);

        $users = [];
        if (auth()->user()->user_type === 'admin') {
            $users = \App\Models\User::whereIn('user_type', ['user', 'admin'])
                ->orderBy('name')
                ->get(['id', 'name']);
        }
        // dd($orders);

        return view('layouts.menus.view_orders_simple', compact('orders', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'main_service_name' => 'required|string',
            'main_service_fee' => 'required|numeric',
            'scope_service_name' => 'nullable|string',
            'scope_service_fee' => 'nullable|numeric',
            'scope_service_id' => 'nullable|exists:services,id',
        ]);
        Order::create([
            'user_id' => auth()->id(),
            'member_id' => $request->member_id,
            'main_service_name' => $request->main_service_name,
            'main_service_fee' => $request->main_service_fee,
            'scope_service_name' => $request->scope_service_name,
            'scope_service_fee' => $request->scope_service_fee,
            'scope_service_id' => $request->scope_service_id,  // ✅ store it
        ]);
        return redirect()->back()->with('success', 'Order placed successfully!');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->back()->with('success', 'Order deleted successfully!');
    }

    public function singleShow($id)
    {
        $order = Order::with(['member', 'user.assignedManagerDetails', 'documents'])->findOrFail($id);

        // Fetch all chats related to this order
        $chats = OrderChat::where('order_id', $id)->orderBy('created_at', 'asc')->get();

        return view('layouts.menus.view_order_single', compact('order', 'chats'));
    }

    public function editshow($id)
    {
        $order = Order::with(['member', 'user.assignedManagerDetails'])->findOrFail($id);
        $user_type = Auth::user()->user_type;

        // Fetch all chats related to this order
        $chats = OrderChat::where('order_id', $id)->orderBy('created_at', 'asc')->get();

        return view('layouts.menus.order_edit', compact('order', 'chats'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'lead_status' => 'required|string',
            'order_status' => 'required|string',
            'review' => 'nullable|string',
            'order_id' => 'required|integer',
        ]);

        // Directly update using Order model
        DB::table('orders')
            ->where('id', $id)
            ->update([
                'lead_status' => $request->lead_status,
                'order_status' => $request->order_status,
                'review' => $request->review,
            ]);

        return back()->with('success', 'Order updated successfully.');
    }

    public function updateDocumentStatus(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'status' => 'required|string|in:Approved,Rejected,Pending',
        ]);

        \App\Models\Document::whereIn('id', $request->document_ids)
            ->update(['status' => $request->status]);

        return back()->with('success', 'Document status updated successfully.');
    }

    public function storeChat(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        OrderChat::create([
            'order_id' => $id,
            'name' => auth()->user()->name ?? 'Admin',  // or set manually
            'message' => $request->message,
        ]);
        return redirect()->back();
    }

    public function storeDocument(Request $request, $order_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move the file to public/documents
            $file->move(public_path('documents'), $filename);

            // Save to DB
            Document::create([
                'order_id' => $order_id,
                'name' => $request->name,
                'file_path' => 'documents/' . $filename,  // relative path for easy access via asset()
            ]);
        }

        return redirect()->back()->with('success', 'Document uploaded successfully!');
    }

    public function getDocuments($id)
    {
        $order = Order::with('documents')->findOrFail($id);
        return view('view_order_single', compact('order'));
    }

    public function showCountDocumnet($id)
    {
        $order = Order::withCount('documents')->findOrFail($id);

        return view('layouts.menus.view_orders', compact('order'));
    }

    public function dropdownindex(Request $request)
    {
        $query = Order::query();

        // Filter by Lead ID
        if ($request->filled('lead_id')) {
            $numericId = ltrim($request->lead_id, '0');  // remove leading zeros
            $query->where('id', $numericId);
        }
        // Filter by custom Order ID
        if ($request->filled('order_id')) {
            $query->where('id', 'like', '%' . $request->order_id . '%');
        }
        // Filter by Lead Status
        if ($request->filled('lead_status') && $request->lead_status !== 'All') {
            $query->where('lead_status', $request->lead_status);
        }

        // Filter by Order Status
        if ($request->filled('order_status') && $request->order_status !== 'All') {
            $query->where('order_status', $request->order_status);
        }
        // Filter by Applicant
        if ($request->filled('applicant')) {
            $query->whereHas('member', function ($q) use ($request) {
                $q
                    ->where('first_name', 'like', '%' . $request->applicant . '%')
                    ->orWhere('email', 'like', '%' . $request->applicant . '%')
                    ->orWhere('mobile', 'like', '%' . $request->applicant . '%');
            });
        }
        // ✅ Filter by Subscriber (corrected to use 'user' relationship)
        if ($request->filled('subscriber')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q
                    ->where('name', 'like', '%' . $request->subscriber . '%')
                    ->orWhere('email', 'like', '%' . $request->subscriber . '%')
                    ->orWhere('id', $request->subscriber);
            });
        }
        // Filter by Product Category
        if ($request->filled('product_category') && $request->product_category !== 'All') {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('product_category', $request->product_category);
            });
        }

        // ✅ Correct eager loading relationships
        $orders = $query->with(['member', 'user', 'service'])->latest()->paginate(20);

        return view('layouts.menus.view_orders', compact('orders'));
    }

    /**
     * Stream Invoice PDF for the given order
     */
    public function invoice($id)
    {
        $order = Order::with([
            'member',
            'service',
            'documents',
            'user',
            'user.assignedManager',
            'user.branch',
        ])->findOrFail($id);

        $data = [
            'order' => $order,
            'issuedAt' => now()->format('F d, Y'),
            'companyName' => config('app.name'),
            'invoiceNo' => 'INV/SUB-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
            'orderNo' => str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);
        return $pdf->stream('invoice_' . $order->id . '.pdf');
    }
}
