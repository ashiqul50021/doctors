<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\Advertisement;
use App\Models\District;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with(['user', 'speciality', 'reviews', 'district', 'area'])
            ->where('status', 'approved');

        // Keyword search
        if ($request->has('keywords') && $request->keywords != '') {
            $keywords = $request->keywords;
            $query->where(function ($q) use ($keywords) {
                $q->whereHas('user', function ($hq) use ($keywords) {
                    $hq->where('name', 'like', '%' . $keywords . '%');
                })
                    ->orWhereHas('speciality', function ($hq) use ($keywords) {
                        $hq->where('name', 'like', '%' . $keywords . '%');
                    })
                    ->orWhere('clinic_name', 'like', '%' . $keywords . '%');
            });
        }

        // Location search (legacy)
        if ($request->has('location') && $request->location != '') {
            $query->where(function ($q) use ($request) {
                $q->where('clinic_city', 'like', '%' . $request->location . '%')
                    ->orWhere('clinic_address', 'like', '%' . $request->location . '%');
            });
        }

        // Speciality filter (single from home)
        if ($request->has('speciality_id') && $request->speciality_id != '') {
            $query->where('speciality_id', $request->speciality_id);
        }

        // Speciality filter (multi-select from sidebar)
        if ($request->has('select_specialist')) {
            $selectedSpecialities = $request->select_specialist;
            if (is_array($selectedSpecialities) && count($selectedSpecialities) > 0) {
                $query->whereIn('speciality_id', $selectedSpecialities);
            }
        }

        // District filter
        if ($request->has('district_id') && $request->district_id != '') {
            $query->where('district_id', $request->district_id);
        }

        // Area filter
        if ($request->has('area_id') && $request->area_id != '') {
            $query->where('area_id', $request->area_id);
        }

        // Gender filter
        if ($request->has('gender') && is_array($request->gender) && count($request->gender) > 0) {
            $query->whereIn('gender', $request->gender);
        }

        // Fee range filter
        if ($request->has('fee_min') && $request->fee_min != '') {
            $query->where('consultation_fee', '>=', $request->fee_min);
        }
        if ($request->has('fee_max') && $request->fee_max != '') {
            $query->where('consultation_fee', '<=', $request->fee_max);
        }

        // Experience filter
        if ($request->has('experience') && $request->experience != '') {
            $query->where('experience_years', '>=', $request->experience);
        }

        // Rating filter
        if ($request->has('rating') && $request->rating != '') {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->where('is_approved', true);
            }, '>=', 1)
                ->withAvg([
                    'reviews' => function ($q) {
                        $q->where('is_approved', true);
                    }
                ], 'rating')
                ->having('reviews_avg_rating', '>=', $request->rating);
        }

        // Online consultation filter
        if ($request->has('online_consultation') && $request->online_consultation == '1') {
            $query->where('online_consultation', true);
        }

        // Home visit filter
        if ($request->has('home_visit') && $request->home_visit == '1') {
            $query->where('home_visit', true);
        }

        // Verified only filter
        if ($request->has('verified_only') && $request->verified_only == '1') {
            $query->where('is_verified', true);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'relevance');
        switch ($sortBy) {
            case 'fee_low':
                $query->orderBy('consultation_fee', 'asc');
                break;
            case 'fee_high':
                $query->orderBy('consultation_fee', 'desc');
                break;
            case 'experience':
                $query->orderBy('experience_years', 'desc');
                break;
            case 'rating':
                $query->withAvg([
                    'reviews' => function ($q) {
                        $q->where('is_approved', true);
                    }
                ], 'rating')
                    ->orderByDesc('reviews_avg_rating');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }

        $doctors = $query->paginate(10);
        $specialities = Speciality::where('is_active', true)->get();
        $districts = District::orderBy('name')->get();

        // Fetch ads
        $advertisements = Advertisement::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->inRandomOrder()
            ->get();

        if ($request->ajax()) {
            return view('frontend.super-doctor-list', compact('doctors', 'advertisements'))->render();
        }

        return view('frontend.search', compact('doctors', 'specialities', 'districts', 'advertisements'));
    }
}

