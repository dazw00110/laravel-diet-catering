<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;

/**
 * 🧪 TestCascadeDeleteSeeder
 *
 * Ten seeder służy do **ręcznego przetestowania kaskadowego usuwania danych** w bazie.
 *
 * 🔍 Co sprawdza?
 * ---------------------------------------
 * 1. Tworzy testowego użytkownika.
 * 2. Tworzy produkt.
 * 3. Tworzy zamówienie dla użytkownika.
 * 4. Tworzy pozycję zamówienia (OrderItem) z tym produktem.
 * 5. Tworzy opinię (ProductReview) wystawioną przez tego użytkownika.
 * 6. Usuwa użytkownika.
 *
 * ✅ Jeśli kaskadowe relacje są poprawnie ustawione (np. onDelete('cascade')),
 *    to Laravel (lub baza danych) automatycznie usunie:
 *    - jego zamówienia (Order),
 *    - powiązane pozycje zamówień (OrderItem),
 *    - opinie (ProductReview).
 *
 * 🧪 Na końcu wypisuje do konsoli, czy któreś z tych danych przetrwały — jeśli NIE, to test przeszedł pomyślnie.
 *
 * 💡 Jak uruchomić?
 * ---------------------------------------
 * 1. Wklej `TestCascadeDeleteSeeder::class` do tablicy `$this->call()` w `DatabaseSeeder.php`
 *    lub uruchom samodzielnie:
 *
 *    php artisan db:seed --class=TestCascadeDeleteSeeder
 *
 * 2. W konsoli pojawi się raport:
 *    - "Czy zamówienie istnieje?" TAK/NIE
 *    - "Czy pozycje zamówienia istnieją?" TAK/NIE
 *    - "Czy recenzje istnieją?" TAK/NIE
 *
 * 👁 Jeśli wszystkie wyniki to **NIE**, oznacza to, że kaskadowe usuwanie działa poprawnie.
 *
 * 🛠 Upewnij się, że:
 * - relacje w modelach mają `onDelete('cascade')`,
 * - nie ma błędów w migracjach,
 * - powiązane modele są poprawnie zdefiniowane.
 *
 * 📌 Przykładowe zastosowanie:
 * - testowanie po migracji,
 * - testowanie po zmianie kluczy obcych,
 * - debugowanie usuwania kont użytkowników.
 */

class TestCascadeDeleteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_reviews')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate();

        // Create a test user
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'Użytkownik',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'birth_date' => now()->subYears(20),
            'user_type_id' => 1,
        ]);

        // Create a product
        $product = Product::create([
            'name' => 'Test Produkt',
            'description' => 'Opis testowy',
            'price' => 100,
            'calories' => 400,
            'is_vegan' => false,
            'is_vegetarian' => true,
        ]);

        // Create an order for the user
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(3),
            'total_price' => 300,
            'city' => 'Rzeszów',
            'postal_code' => '35-001',
            'street' => 'Testowa',
            'apartment_number' => '10',
        ]);

        // Add order item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 100,
        ]);

        // Add a product review
        ProductReview::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Świetny catering!',
        ]);

        //  We remove the user and test cascading
        $user->delete();

        //  We check if everything has been deleted
        $this->command->info('Czy zamówienie istnieje? ' . (Order::where('id', $order->id)->exists() ? 'TAK' : 'NIE'));
        $this->command->info('Czy pozycje zamówienia istnieją? ' . (OrderItem::where('order_id', $order->id)->exists() ? 'TAK' : 'NIE'));
        $this->command->info('Czy recenzje istnieją? ' . (ProductReview::where('user_id', $user->id)->exists() ? 'TAK' : 'NIE'));
    }
}
