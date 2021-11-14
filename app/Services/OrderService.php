<?php


namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Auth;
use Validator;

class OrderService
{

    /**
     * @param $data
     * @return mixed
     */
    public function validator($data)
    {

        $rules = [
            'address.first_name'    => 'required|string',
            'address.last_name'     => 'required|string',
            'address.email'         => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'address.phone'         => 'required|string',
            'address.address'       => 'required|string',
            'address.city'          => 'required|string',
            'address.zipcode'       => 'required|string',
            'address.country'       => 'required|string',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity'   => 'required|integer',
        ];

        return Validator::make($data, $rules);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function updateValidator($data)
    {
        //if product_id is specified, quantity is required
        //if quantity is specified, product_id is required
        $rules = [
            'address.first_name'    => 'sometimes|required|string',
            'address.last_name'     => 'sometimes|required|string',
            'address.email'         => 'sometimes|required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'address.phone'         => 'sometimes|required|string',
            'address.address'       => 'sometimes|required|string',
            'address.city'          => 'sometimes|required|string',
            'address.zipcode'       => 'sometimes|required|string',
            'address.country'       => 'sometimes|required|string',
            'products.*.product_id' => 'required_with:products.*.quantity|integer|exists:products,id',
            'products.*.quantity'   => 'required_with:products.*.product_id',
        ];

        return Validator::make($data, $rules);
    }

    /**
     * Handles an error response formatting it according to our spec.
     *
     * @param array $error
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respondWithError($error, $headers = [])
    {
        return response()->json(['errors' => $error], 400);
    }

    /**
     * Calculate total amount
     * @param array $data
     * @return bool|float|int
     */
    public function total($data)
    {
        if (is_array($data) && !empty($data)) {
            $total = 0;
            foreach ($data as $datum) {
                $product = Product::find($datum['product_id']);
                $total   += ($product->price) * $datum['quantity'];
            }

            return $total;
        }
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    public function fieldCheck($data)
    {
        if (is_array($data) && !empty($data)) {
            foreach ($data as $datum) {

                if (!isset($datum['product_id'])) return false;

                if (!isset($datum['quantity'])) return false;
            }
        }

        return true;
    }

    /**
     * Checks if there is enough stock
     * @param $data
     * @return bool
     */
    public function inventoryCheck($data)
    {
        if (is_array($data) && !empty($data)) {
            foreach ($data as $datum) {
                $product = Product::find($datum['product_id']);

                if (!isset($datum['quantity'])) return false;

                if ($product->stock < $datum['quantity']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param Order $order
     * @param array $product
     */
    public function createOrderDetail(Order $order, $product)
    {
        OrderDetail::updateOrCreate([
            'order_id'   => $order->id,
            'product_id' => $product['product_id']
        ], [
            'order_id'    => $order->id,
            'product_id'  => $product['product_id'],
            'quantity'    => $product['quantity'],
            'unit_price'  => Product::find($product['product_id'])->price,
            'total_price' => Product::find($product['product_id'])->price * $product['quantity'],
        ]);
    }
}
