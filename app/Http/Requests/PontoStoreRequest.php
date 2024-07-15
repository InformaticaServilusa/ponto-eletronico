<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PontoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //Provavelmente será se o ponto pertencer ao utilizador, ou se o utilizador for um coordenador
        //e se o ponto pertencer ao mes que estamos no momento
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    //TODO: Quando os operadores de CallCenter poderem introduzir pontos, no turno da noite a saida_noite < entrada_noite para o turno das 2300-0700
    public function rules()
    {
        return [
            'registo_id' => 'required|integer',
            'entrada_manha' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_tarde') === null && $this->input('entrada_noite') === null;
                }),
            ],
            'saida_manha' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_manha') !== null;
                }),
                function ($att, $value, $valid) {
                    $entrada_manha = $this->input('entrada_manha');
                    if (!empty($entrada_manha) && $value < $entrada_manha) {
                        $valid('A hora de saída da manhã deve ser maior que a hora de entrada da manhã.');
                    }
                }
            ],
            'entrada_tarde' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_manha') === null && $this->input('entrada_noite') === null;
                }),
                function ($att, $value, $valid) {
                    $saida_manha = $this->input('saida_manha');
                    if (!empty($saida_manha) && $value < $saida_manha) {
                        $valid('A hora de entrada da tarde deve ser maior que a hora saída da manhã.');
                    }
                }
            ],
            'saida_tarde' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_tarde') !== null;
                }),
                function ($att, $value, $valid) {
                    $entrada_tarde = $this->input('entrada_tarde');
                    if (!empty($entrada_tarde) && $value < $entrada_tarde) {
                        $valid('A hora de entrada da tarde deve ser maior que a hora saída da manhã.');
                    }
                }
            ],
            'entrada_noite' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_tarde') === null && $this->input('entrada_manha') === null;
                }),
                function ($att, $value, $valid) {
                    $saida_tarde = $this->input('saida_tarde');
                    if (!empty($saida_tarde) && $value < $saida_tarde) {
                        $valid('A hora de entrada da tarde deve ser maior que a hora saída da manhã.');
                    }
                }
            ],
            'saida_noite' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_noite') !== null;
                }),
                function ($att, $value, $valid) {
                    $entrada_noite = $this->input('entrada_noite');
                    if (!empty($entrada_noite) && $value < $entrada_noite) {
                        $valid('A hora de saída de noite deve ser maior que a hora de entrada da noite.');
                    }
                }
            ],
            'obs_colab' => 'sometimes|nullable|string|max:255',
            'was_folga' => 'sometimes|nullable',
        ];
    }
}
