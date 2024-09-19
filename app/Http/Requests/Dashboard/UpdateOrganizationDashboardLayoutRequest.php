<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrganizationDashboardLayoutRequest extends FormRequest
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
            'card_type'        => 'required|array',
            'card_type.*'      => 'required|in:reports,deadlines,leaderboard,my-challenges,my-labs,my-projects,my-resources,inbox-friends,recommendations,my-organizations,subscription',
            'is_active'        => 'required|array',
            'is_active.*'      => 'required|in:yes,no',
            'position_index'   => 'nullable|array',
            'position_index.*' => 'nullable|integer|min:0|max:10', // Allow nullable values for position_index
        ];

        return array_merge($base_rules, [
            'card_type' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $requiredCardTypes = [
                        'reports',
                        'deadlines',
                        'leaderboard',
                        'my-challenges',
                        'my-labs',
                        'my-projects',
                        'my-resources',
                        'inbox-friends',
                        'recommendations',
                        'my-organizations',
                        'subscription',
                    ];

                    $missingCardTypes = array_diff($requiredCardTypes, $value);

                    if (!empty($missingCardTypes)) {
                        $fail('All card types must be present: '.implode(', ', $missingCardTypes));
                    }

                    $count = count($value);
                    $is_active = $this->input('is_active', []);
                    $position_index = $this->input('position_index', []);

                    // Check that the count of all arrays matches
                    if (count($is_active) !== $count) {
                        $fail('The number of items for '.$attribute.' must match the number of items for is_active.');

                        return;
                    }

                    // Check that position_index is distinct when is_active is "yes"
                    $positionIndices = [];
                    for ($i = 0; $i < $count; $i++) {
                        if ($is_active[$i] === 'yes') {
                            if (!isset($position_index[$i])) {
                                $fail('Position index is required when is_active is "yes".');

                                return;
                            }

                            if (in_array($position_index[$i], $positionIndices)) {
                                $fail('The position_index must be unique when is_active is "yes".');

                                return;
                            }
                            $positionIndices[] = $position_index[$i];
                        } elseif ($is_active[$i] === 'no' && !is_null($position_index[$i])) {
                            $fail('Position index must be null when is_active is "no".');

                            return;
                        }
                    }
                },
            ],
        ]);
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
