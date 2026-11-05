<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;

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
        return Address::where('user_id', $id)->get();
    }

    public function findById(int $id, int $userId): Address
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== $userId) {
            throw new AuthorizationException('This is not your address.');
        }
        return $address;
    }

    public function update(int $id, int $userId, array $data): Address
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== $userId) {
            throw new AuthorizationException('This is not your address.');
        }
        $address->update($data);

        return $address;
    }

    public function delete(int $id, int $userId): void
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== $userId) {
            throw new AuthorizationException('This is not your address.');
        }
        $address->delete();
    }
}