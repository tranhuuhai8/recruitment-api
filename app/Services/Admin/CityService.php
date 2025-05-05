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
        'name' => 'name',
    ];

    protected $searchables = ['name'];

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent | QueryBuilder
    {
        return City::query();
    }

    /**
     * store
     *
     * @param  array $data
     * @return City | array
     */
    public function store(array $data): City | array
    {
        try {
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
    public function update(array $data, int $id): bool | array
    {
        try {
            $city = City::find($id);
            if (!$city) {
                return ResponseHelper::sendError(trans('City not found!'));
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
    public function delete(int $id): bool | array
    {
        try {
            $city = City::find($id);
            if (!$city) {
                return ResponseHelper::sendError(trans('City not found!'));
            }

            return $city->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
