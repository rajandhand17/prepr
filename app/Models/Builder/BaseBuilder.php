<?php

namespace App\Models\Builder;

use App\Helpers\Solr\SolrBaseHelper;
use App\Traits\AuthUserTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class BaseBuilder extends Builder
{
    use AuthUserTrait;

    /**
     * Valid filter keys for apache solr.
     *
     * @var array
     */
    protected array $filterKeys = [];

    /**
     * @return SolrBaseHelper|false
     */
    abstract public function getSolrInstance(): SolrBaseHelper|false;

    /**
     * @param array $filters
     * @param array $validKeys
     *
     * @return false|array
     */
    protected function validFilters(array $filters, array $validKeys): false|array
    {
        if (empty($filters)) {
            return false;
        }
        $filtered = Arr::only($filters, $validKeys);

        return [
            'is_valid' => collect($filtered)->some(function ($value) {
                return !empty($value);
            }),
            'formatted' => $filtered,
        ];
    }

    /**
     * @param string|null $keyword
     * @param array $filters
     * @param string|null $additionalQuery
     * @param int|null $rows
     *
     * @return BaseBuilder
     */
    public function whereSearchFilter(string|null $keyword, array $filters = [], ?string $additionalQuery = null, ?int $rows = 2000): self
    {
        // APPLICATION LANG
        $lang = app()->getLocale();

        // DEFINING A INSTANCE SO THAT WE CAN APPEND ACCORDINGLY
        $builder = $this->where('language', '=', $lang);

        // CHECKS IF THE FILTER CONTAINS ONLY THE ABOVE KEYS FOR PROPER BEHAVIOUR AND ALSO AT LEAST ONE ON THE KEY MUST HAVE SOME VALUE
        $formattedFilter = $this->validFilters($filters, $this->filterKeys);

        // TO AVOID HEAVY LIFTING WE ONLY PASS SEARCH KEY ON SOLR IF THE KEYWORD IS GREATER THAN 1
        $validKeyword = Str::length($keyword ?: '') > 1;

        // IF THERE IS KEYWORD BUT LENGTH IS LESS THAN 1 WE DO A LIKE QUERY IN OUR DATABASE
        if ($keyword && !$validKeyword) {
            $builder = $builder->where('title', 'like', $keyword . '%');
        }

        // IN CASE OF INVALID KEYWORD AND INVALID FILTERS WE AVOID APACHE SOLR
        if (!$validKeyword && !data_get($formattedFilter, 'is_valid') && !$additionalQuery) {
            return $builder;
        }
        // APACHE SOLR SERVICE INSTANCE FOR LAB
        $solrInstance = $this->getSolrInstance();

        if ($solrInstance) {
            $results = $solrInstance->search(
                $keyword,
                data_get($formattedFilter, 'formatted', []),
                rows: $rows
            );
            // THE RESULTS THAT WE GET FROM THE APACHE SOLR'S SEARCH AND FILTER
            $resultIds = collect(data_get($results, 'data') ?? [])->pluck('id')->map(function ($value) {
                return (int)$value;
            })->toArray();
            // FILTERING RESULTS FROM OUR DATABASE BASED ON THE SOLR RESULT AND SORTING ACCORDINGLY
            $builder = $builder->whereIn('id', $resultIds)->orderByRaw('FIELD(id, ' . implode(',', $resultIds) . ')');
        }

        $sortBy = request()->get('sort_by');
        if ($sortBy && in_array($sortBy, ['created_data_asc', 'created_data_desc'])) {
            $sorting = [
                'created_data_asc' => 'asc',
                'created_data_desc' => 'desc'
            ];
            $builder->orderBy('created_at', $sorting[$sortBy]);
        }
        return $builder;
    }
}
