<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required_without:firstName|string|max:255',
            'firstName' => 'required_without:name|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'type' => 'nullable|in:general,support,complaint,suggestion,business,technical',
        ], [
            'name.required_without' => 'الاسم مطلوب',
            'firstName.required_without' => 'الاسم الأول مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'subject.required' => 'الموضوع مطلوب',
            'message.required' => 'الرسالة مطلوبة',
        ]);

        // Determine name based on form type
        $name = $request->name ?? ($request->firstName . ' ' . ($request->lastName ?? ''));

        $contact = Contact::create([
            'user_id' => Auth::id(),
            'name' => $name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'type' => $request->type ?? 'general',
            'priority' => $this->determinePriority($request->type ?? 'general'),
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.');
    }

    private function determinePriority(string $type): string
    {
        return match($type) {
            'complaint' => 'high',
            'support' => 'medium',
            'urgent' => 'urgent',
            default => 'low'
        };
    }
}
