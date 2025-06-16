<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fields = CustomField::latest()->paginate(10);
        return view('custom-fields.index', compact('fields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('custom-fields.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|in:text,date,select,radio,checkbox,textarea',
            'field_options' => 'nullable|string',
        ]);
        
        if (in_array($validated['field_type'], ['select', 'radio'])) {
            $options = explode(',', $request->field_options);
            $validated['field_options'] = array_map('trim', $options);
        } else {
            $validated['field_options'] = null;
        }
        
        CustomField::create($validated);
        
        return redirect()->route('custom-fields.index')->with('success', 'Custom field created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomField $customField)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomField $customField)
    {
        return view('custom-fields.edit', compact('customField'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomField $customField)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|in:text,date,select,radio,checkbox,textarea',
            'field_options' => 'nullable|string',
        ]);
        
        if (in_array($validated['field_type'], ['select', 'radio'])) {
            $options = explode(',', $request->field_options);
            $validated['field_options'] = array_map('trim', $options);
        } else {
            $validated['field_options'] = null;
        }
        
        $customField->update($validated);
        
        return redirect()->route('custom-fields.index')->with('success', 'Custom field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomField $customField)
    {
        $customField->delete();
        return redirect()->route('custom-fields.index')->with('success', 'Custom field deleted successfully.');
    }
}
