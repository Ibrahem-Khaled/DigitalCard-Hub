<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends BaseController
{
    /**
     * Get all sliders
     * 
     * @queryParam position string Filter by position (homepage, category, product, footer). Example: homepage
     * @queryParam active boolean Filter by active status. Example: true
     * @queryParam per_page integer Number of items per page. Example: 15
     */
    public function index(Request $request)
    {
        $query = Slider::query();

        // Filter by position
        if ($request->has('position') && $request->position) {
            $query->forPosition($request->position);
        }

        // Filter by active status
        if ($request->has('active')) {
            if ($request->boolean('active')) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        } else {
            // Default: show only active sliders
            $query->active();
        }

        // Show only currently active sliders (within date range)
        if ($request->boolean('currently_active', true)) {
            $query->currentlyActive();
        }

        // Order by sort_order
        $query->ordered();

        $perPage = $request->get('per_page', 15);
        $sliders = $query->paginate($perPage);

        return $this->paginatedResponse($sliders);
    }

    /**
     * Get homepage sliders (most common use case)
     * 
     * Returns only active sliders for homepage that are currently within their date range
     */
    public function homepage()
    {
        $sliders = Slider::getHomepageSliders();

        return $this->successResponse(SliderResource::collection($sliders));
    }

    /**
     * Get single slider
     */
    public function show($id)
    {
        $slider = Slider::find($id);

        if (!$slider) {
            return $this->notFoundResponse('السلايدر غير موجود');
        }

        return $this->successResponse(new SliderResource($slider));
    }

    /**
     * Get sliders by position
     * 
     * @param string $position The position (homepage, category, product, footer)
     */
    public function byPosition(Request $request, $position)
    {
        $query = Slider::active()
            ->forPosition($position)
            ->currentlyActive()
            ->ordered();

        $perPage = $request->get('per_page', 15);
        $sliders = $query->paginate($perPage);

        return $this->paginatedResponse($sliders);
    }
}

