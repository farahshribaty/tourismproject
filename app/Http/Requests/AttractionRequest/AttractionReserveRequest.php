<?php

namespace App\Http\Requests\AttractionRequest;

use Illuminate\Foundation\Http\FormRequest;

class AttractionReserveRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //'user_id'=>'required',
            'attraction_id'=>'required',
            'book_date'=>'required',
            'adults'=>'required',
            'children'=>'required',
            'check_or_book'=>'required|in:book,check',
            //'payment'=>'',
            'points_added'=> '',
        ];
    }
}
