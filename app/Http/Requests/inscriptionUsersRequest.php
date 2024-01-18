<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class inscriptionUsersRequest extends FormRequest
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
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'image' => 'nullable |file |mimes:jpeg,jpg,png,gif',
            'description' => 'nullable|string|max:255',
            'numeroEnregistrement' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:5',
            'telephone' => 'required|string|max:10',
            'role' => 'required|in:admin,donateur,fondation',
            'bloque' => 'boolean',
            'is_deleted' => 'boolean'
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
            'nom.required' => 'le nom est obligatoire',
            'nom.string' => 'le nom doit être une chaine de caractère',
            'nom.max' => 'le nom ne doit pas depasser 255 caractères',
            'prenom.string' => 'le nom doit être une chaine de caractère',
            'prenom.max' => 'le prenom ne doit pas depasser 255 caractères',
            'image.file' => 'l\'image doit être de type file',
            'image.mimes' => 'l\'image doit respecter ses formats là: jpeg,jpg,png,gif',
            'description.string' => 'la description doit être une chaine de caractère',
            'description.max' => 'la description ne doit pas depasser 255 caractères',
            'numeroEnregistrement.string' => 'le numero d\'enregistrement doit être une chaine de caractère',
            'numeroEnregistrement.max' => 'le numero d\'enregistrement ne doit pas depasser 255 caractères',
            'adresse.string' => 'l\'adrresse  doit être une chaine de caractère',
            'adresse.max' => 'l\'adresse ne doit pas depasser 255 caractères',
            'email.required' => 'l\'email est requis',
            'email.email' => 'l\'email est mal ecris',
            'email.unique' => 'l\'email est unique',
            'password.required' => 'le password est requis',
            'password.string' => 'le password doit être une chaine de caractère',
            'password.min' => 'le password est minimum 5 caractères',
            'telephone.required' => 'le numero de telephone est obligatoire',
            'telephone.string' => 'le numero de telephone est de type string',
            'telephone.max' => 'le numero de telephone doit pas depasser 10 caractères',


        ];
    }
}
