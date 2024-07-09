<?php

namespace App\Helpers\Solr;

use App\Exceptions\SolrException;
use App\Traits\SyncUtilTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Http;

abstract class SolrBaseHelper
{
    use SyncUtilTrait;

    /**
     * @var array<string>
     */
    protected array $searchQueryFields = [];

    /**
     * @var string
     */
    protected string $solrCollection = '';

    /**
     * @var string
     */
    protected string $modelClass = '';

    /**
     * @var string
     */
    protected string $schemaName = '';

    /**
     * @param $value
     *
     * @return array
     */
    abstract public function formatData($value): array;

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return config('solr.APACHE_SOLR_URL');
    }

    /**
     * @return array[]
     */
    protected function getCredentials(): array
    {
        $solr_username = config('solr.APACHE_SOLR_USERNAME');
        $solr_password = config('solr.APACHE_SOLR_PASSWORD');

        return ['username' => $solr_username, 'password' => $solr_password];
    }

    /**
     * @return string[]
     */
    protected function getDefaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @param string $url
     * @param array  $query
     * @param array  $headers
     *
     * @throws SolrException
     *
     * @return array|false|mixed
     */
    public function get(string $url, array $query = [], array $headers = []): mixed
    {
        try {
            $auth = $this->getCredentials();
            $defaultHeaders = $this->getDefaultHeaders();
            $mergedHeader = array_merge($defaultHeaders, $headers ?: []);
            $response = Http::baseUrl($this->getBaseUrl())
                ->withHeaders($mergedHeader)
                ->withBasicAuth(data_get($auth, 'username'), data_get($auth, 'password'))
                ->withQueryParameters($query)
                ->get($url);

            if (!$response->ok()) {
                throw new SolrException($response, 400);
            }

            return $response->json();
        } catch (\Exception $exception) {
            throw new SolrException($exception->getMessage(), 400);
        }
    }

    /**
     * @param array<string,string> $headers
     *
     * @throws SolrException
     */
    public function post(string $url, $data, array $headers = [])
    {
        try {
            $auth = $this->getCredentials();
            $defaultHeaders = $this->getDefaultHeaders();
            $mergedHeader = array_merge($defaultHeaders, $headers ?: []);

            $response = Http::baseUrl($this->getBaseUrl())
                ->withHeaders($mergedHeader)
                ->withBasicAuth(data_get($auth, 'username'), data_get($auth, 'password'))->post($url, $data);

            if (!$response->ok()) {
                throw new SolrException($response, 400);
            }

            return $response->json();
        } catch (\Exception $exception) {
            throw new SolrException($exception->getMessage(), 400);
        }
    }

    /**
     * @param array<string,mixed> $headers
     *
     * @throws SolrException
     */
    public function delete(string $url, array $headers = [])
    {
        try {
            $auth = $this->getCredentials();
            $defaultHeaders = $this->getDefaultHeaders();
            $mergedHeader = array_merge($defaultHeaders, $headers ?: []);
            $response = Http::baseUrl($this->getBaseUrl())
                ->withHeaders($mergedHeader)
                ->withBasicAuth(data_get($auth, 'username'), data_get($auth, 'password'))->delete($url);

            if (!$response->ok()) {
                throw new SolrException($response, 400);
            }

            return $response->json();
        } catch (\Exception $exception) {
            throw new SolrException($exception->getMessage(), 400);
        }
    }

    /**
     * @param ?string     $keyword
     * @param array       $filters
     * @param string|null $additionalQuery
     *
     * @return array
     */
    protected function prepareSearchQuery(?string $keyword, array $filters = [], ?string $additionalQuery = null): array
    {
        $query = '';

        if ($keyword) {
            foreach ($this->searchQueryFields as $key => $field) {
                $string = count($this->searchQueryFields) > ($key + 1) ? '%s:"%s" AND ' : '%s:"%s"^20 ';
                $query .= sprintf($string, $field, $keyword);
            }
        }

        if ($filters) {
            $count = 0;
            foreach ($filters as $key => $filter) {
                if ($filter) {
                    if ($count === 0 && $keyword) {
                        $query .= ' AND ';
                    } elseif ($count > 0) {
                        $query .= ' AND ';
                    }

                    if (is_array($filter)) {
                        $query .= sprintf('%s:(%s) ', $key, implode(' OR ', $filter));
                    } else {
                        $query .= sprintf('%s:%s', $key, $filter);
                    }
                    $count++;
                }
            }
        }

        if ($additionalQuery) {
            $query = ($query ? "($query) AND " : '').$additionalQuery;
        }

        return [
            'q' => $query,
        ];
    }

    /**
     * @param int $total
     * @param int $page
     * @param int $per_page
     *
     * @return int[]
     */
    protected function prepareMetaData(int $total, int $page = 1, int $per_page = 10): array
    {
        $ratio = (float) ($per_page > 0 ? $total / $per_page : 0);
        $last_page = $ratio > floor($ratio) ? floor($ratio) + 1 : floor($ratio);

        return [
            'current_page' => $page,
            'per_page'     => $per_page,
            'total'        => $total,
            'last_page'    => $last_page,
        ];
    }

    /**
     * @param string|null $keyword
     * @param array       $filters
     * @param string|null $additionalQuery
     * @param int         $rows
     *
     * @return array|false
     */
    public function search(?string $keyword, array $filters = [], ?string $additionalQuery = null, int $rows = 2000): array|false
    {
        try {
            if (!$keyword && empty($filters)) {
                return [];
            }

            /** PREPARING THE QUERY STRING FOR SOLR */
            $query = $this->prepareSearchQuery($keyword, $filters, $additionalQuery);

            /** SKIP AND TAKE CONCEPT AS DATABASE - PAGINATION */
            $url = sprintf('/solr/%s/select', $this->solrCollection);

            $result = $this->get(
                $url,
                array_merge($query, [
                    'start'              => 0,
                    'rows'               => $rows,
                    'spellcheck'         => 'true',
                    'defType'            => 'edismax',
                    'spellcheck.collate' => 'true',
                    'debug'              => 'true',
                    'wt'                 => 'json',
                    'indent'             => 'true',
                ])
            );

//            $total = data_get($result, 'response.numFound');
//            dd($result);
            return [
                'data' => data_get($result, 'response.docs', []),
                //                'data' => $result,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @throws SolrException
     */
    public function update($data): void
    {
        $this->post(sprintf('/solr/%s/update?commit=true', $this->solrCollection), $data);
    }

    /**
     * @throws SolrException
     *
     * @return void
     */
    public function migrate(): void
    {
        $schemaDefinition = require base_path(sprintf('/app/Helpers/Solr/Schema/%s.php', $this->getSchemaName()));
        $schema = data_get($schemaDefinition, 'schema');
        $this->post(sprintf('solr/%s/schema', $this->solrCollection), [
            'add-field' => $schema,
        ]);
    }

    /**
     * @throws SolrException
     *
     * @return void
     */
    public function migrateTextField(): void
    {
        $this->post(sprintf('solr/%s/schema', $this->solrCollection), [
            'add-field-type' => [
                'name'                 => 'text_edge_ngram',
                'class'                => 'solr.TextField',
                'positionIncrementGap' => 100,
                'analyzer'             => [
                    'tokenizer' => [
                        'class' => 'solr.StandardTokenizerFactory',
                    ],
                    'filters' => [
                        [
                            'class' => 'solr.LowerCaseFilterFactory',
                        ],
                        [
                            'class'       => 'solr.EdgeNGramFilterFactory',
                            'minGramSize' => 2,
                            'maxGramSize' => 20,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param array $where
     *
     * @throws SolrException
     * @throws BindingResolutionException
     *
     * @return void
     */
    public function sync(array $where = []): void
    {
        try {
            $model = app()->make($this->modelClass);
            $labCount = $model->count();
            $batches = $this->prepareBatch($labCount);
            foreach ($batches as $key => $batch) {
                $data = $model::query()->where($where)->skip(data_get($batch, 'skip'))->take(data_get($batch, 'take'))->orderBy('id')->get();
                $dataFormatted = $data->map(function ($value) {
                    return $this->formatData($value);
                });
                $this->update($dataFormatted);
//                sleep(5);
            }
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());

            throw $exception;
        }
    }

    /**
     * @param $model
     *
     * @return void
     */
    public function syncSingleton($model): void
    {
        try {
            $dataFormatted = $this->formatData($model);
            $this->update([$dataFormatted]);
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());
        }
    }

    /**
     * @param $id
     */
    public function deleteSingleton($id): void
    {
        try {
            $this->update([
                'delete' => [
                    'id' => $id,
                ],
            ]);
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());
        }
    }

    /**
     * @param array $where
     *
     * @throws BindingResolutionException
     * @throws SolrException
     *
     * @return void
     */
    public function syncDeleted(array $where = []): void
    {
        try {
            $model = app()->make($this->modelClass);
            $trashedCount = $model->onlyTrashed()->count();
            $batches = $this->prepareBatch($trashedCount);
            foreach ($batches as $batch) {
                $data = $model::query()->onlyTrashed()->where($where)->skip(data_get($batch, 'skip'))->take(data_get($batch, 'take'))->orderBy('id')->get();
                $this->update(['delete' => $data->pluck('id')->toArray()]);
            }
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());

            throw $exception;
        }
    }
}
