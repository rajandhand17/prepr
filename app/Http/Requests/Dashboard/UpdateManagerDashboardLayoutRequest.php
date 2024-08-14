<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateManagerDashboardLayoutRequest extends FormRequest
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
        $base_rules = [
            'card_type'         => 'required|array',
            'card_type.*'       => 'in:reports,deadlines,leaderboard,my-challenges,my-labs,my-projects,my-resources,inbox-friends,recommendations,my-organizations,subscription',
            'is_active'         => 'required|array',
            'is_active.*'       => 'in:yes,no',
            'position_x'        => 'nullable|array',
            'position_x.*'      => 'in:0,1,2,3',
            'position_y'        => 'nullable|array',
            'position_y.*'      => 'in:0,1,2,3',
        ];

        return array_merge($base_rules, [
            'card_type' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $count = count($value);
                    $is_active = $this->input('is_active', []);
                    $position_x = $this->input('position_x', []);
                    $position_y = $this->input('position_y', []);

                    // Check that the count of all arrays matches
                    if (
                        count($is_active) !== $count ||
                        count($position_x) !== $count ||
                        count($position_y) !== $count
                    ) {
                        $fail('The number of items for '.$attribute.' must match the number of items for is_active, position_x, and position_y.');
                    }

                    // Check that position_x and position_y combinations are unique, except when is_active is "no"
                    $combinations = [];
                    for ($i = 0; $i < $count; $i++) {
                        if ($is_active[$i] === 'yes') {
                            $combination = $position_x[$i].'-'.$position_y[$i];
                            if (in_array($combination, $combinations)) {
                                $fail('The combination of position_x and position_y must be unique when is_active is "yes".');

                                return;
                            }
                            $combinations[] = $combination;
                        }
                    }
                },
            ],
        ]);

        return $base_rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }
}
