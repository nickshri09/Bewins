<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Order;
use App\Models\CustomService;
use App\Models\MainService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Razorpay\Api\Api;
use App\Models\MemberWallet;
use App\Models\MemberWalletTransaction;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class MobileOrderController extends Controller
{
    protected $serviceCategories = [
        'amendment' => [
            'name' => 'Amendment',
            'sub_categories' => [
                'gst' => 'GST',
                'iec' => 'IEC',
                'msme' => 'MSME',
                'fssai' => 'FSSAI/FOSCOS'
            ]
        ],
        'audit' => [
            'name' => 'Audit',
            'sub_categories' => [
                'gst' => 'GST',
                'income_tax' => 'Income Tax',
                'trust' => 'Trust'
            ]
        ],
        'company' => [
            'name' => 'Company',
            'sub_categories' => [
                'private_limited' => 'Private Limited',
                'public_limited' => 'Public Limited',
                'fpo' => 'Farmer Producer Organizations(FPO)',
                'llp' => 'Limited Liability Partnership (LLP)',
                'opc' => 'One Person Company (OPC)',
                'nidhi' => 'National Initiative For Developing and Harnessing innovation/ NIDHI',
                'section_8' => 'Section 8 Company',
                'insurance_broker' => 'Insurance Broker/DB',
                'insurance_marketing_firm' => 'Insurance Marketing Firm/IMF'
            ]
        ],
        'IntellectualProperty' => [
            'name' => 'Intellectual Property',
            'sub_categories' => [
                'trademark' => 'Trademark'
            ]
        ],
        'Licences' => [
            'name' => 'Licences',
            'sub_categories' => [
                'fssai' => 'FSSAI OR FOSCOS',
                'health' => 'HEALTH LICENCE',
                'labour' => 'LABOUR LICENCE',
                'trade' => 'TRADE LICENCE'
            ]
        ],
        'MarketingMaterial' => [
            'name' => 'Marketing Material',
            'sub_categories' => [
                'designing' => 'Designing'
            ]
        ],
        'Miscellaneous' => [
            'name' => 'Miscellaneous',
            'sub_categories' => [
                'advance_tax' => 'ADVANCE TAX',
                'ca_assistance' => 'CA ASSISTANCE',
                'financials' => 'FINANCIALS',
                'gst_composition' => 'GST COMPOSITION TO REGULAR',
                'labour_card' => 'LABOUR CARD',
                'logo' => 'LOGO',
                'pan_aadhaar' => 'PAN AND AADHAAR LINK',
                'partnership' => 'PARTNERSHIP',
                'pps_services' => 'PPS Services (Plug and Play Services)',
                'recall_plan' => 'RECALL PLAN FOR FSSAI OR FOSCOS',
                'revocation_of_gst' => 'REVOCATION OF GST',
                'tds' => 'TDS',
            ]
        ],
        'Registration' => [
            'name' => 'Registration & Certification',
            'sub_categories' => [
                '12a_80g' => '12A and 80G',
                '80g' => '80G',
                'apeda' => 'APEDA',
                'brn' => 'Business Registration Number(BRN)',
                'darpan' => 'DARPAN',
                'dsc' => 'Digital Signature Certificate(DSC)',
                'dpcc' => 'DPCC(Delhi Pollution Control)',
                'eway' => 'E-WAY',
                'epch' => 'EPCH',
                'fassai_or_foscos' => 'FASSAI OR FOSCOS',
                'gem' => 'GEM',
                'goods_and_services_tax' => 'GOODS AND SERVICES TAX (GST)',
                'gumasta' => 'GUMASTA',
                'import_export_code' => 'IMPORT EXPORT CODE (IEC)',
                'iso' => 'ISO',
                'lut' => 'LUT',
                'micro_small_medium_enterprise' => 'MICRO, SMALL AND MEDIUM ENTERPRISE (MSME)',
                'niti_ayog' => 'NITI AYOG',
                'pan_card' => 'PAN CARD',
                'pf_esi' => 'PF AND ESI',
                'ptax_prtc' => 'PTAX OR PRTC',
                'shop' => 'SHOP',
                'startup' => 'STARTUP',
                'tan_card' => 'TAN CARD',
            ]
        ],
        'NetWorth' => [
            'name' => 'Net Worth',
            'sub_categories' => [
                'net_worth' => 'NET WORTH'
            ]
        ],
        'NoticesLitigations' => [
            'name' => 'Notices & Litigations',
            'sub_categories' => [
                'income_tax' => 'INCOME TAX'
            ]
        ],
        'ProjectReport' => [
            'name' => 'Project Report & CMA',
            'sub_categories' => [
                'project_report_cma' => 'PROJECT REPORT AND/OR CMA'
            ]
        ],
        'Renewals' => [
            'name' => 'Renewals',
            'sub_categories' => [
                'epch_renewal' => 'EPCH Renewal',
                'fssai_renewal' => 'FSSAI OR FOSCOS'
            ]
        ],
        'Returns' => [
            'name' => 'Returns',
            'sub_categories' => [
                'esi' => 'ESI',
                'gst' => 'GST',
                'itr' => 'ITR',
                'itr_u' => 'ITR-U',
                'pf' => 'PF',
                'pf_esi' => 'PF AND ESI',
                'tds' => 'TDS'
            ]
        ],
        'Roc' => [
            'name' => 'ROC',
            'sub_categories' => [
                'compliance' => 'COMPLIANCE',
                'conversion' => 'CONVERSION',
                'name_approval' => 'NAME APPROVAL'
            ]
        ],
        'Surrender' => [
            'name' => 'Surrender & Closure',
            'sub_categories' => [
                'company' => 'COMPANY',
                'gst' => 'GST',
                'pan_card' => 'PAN CARD',
                'pf' => 'PF'
            ]
        ]
    ];

    /**
     * Get all service categories
     */
    public function getCategories()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->serviceCategories
        ]);
    }

    /**
     * Get services by category and subcategory
     */
    public function getServices($category, $subCategory = null)
    {
        if (!array_key_exists($category, $this->serviceCategories)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid category'
            ], 404);
        }

        if ($subCategory && !array_key_exists($subCategory, $this->serviceCategories[$category]['sub_categories'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid subcategory'
            ], 404);
        }

        $customServices = CustomService::where('category', $category);
        if ($subCategory) {
            $customServices = $customServices->where('sub_category', $subCategory);
        }
        $customServices = $customServices->get();

        $mainServices = MainService::all();

        return response()->json([
            'status' => 'success',
            'data' => [
                'custom_services' => $customServices,
                // 'main_services' => $mainServices,
                'category' => $category,
                'sub_category' => $subCategory
            ]
        ]);
    }

    /**
     * Get service details for order
     */
    public function getServiceDetails($id)
    {
        // The web flow opens order details using CustomService IDs. Resolve
        // custom services first so overlapping numeric IDs do not load an
        // unrelated main service and return the wrong scope fee list.
        $customService = CustomService::with('mainService')->find($id);

        if ($customService) {
            $mainService = MainService::find($customService->main_service_id);

            if (!$mainService) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Main service not found'
                ], 404);
            }

            $service = (object)[
                'id' => $customService->id,
                'service_name' => $mainService->name,
                'custom_service_name' => $customService->custom_service_name,
                'fee' => $mainService->amount,
                'obligation' => $customService->obligation ?? 'N/A',
                'frequency' => $customService->frequency ?? 'N/A',
                'maximum_tat' => $customService->maximum_tat ?? 'N/A',
                'category' => $customService->category,
                'sub_category' => $customService->sub_category,
                'main_service_id' => $customService->main_service_id,
            ];

            $scopeServices = Service::where('main_service_id', $customService->main_service_id)->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'service' => $service,
                    'scope_services' => $scopeServices
                ]
            ]);
        }

        $mainService = MainService::find($id);

        if (!$mainService) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $service = (object)[
            'id' => $mainService->id,
            'service_name' => $mainService->name,
            'custom_service_name' => null,
            'fee' => $mainService->amount,
            'obligation' => 'N/A',
            'frequency' => 'N/A',
            'maximum_tat' => 'N/A',
            'category' => 'general',
            'sub_category' => 'general',
            'main_service_id' => $mainService->id,
        ];

        $scopeServices = Service::where('main_service_id', $mainService->id)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'service' => $service,
                'scope_services' => $scopeServices
            ]
        ]);
    }

    /**
     * Create Razorpay order for payment
     */
    public function createRazorpayOrder(Request $request)
    {
        $request->validate([
            'main_service_name' => 'required|string',
            'main_service_fee' => 'required|numeric',
            'scope_service_name' => 'nullable|string',
            'scope_service_fee' => 'nullable|numeric',
            'scope_service_id' => 'nullable|exists:services,id',
        ]);

        $user = auth()->user();

        // Calculate total amount
        $totalAmount = (float) $request->main_service_fee + (float) ($request->scope_service_fee ?? 0);

        // Get Razorpay credentials
        $razorpayKey = config('services.razorpay.key');
        $razorpaySecret = config('services.razorpay.secret');

        if (!$razorpayKey || !$razorpaySecret) {
            return response()->json([
                'status' => 'error',
                'message' => 'Razorpay credentials not configured'
            ], 500);
        }

        try {
            $api = new Api($razorpayKey, $razorpaySecret);

            // Create Razorpay order
            $orderData = [
                'receipt' => 'order_' . time(),
                'amount' => $totalAmount * 100, // Razorpay amount in paise
                'currency' => 'INR',
                'payment_capture' => 1, // auto capture
                'notes' => [
                    'user_id' => $user->id,
                    'main_service_name' => $request->main_service_name,
                    'scope_service_name' => $request->scope_service_name,
                ]
            ];

            $razorpayOrder = $api->order->create($orderData);

            // Create pending order in database
            $order = Order::create([
                'user_id' => $user->id,
                'member_id' => $user->id, // Logged-in user is the member
                'main_service_name' => $request->main_service_name,
                'main_service_fee' => $request->main_service_fee,
                'scope_service_name' => $request->scope_service_name,
                'scope_service_fee' => $request->scope_service_fee,
                'scope_service_id' => $request->scope_service_id,
                'lead_status' => 'Pending Payment',
                'order_status' => 'pending',
                'razorpay_order_id' => $razorpayOrder['id']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Razorpay order created successfully.',
                'data' => [
                    'order_id' => $order->id,
                    'razorpay_order_id' => $razorpayOrder['id'],
                    'razorpay_key' => $razorpayKey,
                    'amount' => $totalAmount,
                    'currency' => 'INR'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create Razorpay order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create Order and Pay with Wallet
     */
    public function createOrderWithWallet(Request $request)
    {
        $request->validate([
            'main_service_name' => 'required|string',
            'main_service_fee' => 'required|numeric',
            'scope_service_name' => 'nullable|string',
            'scope_service_fee' => 'nullable|numeric',
            'scope_service_id' => 'nullable|exists:services,id',
        ]);

        $user = auth()->user();
        
        $member = ($user instanceof \App\Models\Member) ? $user : \App\Models\Member::where('user_id', $user->id)->first();
        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member record not found.'], 404);
        }

        // Calculate total amount
        $totalAmount = (float) $request->main_service_fee + (float) ($request->scope_service_fee ?? 0);

        $wallet = MemberWallet::firstOrCreate(
            ['member_id' => $member->id],
            ['balance' => 0]
        );

        if ($wallet->balance < $totalAmount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance. Please add money to your wallet.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Deduct balance
            $wallet->balance -= $totalAmount;
            $wallet->save();

            // Create order (completed/paid)
            $order = Order::create([
                'user_id' => $user->id,
                'member_id' => $member->id,
                'main_service_name' => $request->main_service_name,
                'main_service_fee' => $request->main_service_fee,
                'scope_service_name' => $request->scope_service_name,
                'scope_service_fee' => $request->scope_service_fee,
                'scope_service_id' => $request->scope_service_id,
                'lead_status' => 'In Process',
                'order_status' => 'completed',
                'payment_status' => 'paid',
                'razorpay_payment_id' => 'WALLET_PAY_' . time(),
            ]);

            // Create wallet transaction
            MemberWalletTransaction::create([
                'member_wallet_id' => $wallet->id,
                'member_id'        => $member->id,
                'type'             => 'debit',
                'amount'           => $totalAmount,
                'gross_amount'     => 0, // Not interest
                'order_id'         => $order->id,
                'note'             => 'Paid for order: ' . $request->main_service_name,
                'created_by'       => ($user instanceof \App\Models\Member) ? $user->user_id : $user->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created and paid successfully from wallet.',
                'data' => [
                    'order_id' => $order->id,
                    'amount' => $totalAmount,
                    'wallet_balance' => (float) $wallet->balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Razorpay payment and complete order
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $user = auth()->user();
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->razorpay_order_id !== $request->razorpay_order_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Razorpay order ID'
            ], 400);
        }

        $razorpaySecret = config('services.razorpay.secret');

        try {
            // Verify signature
            $generatedSignature = hash_hmac('sha256', $request->razorpay_order_id . '|' . $request->razorpay_payment_id, $razorpaySecret);

            if ($generatedSignature !== $request->razorpay_signature) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payment signature'
                ], 400);
            }

            // Update order status
            $order->lead_status = 'In Process';
            $order->order_status = 'completed';
            $order->razorpay_payment_id = $request->razorpay_payment_id;
            $order->payment_status = 'paid';
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified and order completed successfully.',
                'data' => [
                    'order_id' => $order->id
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's orders list
     */
    public function getOrders(Request $request)
    {
        $user = auth()->user();

        $query = Order::with(['member', 'service', 'user'])
            ->where('user_id', $user->id)
            ->latest();

        // Apply filters
        if ($request->filled('lead_status')) {
            $query->where('lead_status', $request->lead_status);
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        $orders = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    /**
     * Get single order details
     */
    public function getOrderDetails($id)
    {
        $user = auth()->user();

        $order = Order::with(['member', 'user.assignedManagerDetails', 'documents'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }
}
