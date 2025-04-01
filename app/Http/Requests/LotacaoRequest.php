<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LotacaoRequest extends FormRequest
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
            'pes_id' => ['required', 'exists:pessoa,pes_id'],
            'unid_id' => ['required', 'exists:unidade,unid_id'],
            'lot_data_lotacao' => ['required', 'date'],
            'lot_data_remocao' => ['nullable', 'date', 'after_or_equal:lot_data_lotacao'],
            'lot_portaria' => ['required', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'pes_id' => 'pessoa',
            'unid_id' => 'unidade',
            'lot_data_lotacao' => 'data da lotação',
            'lot_data_remocao' => 'data da remoção',
            'lot_portaria' => 'portaria',
        ];
    }
}
