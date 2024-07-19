<?php

namespace App\Http\Requests\Public\AdvanceSearch;

use App\Http\Requests\BaseRequest;

class AdvanceSearchBaseFormRequest extends BaseRequest
{
    /**
     * @var array|int[]
     */
    protected array $privacyMap = [
        'public'  => 0,
        'private' => 1,
    ];

    /**
     * @var array|int[]
     */
    protected array $statusMap = [
        'draft'     => 0,
        'published' => 1,
        'archive'   => 2,
    ];

    public function mapConstants(array|null $value, array $statusMap = []): ?array
    {
        if (!$value) {
            return null;
        }
        $formatted = collect();

        collect($value)->each(function ($item, $key) use ($formatted, $statusMap) {
            $mappedValue = data_get($statusMap, $item);
            if ($mappedValue !== null) {
                $formatted->put($key, $mappedValue);
            }
        });

        return $formatted->toArray();
    }
}
