<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\CustomField;
use App\Models\ContactCustomValue;
use App\Models\MergedContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use View;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::active()->latest()->paginate(10);
        $customFields = CustomField::all();
        return view('contacts.index', compact('contacts', 'customFields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customFields = CustomField::all();
        $action = route('contacts.store');
        $method = 'POST'; 
        $contact = new Contact();
        return view('contacts.create', compact('customFields', 'action', 'method', 'contact'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateContact($request);
    
        try {
            if ($request->hasFile('profile_image')) {
                $validated['profile_image'] = $request->file('profile_image')->store('profile_images');
            }
            
            if ($request->hasFile('additional_file')) {
                $validated['additional_file'] = $request->file('additional_file')->store('additional_files');
            }
            
            $contact = Contact::create($validated);
            $this->saveCustomFields($contact, $request);
            
            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully',
                'contact' => $contact->load('customValues.field')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating contact: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        // echo "<pre>"; print_r($contact->toArray()); exit;
        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        $customFields = CustomField::all();
        $action = route('contacts.update', $contact);
        $method = 'PUT';
        return view('contacts.edit', compact('contact', 'customFields', 'action', 'method'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $validated = $this->validateContact($request, $contact->id);
    
        try {
            if ($request->hasFile('profile_image')) {
                if ($contact->profile_image) {
                    Storage::delete($contact->profile_image);
                }
                $validated['profile_image'] = $request->file('profile_image')->store('profile_images');
            }
            
            if ($request->hasFile('additional_file')) {
                if ($contact->additional_file) {
                    Storage::delete($contact->additional_file);
                }
                $validated['additional_file'] = $request->file('additional_file')->store('additional_files');
            }
            
            $contact->update($validated);
            $this->saveCustomFields($contact, $request);
            
            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully',
                'contact' => $contact->load('customValues.field')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating contact: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        try {
            if ($contact->profile_image) {
                Storage::delete($contact->profile_image);
            }
            if ($contact->additional_file) {
                Storage::delete($contact->additional_file);
            }
            
            $contact->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting contact: ' . $e->getMessage()
            ], 500);
        }
    }

    // Merge functionality
    public function show_merge_form(Contact $contact)
    {
        $contacts = Contact::active()->where('id', '!=', $contact->id)->get();
        return view('contacts.merge', compact('contact', 'contacts'));
    }
    
    public function merge(Request $request, Contact $contact)
    {
        $request->validate([
            'master_contact_id' => 'required|exists:contacts,id',
        ]);
        
        $masterContact = Contact::findOrFail($request->master_contact_id);
        
        // Merge logic
        $mergedData = $this->performMerge($masterContact, $contact);
        
        // Create merge record
        MergedContact::create([
            'master_contact_id' => $masterContact->id,
            'merged_contact_id' => $contact->id,
            'merged_data' => $mergedData
        ]);
        
        $contact->update(['is_active' => false]);
        
        return redirect()->route('contacts.show', $masterContact)
            ->with('success', 'Contacts merged successfully.')
            ->with('merged_data', $mergedData);
    }
    
    // AJAX methods
    
    public function api_index()
    {
        $contacts = Contact::active()->with('customValues.field')->get();
        return response()->json($contacts);
    }
    
    public function api_store(Request $request)
    {
        $validated = $this->validateContact($request);
        
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('profile_images');
        }
        
        if ($request->hasFile('additional_file')) {
            $validated['additional_file'] = $request->file('additional_file')->store('additional_files');
        }
        
        $contact = Contact::create($validated);
        
        $this->saveCustomFields($contact, $request);
        
        return response()->json([
            'success' => true,
            'contact' => $contact->load('customValues.field')
        ]);
    }
    
    public function api_update(Request $request, Contact $contact)
    {
        $validated = $this->validateContact($request, $contact->id);
        
        if ($request->hasFile('profile_image')) {
            if ($contact->profile_image) {
                Storage::delete($contact->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('profile_images');
        }
        
        if ($request->hasFile('additional_file')) {
            if ($contact->additional_file) {
                Storage::delete($contact->additional_file);
            }
            $validated['additional_file'] = $request->file('additional_file')->store('additional_files');
        }
        
        $contact->update($validated);
        
        $this->saveCustomFields($contact, $request);
        
        return response()->json([
            'success' => true,
            'contact' => $contact->load('customValues.field')
        ]);
    }
    
    public function api_destroy(Contact $contact)
    {
        if ($contact->profile_image) {
            Storage::delete($contact->profile_image);
        }
        if ($contact->additional_file) {
            Storage::delete($contact->additional_file);
        }
        
        $contact->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function api_search(Request $request)
    {
        $query = Contact::active()->with('customValues.field');
        
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        if ($request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('custom_fields')) {
            foreach ($request->custom_fields as $fieldId => $value) {
                if (!empty($value)) {
                    $query->whereHas('customValues', function($q) use ($fieldId, $value) {
                        $q->where('field_id', $fieldId)
                        ->where('field_value', 'like', '%'.$value.'%');
                    });
                }
            }
        }
        
        $contacts = $query->get();
        
        $contacts = $query->paginate(10);
        $customFields = CustomField::all();
        
        if ($request->ajax()) {
            return view('contacts.partials.table', compact('contacts', 'customFields'));
        }

        return view('contacts.partials.table', compact('contacts', 'customFields'));
    }
    

    // Helper methods
    public function validateContact(Request $request, $ignoreId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email' . ($ignoreId ? ',' . $ignoreId : ''),
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|max:2048',
            'additional_file' => 'nullable|file|max:5120',
        ];
        
        return $request->validate($rules);
    }
    
    public function saveCustomFields(Contact $contact, Request $request)
    {
        $customFields = CustomField::all();
        
        foreach ($customFields as $field) {
            $value = $request->input('custom_field_' . $field->id);
            
            if ($value !== null) {
                ContactCustomValue::updateOrCreate(
                    ['contact_id' => $contact->id, 'field_id' => $field->id],
                    ['field_value' => $value]
                );
            } else {
                ContactCustomValue::where('contact_id', $contact->id)
                    ->where('field_id', $field->id)
                    ->delete();
            }
        }
    }
    
    public function performMerge(Contact $master, Contact $secondary)
    {
        $mergedData = [];
        
        $standardFields = ['phone', 'gender', 'profile_image', 'additional_file'];
        
        foreach ($standardFields as $field) {
            if (empty($master->$field) && !empty($secondary->$field)) {
                $master->$field = $secondary->$field;
                $mergedData[$field] = [
                    'from' => $secondary->$field,
                    'to' => $master->$field
                ];
            }
        }
        
        $master->save();
        
        foreach ($secondary->customValues as $secondaryValue) {
            $existingValue = $master->customValues()
                ->where('field_id', $secondaryValue->field_id)
                ->first();
            
            if (!$existingValue) {
                $master->customValues()->create([
                    'field_id' => $secondaryValue->field_id,
                    'field_value' => $secondaryValue->field_value
                ]);
                
                $mergedData['custom_fields'][$secondaryValue->field_id] = [
                    'from' => $secondaryValue->field_value,
                    'to' => $secondaryValue->field_value
                ];
            } elseif ($existingValue->field_value !== $secondaryValue->field_value) {
                $mergedData['custom_fields'][$secondaryValue->field_id] = [
                    'from' => $secondaryValue->field_value,
                    'to' => $existingValue->field_value,
                    'action' => 'kept_master_value'
                ];
            }
        }
        
        return $mergedData;
    }
}
