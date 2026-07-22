<?php

namespace App\Http\Controllers\Spot;

use App\Http\Controllers\Controller;
use App\Models\AdditionalService;
use Illuminate\Http\Request;
use App\Models\SpotBooking;
use App\Models\SpotPackage;
use App\Models\AdditionalServicePrice;
use App\Models\Spot;
use App\Models\SpotDetail;
use Illuminate\Support\Facades\Storage;





class AdditionalServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $services = AdditionalService::with('spots')
        ->latest()
        ->paginate(20);

    return view('pages.additional_service.index', compact('services'));
}
public function priceIndex()
{
    $services = AdditionalServicePrice::with('service')
        ->latest()
        ->paginate(2000000);

    return view('pages.additional_service.price_index', compact('services'));
}
    public function priceRules()
    {
        $services = AdditionalService::all();
        return view('pages.additional_service.price_add', compact('services'));
    }
public function additionalPriceStore(Request $request)
{
    $request->validate([
        'additional_service_id' => 'required|array',
        'additional_service_id.*' => 'exists:additional_services,id',
        'min_person' => 'required|numeric|min:0',
        'max_person' => 'required|numeric|gte:min_person',
        'price' => 'required|numeric|min:0',
        'status' => 'required|boolean',
    ]);

    foreach ($request->additional_service_id as $serviceId) {
        AdditionalServicePrice::create([
            'additional_service_id' => $serviceId,
            'min_person' => $request->min_person,
            'max_person' => $request->max_person,
            'price' => $request->price,
            'status' => $request->status,
        ]);
    }

    return redirect()
        ->route('additional-services.price.index')
        ->with('success', 'Additional Service Price Rule Created Successfully');
}
public function additionalPriceEdit($id)
{
    $rule = AdditionalServicePrice::findOrFail($id);
    $services = AdditionalService::all();

    return view('pages.additional_service.price_edit', compact('rule', 'services'));
}public function additionalPriceUpdate(Request $request, $id)
{
    $request->validate([
        'additional_service_id' => 'required|array',
        'additional_service_id.*' => 'exists:additional_services,id',
        'min_person' => 'required|numeric|min:0',
        'max_person' => 'required|numeric|gte:min_person',
        'price' => 'required|numeric|min:0',
        'status' => 'required|boolean',
    ]);

    $rule = AdditionalServicePrice::findOrFail($id);

    // 🔥 update main rule
    $rule->update([
        'min_person' => $request->min_person,
        'max_person' => $request->max_person,
        'price' => $request->price,
        'status' => $request->status,
    ]);

    // 🔥 IMPORTANT:
    // তুমি multiple service select করো, তাই existing rule delete করে নতুন create করবো
    AdditionalServicePrice::where('id', $id)->delete();

    foreach ($request->additional_service_id as $serviceId) {
        AdditionalServicePrice::create([
            'additional_service_id' => $serviceId,
            'min_person' => $request->min_person,
            'max_person' => $request->max_person,
            'price' => $request->price,
            'status' => $request->status,
        ]);
    }

    return redirect()
        ->route('additional-services.price.index')
        ->with('success', 'Price Rule Updated Successfully');
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $spots= Spot::all();

        return view('pages.additional_service.create',compact('spots'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'status' => 'required|boolean',
        'spot_ids' => 'nullable|array',
    ]);

    // 🔥 create service
    $service = AdditionalService::create([
        'title' => $request->title,
        'description' => $request->description,
        'price' => $request->price,
        'status' => $request->status,
        'is_frontend' => $request->is_frontend,
        'is_backend' => $request->is_backend,
        'is_global' => 0, // default
    ]);

    $spotIds = $request->spot_ids ?? [];

    // 🔥 GLOBAL CHECK (IMPORTANT FIX)
    if (in_array('global', $spotIds)) {

        // global service
        $service->update([
            'is_global' => 1
        ]);

        // optional: clear pivot
        $service->spots()->detach();

    } else {

        $service->update([
            'is_global' => 0
        ]);

        // remove "global" if accidentally sent
        $spotIds = array_filter($spotIds, function ($id) {
            return $id !== 'global';
        });

        // sync spots
        if (!empty($spotIds)) {
            $service->spots()->sync($spotIds);
        }
    }

    return redirect()->route('additional-services.index')
        ->with('message', 'Additional service created successfully!');
}


    /**
     * Display the specified resource.
     */
    public function show(AdditionalService $additionalService)
    {
        return view('pages.additional_service.show', compact('additionalService'));
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit(AdditionalService $additionalService)
{
    $spots = Spot::all();

    // selected spot ids (pivot table থেকে)
    $selectedSpotIds = $additionalService->spots->pluck('id')->toArray();

    return view('pages.additional_service.edit', compact(
        'additionalService',
        'spots',
        'selectedSpotIds'
    ));
}
    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, AdditionalService $additionalService)
{
    $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'price'       => 'required|numeric|min:0',
        'status'      => 'required|boolean',
        'spot_ids'    => 'nullable|array',
    ]);

    // 🔥 update main service
    $additionalService->update([
        'title' => $request->title,
        'description' => $request->description,
        'price' => $request->price,
        'status' => $request->status,
        'is_backend' => $request->is_backend,
        'is_frontend' => $request->is_frontend,
    ]);

    $spotIds = $request->spot_ids ?? [];

    // 🔥 GLOBAL logic
    if (in_array('global', $spotIds)) {

        $additionalService->update([
            'is_global' => 1
        ]);

        // remove all spot relations
        $additionalService->spots()->detach();

    } else {

        $additionalService->update([
            'is_global' => 0
        ]);

        // remove "global" if exists
        $spotIds = array_filter($spotIds, function ($id) {
            return $id !== 'global';
        });

        // sync spots
        if (!empty($spotIds)) {
            $additionalService->spots()->sync($spotIds);
        } else {
            $additionalService->spots()->detach();
        }
    }

    return redirect()
        ->route('additional-services.index')
        ->with([
            'message' => 'Additional service updated successfully!',
            'alert-type' => 'success'
        ]);
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdditionalService $additionalService)
    {
        $additionalService->delete();

        return redirect()
            ->route('additional-services.index')
            ->with([
                'message' => 'Additional service deleted successfully!',
                'alert-type' => 'success'
            ]);
    }
public function additionalPriceDestroy($id)
{
    $rule = AdditionalServicePrice::findOrFail($id);

    $rule->delete();

    return redirect()
        ->route('additional-services.price.index')
        ->with('success', 'Price Rule Deleted Successfully');
}
    public function toggleEditableAjax(Request $request)
{
    $service = AdditionalService::findOrFail($request->service_id);
    $service->editable_status = $request->status;
    $service->save();

    return response()->json(['success' => true]);
}

}
