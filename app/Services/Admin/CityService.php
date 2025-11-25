<?php

namespace App\Services\Admin;

use App\Helpers\ResponseHelper;
use App\Models\City;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CityService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'name' => 'name',
    ];

    protected $searchables = ['name', 'description'];

    protected $filterables = [
        'status' => 'filterByStatus',
        'parent_id' => 'filterByParent',
    ];

    /**
     * filterByStatus
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByStatus(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('status', +$filter['data']);
    }

    /**
     * Method filterByParent
     *
     * @param Eloquent $query [explicite description]
     * @param array $filter [explicite description]
     *
     * @return Eloquent
     */
    public function filterByParent(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        $data = (array) json_decode($filter['data'], true);
        if (!count($data)) {
            return $query;
        }

        return $query->whereIn('parent_id', $data);
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return City::with('parent');
    }

    /**
     * store
     *
     * @param  array $data
     * @return City | array
     */
    public function store(array $data): City|array
    {
        try {
            if ($this->validateCityName($data)) {
                return ResponseHelper::sendError(trans('response.data_exist'));
            }

            return City::create($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * update
     *
     * @param  array $data
     * @param  int $id
     * @return bool | array
     */
    public function update(array $data, int $id): bool|array
    {
        try {
            $city = City::find($id);
            if (!$city) {
                return ResponseHelper::notFound();
            }

            if ($this->validateCityName($data, $id)) {
                return ResponseHelper::sendError(trans('response.data_exist'));
            }

            return $city->update($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * delete
     *
     * @param  int $id
     * @return bool | array
     */
    public function delete(int $id): bool|array
    {
        try {
            $city = City::with(['jobs', 'companies', 'child'])->find($id);
            if (!$city) {
                return ResponseHelper::notFound();
            }

            if ($city->child->count() || $city->jobs->count() || $city->companies->count()) {
                return ResponseHelper::sendError(trans('response.data_used'));
            }

            return $city->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method validateCityName
     *
     * @param array $data [explicite description]
     * @param $id [explicite description]
     *
     * @return bool
     */
    public function validateCityName(array $data, $id = null): bool
    {
        $name = mb_strtolower($data['name']);
        $parentId = $data['parent_id'] ?? null;

        return City::query()
            ->whereRaw('LOWER(name) = ?', [$name])
            ->when($parentId, function ($q) use ($parentId) {
                $q->where('parent_id', $parentId);
            }, function ($q) {
                $q->whereNull('parent_id');
            })
            ->when($id, function ($q) use ($id) {
                $q->where('id', '!=', $id);
            })
            ->exists();
    }
}
