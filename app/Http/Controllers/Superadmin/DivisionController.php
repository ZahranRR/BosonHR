<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:divisions.index')->only('index');
        $this->middleware('permission:divisions.create')->only(['create', 'store']);
        $this->middleware('permission:divisions.edit')->only(['edit', 'update']);
        $this->middleware('permission:divisions.delete')->only('destroy');
    }
    public function index(Request $request)
    {
        $search = $request->query('search');

        $divisions = Division::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
        })
            ->orderBy('name', 'asc') // Panggil orderBy sebelum paginate
            ->paginate(10);

        return view('Superadmin.Employeedata.Division.index', compact('divisions'));
    }

    public function create()
    {
        return view('Superadmin.Employeedata.Division.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'has_overtime' => 'required|boolean',
            'work_days' => 'required|array',
            'work_days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);
        try {
            Division::create($request->all());

            return redirect()->route('divisions.index')->with('success', 'Division created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function edit(Division $division)
    {
        return view('Superadmin.Employeedata.Division.update', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'overtime' => 'required|boolean',
            'work_days' => 'required|array',
            'work_days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);
        try {
            $division->update($request->all());

            return redirect()->route('divisions.index')->with('success', 'Division updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return redirect()->route('divisions.index')->with('success', 'Division deleted successfully.');
    }
}
