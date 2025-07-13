<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
    $supplierId = $this->route('supplier'); // ambil ID dari route

    return [
        'name' => [
            'required',
            'string',
            Rule::unique('suppliers', 'name')->ignore($supplierId),
        ],
        'phone' => 'required|string',
        'email' => 'nullable|email',
        'address' => 'nullable|string',
        'note' => 'nullable|string',
    ];
}

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama supplier harus diisi.',
            'name.string' => 'Nama supplier harus berupa teks.',
            'name.unique' => 'Nama supplier sudah terdaftar.',
            'phone.required' => 'Nomor telepon harus diisi.',
            'phone.phone' => 'Nomor telepon tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'address.string' => 'Alamat harus berupa teks.',
        ];
    }
}
