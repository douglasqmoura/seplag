<?php

namespace App\Http\Requests;

use App\Models\ServidorEfetivo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServidorEfetivoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $matricula = $this->route('servidor_efetivo');
        $pesId = ServidorEfetivo::where('se_matricula', $matricula)->value('pes_id');

        return [
            'matricula' => [
                'required',
                'string',
                'max:20',
                Rule::unique('servidor_efetivo', 'se_matricula')->ignore($pesId, 'pes_id'),
            ],
            'nome' => ['required', 'string', 'max:200'],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['required', 'in:Masculino,Feminino,Outro'],
            'mae' => ['required', 'string', 'max:200'],
            'pai' => ['nullable', 'string', 'max:200'],

            'tipo_logradouro' => ['nullable', 'string', 'max:50'],
            'logradouro' => ['required', 'string', 'max:200'],
            'numero' => ['required', 'integer'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:200'],
            'uf' => ['required', 'string', 'size:2'],
        ];
    }

    public function attributes(): array
    {
        return [
            'matricula' => 'matrícula',
            'data_nascimento' => 'data de nascimento',
            'mae' => 'nome da mãe',
            'pai' => 'nome do pai',
            'tipo_logradouro' => 'tipo de logradouro',
            'numero' => 'número',
            'uf' => 'UF',
        ];
    }
}
