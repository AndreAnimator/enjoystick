<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function __construct(
        private AddressService $addressService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        return AddressResource::collection($this->addressService->listByUser($userId));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        $address = $this->addressService->create($request->validated());

        return new AddressResource($address);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, Request $request)
    {
        $userId = $request->user()->id;

        return new AddressResource($this->addressService->findById($id, $userId));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, int $id)
    {
        $userId = $request->user()->id;
        $address = $this->addressService->update($id, $userId, $request->validated());

        return new AddressResource($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, Request $request)
    {
        $userId = $request->user()->id;
        $this->addressService->delete($id, $userId);

        return response()->noContent();
    }
}
