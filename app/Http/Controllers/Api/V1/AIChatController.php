<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Models\AIKnowledgeBase;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AIChatController extends BaseController
{
    private $openaiApiKey;
    private $openaiBaseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
    }

    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string',
            'context' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        try {
            $userMessage = $request->input('message');
            $sessionId = $request->input('session_id', 'default');
            $context = $request->input('context', []);

            // Get conversation history
            $conversationHistory = $this->getConversationHistory($sessionId);

            // Analyze user intent
            $intent = $this->analyzeIntent($userMessage);

            // Get relevant products if needed
            $relevantProducts = $this->getRelevantProducts($userMessage, $intent);

            // Get site information
            $siteInfo = $this->getSiteInformation();
            
            // Get relevant knowledge base items
            $knowledgeBase = $this->getRelevantKnowledgeBase($userMessage);

            // Prepare system prompt
            $systemPrompt = $this->buildSystemPrompt($relevantProducts, $siteInfo, $knowledgeBase, $context);

            // Build messages array
            $messages = $this->buildMessagesArray($systemPrompt, $conversationHistory, $userMessage);

            // Call OpenAI API
            $aiResponse = $this->callOpenAI($messages);

            // Process AI response
            $processedResponse = $this->processAIResponse($aiResponse, $intent, $relevantProducts);

            // Save conversation
            $this->saveConversation($sessionId, $userMessage, $processedResponse['message']);

            return $this->successResponse($processedResponse);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return $this->errorResponse('عذراً، حدث خطأ في النظام. يرجى المحاولة مرة أخرى.', 500);
        }
    }

    public function getProductSuggestions(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return $this->successResponse([]);
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

        return $this->successResponse(ProductResource::collection($products));
    }

    // Private methods (same as original)
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

        $keywords = $this->extractKeywords($message);

        if (empty($keywords)) {
            return Product::active()->featured()->limit(5)->get();
        }

        $query = Product::active();

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

        if ($products->isEmpty()) {
            $products = Product::active()->featured()->limit(5)->get();
        }

        return $products;
    }

    private function extractKeywords($message)
    {
        $message = strtolower($message);
        $stopWords = ['في', 'من', 'إلى', 'على', 'هذا', 'هذه', 'التي', 'الذي', 'التي', 'أريد', 'أحتاج', 'أبحث', 'عن', 'من', 'بطاقة', 'بطاقات'];
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

    private function getSiteInformation()
    {
        $cacheKey = 'ai_site_information';
        
        return Cache::remember($cacheKey, now()->addHours(24), function () {
            $info = [];
            $info['site_name'] = Setting::get('site_name', 'متجر البطاقات الرقمية');
            $info['site_description'] = Setting::get('site_description', '');
            $info['categories'] = Category::active()
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'description']);
            return $info;
        });
    }

    private function getRelevantKnowledgeBase($message)
    {
        $keywords = $this->extractKeywords($message);
        
        if (empty($keywords)) {
            return AIKnowledgeBase::active()
                ->orderBy('priority', 'desc')
                ->limit(5)
                ->get();
        }

        $query = AIKnowledgeBase::active();
        
        foreach ($keywords as $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%")
                  ->orWhereJsonContains('tags', $keyword);
            });
        }

        $results = $query->orderBy('priority', 'desc')->limit(5)->get();
        
        if ($results->isEmpty()) {
            return AIKnowledgeBase::active()
                ->orderBy('priority', 'desc')
                ->limit(3)
                ->get();
        }

        return $results;
    }

    private function buildSystemPrompt($products, $siteInfo, $knowledgeBase, $context)
    {
        $currencyService = app(CurrencyService::class);
        $userCurrency = 'OMR'; // Default for API
        $currencySymbol = $currencyService->getCurrencySymbol($userCurrency);
        
        $productInfo = '';
        if (collect($products)->isNotEmpty()) {
            $productInfo = "\n\nالمنتجات المتاحة في المتجر:\n";
            foreach ($products as $product) {
                $price = $currencyService->convert($product->price, $userCurrency);
                $formattedPrice = number_format($price, 2) . ' ' . $currencySymbol;
                
                $productInfo .= "- {$product->name}: {$product->short_description} (السعر: {$formattedPrice})\n";
                if ($product->sale_price) {
                    $salePrice = $currencyService->convert($product->sale_price, $userCurrency);
                    $formattedSalePrice = number_format($salePrice, 2) . ' ' . $currencySymbol;
                    $productInfo .= "  (خصم: {$formattedSalePrice} بدلاً من {$formattedPrice})\n";
                }
            }
        }

        $categoriesInfo = '';
        if (isset($siteInfo['categories']) && !empty($siteInfo['categories'])) {
            $categoriesInfo = "\n\nالفئات المتاحة في المتجر:\n";
            foreach ($siteInfo['categories'] as $category) {
                $categoriesInfo .= "- {$category->name}";
                if ($category->description) {
                    $categoriesInfo .= ": {$category->description}";
                }
                $categoriesInfo .= "\n";
            }
        }

        $knowledgeInfo = '';
        if (!empty($knowledgeBase)) {
            $knowledgeInfo = "\n\nمعلومات مهمة عن المتجر:\n";
            foreach ($knowledgeBase as $kb) {
                $knowledgeInfo .= "- {$kb->title}: {$kb->content}\n";
            }
        }

        $siteName = $siteInfo['site_name'] ?? 'متجر البطاقات الرقمية';
        $siteDescription = $siteInfo['site_description'] ?? '';

        return "أنت مساعد ذكي خاص بمتجر {$siteName} فقط. مهمتك الوحيدة هي مساعدة العملاء فيما يتعلق بهذا المتجر ومحتواه فقط.

{$siteDescription}

قواعد صارمة يجب اتباعها:
1. يجب أن تجيب فقط عن أسئلة متعلقة بهذا المتجر ومحتواه (المنتجات، الفئات، السياسات، طرق الدفع، الشحن، إلخ)
2. إذا سألك المستخدم عن أي شيء خارج نطاق المتجر (أخبار، طقس، معلومات عامة، مواقع أخرى، إلخ)، يجب أن تقول: 'عذراً، أنا مساعد خاص بهذا المتجر فقط. يمكنني مساعدتك في البحث عن المنتجات، معلومات الطلبات، أو أي استفسار متعلق بالمتجر.'
3. تحدث باللغة العربية دائماً
4. كن مفيداً ومهذباً ومهنياً
5. استخدم الأسعار بالعملة المختارة: {$currencySymbol}
6. إذا لم تجد منتجاً محدداً، اقترح منتجات مشابهة من المتجر
7. شجع العملاء على الشراء بطريقة مهذبة واحترافية
8. لا تخترع معلومات غير موجودة في المتجر
9. إذا لم تعرف الإجابة، اعترف بذلك ووجه المستخدم لخدمة العملاء

{$categoriesInfo}

{$productInfo}

{$knowledgeInfo}

تذكر: أنت مساعد خاص بهذا المتجر فقط. لا تجب عن أي أسئلة خارج نطاق المتجر.";
    }

    private function buildMessagesArray($systemPrompt, $conversationHistory, $userMessage)
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        $recentHistory = array_slice($conversationHistory, -6);
        foreach ($recentHistory as $message) {
            $messages[] = [
                'role' => $message['role'],
                'content' => $message['content']
            ];
        }

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

        $history = array_slice($history, -20);

        Cache::put($cacheKey, $history, now()->addHours(24));
    }
}

