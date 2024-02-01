<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class abonnementRequest extends FormRequest
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
            'donateur_id' => 'required|numeric',
            'fondation_id' => 'required|numeric',
            'suivre' => 'boolean'
          
        ];
    }

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success'=> false,
            'error'=> true,
            'message'=> 'Erreur de validation',
            'errorsList'=> $validator->errors()
        ]));
    }


    public function messages()
    {
        return [
            'donateur_id.required' => 'Le donateur_id est obligatoire',
            'donateur_id.numeric' => 'le donateur_id doit être de type numerique ',
            'fondation_id.required' => 'fondation_id est obligatoire',
            'fondation_id.numeric' => 'fondation_id doit être de type numerique '
        ];
    }
}
