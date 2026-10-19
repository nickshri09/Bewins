<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Member;
use App\Models\Order;
use App\Models\CustomService;
use App\Models\MainService;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MainServiceController extends Controller
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

    public function __construct()
    {
        // Set timezone to India/Mumbai
        $timezone = 'Asia/Kolkata';
        date_default_timezone_set($timezone);
        config(['app.timezone' => $timezone]);
        view()->share('serviceCategories', $this->serviceCategories);
    }

    public function index($category, $subCategory = null)
    {
        if (!array_key_exists($category, $this->serviceCategories)) {
            abort(404, 'Invalid category');
        }

        if ($subCategory && !array_key_exists($subCategory, $this->serviceCategories[$category]['sub_categories'])) {
            abort(404, 'Invalid subcategory');
        }

        $customServices = CustomService::where('category', $category);
        if ($subCategory) {
            $customServices = $customServices->where('sub_category', $subCategory);
        }
        $customServices = $customServices->get();

        // $services = Service::all();
        $mainServices = MainService::all();

        return view('services.index', compact('customServices', 'mainServices', 'category', 'subCategory'));
    }

    public function create($category, $subCategory = null)
    {
        if (!array_key_exists($category, $this->serviceCategories)) {
            abort(404, 'Invalid category');
        }

        if ($subCategory && !array_key_exists($subCategory, $this->serviceCategories[$category]['sub_categories'])) {
            abort(404, 'Invalid subcategory');
        }

        // $services = Service::all();
        $mainServices = MainService::all();
        return view('services.create', compact('mainServices', 'category', 'subCategory'));
    }

    public function store(Request $request, $category, $subCategory = null)
    {
        // Temporary debug: show category and subCategory with request payload
        // dd(array_merge(['category' => $category, 'subCategory' => $subCategory], $request->all()));
        if (!array_key_exists($category, $this->serviceCategories)) {
            abort(404, 'Invalid category');
        }

        if ($subCategory && !array_key_exists($subCategory, $this->serviceCategories[$category]['sub_categories'])) {
            abort(404, 'Invalid subcategory');
        }

        $request->validate([
            'main_service_id' => 'required|exists:main_services,id',
            'custom_service_name' => 'required|string|max:255',
            'obligation' => 'nullable|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'maximum_tat' => 'nullable|string|max:255'
        ]);

        $customService = CustomService::create([
            // Map new input names to existing DB columns
            'main_service_id' => $request->main_service_id,
            'custom_service_name' => $request->custom_service_name,
            'category' => $category,
            'sub_category' => $subCategory,
            'obligation' => $request->obligation,
            'frequency' => $request->frequency,
            'maximum_tat' => $request->maximum_tat
        ]);

        $redirectRoute = $subCategory ?
            route('services.index', ['category' => $category, 'subCategory' => $subCategory]) :
            route('services.index', ['category' => $category]);

        return redirect($redirectRoute)
            ->with('success', 'Service added successfully');
    }

    public function show($category, $subCategory = null, $id)
    {
        if (!array_key_exists($category, $this->serviceCategories)) {
            abort(404, 'Invalid category');
        }

        if ($subCategory && !array_key_exists($subCategory, $this->serviceCategories[$category]['sub_categories'])) {
            abort(404, 'Invalid subcategory');
        }

        $customService = CustomService::findOrFail($id);
        // Fetch the selected main service for display
        $mainService = \App\Models\MainService::find($customService->main_service_id);

        if (!$mainService) {
            abort(404, 'Main service not found.');
        }

        // Prepare display object: show main service name/fee, and selected custom service details
        $service = (object)[
            'service_name' => $mainService->name,
            'fee' => $mainService->amount,
            'obligation' => $customService->obligation ?? 'N/A',
            'frequency' => $customService->frequency ?? 'N/A',
            'maximum_tat' => $customService->maximum_tat ?? 'N/A',
        ];

        // Scope Fee List: all Services under the same main_service_id
        $scopeServices = Service::where('main_service_id', $customService->main_service_id)->get();

        $members = Member::all();

        return view('services.order', compact('service', 'scopeServices', 'members', 'category', 'subCategory'));
    }

    public function storeOrder(Request $request, $category, $subCategory = null)
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

        $request->validate([
            'member_id' => 'required|exists:members,id',
            'main_service_name' => 'required|string',
            'main_service_fee' => 'required|numeric',
            'scope_service_name' => 'nullable|string',
            'scope_service_fee' => 'nullable|numeric',
            'scope_service_id' => 'nullable|exists:services,id',
        ]);

        // Calculate total amount
        $totalAmount = (float) $request->main_service_fee + (float) ($request->scope_service_fee ?? 0);

        // Check wallet balance
        $user = auth()->user();
        if ($user->wallet_amount < $totalAmount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance. Please recharge your wallet.'
            ], 422);
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'member_id' => $request->member_id,
            'main_service_name' => $request->main_service_name,
            'main_service_fee' => $request->main_service_fee,
            'scope_service_name' => $request->scope_service_name,
            'scope_service_fee' => $request->scope_service_fee,
            'scope_service_id' => $request->scope_service_id,
            'lead_status' => 'In Process',
            'order_status' => 'completed'
        ]);

        // Deduct amount from wallet
        $user->wallet_amount -= $totalAmount;
        $user->save();

        // Record wallet transaction
        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $totalAmount,
            'type' => 'debit',
            'note' => "Order #{$order->id} - {$request->main_service_name}" . ($request->scope_service_name ? " + {$request->scope_service_name}" : ""),
            'created_by' => Auth::id(),
            'created_at' => Carbon::now()->setTimezone('Asia/Kolkata')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order placed successfully.',
            'order_id' => $order->id,
            'redirect' => route('orders.show', $order->id)
        ]);
    }
}
