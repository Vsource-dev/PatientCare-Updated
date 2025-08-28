<?php
// app/Http/Controllers/Admin/ResourceController.php

namespace App\Http\Controllers\Admin;

// Import the base controller, request class, and models we need
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Bed;
use App\Models\Department;

class ResourceController extends Controller
{
    /**
     * Show the list of rooms and beds.
     */
    public function index()
    {
        // Get all rooms, and also load related department + beds
        $rooms = Room::with(['department','beds'])->get();

        // Get all departments (used when creating new rooms)
        $departments = Department::all();

        // Pass data to the view (resources/index.blade.php)
        return view('admin.resources.index', compact('rooms','departments'));
    }

    /**
     * Show the form page for creating rooms/beds.
     */
    public function create()
    {
        // Load departments for the "department" dropdown
        $departments = Department::all();

        // Load all rooms with department (used when adding a bed)
        $rooms = Room::with('department')->get();

        // Pass data to the view (resources/create.blade.php)
        return view('admin.resources.create', compact('departments', 'rooms'));
    }

    /**
     * Store a newly created room or bed in the database.
     */
    public function store(Request $request)
    {
        // Check if the type being created is "room"
        if ($request->type === 'room') {
            // ✅ Validate input for a room
            $data = $request->validate([
                'department_id' => 'required|exists:departments,department_id', // must exist in departments table
                'room_number'   => 'required|string|max:50|unique:rooms,room_number', // unique room number
                'status'        => 'required|in:available,unavailable', // only allow these values
                'capacity'      => 'required|integer|min:1', // must be at least 1 bed
                'rate'          => 'required|numeric|min:0',
            ]);

            // Create the room record in the database
            Room::create($data);

        } 
        
        // Redirect back to index page with a success message
        return redirect()->route('admin.resources.index')
                         ->with('success', 'Resource created successfully.');
    }

    /**
     * Show the edit form for a room or bed.
     */
    public function edit($type, $id)
    {
        // Get all departments for the dropdown
        $departments = Department::all();

        // Get all rooms (for selecting when editing a bed)
        $rooms = Room::with('department')->get();

        if ($type === 'room') {
            // Find the room by ID (or fail if not found)
            $room = Room::findOrFail($id);

            // Pass data to edit view
            return view('admin.resources.edit', compact('room', 'departments', 'rooms'));
        }

        // Otherwise we’re editing a bed
        $bed = Bed::with('room.department')->findOrFail($id);

        // Pass data to edit view
        return view('admin.resources.edit', compact('bed', 'departments', 'rooms'));
    }

    /**
     * Update an existing room or bed.
     */
    public function update(Request $request, $type, $id)
{
    if ($type === 'room') {
        $room = Room::with('beds')->findOrFail($id);

        $data = $request->validate([
            'department_id' => 'required|exists:departments,department_id',
            'room_number'   => 'required|string|max:50|unique:rooms,room_number,'.$room->room_id.',room_id',
            'status'        => 'required|in:available,unavailable',
            'capacity'      => 'required|integer|min:1',
            'rate'          => 'required|numeric|min:0',
        ]);

        // Update room info
        $room->update($data);

        // Get current bed count
        $currentBeds = $room->beds()->count();
        $newCapacity = $data['capacity'];

        if ($newCapacity > $currentBeds) {
            // ✅ Add missing beds
            for ($i = $currentBeds + 1; $i <= $newCapacity; $i++) {
                Bed::create([
                    'room_id'    => $room->room_id,
                    'bed_number' => $room->room_number . '-B' . $i,
                    'status'     => 'available',
                    'rate'       => $data['rate'],
                ]);
            }
        } elseif ($newCapacity < $currentBeds) {
            // ✅ Remove extra unoccupied beds
            $bedsToRemove = $room->beds()
                ->whereNull('patient_id')
                ->orderByDesc('bed_id')
                ->take($currentBeds - $newCapacity)
                ->get();

            foreach ($bedsToRemove as $bed) {
                $bed->delete();
            }
        }

    } else {
        // --- Existing bed update logic ---
        $bed = Bed::findOrFail($id);

        $data = $request->validate([
            'room_id'    => 'required|exists:rooms,room_id',
            'bed_number' => 'required|string|max:50|unique:beds,bed_number,'.$bed->bed_id.',bed_id',
            'status'     => 'required|in:available,occupied',
            'rate'       => 'required|numeric|min:0',
            'patient_id' => 'nullable|exists:patients,patient_id',
        ]);

        $bed->update($data);
    }

    return redirect()->route('admin.resources.index')
                     ->with('success', ucfirst($type).' updated successfully.');
}

    /**
     * Delete a room or bed.
     */
    public function destroy($type, $id)
    {
        if ($type === 'room') {
            // Delete a room (and probably cascade delete its beds if set in model)
            Room::destroy($id);
        } else {
            // Delete a bed
            Bed::destroy($id);
        }

        // Redirect back with a success message
        return back()->with('success', ucfirst($type).' deleted.');
    }
}
