<?php

namespace Database\Seeders;

use App\Models\Unidade;
use Illuminate\Database\Seeder;

class UnidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unidade::create([
            'unid_nome' => 'Secretaria de Planejamento e Gestão',
            'unid_sigla' => 'SEPLAG',
        ]);

        Unidade::create([
            'unid_nome' => 'Universidade do Estado de Mato Grosso',
            'unid_sigla' => 'UNEMAT',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Ciência, Tecnologia e Inovação',
            'unid_sigla' => 'SECITECI',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Educação',
            'unid_sigla' => 'SEDUC',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Saúde',
            'unid_sigla' => 'SES',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Segurança Pública',
            'unid_sigla' => 'SESP',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Infraestrutura e Logística',
            'unid_sigla' => 'SINFRA',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Meio Ambiente',
            'unid_sigla' => 'SEMA',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Assistência Social e Cidadania',
            'unid_sigla' => 'SETASC',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Cultura, Esporte e Lazer',
            'unid_sigla' => 'SECEL',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Agricultura Familiar e Assuntos Fundiários',
            'unid_sigla' => 'SEAF',
        ]);

        Unidade::create([
            'unid_nome' => 'Secretaria de Estado de Desenvolvimento Econômico',
            'unid_sigla' => 'SEDEC',
        ]);
    }
}
