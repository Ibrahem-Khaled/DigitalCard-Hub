<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIChatController extends Controller
{
    private $openaiApiKey;
    private $openaiBaseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
    }

    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'session_id' => 'nullable|string',
                'context' => 'nullable|array'
            ]);

            $userMessage = $request->input('message');
            $sessionId = $request->input('session_id', 'default');
            $context = $request->input('context', []);

            // Get conversation history
            $conversationHistory = $this->getConversationHistory($sessionId);

            // Analyze user intent
            $intent = $this->analyzeIntent($userMessage);

            // Get relevant products if needed
            $relevantProducts = $this->getRelevantProducts($userMessage, $intent);

            // Prepare system prompt
            $systemPrompt = $this->buildSystemPrompt($relevantProducts, $context);

            // Build messages array
            $messages = $this->buildMessagesArray($systemPrompt, $conversationHistory, $userMessage);

            // Call OpenAI API
            $aiResponse = $this->callOpenAI($messages);

            // Process AI response
            $processedResponse = $this->processAIResponse($aiResponse, $intent, $relevantProducts);

            // Save conversation
            $this->saveConversation($sessionId, $userMessage, $processedResponse['message']);

            return response()->json($processedResponse);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'عذراً، حدث خطأ في النظام. يرجى المحاولة مرة أخرى.',
                'action' => null,
                'products' => []
            ], 500);
        }
    }

    private function analyzeIntent($message)
    {
        $message = strtolower($message);

        if (strpos($message, 'بحث') !== false || strpos($message, 'منتج') !== false || strpos($message, 'بطاقة') !== false) {
            return 'search_products';
        }

        if (strpos($message, 'سلة') !== false || strpos($message, 'عربة') !== false) {
            return 'cart_help';
        }

        if (strpos($message, 'سعر') !== false || strpos($message, 'تكلفة') !== false) {
            return 'product_info';
        }

        if (strpos($message, 'مساعدة') !== false || strpos($message, 'مساعدة') !== false) {
            return 'help';
        }

        return 'general';
    }

    private function getRelevantProducts($message, $intent)
    {
        if (!in_array($intent, ['search_products', 'product_info', 'cart_help'])) {
            return collect([]);
        }

        // Extract keywords from message
        $keywords = $this->extractKeywords($message);

        if (empty($keywords)) {
            return Product::active()->featured()->limit(5)->get();
        }

        $query = Product::active();

        // Search by name, description, or tags
        foreach ($keywords as $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('short_description', 'like', "%{$keyword}%")
                  ->orWhere('brand', 'like', "%{$keyword}%")
                  ->orWhere('card_provider', 'like', "%{$keyword}%")
                  ->orWhere('card_type', 'like', "%{$keyword}%")
                  ->orWhereJsonContains('tags', $keyword);
            });
        }

        $products = $query->limit(8)->get();

        // If no products found, return featured products
        if ($products->isEmpty()) {
            $products = Product::active()->featured()->limit(5)->get();
        }

        return $products;
    }

    private function extractKeywords($message)
    {
        $message = strtolower($message);

        // Common Arabic stop words
        $stopWords = ['في', 'من', 'إلى', 'على', 'هذا', 'هذه', 'التي', 'الذي', 'التي', 'أريد', 'أحتاج', 'أبحث', 'عن', 'من', 'بطاقة', 'بطاقات'];

        // Extract words
        $words = preg_split('/\s+/', $message);
        $keywords = [];

        foreach ($words as $word) {
            $word = trim($word, '.,!?');
            if (strlen($word) > 2 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }

        return array_unique($keywords);
    }

    private function buildSystemPrompt($products, $context)
    {
        $productInfo = '';

        if (collect($products)->isNotEmpty()) {
            $productInfo = "\n\nالمنتجات المتاحة في المتجر:\n";
            foreach ($products as $product) {
                $productInfo .= "- {$product->name}: {$product->short_description} (السعر: {$product->current_price} ر.س)\n";
                if ($product->sale_price) {
                    $productInfo .= "  (خصم: {$product->sale_price} ر.س بدلاً من {$product->price} ر.س)\n";
                }
            }
        }

        return "أنت مساعد ذكي لمتجر البطاقات الرقمية. مهمتك مساعدة العملاء في:

1. البحث عن المنتجات وإعطاء معلومات مفصلة عنها
2. إضافة المنتجات إلى السلة
3. الإجابة على استفسارات العملاء
4. حل المشاكل التقنية
5. تقديم نصائح للشراء

قواعد مهمة:
- تحدث باللغة العربية دائماً
- كن مفيداً ومهذباً
- قدم روابط مباشرة للمنتجات عند الحاجة
- استخدم تنسيق [اسم المنتج](رابط المنتج) للروابط
- إذا لم تجد منتجاً محدداً، اقترح منتجات مشابهة
- شجع العملاء على الشراء بطريقة مهذبة

{$productInfo}

السياق الحالي: " . json_encode($context, JSON_UNESCAPED_UNICODE);
    }

    private function buildMessagesArray($systemPrompt, $conversationHistory, $userMessage)
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Add recent conversation history (last 6 messages)
        $recentHistory = array_slice($conversationHistory, -6);
        foreach ($recentHistory as $message) {
            $messages[] = [
                'role' => $message['role'],
                'content' => $message['content']
            ];
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        return $messages;
    }

    private function callOpenAI($messages)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiApiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->openaiBaseUrl, [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'presence_penalty' => 0.1,
            'frequency_penalty' => 0.1,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $data = $response->json();

        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \Exception('Invalid OpenAI API response');
        }

        return $data['choices'][0]['message']['content'];
    }

    private function processAIResponse($aiResponse, $intent, $products)
    {
        $response = [
            'message' => $aiResponse,
            'action' => null,
        ];

        // Extract action from AI response
        if (strpos($aiResponse, 'add_to_cart') !== false) {
            $response['action'] = 'add_to_cart';
        } elseif (strpos($aiResponse, 'redirect') !== false) {
            $response['action'] = 'redirect';
        }

        return $response;
    }

    private function getConversationHistory($sessionId)
    {
        $cacheKey = "ai_chat_history_{$sessionId}";
        return Cache::get($cacheKey, []);
    }

    private function saveConversation($sessionId, $userMessage, $aiResponse)
    {
        $cacheKey = "ai_chat_history_{$sessionId}";
        $history = Cache::get($cacheKey, []);

        $history[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()
        ];

        $history[] = [
            'role' => 'assistant',
            'content' => $aiResponse,
            'timestamp' => now()
        ];

        // Keep only last 20 messages
        $history = array_slice($history, -20);

        Cache::put($cacheKey, $history, now()->addHours(24));
    }

    public function getProductSuggestions(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%")
                  ->orWhere('card_provider', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'slug', 'current_price', 'image']);

        return response()->json($products);
    }
}
