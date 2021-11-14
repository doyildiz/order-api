<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateRequest;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{

    /**
     * @var OrderService
     */
    public $orderService;

    /**
     * CartController constructor.
     */
    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = $this->orderService->validator($request->all());
            if ($validator->fails()) {
                return $this->orderService->respondWithError($validator->errors());
            }

            $data = $request->all();

            if (!$this->orderService->inventoryCheck($data['products'])) {
                return response()->json(['message' => 'Not enough items'], 422);
            }
            $order = Order::create([
                'customer_id'   => auth()->id(),
                'total'         => $this->orderService->total($data['products']) ? $this->orderService->total($data['products']) : 0,
                'shipping_date' => Carbon::today()->addDays(rand(3, 7))->toDateString()
            ]);

            if ($order) {
                foreach ($data['products'] as $product) {
                    $this->orderService->createOrderDetail($order, $product);
                }
                $address = ['order_id' => $order->id] + $data['address'];
                Address::updateOrCreate([
                    'order_id' => $order->id,
                ], $address);

                return response()->json(
                    new OrderResource($order), 200
                );
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json($exception->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) return response()
            ->json(['message' => 'Order not found'], 404);

        if ($order->customer_id != auth()->id()) return response()
            ->json(['message' => 'Unauthenticated'], 401);

        if ($order->shipping_date <= Carbon::today()) return response()
            ->json(['message' => 'Shipping date is passed'], 400);

        $validator = $this->orderService->updateValidator($request->all());

        if ($validator->fails()) {
            return $this->orderService->respondWithError($validator->errors());
        }
        $data = $request->all();

        if (array_key_exists('products', $data) && count($data['products'])) {

            if (!$this->orderService->fieldCheck($data['products'])) {
                return response()->json(['message' => 'Missing parameter'], 400);
            }

            if (!$this->orderService->inventoryCheck($data['products'])) {
                return response()->json(['message' => 'Not enough items'], 400);
            }
            $order->details->each(function ($detail) {
                $detail->delete();
            });
            foreach ($data['products'] as $product) {
                $this->orderService->createOrderDetail($order, $product);
            }
        }

        if (array_key_exists('address', $data) && count($data['address'])) {
            Address::updateOrCreate([
                'order_id' => $order->id,
            ], $data['address']);
        }

        return response()->json(
            new OrderResource($order), 200
        );
    }


    /**
     * Returns all orders or specified order belonging to auth user
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request, $id = null)
    {
        $take = 5;
        $skip = 0;

        if ($request->get('page')) {
            $skip = $take * ($request->get('page') - 1);
        }

        $query = Order::query();

        $query->take($take)->skip($skip);

        if ($id) $query->where('id', $id);

        $query->where('customer_id', auth()->id());
        $orders = $query->get();

        return response()->json(['data' => OrderResource::collection($orders)], 200);
    }
}
