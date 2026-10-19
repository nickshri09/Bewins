<?php

namespace App\Http\Controllers;

use App\Models\AccessControl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $alert_col = 'col-lg-8 offset-lg-2';
        $role_id = null;
        $permission_list = [];
        $permission = [];

        return view('backend.permission.create', compact('alert_col', 'role_id', 'permission', 'permission_list'));
    }

    public function show($role_id = '')
    {
        $alert_col = 'col-lg-8 offset-lg-2';
        $permission_list = array();

        if ($role_id != '') {
            $permission_list = AccessControl::where('role_id', $role_id)
                ->pluck('permission')
                ->toArray();
        }

        $ignoreRoute = array(
            // 'support_tickets.destroy',
        );

        $routeCollection = Route::getRoutes();

        $routes = [];

        // loop through the collection of routes
        foreach ($routeCollection as $route) {
            // get the action which is an array of items
            $action = $route->getAction();

            // if the action has the key 'controller'
            if (array_key_exists('controller', $action)) {
                $middlewares = isset($action['middleware']) ? (is_array($action['middleware']) ? $action['middleware'] : [$action['middleware']]) : [];

                // Include customer routes

                /*
                 * if (in_array('customer', $middlewares)) {
                 *     continue;
                 * }
                 */

                // Skip if it doesn't have any of the expected backend middlewares
                if (empty(array_intersect(['permission', 'admin', 'auth'], $middlewares))) {
                    continue;
                }

                // explode the string with @ creating an array with a count of 2
                $explodedAction = explode('@', $action['controller']);

                if (!isset($routes[$explodedAction[0]])) {
                    $routes[$explodedAction[0]] = [];
                }

                if (isset($explodedAction[1]) && strpos($explodedAction[0], 'App') === 0) {
                    $test = new $explodedAction[0]();
                    if (method_exists($test, $explodedAction[1])) {
                        $routes[$explodedAction[0]][] = array('method' => $explodedAction[1], 'action' => $route->action);
                    }
                }
            }
        }

        $permission = array();

        foreach ($routes as $key => $route) {
            foreach ($route as $r) {
                if (array_key_exists('as', $r['action'])) {
                    $routeName = $r['action']['as'];
                    // If not needed so ignore
                    if (in_array($routeName, $ignoreRoute)) {
                        continue;
                    }
                    $permission[$key][$routeName] = $r['method'];
                }
            }
        }

        $permission = $this->transformPermissionForDisplay($permission);

        // Add manual Navigation & Heading permissions
        $navigationHeadings = [
            'name' => 'Navigation & Headings',
            'slug' => 'navigation-headings',
            'permissions' => [
                ['name' => 'section.user_management', 'label' => 'Show User Management Section', 'method' => 'show'],
                ['name' => 'section.taxation', 'label' => 'Show Taxation Section', 'method' => 'show'],
                ['name' => 'section.orders', 'label' => 'Show Orders Sub-section', 'method' => 'show'],
                ['name' => 'section.service_list', 'label' => 'Show Service List Sub-section', 'method' => 'show'],
                ['name' => 'section.action_required', 'label' => 'Show Action Required Sub-section', 'method' => 'show'],
                ['name' => 'section.fixed_deposit', 'label' => 'Show Fixed Deposit Section', 'method' => 'show'],
                ['name' => 'section.investment', 'label' => 'Show Investment Section', 'method' => 'show'],
                ['name' => 'section.expected_returns', 'label' => 'Show Expected Returns Section', 'method' => 'show'],
                ['name' => 'section.loans', 'label' => 'Show Loans Section', 'method' => 'show'],
                ['name' => 'section.branches', 'label' => 'Show Branches Section', 'method' => 'show'],
                ['name' => 'section.clients', 'label' => 'Show Clients Section', 'method' => 'show'],
                ['name' => 'section.accounts', 'label' => 'Show Accounts Section', 'method' => 'show'],
                ['name' => 'section.deposit', 'label' => 'Show Deposit Section', 'method' => 'show'],
                ['name' => 'section.withdraw', 'label' => 'Show Withdraw Section', 'method' => 'show'],
                ['name' => 'section.transactions', 'label' => 'Show Transactions Section', 'method' => 'show'],
                ['name' => 'section.expense', 'label' => 'Show Expense Section', 'method' => 'show'],
                ['name' => 'section.employee_management', 'label' => 'Show Employee Management Section', 'method' => 'show'],
                ['name' => 'section.reports', 'label' => 'Show Reports Section', 'method' => 'show'],
                ['name' => 'section.knowledge_base', 'label' => 'Show Knowledge Base Section', 'method' => 'show'],
                ['name' => 'section.pricing_plans', 'label' => 'Show Pricing Plans Section', 'method' => 'show'],
                ['name' => 'section.system_settings', 'label' => 'Show System Settings Section', 'method' => 'show'],
                ['name' => 'orders.download', 'label' => 'Download All Orders History Link', 'method' => 'show'],
                ['name' => 'approval.index', 'label' => 'Show Approval Pending Link', 'method' => 'show'],
                ['name' => 'documents.pending', 'label' => 'Show Document Pending Link', 'method' => 'show'],
                ['name' => 'section.customer_loans', 'label' => 'Show Portal: My Loans Section', 'method' => 'show'],
                ['name' => 'section.customer_transfers', 'label' => 'Show Portal: Transfer Money Section', 'method' => 'show'],
                ['name' => 'section.customer_deposits', 'label' => 'Show Portal: Deposit Money Section', 'method' => 'show'],
                ['name' => 'section.customer_withdrawals', 'label' => 'Show Portal: Withdraw Money Section', 'method' => 'show'],
                ['name' => 'section.customer_transactions', 'label' => 'Show Portal: Transaction Requests Section', 'method' => 'show'],
                ['name' => 'section.customer_reports', 'label' => 'Show Portal: Reports Section', 'method' => 'show'],
            ]
        ];

        // Add Service Categories dynamically
        $serviceCategoriesMap = [
            'amendment' => 'Amendment',
            'audit' => 'Audit',
            'company' => 'Company',
            'IntellectualProperty' => 'Intellectual Property',
            'Licences' => 'Licences',
            'MarketingMaterial' => 'Marketing Material',
            'Miscellaneous' => 'Miscellaneous',
            'NetWorth' => 'Net Worth',
            'NoticesLitigations' => 'Notices & Litigations',
            'ProjectReport' => 'Project Report & CMA',
            'Registration' => 'Registration & Certification',
            'Renewals' => 'Renewals',
            'Returns' => 'Returns',
            'Roc' => 'ROC',
            'Surrender' => 'Surrender & Closure',
        ];

        $servicePermissions = [];
        foreach ($serviceCategoriesMap as $slug => $name) {
            $servicePermissions[] = [
                'name' => 'category.' . strtolower($slug),
                'label' => 'Show ' . $name . ' Category',
                'method' => 'show'
            ];
        }

        $serviceModule = [
            'name' => 'Service Categories',
            'slug' => 'service-categories',
            'permissions' => $servicePermissions
        ];

        $permission[] = $navigationHeadings;
        $permission[] = $serviceModule;

        return view('backend.permission.create', compact('permission', 'permission_list', 'role_id', 'alert_col'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required',
            'permissions' => 'required',
        ]);

        DB::beginTransaction();

        $permission = AccessControl::where('role_id', $request->role_id);
        $permission->delete();

        foreach ($request->permissions as $role) {
            $permission = new AccessControl();
            $permission->role_id = $request->role_id;
            $permission->permission = $role;
            $permission->save();
        }

        DB::commit();

        return redirect()->route('permission.index')->with('success', _lang('Saved successfully'));
    }

    private function transformPermissionForDisplay(array $permission): array
    {
        return collect($permission)
            ->map(function ($routes, $controller) {
                $meta = $this->resolveModuleMeta($controller);

                $items = collect($routes)
                    ->map(function ($method, $routeName) {
                        return [
                            'name' => $routeName,
                            'method' => $method,
                            'label' => $this->formatPermissionLabel($routeName),
                        ];
                    })
                    ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->toArray();

                if (empty($items)) {
                    return null;
                }

                // Inject Heading Permission for each module
                $sectionPerm = 'section.' . $meta['slug'];
                array_unshift($items, [
                    'name' => $sectionPerm,
                    'method' => 'show',
                    'label' => 'Show ' . $meta['name'] . ' Section',
                ]);

                return array_merge($meta, ['permissions' => $items]);
            })
            ->filter()
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->toArray();
    }

    private function resolveModuleMeta(string $controller): array
    {
        $base = class_basename($controller);
        $moduleName = Str::replaceLast('Controller', '', $base);

        if ($moduleName === '') {
            $moduleName = $base;
        }

        $label = Str::headline(str_replace('_', ' ', $moduleName));
        $slug = Str::slug($moduleName);

        if ($slug === '') {
            $slug = Str::slug($base);
        }

        return [
            'controller' => $controller,
            'name' => $label,
            'slug' => $slug,
        ];
    }

    private function formatPermissionLabel(string $routeName): string
    {
        $segments = explode('.', $routeName);

        if (count($segments) === 1) {
            return Str::headline(str_replace('_', ' ', $routeName));
        }

        $primary = array_shift($segments);
        $primaryLabel = Str::headline(str_replace('_', ' ', $primary));

        $dictionary = [
            'index' => 'View List',
            'create' => 'Create',
            'store' => 'Store',
            'show' => 'View Details',
            'edit' => 'Edit',
            'update' => 'Update',
            'destroy' => 'Delete',
            'filter' => 'Filter',
            'download' => 'Download',
            'import' => 'Import',
            'export' => 'Export',
            'approve' => 'Approve',
            'reject' => 'Reject',
        ];

        $actionLabels = collect($segments)->map(function ($segment) use ($dictionary) {
            $normalized = strtolower($segment);
            if (array_key_exists($normalized, $dictionary)) {
                return $dictionary[$normalized];
            }

            return Str::headline(str_replace('_', ' ', $segment));
        });

        $actionLabel = $actionLabels->implode(' › ');

        return trim($primaryLabel . ' — ' . $actionLabel);
    }
}
