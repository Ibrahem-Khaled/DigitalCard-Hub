<?php

namespace Tests\Feature\Dashboard;

use App\Http\Middleware\TrackUserSession;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_orders_by_status(): void
    {
        $admin = $this->createUser(['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $customerOne = $this->createUser(['email' => 'customer1@example.com']);
        $customerTwo = $this->createUser(['email' => 'customer2@example.com']);

        $this->createOrder($customerOne, ['order_number' => 'ORD-STATUS-1', 'status' => 'pending']);
        $matching = $this->createOrder($customerTwo, ['order_number' => 'ORD-STATUS-2', 'status' => 'processing']);

        $response = $this->get(route('dashboard.orders.index', ['status' => 'processing']));

        $response->assertStatus(200);
        $response->assertSee($matching->order_number);
        $response->assertDontSee('ORD-STATUS-1');
    }

    public function test_it_filters_orders_by_payment_status_and_method(): void
    {
        $admin = $this->createUser(['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $customer = $this->createUser(['email' => 'customer@example.com']);

        $this->createOrder($customer, [
            'order_number' => 'ORD-PAYMENT-1',
            'payment_status' => 'pending',
            'payment_method' => 'paypal',
        ]);

        $matching = $this->createOrder($customer, [
            'order_number' => 'ORD-PAYMENT-2',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
        ]);

        $response = $this->get(route('dashboard.orders.index', [
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
        ]));

        $response->assertStatus(200);
        $response->assertSee($matching->order_number);
        $response->assertDontSee('ORD-PAYMENT-1');
    }

    public function test_it_filters_orders_by_user(): void
    {
        $admin = $this->createUser(['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $customerOne = $this->createUser(['email' => 'customer1@example.com']);
        $customerTwo = $this->createUser(['email' => 'customer2@example.com']);

        $matching = $this->createOrder($customerOne, ['order_number' => 'ORD-USER-1']);
        $this->createOrder($customerTwo, ['order_number' => 'ORD-USER-2']);

        $response = $this->get(route('dashboard.orders.index', [
            'user_id' => $customerOne->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($matching->order_number);
        $response->assertDontSee('ORD-USER-2');
    }

    public function test_it_filters_orders_by_amount_range(): void
    {
        $admin = $this->createUser(['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $customer = $this->createUser(['email' => 'customer@example.com']);

        $this->createOrder($customer, ['order_number' => 'ORD-AMOUNT-LOW', 'total_amount' => 50, 'subtotal' => 50]);
        $matching = $this->createOrder($customer, ['order_number' => 'ORD-AMOUNT-MID', 'total_amount' => 150, 'subtotal' => 150]);
        $this->createOrder($customer, ['order_number' => 'ORD-AMOUNT-HIGH', 'total_amount' => 300, 'subtotal' => 300]);

        $response = $this->get(route('dashboard.orders.index', [
            'amount_from' => 100,
            'amount_to' => 250,
        ]));

        $response->assertStatus(200);
        $response->assertSee($matching->order_number);
        $response->assertDontSee('ORD-AMOUNT-LOW');
        $response->assertDontSee('ORD-AMOUNT-HIGH');
    }

    public function test_it_filters_orders_by_date_range(): void
    {
        $admin = $this->createUser(['email' => 'admin@example.com']);
        $this->actingAs($admin);

        $customer = $this->createUser(['email' => 'customer@example.com']);

        $inside = $this->createOrder($customer, ['order_number' => 'ORD-DATE-INSIDE']);
        $outside = $this->createOrder($customer, ['order_number' => 'ORD-DATE-OUTSIDE']);

        $insideDate = Carbon::now()->subDays(3);
        $outsideDate = Carbon::now()->subDays(15);

        DB::table('orders')->whereKey($inside->id)->update([
            'created_at' => $insideDate,
            'updated_at' => $insideDate,
        ]);

        DB::table('orders')->whereKey($outside->id)->update([
            'created_at' => $outsideDate,
            'updated_at' => $outsideDate,
        ]);

        $response = $this->get(route('dashboard.orders.index', [
            'date_from' => Carbon::now()->subDays(5)->toDateString(),
            'date_to' => Carbon::now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee('ORD-DATE-INSIDE');
        $response->assertDontSee('ORD-DATE-OUTSIDE');
    }

    private function createUser(array $overrides = []): User
    {
        static $counter = 1;

        $defaults = [
            'first_name' => 'Test',
            'last_name' => 'User' . $counter,
            'email' => 'user' . $counter . '@example.com',
            'phone' => null,
            'password' => bcrypt('password'),
            'is_active' => true,
        ];

        $counter++;

        return User::create(array_merge($defaults, $overrides));
    }

    private function createOrder(User $user, array $overrides = []): Order
    {
        $defaults = [
            'user_id' => $user->id,
            'order_number' => 'ORD-' . uniqid(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
            'payment_reference' => null,
            'subtotal' => 100,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 100,
            'currency' => 'USD',
            'coupon_code' => null,
            'shipping_address' => ['line1' => '123 Test St'],
            'billing_address' => ['line1' => '123 Test St'],
            'notes' => null,
        ];

        return Order::create(array_merge($defaults, $overrides));
    }
}


