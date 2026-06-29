<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'specialization_id' => ['nullable', 'integer'],
            'specialization_slug' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female,other'],
            'min_experience' => ['nullable', 'integer', 'min:0'],
            'min_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'max_fee' => ['nullable', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'in:experience_years,rating,consultation_fee,name'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'rating';
        $sortDir = $validated['sort_dir'] ?? 'desc';
        $perPage = $validated['per_page'] ?? 15;

        $allowedSorts = [
            'experience_years',
            'rating',
            'consultation_fee',
            'name',
        ];

        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'rating';

        $query = Doctor::query()
            ->with('specialization');

        if (!empty($validated['search'])) {
            $query->search($validated['search']);
        }

        $query->filter($validated);

        if (!empty($validated['specialization_slug'])) {
            $query->whereHas('specialization', function ($q) use ($validated) {
                $q->where('slug', $validated['specialization_slug']);
            });
        }

        if ($request->has('is_available')) {
            $query->where('is_available', $validated['is_available']);
        } else {
            $query->where('is_available', true);
        }

        $doctors = $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return DoctorResource::collection($doctors)
            ->additional([
                'meta' => [
                    'current_page' => $doctors->currentPage(),
                    'last_page' => $doctors->lastPage(),
                    'total' => $doctors->total(),
                ],
            ]);
    }

    public function show(Doctor $doctor): DoctorResource
    {
        return new DoctorResource(
            $doctor->load('specialization')
        );
    }

    public function store(StoreDoctorRequest $request): DoctorResource
    {
        $data = $request->validated();

        $doctor = Doctor::create($data);

        return new DoctorResource($doctor->load('specialization'));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): DoctorResource
    {
        $data = $request->validated();

        $doctor->update($data);

        return new DoctorResource($doctor->load('specialization'));
    }

    public function destroy(Doctor $doctor): Response
    {

        $doctor->delete();

        return response()->noContent();
    }
}