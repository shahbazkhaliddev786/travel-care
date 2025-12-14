<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function index()
    {
        $specialties = $this->getSpecialties();
        
        // Get top doctors (verified doctors with highest ratings, limited to 6)
        $topDoctors = \App\Models\Doctor::with(['reviews'])
            ->where('is_verified', true)
            ->orderByDesc(function($query) {
                return $query->selectRaw('AVG(rating)')
                    ->from('reviews')
                    ->whereColumn('reviews.reviewable_id', 'doctors.id')
                    ->where('reviews.reviewable_type', 'App\\Models\\Doctor');
            })
            ->take(6)
            ->get();
        
        return view('home', compact('specialties', 'topDoctors'));
    }

    public function category(Request $request)
    {
        $query = \App\Models\Doctor::with(['user', 'reviews'])
            ->where('is_verified', true);

        // Apply filters
        if ($request->filled('location')) {
            $query->where('city', 'LIKE', '%' . $request->input('location') . '%');
        }

        if ($request->filled('hospital')) {
            $query->where('working_location', 'LIKE', '%' . $request->input('hospital') . '%');
        }

        // Use description as proxy for education keywords
        if ($request->filled('education')) {
            $query->where('description', 'LIKE', '%' . $request->input('education') . '%');
        }

        // Price filters
        $minPrice = $request->input('min-price');
        $maxPrice = $request->input('max-price');
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('consultation_fee', '>=', (float) $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('consultation_fee', '<=', (float) $maxPrice);
        }

        // Generic search by name or specialty
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function($q) use ($term) {
                $q->where('name', 'LIKE', '%' . $term . '%')
                  ->orWhere('specialization', 'LIKE', '%' . $term . '%');
            });
        }

        $doctors = $query->paginate(8);
        // Preserve filters in pagination links
        $doctors->appends($request->query());

        if ($request->ajax()) {
            $html = view('partials.category-doctors', compact('doctors'))->render();
            return response()->json(['html' => $html]);
        }

        return view('category', [
            'specialty' => 'Safe Travel',
            'specialtySlug' => null,
            'doctors' => $doctors
        ]);
    }

    public function categoryBySpecialty($specialty, Request $request)
    {
        // Convert slug back to readable name
        $specialtyName = ucwords(str_replace('-', ' ', $specialty));

        $query = \App\Models\Doctor::with(['user', 'reviews'])
            ->where('is_verified', true)
            ->where('specialization', 'LIKE', '%' . $specialtyName . '%');

        // Apply filters
        if ($request->filled('location')) {
            $query->where('city', 'LIKE', '%' . $request->input('location') . '%');
        }

        if ($request->filled('hospital')) {
            $query->where('working_location', 'LIKE', '%' . $request->input('hospital') . '%');
        }

        if ($request->filled('education')) {
            $query->where('description', 'LIKE', '%' . $request->input('education') . '%');
        }

        $minPrice = $request->input('min-price');
        $maxPrice = $request->input('max-price');
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('consultation_fee', '>=', (float) $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('consultation_fee', '<=', (float) $maxPrice);
        }

        // Generic search by name or specialty within selected specialty
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function($q) use ($term) {
                $q->where('name', 'LIKE', '%' . $term . '%')
                  ->orWhere('specialization', 'LIKE', '%' . $term . '%');
            });
        }

        $doctors = $query->paginate(8);
        // Preserve filters in pagination links
        $doctors->appends($request->query());

        if ($request->ajax()) {
            $html = view('partials.category-doctors', compact('doctors'))->render();
            return response()->json(['html' => $html]);
        }

        return view('category', [
            'specialty' => $specialtyName,
            'specialtySlug' => $specialty,
            'doctors' => $doctors
        ]);
    }

    private function getSpecialties()
    {
        $base = [
            ['name' => 'Therapist', 'icon' => 'assets/specialty icons/therapist.svg'],
            ['name' => 'Heart Issue', 'icon' => 'assets/specialty icons/heart-issue.svg'],
            ['name' => 'Dental Care', 'icon' => 'assets/specialty icons/dental.svg'],
            ['name' => 'Neurologist', 'icon' => 'assets/specialty icons/neurologist.svg'],
            ['name' => 'Dermatology', 'icon' => 'assets/specialty icons/dermatology.svg'],
            ['name' => 'Endocrinology', 'icon' => 'assets/specialty icons/Endocrinology.svg'],
            ['name' => 'Surgery', 'icon' => 'assets/specialty icons/surgery.svg'],
            ['name' => 'Oncology', 'icon' => 'assets/specialty icons/oncology.svg'],
            ['name' => 'Geneticist', 'icon' => 'assets/specialty icons/geneticist.svg'],
        ];

        // Compute real counts for verified doctors per specialty (case-insensitive LIKE match)
        $specialties = [];
        foreach ($base as $item) {
            $count = \App\Models\Doctor::where('is_verified', true)
                ->where('specialization', 'LIKE', '%' . $item['name'] . '%')
                ->count();

            $specialties[] = [
                'name' => $item['name'],
                'icon' => $item['icon'],
                'count' => $count,
            ];
        }

        return $specialties;
    }
}
