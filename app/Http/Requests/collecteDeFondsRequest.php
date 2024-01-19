<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class collecteDeFondsRequest extends FormRequest
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
            'titre' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|file |mimes:jpeg,jpg,png,gif',
            'objectifFinancier' => 'required|numeric',
            'numeroCompte' => 'required|numeric',
          
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
            'titre.required' => 'Le titre est obligatoire',
            'titre.string' => 'le titre doit être une chaine de caractère ',
            'description.required' => 'La description est obligatoire',
            'description.string' => 'la description doit être une chaine de caractère ',
            'image.required' => 'l\'image est obligatoire',
            'image.file' => 'l\'image doit être de type file',
            'image.mimes' => 'l\'image doit respecter ses formats là: jpeg,jpg,png,gif',
            'objectifFinancier.required' => 'L\'objectif est obligatoire',
            'objectifFinancier.numeric' => 'l\'objectif financier doit un nombre numerique ',
            'numereCompte.required' => 'Le numero de compte est obligatoire',
            'numeroCompte.numeric' => 'le numero de compte doit un nombre numerique ',
        ];
    }
}
