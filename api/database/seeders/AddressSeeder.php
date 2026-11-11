<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Endereços fictícios para os clientes do UserSeeder. O primeiro cliente tem dois,
     * para testar a escolha de endereço no checkout e o gerenciamento de endereços no perfil.
     */
    public function run(): void
    {
        $addresses = [
            'adrivasco@gmail.com' => [
                [
                    'recipient_name' => 'Adriano Vasconscelos',
                    'postal_code' => '80010-000',
                    'street' => 'Rua das Acácias',
                    'number' => '120',
                    'complement' => 'Apto 31',
                    'neighborhood' => 'Centro',
                    'city' => 'Curitiba',
                    'state' => 'PR',
                    'country' => 'Brasil',
                ],
                [
                    'recipient_name' => 'Adriano Vasconscelos (trabalho)',
                    'postal_code' => '30112-000',
                    'street' => 'Avenida dos Jogadores',
                    'number' => '2500',
                    'complement' => 'Sala 8',
                    'neighborhood' => 'Savassi',
                    'city' => 'Belo Horizonte',
                    'state' => 'MG',
                    'country' => 'Brasil',
                ],
            ],
            'ph.grego@outlook.com' => [
                [
                    'recipient_name' => 'Pedro Hercules',
                    'postal_code' => '51020-000',
                    'street' => 'Rua do Controle',
                    'number' => '45',
                    'complement' => null,
                    'neighborhood' => 'Boa Viagem',
                    'city' => 'Recife',
                    'state' => 'PE',
                    'country' => 'Brasil',
                ],
            ],
            'estupidezhonesta@proton.com' => [
                [
                    'recipient_name' => 'André Miranda',
                    'postal_code' => '90570-000',
                    'street' => 'Travessa dos Joysticks',
                    'number' => '7',
                    'complement' => 'Casa B',
                    'neighborhood' => 'Moinhos de Vento',
                    'city' => 'Porto Alegre',
                    'state' => 'RS',
                    'country' => 'Brasil',
                ],
            ],
        ];

        foreach ($addresses as $email => $userAddresses) {
            $user = User::where('email', $email)->firstOrFail();

            foreach ($userAddresses as $address) {
                Address::firstOrCreate(
                    ['user_id' => $user->id, 'street' => $address['street'], 'number' => $address['number']],
                    Arr::except($address, ['street', 'number'])
                );
            }
        }
    }
}