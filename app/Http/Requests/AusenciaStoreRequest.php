<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AusenciaStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //TODO: Verificar se o utilizador tem permissao para criar/alterar uma ausencia
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'registo_id' => 'required|integer',
            'data' => 'date|date_format:Y-m-d',
            'tipo_ausencia_id' => 'required|integer|exists:tipo_ausencia,id',
            'hora_inicio' => 'sometimes|nullable|date_format:H:i',
            'hora_fim' => [
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('hora_inicio') !== null;
                }),
            ],
            'obs_colab' => 'sometimes|nullable|string|max:255',
            'anexo' => 'sometimes|nullable|file|mimes:pdf,doc,docx,txt,jpeg|max:2048',
        ];
    }
}
