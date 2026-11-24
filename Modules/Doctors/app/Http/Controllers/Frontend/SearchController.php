<?php

namespace Modules\Doctors\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Doctors\Models\Doctor;
use Modules\Doctors\Models\Speciality;
use App\Models\Advertisement;
use App\Models\District;
use App\Models\Area;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with(['user', 'speciality', 'reviews', 'district', 'area'])
                       ->where('status', 'approved');

        if ($request->has('keywords') && $request->keywords != '') {
            $keywords = $request->keywords;
            $query->where(function($q) use ($keywords) {
                $q->whereHas('user', function($hq) use ($keywords) {
                    $hq->where('name', 'like', '%'.$keywords.'%');
                })
                ->orWhereHas('speciality', function($hq) use ($keywords) {
                    $hq->where('name', 'like', '%'.$keywords.'%');
                })
                ->orWhere('clinic_name', 'like', '%'.$keywords.'%');
            });
        }

        if ($request->has('location') && $request->location != '') {
            $query->where(function($q) use ($request) {
                $q->whereHas('district', function($dq) use ($request) {
                    $dq->where('name', 'like', '%'.$request->location.'%');
                })
                ->orWhereHas('area', function($aq) use ($request) {
                    $aq->where('name', 'like', '%'.$request->location.'%');
                })
                ->orWhere('clinic_address', 'like', '%'.$request->location.'%');
            });
        }

        if ($request->has('speciality_id') && $request->speciality_id != '') {
            $query->where('speciality_id', $request->speciality_id);
        }

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

        // Random order; the seed is kept in session so pagination doesn't repeat doctors.
        // A fresh seed is generated whenever the first page is opened.
        if (!$request->filled('page') || (int) $request->page <= 1 || !$request->session()->has('doctors_random_seed')) {
            $request->session()->put('doctors_random_seed', random_int(1, 999999));
        }
        $doctors = $query->inRandomOrder($request->session()->get('doctors_random_seed'))->paginate(10);
        $specialities = Speciality::where('is_active', true)->get();
        $districts = District::orderBy('name')->get();
        $areas = collect();

        if ($request->filled('district_id')) {
            $areas = Area::where('district_id', $request->district_id)->orderBy('name')->get();
        }

        // Fetch ads
        $advertisements = Advertisement::where('is_active', true)
                                       ->where(function($q) {
                                            $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                                       })
                                       ->where(function($q) {
                                            $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                                       })
                                       ->inRandomOrder()
                                       ->get();

        return view('doctors::frontend.search', compact('doctors', 'specialities', 'districts', 'areas', 'advertisements'));
    }
}
