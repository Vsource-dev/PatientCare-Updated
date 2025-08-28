<?php

namespace App\Http\Controllers;

use App\Models\HospitalService;
use Illuminate\Http\Request;

class HospitalServiceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_name'   => 'required|string|max:150',
            'department_id'  => 'required|exists:departments,department_id',
            'price'          => 'required|numeric|min:0',
            'quantity'      => 'required|integer|min:0',   
        ]);

        $service = HospitalService::create($data);

        return back()->with('success', 'Item “'.$service->service_name.'” added.');
    }

    public function update(Request $request, HospitalService $service)
    {
        $data = $request->validate([
            'service_name'  => 'required|string|max:150',
            'department_id' => 'required|exists:departments,department_id',
            'price'         => 'required|numeric|min:0',
            'quantity'      => 'required|integer|min:0',   
        ]);

        $service->update($data);

        return back()->with('success', 'Item “'.$service->service_name.'” updated.');
    }

    public function destroy(HospitalService $service)
    {
        $service->delete();

        return back()->with('success', 'Item deleted.');
    }

    public function department()
{
    return $this->belongsTo(\App\Models\Department::class, 'department_id', 'department_id');
}

}
