<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PartnersSeeder extends Seeder
{
    public function run()
    {
        // Obtener la primera empresa (o crear una si no existe)
        $company = Company::first();
        if (!$company) {
            $company = Company::create([
                'razon_social' => 'Empresa de Prueba S.A.C.',
                'nombre_comercial' => 'EMPRESA PRUEBA',
                'ruc' => '20123456789',
                'direccion' => 'Av. Ejemplo 123, Lima - Perú',
                'sol_user' => 'user_prueba',
                'sol_password' => Hash::make('password_prueba'),
            ]);
        }

        // Crear un usuario para la empresa si no existe
        $user = User::where('company_id', $company->id)->first();
        if (!$user) {
            $user = User::create([
                'company_id' => $company->id,
                'name' => 'Admin Prueba',
                'email' => 'admin@prueba.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);
        }

        // Crear algunos socios de ejemplo
        $partners = [
            [
                'company_id' => $company->id,
                'document_type' => 'RUC',
                'document_number' => '20100000001',
                'name' => 'Proveedor de Prueba S.A.C.',
                'address' => 'Av. Prueba 456, Lima - Perú',
                'is_supplier' => 1,
                'is_customer' => 0,
            ],
            [
                'company_id' => $company->id,
                'document_type' => 'RUC',
                'document_number' => '20100000002',
                'name' => 'Cliente de Prueba S.A.C.',
                'address' => 'Calle Prueba 789, Lima - Perú',
                'is_supplier' => 0,
                'is_customer' => 1,
            ],
            [
                'company_id' => $company->id,
                'document_type' => 'DNI',
                'document_number' => '12345678',
                'name' => 'Juan Pérez Gómez',
                'address' => 'Jr. Prueba 101, Lima - Perú',
                'is_supplier' => 1,
                'is_customer' => 1,
            ],
        ];

        foreach ($partners as $partnerData) {
            Partner::create($partnerData);
        }

        $this->command->info('Socios de negocio de prueba creados exitosamente.');
    }
}