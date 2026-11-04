<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;
use Illuminate\Http\Request;

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

        return AddressResource::collection($this->addressService->listAll($userId));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        $userId = $request->user()->id;

        $address = $this->addressService->create($userId, $request->validated());

        return new AddressResource($address);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new AddressResource($this->addressService->finById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, int $id)
    {
        // pensei em fazer $userId = $request->user()->id; mas teria q ser outra rota
        $address = $this->addressService->update($id, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->addressService->delete($id);

        return response()->noContent();
    }
}
