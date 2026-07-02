<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // 1. Lấy danh sách ID khách hàng và sản phẩm hiện có
        $userIds = User::pluck('id')->toArray();
        $products = Product::all();

        if (empty($userIds) || $products->isEmpty()) {
            $this->command->error("Chưa có User hoặc Product trong DB. Hãy tạo chúng trước!");
            return;
        }

        // 2. Tạo vòng lặp tạo 10 đơn hàng mẫu
        for ($i = 1; $i <= 10; $i++) {
            DB::beginTransaction();
            try {
                // Tạo đơn hàng chính
                $order = Order::create([
                    'user_id' => $userIds[array_rand($userIds)],
                    'total_amount' => 0, // Tạm thời để 0
                    'status' => 'paid',  // Để paid để test báo cáo doanh thu
                    'vnp_txn_ref' => 'VNP_TEST_' . time() . $i,
                    'created_at' => now()->subDays(rand(1, 20)), // Ngày ngẫu nhiên trong 20 ngày qua
                ]);

                $orderTotal = 0;
                // Mỗi đơn hàng lấy ngẫu nhiên 2 sản phẩm
                $randomProducts = $products->random(2);

                foreach ($randomProducts as $product) {
                    $qty = rand(1, 3);
                    $price = $product->price;
                    $orderTotal += ($price * $qty);

                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => $price,
                    ]);
                }

                // Cập nhật lại tổng tiền đúng cho Order
                $order->update(['total_amount' => $orderTotal]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error($e->getMessage());
            }
        }

        $this->command->info("Đã tạo 10 đơn hàng mẫu thành công!");
    }
}