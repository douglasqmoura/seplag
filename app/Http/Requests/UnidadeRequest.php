<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnidadeRequest extends FormRequest
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
        $unidId = $this->route('unidade');

        return [
            'unid_nome' => 'required|string|max:200|unique:unidade,unid_nome'.($unidId ? ','.$unidId.',unid_id' : ''),
            'unid_sigla' => 'required|string|max:20|unique:unidade,unid_sigla'.($unidId ? ','.$unidId.',unid_id' : ''),

            'end_tipo_logradouro' => 'nullable|string|max:50',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'required|integer',
            'end_bairro' => 'required|string|max:100',

            'cid_nome' => 'required|string|max:200',
            'cid_uf' => 'required|string|size:2',
        ];
    }

    public function attributes(): array
    {
        return [
            'unid_nome' => 'nome da unidade',
            'unid_sigla' => 'sigla da unidade',
            'end_logradouro' => 'logradouro',
            'end_numero' => 'número',
            'end_bairro' => 'bairro',
            'end_tipo_logradouro' => 'tipo de logradouro',
            'cid_nome' => 'nome da cidade',
            'cid_uf' => 'UF da cidade',
        ];
    }
}
