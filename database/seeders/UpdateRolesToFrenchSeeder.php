<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class UpdateRolesToFrenchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesTranslations = [
            'business_team' => [
                'display_name' => 'Équipe métier',
                'description' => 'Membres de l\'équipe métier qui créent et gèrent les demandes'
            ],
            'rjg' => [
                'display_name' => 'RJG',
                'description' => 'Responsable Juridique Groupe'
            ],
            'compliance_director' => [
                'display_name' => 'Directeur de la conformité',
                'description' => 'Directeur responsable de la conformité'
            ],
            'drj' => [
                'display_name' => 'DRJ',
                'description' => 'Direction des Risques Juridiques'
            ],
            'internal_contributor' => [
                'display_name' => 'Contributeur interne',
                'description' => 'Contributeur interne avec accès complet aux demandes'
            ],
            'observer' => [
                'display_name' => 'Observateur',
                'description' => 'Observateur avec accès en lecture seule'
            ],
        ];

        foreach ($rolesTranslations as $name => $translation) {
            Role::where('name', $name)->update($translation);
        }
    }
}
