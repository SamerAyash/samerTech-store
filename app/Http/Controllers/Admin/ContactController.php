<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.contact.index');
    }

    /**
     * Get contacts data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $query = Contact::orderByDesc('created_at');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                // Handle global search
                if ($request->has('search') && !empty($request->search['value'])) {
                    $keyword = $request->search['value'];
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', '%' . $keyword . '%')
                          ->orWhere('contacts.email', 'like', '%' . $keyword . '%')
                          ->orWhere('contacts.phone', 'like', '%' . $keyword . '%')
                          ->orWhere('contacts.subject', 'like', '%' . $keyword . '%');
                    });
                }
                
                // Handle column-specific search
                if ($request->has('columns')) {
                    foreach ($request->columns as $column) {
                        if (isset($column['data']) && isset($column['search']['value']) && !empty($column['search']['value'])) {
                            $searchValue = $column['search']['value'];
                            
                            switch ($column['data']) {
                                case 'name':
                                    $query->where('contacts.name', 'like', '%' . $searchValue . '%');
                                    break;
                                case 'email':
                                    $query->where('contacts.email', 'like', '%' . $searchValue . '%');
                                    break;
                                case 'subject':
                                    $query->where('contacts.subject', 'like', '%' . $searchValue . '%');
                                    break;
                            }
                        }
                    }
                }
            })
            ->editColumn('id', function ($contact) {
                return '<span class="text-dark font-weight-bold">#' . $contact->id . '</span>';
            })
            ->editColumn('name', function ($contact) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e($contact->name) . '</span>
                    <span class="text-muted font-size-sm">' . e($contact->email) . '</span>
                </div>';
            })
            ->editColumn('subject', function ($contact) {
                return '<span class="text-dark font-weight-bold">' . e($contact->subject) . '</span>';
            })
            ->editColumn('readed', function ($contact) {
                if ($contact->readed) {
                    return '<span class="badge badge-success">
                        <i class="flaticon2-check-mark"></i> Read
                    </span>';
                }
                return '<span class="badge badge-warning">
                    <i class="flaticon2-mail"></i> Unread
                </span>';
            })
            ->editColumn('created_at', function ($contact) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark">' . $contact->created_at->format('M d, Y') . '</span>
                    <span class="text-muted font-size-sm">' . $contact->created_at->format('h:i A') . '</span>
                </div>';
            })
            ->addColumn('actions', function ($contact) {
                return view('admin.contact._actions', compact('contact'))->render();
            })
            ->rawColumns(['id', 'name', 'phone', 'subject', 'message', 'readed', 'created_at', 'actions'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        // Mark as read when viewing
        if (!$contact->readed) {
            $contact->update(['readed' => true]);
        }
        
        return view('admin.contact.show', compact('contact'));
    }

    /**
     * Toggle read status.
     */
    public function updateStatus(Request $request, Contact $contact)
    {
        $contact->update(['readed' => !$contact->readed]);

        return response()->json([
            'success' => true,
            'message' => 'Contact status updated successfully.',
            'readed' => $contact->readed
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contactName = $contact->name;
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact "' . $contactName . '" deleted successfully.'
        ]);
    }
}
