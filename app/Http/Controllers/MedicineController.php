<?php

namespace App\Http\Controllers;

use App\Models\HospitalService as Service;
use App\Models\MedicineStockMovement as StockMovement;
use App\Models\Department;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
   public function __construct()
{
    $this->middleware('auth');
}

    /**
     * Display a paginated list of medicines with current stock.
     */
    public function index()
    {
        // eager-load department and sum up stock movements
        $medicines = Service::with('department')
            ->withSum('stockMovements as quantity', 'delta')
            ->orderBy('service_name')
            ->paginate(15);

        return view('pharmacy.medicines.index', compact('medicines'));
    }

    /**
     * Show the form for creating a new medicine.
     */
    public function create()
    {
        $departments = Department::orderBy('department_name')->get();
        return view('pharmacy.medicines.create', compact('departments'));
    }

    /**
     * Store a newly created medicine and record its initial stock.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_name'  => 'required|string|max:255',
            'department_id' => 'required|exists:departments,department_id',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'quantity'      => 'required|integer|min:0',
        ]);

        // 1) Create the medicine record
        $medicine = Service::create([
            'service_name'  => $data['service_name'],
            'department_id' => $data['department_id'],
            'price'         => $data['price'],
            'description'   => $data['description'] ?? null,
        ]);

        // 2) Record initial stock movement
        if ($data['quantity'] > 0) {
            StockMovement::create([
                'service_id' => $medicine->service_id,
                'delta'      => $data['quantity'],
                'type'       => 'initial',
                'notes'      => 'Initial stock on creation',
            ]);
        }

        return redirect()
            ->route('pharmacy.medicines.index')
            ->with('success', 'Medicine added successfully.');
    }

    /**
     * Show the form for editing an existing medicine.
     */
    public function edit(Service $medicine)
    {
        $departments = Department::orderBy('department_name')->get();
        // load current stock to prefill the form
        $medicine->loadSum('stockMovements as quantity', 'delta');

        return view('pharmacy.medicines.edit', compact('medicine','departments'));
    }

    /**
     * Update the medicine and record any stock adjustments.
     */
    public function update(Request $request, Service $medicine)
    {
        $data = $request->validate([
            'service_name'  => 'required|string|max:255',
            'department_id' => 'required|exists:departments,department_id',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'quantity'      => 'required|integer|min:0',
        ]);

        // 1) Update core fields
        $medicine->update([
            'service_name'  => $data['service_name'],
            'department_id' => $data['department_id'],
            'price'         => $data['price'],
            'description'   => $data['description'] ?? null,
        ]);

        // 2) Check for stock delta
        $oldQty = $medicine->stockMovements()->sum('delta');
        $newQty = $data['quantity'];
        $diff   = $newQty - $oldQty;

        if ($diff !== 0) {
            StockMovement::create([
                'service_id' => $medicine->service_id,
                'delta'      => $diff,
                'type'       => 'adjustment',
                'notes'      => 'Adjusted stock from '.$oldQty.' to '.$newQty,
            ]);
        }

        return redirect()
            ->route('pharmacy.medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    /**
     * Remove the medicine.
     */
    public function destroy(Service $medicine)
    {
        $medicine->delete(); // cascades stock movements
        return redirect()
            ->route('pharmacy.medicines.index')
            ->with('success', 'Medicine removed successfully.');
    }
}
