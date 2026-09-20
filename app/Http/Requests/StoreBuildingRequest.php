<?php

namespace App\Http\Requests;

use App\Enums\BuildingType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Only living buildings house residents, so any other type keeps zero.
        if ($this->input('type') !== BuildingType::Living->value) {
            $this->merge(['residents_count' => 0]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(BuildingType::class)],
            'price' => ['required', 'integer', 'min:0', 'max:999999999'],
            'income' => ['required', 'integer', 'min:0', 'max:999999999'],
            'upgrade_coefficient' => ['required', 'numeric', 'min:1', 'max:10'],
            'employees_count' => ['required', 'integer', 'min:0', 'max:100000'],
            'residents_count' => ['required', 'integer', 'min:0', 'max:100000'],
            'happiness' => ['required', 'integer', 'min:-100', 'max:100'],
            'attractiveness' => ['required', 'integer', 'min:-100', 'max:100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'type' => __('Type'),
            'price' => __('Price'),
            'income' => __('Income'),
            'upgrade_coefficient' => __('Upgrade coefficient'),
            'employees_count' => __('Employees'),
            'residents_count' => __('Residents'),
            'happiness' => __('Happiness'),
            'attractiveness' => __('Attractiveness'),
        ];
    }
}
