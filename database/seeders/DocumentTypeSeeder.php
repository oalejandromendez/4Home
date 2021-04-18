<?php

namespace Database\Seeders;

use App\Models\Common\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocumentType::create(['name' => 'Cédula Ciudadanía']);
        DocumentType::create(['name' => 'Cédula de Extranjería']);
        DocumentType::create(['name' => 'Pasaporte']);
        DocumentType::create(['name' => 'Registro Civil']);
        DocumentType::create(['name' => 'Tarjeta de Identidad']);
        DocumentType::create(['name' => 'Carné Diplomático']);
        DocumentType::create(['name' => 'PEP Permiso especial de permanencia']);
        DocumentType::create(['name' => 'Salvoconducto de permanencia']);
    }
}
