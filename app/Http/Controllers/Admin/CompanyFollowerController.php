<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyFollowerController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        $perPage = (int) data_get($request, 'per_page', 20);
        $search = data_get($request, 'search');

        $query = DB::table('company_followers as cf')
            ->join('companies as c', 'c.id', '=', 'cf.company_id')
            ->whereNull('cf.deleted_at')
            ->whereNull('c.deleted_at')
            ->select([
                'c.id',
                'c.name',
                'c.short_name',
                'c.logo',
                DB::raw('count(cf.id) as followers_count'),
            ])
            ->groupBy('c.id', 'c.name', 'c.short_name', 'c.logo')
            ->orderByDesc('followers_count');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('c.name', 'like', '%' . $search . '%')
                    ->orWhere('c.short_name', 'like', '%' . $search . '%');
            });
        }

        $data = $query->paginate($perPage);
        return $this->sendSuccessResponse($data);
    }
}
