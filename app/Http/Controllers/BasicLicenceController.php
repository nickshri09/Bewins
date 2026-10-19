<?php

namespace App\Http\Controllers;
use App\Models\Member;
use App\Models\Service;
   
use App\Models\CustomService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BasicLicenceController extends Controller
{ 



public function show($id)
{
    // Get the selected CustomService record
    $customService = CustomService::findOrFail($id);

    // Fetch the related Service using service_id
    $relatedService = Service::find($customService->service_id);

    // If the service was not found, handle gracefully
    if (!$relatedService) {
        abort(404, 'Related service not found.');
    }

    // Combine the data into one object using data from Service table
    $amendService = (object)[
        'service_name' => $relatedService->service_name,
        'fee' => $relatedService->fee,
        'obligation' => $relatedService->obligation,
        'frequency' => $relatedService->frequency,
        'maximum_tat' => $relatedService->maximum_tat,
    ];

    $scopeServices = Service::where('batch_id', $relatedService->batch_id)
                            ->where('id', '!=', $relatedService->id)
                            ->get();

    $members = Member::all();

    return view('layouts.menus.basic_licence', compact('amendService', 'scopeServices', 'members'));
}




   public function showServiceForm()
    {
        $services = Service::all();
        return view('layouts.menus.service-select', compact('services'));
    }



}
