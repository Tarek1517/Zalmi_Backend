<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Frontend\OrderResurce;
use App\Models\CombinedOrder;
use App\Models\Order;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = CombinedOrder::query()
        ->with('orderDetails.product:id,title,cover_image')
        ->where('user_id', $request->user()->id)
        ->get();
		
		return OrderResurce::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $data = $request->validated();
      
		$data['user_id'] = $request->user()->id;
		$data['order_code'] =  rand(10000000, 99999999);

		$combinedOrder = CombinedOrder::create($data);
		$shopOrders = [];
		$vendorEarnings = [];
		foreach ($data['order_items'] as $item) {
			$shopId = $item['shop_id'];
			$categoryId = $item['category_id'];
			$commissionRate = Category::where('id', $categoryId)->value('commission_rate') ?? 0;
			if (!isset($shopOrders[$shopId])) {
				$shopOrders[$shopId] = Order::create([
                    'order_code' =>  rand(10000000, 99999999),
					'shop_id' => $shopId,
					'combined_order_id' => $combinedOrder->id,
                    'shipping_charge' => $data['shipping_charge'],
					'sub_total' => 0,
					'grand_total'  => 0,
					'seller_balance' => 0,
					'admin_balance'  => 0,
					'order_status'   => 'pending',
					'payment_status' => 'pending',
				]);
			}
			$itemPrice = $item['price'];
			$itemQty = $item['quantity'];

			$product = Product::where('id', $item['id'])->first();
			// if ($product && $product->variations !== null) {
			// 	$productStock = ProductStock::where('product_id', $item['id'])
			// 		->where('price', $item['price'])
			// 		->first(['stock']);
			// 	if ($productStock) {
			// 		$new_stock = $productStock->stock - $item['quantity'];
			// 		ProductStock::where('product_id', $item['id'])
			// 			->where('price', $item['price'])
			// 			->update(['stock' => $new_stock]);
			// 	}
			// } else {
			// 	$new_stock = $product->stock - $item['quantity'];
			// 	$product->update(['stock' => $new_stock]);
			// }

			$totalPrice = $itemPrice * $itemQty;
			OrderDetail::create([
				'combined_order_id' => $combinedOrder->id,
				'order_id'   => $shopOrders[$shopId]->id,
				'product_id' => $item['id'],
				'price'      => $itemPrice,
				'quantity'   => $itemQty,
			]);
			$shopOrders[$shopId]->sub_total += $totalPrice;
            $shopOrders[$shopId]->grand_total += $totalPrice + $data['shipping_charge'];
			$adminEarningsAmount = ($totalPrice * $commissionRate) / 100;
			$vendorEarningsAmount = $totalPrice - $adminEarningsAmount;
			$shopOrders[$shopId]->vendor_balance += $vendorEarningsAmount;
			$shopOrders[$shopId]->admin_balance += $adminEarningsAmount;

			if (!isset($vendorEarnings[$shopId])) {
				$vendorEarnings[$shopId] = 0;
			}
			$vendorEarnings[$shopId] += $vendorEarningsAmount;
		}
		foreach ($shopOrders as $shopOrder) {
			$shopOrder->save();
		}

		if ($data['payment_method'] === 'sslcommerz') {
			// $paymentResponse = $paymentService->initiatePayment($data);
			// if (isset($paymentResponse['status']) && $paymentResponse['status'] == 'fail') {
			// 	return response()->json(['message' => 'Payment initiation failed'], 400);
			// }
			// return response()->json(json_decode($paymentResponse, true));
		}



		return Response::HTTP_OK;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
