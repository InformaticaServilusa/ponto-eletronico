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
    public function rules()
    {
        return [
            'registo_id' => 'required|integer',
            'entrada_manha' => 'sometimes|nullable|date_format:H:i',
            'saida_manha' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_manha') !== null;
                }),
                'gte:entrada_manha',
            ],
            'entrada_tarde' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_manha') === null ;
                }),
                'gte:saida_manha',
            ],
            'saida_tarde' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_tarde') !== null;
                }),
                'gte:entrada_tarde',
            ],
            'entrada_noite' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_tarde') === null && $this->input('entrada_manha') === null;
                }),
            ],
            'saida_noite' => [
                'sometimes',
                'nullable',
                'date_format:H:i',
                Rule::requiredIf(function () {
                    return $this->input('entrada_noite') !== null;
                }),
                'gte:entrada_noite',
            ],
            'obs_colab' => 'sometimes|nullable|string|max:255',
        ];
    }
}
