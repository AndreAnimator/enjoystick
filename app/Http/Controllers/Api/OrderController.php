<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\PayOrderRequest;
use App\Http\Requests\UpdateStatusOrderRequest;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Http\Library\ApiHelpers;
use Illuminate\Auth\Access\AuthorizationException;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return OrderResource::collection($this->orderService->ListUserOrders($request));
    }

    public function listAll(Request $request)
    {
        $is_admin = $this->isAdmin($request->user());
        if (!$is_admin) {
            throw new AuthorizationException('This action is unauthorized.');
        }
        return OrderResource::collection($this->orderService->ListAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        return new OrderResource($this->orderService->create($request->validated(), $request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        return new OrderResource($this->orderService->findById($request, $id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusOrderRequest $request, int $id)
    {
        $is_admin = $this->isAdmin($request->user());
        if (!$is_admin) {
            throw new AuthorizationException('This action is unauthorized.');
        }
        return new OrderResource($this->orderService->update($request->validated()), $id);
    }

    public function pay(PayOrderRequest $request, int $id)
    {
        return new OrderResource($this->orderService->pay($request, $id));
    }

    public function cancel(int $id)
    {
        return new OrderResource($this->orderService->cancel($id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
