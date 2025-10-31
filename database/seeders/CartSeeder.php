<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // مسح البيانات الموجودة
        DB::table('cart_items')->delete();
        DB::table('carts')->delete();

        // الحصول على المستخدمين والمنتجات
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('لا توجد مستخدمين أو منتجات. يرجى تشغيل UserSeeder و ProductSeeder أولاً.');
            return;
        }

        // إنشاء سلات نشطة
        $this->createActiveCarts($users, $products);

        // إنشاء سلات متروكة
        $this->createAbandonedCarts($users, $products);

        // إنشاء سلات للزوار
        $this->createGuestCarts($products);

        $this->command->info('تم إنشاء بيانات السلة بنجاح!');
    }

    /**
     * إنشاء سلات نشطة
     */
    private function createActiveCarts($users, $products)
    {
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $cart = Cart::create([
                'user_id' => $user->id,
                'session_id' => 'session_' . uniqid(),
                'coupon_code' => rand(0, 1) ? 'DISCOUNT10' : null,
                'discount_amount' => rand(0, 1) ? rand(5, 50) : 0,
                'tax_amount' => rand(5, 25),
                'shipping_amount' => rand(0, 1) ? rand(10, 30) : 0,
                'currency' => 'USD',
                'is_abandoned' => false,
                'last_activity_at' => now()->subHours(rand(1, 24)),
                'created_at' => now()->subDays(rand(1, 7)),
            ]);

            // إضافة منتجات للسلة
            $this->addItemsToCart($cart, $products);

            // تحديث المبلغ الإجمالي
            $cart->update(['total_amount' => $cart->calculateTotal()]);
        }
    }

    /**
     * إنشاء سلات متروكة
     */
    private function createAbandonedCarts($users, $products)
    {
        for ($i = 0; $i < 25; $i++) {
            $user = $users->random();
            $abandonedAt = now()->subDays(rand(1, 30));

            $cart = Cart::create([
                'user_id' => $user->id,
                'session_id' => 'session_' . uniqid(),
                'coupon_code' => rand(0, 1) ? 'SAVE20' : null,
                'discount_amount' => rand(0, 1) ? rand(10, 100) : 0,
                'tax_amount' => rand(5, 30),
                'shipping_amount' => rand(0, 1) ? rand(15, 40) : 0,
                'currency' => 'USD',
                'is_abandoned' => true,
                'abandoned_at' => $abandonedAt,
                'last_activity_at' => $abandonedAt->subHours(rand(1, 12)),
                'created_at' => $abandonedAt->subDays(rand(1, 5)),
            ]);

            // إضافة منتجات للسلة
            $this->addItemsToCart($cart, $products);

            // تحديث المبلغ الإجمالي
            $cart->update(['total_amount' => $cart->calculateTotal()]);
        }
    }

    /**
     * إنشاء سلات للزوار
     */
    private function createGuestCarts($products)
    {
        for ($i = 0; $i < 10; $i++) {
            $cart = Cart::create([
                'user_id' => null,
                'session_id' => 'guest_session_' . uniqid(),
                'coupon_code' => null,
                'discount_amount' => 0,
                'tax_amount' => rand(5, 20),
                'shipping_amount' => rand(0, 1) ? rand(10, 25) : 0,
                'currency' => 'USD',
                'is_abandoned' => rand(0, 1),
                'abandoned_at' => rand(0, 1) ? now()->subDays(rand(1, 15)) : null,
                'last_activity_at' => now()->subHours(rand(1, 48)),
                'created_at' => now()->subDays(rand(1, 10)),
            ]);

            // إضافة منتجات للسلة
            $this->addItemsToCart($cart, $products);

            // تحديث المبلغ الإجمالي
            $cart->update(['total_amount' => $cart->calculateTotal()]);
        }
    }

    /**
     * إضافة منتجات للسلة
     */
    private function addItemsToCart($cart, $products)
    {
        $itemsCount = rand(1, min(3, $products->count()));
        $selectedProducts = $products->random($itemsCount);

        foreach ($selectedProducts as $product) {
            $quantity = rand(1, 3);
            $price = $product->price ?? rand(10, 100);

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'total_price' => $quantity * $price,
                'notes' => rand(0, 1) ? 'ملاحظة خاصة للمنتج' : null,
            ]);
        }
    }
}
