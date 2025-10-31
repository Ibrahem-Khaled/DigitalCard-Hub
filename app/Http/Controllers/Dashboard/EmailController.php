<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class EmailController extends Controller
{
    /**
     * Display the email sending form.
     */
    public function index()
    {
        return view('dashboard.email.index');
    }

    /**
     * Send a test email.
     */
    public function sendTestEmail(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'recipient_email' => 'required|email|max:255',
                'recipient_name' => 'nullable|string|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
                'sender_name' => 'nullable|string|max:255',
                'attachments' => 'nullable|array|max:5',
                'attachments.*' => 'file|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif|max:10240', // 10MB max
            ], [
                'recipient_email.required' => 'البريد الإلكتروني للمستقبل مطلوب',
                'recipient_email.email' => 'البريد الإلكتروني غير صحيح',
                'subject.required' => 'موضوع الرسالة مطلوب',
                'message.required' => 'محتوى الرسالة مطلوب',
                'message.max' => 'محتوى الرسالة لا يجب أن يتجاوز 5000 حرف',
                'attachments.max' => 'لا يمكن إرفاق أكثر من 5 ملفات',
                'attachments.*.file' => 'الملف المرفق غير صحيح',
                'attachments.*.mimes' => 'نوع الملف غير مدعوم',
                'attachments.*.max' => 'حجم الملف لا يجب أن يتجاوز 10 ميجابايت',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى تصحيح الأخطاء التالية:',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare email data
            $emailData = [
                'subject' => $request->subject,
                'body' => $request->message,
                'recipient_email' => $request->recipient_email,
                'recipient_name' => $request->recipient_name ?: 'عزيزي العميل',
                'sender_name' => $request->sender_name ?: config('mail.from.name', 'متجر البطاقات الرقمية'),
                'attachments' => []
            ];

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('email-attachments', 'public');
                    $emailData['attachments'][] = [
                        'path' => storage_path('app/public/' . $path),
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                    ];
                }
            }

            // Create and send the email
            $email = new TestEmail(
                $emailData['subject'],
                $emailData['body'],
                $emailData['recipient_email'],
                $emailData['recipient_name'],
                $emailData['sender_name'],
                $emailData['attachments']
            );

            // Send the email
            Mail::to($emailData['recipient_email'])->send($email);

            // Log the email sending
            Log::info('Test email sent successfully', [
                'recipient' => $emailData['recipient_email'],
                'subject' => $emailData['subject'],
                'sent_at' => now(),
                'sender_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clean up temporary files
            $this->cleanupAttachments($emailData['attachments']);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الرسالة بنجاح!',
                'data' => [
                    'recipient' => $emailData['recipient_email'],
                    'subject' => $emailData['subject'],
                    'sent_at' => now()->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to send test email', [
                'error' => $e->getMessage(),
                'recipient' => $request->recipient_email ?? 'unknown',
                'subject' => $request->subject ?? 'unknown',
                'sent_at' => now(),
            ]);

            // Clean up attachments in case of error
            if (isset($emailData['attachments'])) {
                $this->cleanupAttachments($emailData['attachments']);
            }

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إرسال الرسالة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send bulk emails (for future use).
     */
    public function sendBulkEmails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'recipients' => 'required|array|min:1|max:100',
                'recipients.*' => 'email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
                'sender_name' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى تصحيح الأخطاء التالية:',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sentCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($request->recipients as $recipient) {
                try {
                    $email = new TestEmail(
                        $request->subject,
                        $request->message,
                        $recipient,
                        'عزيزي العميل',
                        $request->sender_name ?: config('mail.from.name', 'متجر البطاقات الرقمية'),
                        []
                    );

                    Mail::to($recipient)->send($email);
                    $sentCount++;

                } catch (Exception $e) {
                    $failedCount++;
                    $errors[] = "فشل إرسال الرسالة إلى {$recipient}: " . $e->getMessage();
                }
            }

            Log::info('Bulk emails sent', [
                'total' => count($request->recipients),
                'sent' => $sentCount,
                'failed' => $failedCount,
                'subject' => $request->subject,
                'sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال {$sentCount} رسالة بنجاح",
                'data' => [
                    'sent_count' => $sentCount,
                    'failed_count' => $failedCount,
                    'errors' => $errors,
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send bulk emails', [
                'error' => $e->getMessage(),
                'recipients_count' => count($request->recipients ?? []),
                'sent_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إرسال الرسائل: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get email configuration status.
     */
    public function getEmailStatus()
    {
        try {
            $config = [
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_username' => config('mail.mailers.smtp.username'),
                'mail_from_address' => config('mail.from.address'),
                'mail_from_name' => config('mail.from.name'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
            ];

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب إعدادات البريد: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test email configuration.
     */
    public function testEmailConfig()
    {
        try {
            // Send a test email to the configured from address
            $email = new TestEmail(
                'اختبار إعدادات البريد الإلكتروني',
                'هذه رسالة اختبار للتأكد من صحة إعدادات البريد الإلكتروني.',
                config('mail.from.address'),
                'المدير',
                config('mail.from.name', 'متجر البطاقات الرقمية'),
                []
            );

            Mail::to(config('mail.from.address'))->send($email);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال رسالة اختبار بنجاح! تحقق من صندوق الوارد.',
            ]);

        } catch (Exception $e) {
            Log::error('Email configuration test failed', [
                'error' => $e->getMessage(),
                'tested_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل اختبار إعدادات البريد: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clean up temporary attachment files.
     */
    private function cleanupAttachments(array $attachments)
    {
        foreach ($attachments as $attachment) {
            if (isset($attachment['path']) && file_exists($attachment['path'])) {
                try {
                    unlink($attachment['path']);
                } catch (Exception $e) {
                    Log::warning('Failed to cleanup attachment file', [
                        'file' => $attachment['path'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}


