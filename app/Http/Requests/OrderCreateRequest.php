<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\AssetSymbol;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderCreateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'symbol' => ['required', Rule::enum(AssetSymbol::class)],
            'side' => ['required', 'in:buy,sell'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'amount' => ['required', 'numeric', 'min:0.0001'],
        ];
    }
}
