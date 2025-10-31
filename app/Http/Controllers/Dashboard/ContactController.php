<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['user', 'assignedTo']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['created_at', 'name', 'subject', 'priority', 'status', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $contacts = $query->paginate(15);

        // Statistics
        $stats = [
            'total' => Contact::count(),
            'new' => Contact::new()->count(),
            'in_progress' => Contact::inProgress()->count(),
            'resolved' => Contact::resolved()->count(),
            'high_priority' => Contact::highPriority()->count(),
            'unassigned' => Contact::unassigned()->count(),
            'today' => Contact::whereDate('created_at', today())->count(),
            'this_week' => Contact::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // Filter options
        $filterOptions = [
            [
                'name' => 'status',
                'label' => 'الحالة',
                'type' => 'select',
                'placeholder' => 'اختر الحالة',
                'options' => [
                    'new' => 'جديد',
                    'in_progress' => 'قيد المعالجة',
                    'resolved' => 'تم الحل',
                    'closed' => 'مغلق',
                    'spam' => 'مزعج',
                ]
            ],
            [
                'name' => 'type',
                'label' => 'النوع',
                'type' => 'select',
                'placeholder' => 'اختر النوع',
                'options' => [
                    'general' => 'عام',
                    'support' => 'دعم فني',
                    'complaint' => 'شكوى',
                    'suggestion' => 'اقتراح',
                    'business' => 'أعمال',
                    'technical' => 'تقني',
                ]
            ],
            [
                'name' => 'priority',
                'label' => 'الأولوية',
                'type' => 'select',
                'placeholder' => 'اختر الأولوية',
                'options' => [
                    'low' => 'منخفض',
                    'medium' => 'متوسط',
                    'high' => 'عالي',
                    'urgent' => 'عاجل',
                ]
            ],
            [
                'name' => 'assigned_to',
                'label' => 'المعين إلى',
                'type' => 'select',
                'placeholder' => 'اختر المستخدم',
                'options' => collect(User::where('is_active', true)->get(['id', 'first_name', 'last_name']))
                    ->mapWithKeys(function ($user) {
                        return [$user->id => $user->display_name];
                    })->toArray()
            ],
            [
                'name' => 'date_from',
                'label' => 'من تاريخ',
                'type' => 'date',
            ],
            [
                'name' => 'date_to',
                'label' => 'إلى تاريخ',
                'type' => 'date',
            ],
        ];

        return view('dashboard.contacts.index', compact('contacts', 'stats', 'filterOptions'));
    }

    public function show(Contact $contact)
    {
        $contact->load(['user', 'assignedTo']);
        return view('dashboard.contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $contact->load(['user', 'assignedTo']);
        $users = User::where('is_active', true)->get(['id', 'first_name', 'last_name']);

        $statuses = [
            'new' => 'جديد',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق',
            'spam' => 'مزعج',
        ];

        $types = [
            'general' => 'عام',
            'support' => 'دعم فني',
            'complaint' => 'شكوى',
            'suggestion' => 'اقتراح',
            'business' => 'أعمال',
            'technical' => 'تقني',
        ];

        $priorities = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
        ];

        return view('dashboard.contacts.edit', compact('contact', 'users', 'statuses', 'types', 'priorities'));
    }

    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved,closed,spam',
            'type' => 'required|in:general,support,complaint,suggestion,business,technical',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'admin_response' => 'nullable|string|max:5000',
        ]);

        $contact->update([
            'status' => $request->status,
            'type' => $request->type,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'admin_response' => $request->admin_response,
            'responded_at' => $request->admin_response ? now() : $contact->responded_at,
        ]);

        return redirect()->route('dashboard.contacts.show', $contact)
            ->with('success', 'تم تحديث رسالة التواصل بنجاح');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('dashboard.contacts.index')
            ->with('success', 'تم حذف رسالة التواصل بنجاح');
    }

    public function markAsInProgress(Contact $contact)
    {
        $contact->markAsInProgress();
        return response()->json(['success' => true, 'message' => 'تم تحديث الحالة إلى قيد المعالجة']);
    }

    public function markAsResolved(Contact $contact)
    {
        $contact->markAsResolved();
        return response()->json(['success' => true, 'message' => 'تم تحديث الحالة إلى تم الحل']);
    }

    public function markAsClosed(Contact $contact)
    {
        $contact->markAsClosed();
        return response()->json(['success' => true, 'message' => 'تم تحديث الحالة إلى مغلق']);
    }

    public function markAsSpam(Contact $contact)
    {
        $contact->markAsSpam();
        return response()->json(['success' => true, 'message' => 'تم تحديث الحالة إلى مزعج']);
    }

    public function assignTo(Request $request, Contact $contact)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->assigned_to);
        $contact->assignTo($user);

        return response()->json([
            'success' => true,
            'message' => "تم تعيين الرسالة إلى {$user->display_name}"
        ]);
    }

    public function respond(Request $request, Contact $contact)
    {
        $request->validate([
            'admin_response' => 'required|string|max:5000',
        ]);

        $contact->respond($request->admin_response);

        return response()->json(['success' => true, 'message' => 'تم إرسال الرد بنجاح']);
    }

    public function updatePriority(Request $request, Contact $contact)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $contact->updatePriority($request->priority);

        return response()->json(['success' => true, 'message' => 'تم تحديث الأولوية بنجاح']);
    }

    public function updateType(Request $request, Contact $contact)
    {
        $request->validate([
            'type' => 'required|in:general,support,complaint,suggestion,business,technical',
        ]);

        $contact->updateType($request->type);

        return response()->json(['success' => true, 'message' => 'تم تحديث النوع بنجاح']);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_in_progress,mark_resolved,mark_closed,mark_spam,assign,delete',
            'contact_ids' => 'required|array|min:1',
            'contact_ids.*' => 'exists:contacts,id',
        ]);

        $contacts = Contact::whereIn('id', $request->contact_ids);

        switch ($request->action) {
            case 'mark_in_progress':
                $contacts->update(['status' => 'in_progress']);
                $message = 'تم تحديث الحالة إلى قيد المعالجة';
                break;
            case 'mark_resolved':
                $contacts->update(['status' => 'resolved']);
                $message = 'تم تحديث الحالة إلى تم الحل';
                break;
            case 'mark_closed':
                $contacts->update(['status' => 'closed']);
                $message = 'تم تحديث الحالة إلى مغلق';
                break;
            case 'mark_spam':
                $contacts->update(['status' => 'spam']);
                $message = 'تم تحديث الحالة إلى مزعج';
                break;
            case 'assign':
                $request->validate(['assigned_to' => 'required|exists:users,id']);
                $contacts->update(['assigned_to' => $request->assigned_to]);
                $message = 'تم تعيين الرسائل بنجاح';
                break;
            case 'delete':
                $contacts->delete();
                $message = 'تم حذف الرسائل بنجاح';
                break;
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function export(Request $request)
    {
        $query = Contact::with(['user', 'assignedTo']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $contacts = $query->orderBy('created_at', 'desc')->get();

        $filename = 'contacts_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // Headers
            fputcsv($file, [
                'ID', 'الاسم', 'البريد الإلكتروني', 'الهاتف', 'الموضوع', 'النوع',
                'الأولوية', 'الحالة', 'المستخدم المسجل', 'المعين إلى', 'تاريخ الإنشاء', 'تاريخ الرد'
            ]);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->name,
                    $contact->email,
                    $contact->phone ?? '',
                    $contact->subject,
                    $contact->type_text,
                    $contact->priority_text,
                    $contact->status_text,
                    $contact->user ? $contact->user->display_name : 'غير مسجل',
                    $contact->assignedTo ? $contact->assignedTo->display_name : 'غير معين',
                    $contact->formatted_created_at,
                    $contact->formatted_responded_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function stats()
    {
        $stats = [
            'total_contacts' => Contact::count(),
            'new_contacts' => Contact::new()->count(),
            'in_progress_contacts' => Contact::inProgress()->count(),
            'resolved_contacts' => Contact::resolved()->count(),
            'closed_contacts' => Contact::closed()->count(),
            'spam_contacts' => Contact::spam()->count(),
            'high_priority_contacts' => Contact::highPriority()->count(),
            'unassigned_contacts' => Contact::unassigned()->count(),
            'today_contacts' => Contact::whereDate('created_at', today())->count(),
            'this_week_contacts' => Contact::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_contacts' => Contact::whereMonth('created_at', now()->month)->count(),
        ];

        // Type statistics
        $typeStats = Contact::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => $item->count];
            });

        // Priority statistics
        $priorityStats = Contact::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->priority => $item->count];
            });

        // Status statistics
        $statusStats = Contact::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        // Monthly trend
        $monthlyTrend = Contact::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'stats' => $stats,
            'type_stats' => $typeStats,
            'priority_stats' => $priorityStats,
            'status_stats' => $statusStats,
            'monthly_trend' => $monthlyTrend,
        ]);
    }
}
