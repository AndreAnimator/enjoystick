<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Database\Eloquente\Collection;

class AddressService
{
    public function create(array $data): Address
    {
        return Address::create($data);
    }

    public function listAll(): Collection
    {
        return Address::all();
    }

    public function listByUser(int $id): Collection
    {
        return Address::where('user_id', $id);
    }

    public function findById(int $id): Address
    {
        return Address::findOrFail($id);
    }

    public function update(int $id, array $data): Adress
    {
        $address = Address::findOrFail($id);
        $address->update($data);

        return $address;
    }

    public function delete(int $id): void
    {
        Address::findOrFail($id)->delete();
    }
}