<?php

namespace App\Services;

use Closure;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class BaseService
{
    protected $orderDirMap = [
        'ascending' => 'asc',
        'descending' => 'desc',
    ];

    /**
     * [ columnKey => fieldName ]
     *
     * @var string[]
     */
    protected $searchables = [];

    /**
     * [ column1, column2 ]
     *
     * @var string[]
     */
    protected $orderables = [];

    /**
     * [ columnKey => filterFunction ]
     *
     * @var string[]
     */
    protected $filterables = [];

    protected $isPaginate = true;
    protected $fields = [];
    protected $query;
    protected $perPage;

    public function __construct()
    {
        $this->perPage = config('base.per_page');
    }

    public static function getInstance()
    {
        return app(static::class);
    }

    protected function currentQuery()
    {
        if (!$this->query) {
            $this->newQuery();
        }

        return $this->query;
    }

    public function data($search, $orders, $filters, $perPage = null, $all = false)
    {
        $perPage = $perPage ?? $this->perPage;
        $query = $this->currentQuery();

        $this->applySearchToQuery($search, $query);
        $this->applyFilterToQuery($filters, $query);
        $this->applyOrderToQuery($orders, $query);

        if ($all) {
            return $query->get();
        }
        return $query->paginate(intval($perPage))->withQueryString();
    }

    protected function applySearchToQuery($search, Builder $query)
    {
        if (empty($search) || !is_string($search) || !count($this->searchables)) {
            return $query;
        }

        $content = '%' . trim($search) . '%';
        $query->where(
            function ($q) use ($content) {
                foreach ($this->searchables as $searchable) {
                    $q->orWhere($searchable, 'like', $content);
                }
            }
        );

        return $query;
    }

    protected function applyFilterToQuery($filters, $query)
    {
        if (!is_array($filters)) {
            return $query;
        }

        foreach ($filters as $filter) {
            $issetFilter = !empty($filter['key'])
                && isset($filter['data'])
                && isset($this->filterables[$filter['key']]);
            if ($issetFilter && $filter['data'] !== null && $filter['data'] !== '') {
                $funName = $this->filterables[$filter['key']];
                if (method_exists($this, $funName)) {
                    $this->{$funName}($query, $filter, $filters);
                } else {
                    $this->defaultFilter($query, $filter, $filters);
                }
            }
        }

        return $query;
    }

    protected function applyCustomerRole(Builder $query, $companyId, bool $condition): Builder
    {
        return $query->when(
            $condition,
            function (Builder $query) use ($companyId) {
                $query->where('companies.id', $companyId);
            }
        );
    }

    protected function defaultFilter($query, $filter, $filters)
    {
        $query->where($this->filterables[$filter['key']], $filter['data']);

        return $query;
    }

    protected function applyOrderToQuery($orders, $query)
    {
        if (!is_array($orders)) {
            return $query;
        }

        foreach ($orders as $order) {
            if (!empty($order['key']) && !empty($order['dir'])) {
                $dir = Str::lower($order['dir']);
                if (!empty($this->orderDirMap[$dir]) && !empty($this->orderables[$order['key']])) {
                    $query->orderBy($this->orderables[$order['key']], $this->orderDirMap[$dir]);
                }
            }
        }

        return $query;
    }

    public function with($relations, $callback = null)
    {
        $this->currentQuery()->with(...func_get_args());

        return $this;
    }

    public function whereExists(Closure $callback, $boolean = 'and', $not = false)
    {
        $this->currentQuery()->whereExists(...func_get_args());

        return $this;
    }

    public function withCount($relations)
    {
        $this->currentQuery()->withCount(...func_get_args());

        return $this;
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->currentQuery()->orderBy(...func_get_args());

        return $this;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->currentQuery()->where(...func_get_args());

        return $this;
    }

    /**
     * @return $this
     */
    public function newQuery()
    {
        $this->query = $this->makeNewQuery();

        return $this;
    }

    /**
     * Add a join clause to the query.
     *
     * @param  string          $table
     * @param  string|\Closure $first
     * @param  string|null     $operator
     * @param  string|null     $second
     * @param  string          $type
     * @param  bool            $where
     * @return $this
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $this->currentQuery()->join(...func_get_args());

        return $this;
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array $columns
     * @return $this
     */
    public function select(array $columns = ['*'])
    {
        $this->currentQuery()->select(...func_get_args());

        return $this;
    }

    /**
     * Add a "group by" clause to the query.
     *
     * @param  array|string ...$groups
     * @return $this
     */
    public function groupBy(...$groups)
    {
        $this->currentQuery()->groupBy(...func_get_args());

        return $this;
    }

    /**
     * @param  $data
     * @return mixed|null
     */
    protected function parseParams($data)
    {
        try {
            return json_decode($data, true);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     *
     * @return string
     */
    protected function getSelectRaw()
    {
        return implode(', ', $this->fields);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    abstract public function makeNewQuery();

    public function upsertOrDeleteWhereCondition($model, $insertData, $condition, $updateField)
    {
        [$updateArray, $insertArray] = collect($insertData)->partition(
            function ($item) {
                return isset($item['id']);
            }
        );
        $model->whereNotIn('id', $updateArray->pluck('id'))
            ->where($condition['key'], $condition['value'])
            ->delete();
        $model->upsert($updateArray->toArray(), ['id'], $updateField);
        $model->insert($insertArray->toArray());
    }
}
