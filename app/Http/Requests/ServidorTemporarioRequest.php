<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServidorTemporarioRequest extends FormRequest
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
        return [
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

            'data_admissao' => ['required', 'date'],
            'data_demissao' => ['nullable', 'date'],

            'fotos.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
