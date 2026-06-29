<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecializationResource;
use App\Models\Specialization;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class SpecializationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $specializations = Specialization::query()
            ->withCount('doctors')
            ->when(request()->boolean('has_doctors'), function ($query) {
                $query->has('doctors');
            })
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%');
            })
            ->orderBy('name')
            ->get();

        return SpecializationResource::collection($specializations);
    }
}
