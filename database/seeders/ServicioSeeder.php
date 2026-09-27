<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            [
                'servicio' => 'Consulta médica general',
                'descripcion' => 'Consulta médica de atención general.',
            ],
            [
                'servicio' => 'Consulta especializada',
                'descripcion' => 'Consulta médica realizada por un especialista.',
            ],
            [
                'servicio' => 'Consulta de seguimiento',
                'descripcion' => 'Consulta para seguimiento de una atención médica previa.',
            ],
            [
                'servicio' => 'Control médico',
                'descripcion' => 'Evaluación general de control del estado de salud del paciente.',
            ],
            [
                'servicio' => 'Curación',
                'descripcion' => 'Procedimiento de limpieza y tratamiento de heridas.',
            ],
            [
                'servicio' => 'Aplicación de inyección',
                'descripcion' => 'Servicio de administración de medicamentos mediante inyección.',
            ],
            [
                'servicio' => 'Examen de laboratorio',
                'descripcion' => 'Servicio relacionado con la realización de pruebas de laboratorio.',
            ],
            [
                'servicio' => 'Electrocardiograma',
                'descripcion' => 'Estudio para registrar la actividad eléctrica del corazón.',
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }
}
