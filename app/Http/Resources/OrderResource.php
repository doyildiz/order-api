<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'customer_id'   => $this->customer_id,
            'total'         => $this->total,
            'shipping_date' => $this->shipping_date,
            'products'      => OrderDetailResource::collection($this->details),
            'address'       => new AddressResource($this->address),
        ];
    }
}
