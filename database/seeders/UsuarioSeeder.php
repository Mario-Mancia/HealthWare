<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usuario::updateOrCreate(
            [
                'correo' => 'admin@healthware.com',
            ],
            [
                'id_rol' => 1,
                'id_estado' => 1,
                'nombres' => 'Mario Roberto',
                'apellidos' => 'Mancia Peraza',
                'contrasenha' => 'Mario123',
                'telefono' => '+503 2223-2425',
            ]
        );
    }
}
