<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\MainService;
use PDF;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class ServiceController extends Controller
{
   public function create()
{
    $mainServices = MainService::orderBy('name')->get();
    return view('layouts.menus.addservice', compact('mainServices'));
}



public function store(Request $request)
{
    // Validation
    $request->validate([
        'product_category'         => 'nullable|string',
        'services.*.unique_id'     => 'required|distinct',
        'services.*.service_name'  => 'required',
        'services.*.fee'           => 'required|numeric',
        'obligation'               => 'required|string',
        'frequency'                => 'required|string',
        'maximum_tat'              => 'required|string',
        'main_service_id'          => 'required|exists:main_services,id',
    ]);

    $batchId = Str::uuid();

    $selectedMainService = MainService::find($request->main_service_id);

    foreach ($request->services as $srv) {
        Service::create([
            'unique_id'        => $srv['unique_id'],
            'service_name'     => $srv['service_name'],
            'fee'              => $srv['fee'],
            'batch_id'         => $batchId,
            'product_category' => $request->product_category,
            'obligation'       => $request->obligation,
            'frequency'        => $request->frequency,
            'maximum_tat'      => $request->maximum_tat,
            'main_service_id'  => $request->main_service_id,
            'main_service_name'=> $selectedMainService?->name,
        ]);
    }

    return redirect()->route('addservices.index')->with('success', 'Services added successfully!');
}

public function edit($batchId)
{
    $services = Service::where('batch_id', $batchId)->get();

    if ($services->isEmpty()) {
        return redirect()->route('addservices.index')->with('error', 'Service batch not found!');
    }

    $mainService = $services->first();
    $childServices = $services->slice(1);

    return view('layouts.menus.editservice', compact('mainService', 'childServices', 'batchId'));
}

public function update(Request $request, $batchId)
{
    // Validation
    $request->validate([
        'product_category'         => 'nullable|string',
        'services.*.unique_id'     => 'required|distinct',
        'services.*.service_name'  => 'required',
        'services.*.fee'           => 'required|numeric',
        'obligation'               => 'required|string',
        'frequency'                => 'required|string',
        'maximum_tat'              => 'required|string',
        'main_service_id'          => 'required|exists:main_services,id',
    ]);

    // Delete existing services for this batch
    Service::where('batch_id', $batchId)->delete();

    // Create new services
    $selectedMainService = MainService::find($request->main_service_id);
    foreach ($request->services as $srv) {
        Service::create([
            'unique_id'        => $srv['unique_id'],
            'service_name'     => $srv['service_name'],
            'fee'              => $srv['fee'],
            'batch_id'         => $batchId,
            'product_category' => $request->product_category,
            'obligation'       => $request->obligation,
            'frequency'        => $request->frequency,
            'maximum_tat'      => $request->maximum_tat,
            'main_service_id'  => $request->main_service_id,
            'main_service_name'=> $selectedMainService?->name,
        ]);
    }

    return redirect()->route('addservices.index')->with('success', 'Services updated successfully!');
}

    public function index()
    {
        // Load all main services with their related services so mains without services also appear
        $mains = \App\Models\MainService::with('services')->orderBy('created_at', 'desc')->get();

        // Build a grouped collection keyed by main_service_id => Collection<Service>
        $groupedServices = $mains->mapWithKeys(function ($main) {
            return [$main->id => $main->services];
        });

        // Map of main services by id for easy lookup in the view
        $mainServicesById = $mains->keyBy('id');

        return view('layouts.menus.viewservice', compact('groupedServices', 'mainServicesById'));
    }



    public function downloadView()
{
    $groupedServices = Service::all()->groupBy('batch_id');
    return view('layouts.menus.download', compact('groupedServices'));
}



 public function importView()
    {
        return view('layouts.menus.importservice');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            if ($extension === 'csv') {
                $data = $this->parseCsv($file);
            } else {
                $data = $this->parseExcel($file);
            }

            $batchId = Str::uuid();
            $importedCount = 0;
            $failedRows = [];

            foreach ($data as $row) {
                // Normalize and trim
                $mainServiceName = isset($row['main_service_name']) ? trim($row['main_service_name']) : '';
                $uniqueId        = isset($row['unique_id']) ? trim($row['unique_id']) : '';
                $serviceName     = isset($row['service_name']) ? trim($row['service_name']) : '';
                $fee             = isset($row['fee']) ? trim($row['fee']) : '';

                // Validate required fields for new import format
                if ($mainServiceName === '' || $uniqueId === '' || $serviceName === '' || $fee === '' || !is_numeric($fee)) {
                    $failedRows[] = [
                        'unique_id' => $uniqueId,
                        'reason' => 'Missing/invalid required fields (main_service_name, unique_id, service_name, fee)'
                    ];
                    continue;
                }

                // Resolve main_service_id by case-insensitive, trimmed name
                $mainServiceId = $this->resolveMainServiceIdByName($mainServiceName);
                if (!$mainServiceId) {
                    $failedRows[] = [
                        'unique_id' => $uniqueId,
                        'reason' => "Main service '{$mainServiceName}' not found"
                    ];
                    continue;
                }

                // Upsert by unique_id
                Service::updateOrCreate(
                    ['unique_id' => $uniqueId],
                    [
                        'service_name'     => $serviceName,
                        'fee'              => (float) $fee,
                        'batch_id'         => $batchId,
                        'product_category' => $row['product_category'] ?? null,
                        'obligation'       => $row['obligation'] ?? 'w',
                        'frequency'        => $row['frequency'] ?? 'w',
                        'maximum_tat'      => $row['maximum_tat'] ?? 'w',
                        'main_service_id'  => $mainServiceId,
                        'main_service_name'=> $mainServiceName,
                    ]
                );
                $importedCount++;
            }

            $message = "Successfully imported {$importedCount} services!";
            if (!empty($failedRows)) {
                $message .= " Skipped ".count($failedRows)." rows.";
                foreach ($failedRows as $fail) {
                    Log::warning('Service import skipped row', $fail);
                }
            }

            return redirect()->route('addservices.index')
                             ->with('success', $message)
                             ->with('import_errors', $failedRows);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    private function parseCsv($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return $data;
        }

        // Normalize headers: trim, lowercase, spaces to underscore
        $normalizedHeaders = array_map(function ($h) {
            return str_replace(' ', '_', strtolower(trim($h)));
        }, $headers);

        while (($row = fgetcsv($handle)) !== false) {
            // Trim each cell
            $row = array_map(function ($v) { return is_string($v) ? trim($v) : $v; }, $row);
            $assoc = array_combine($normalizedHeaders, $row);
            if ($assoc !== false) {
                $data[] = $assoc;
            }
        }

        fclose($handle);
        return $data;
    }

    private function parseExcel($file)
    {
        // For Excel files, you might want to use a package like PhpSpreadsheet
        // For now, returning empty array - you can implement this based on your needs
        return [];
    }

    private function resolveMainServiceIdByName(string $name): ?int
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return null;
        }

        // Case-insensitive match using lower() on both sides
        $mainService = \App\Models\MainService::whereRaw('LOWER(name) = ?', [mb_strtolower($trimmed)])->first();
        return $mainService?->id;
    }




}
