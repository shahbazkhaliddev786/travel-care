<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class TagManagementController extends Controller
{
    /**
     * Display a listing of all tags.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Tag::query();
        
        // Apply search filter if provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Get tags with usage count
        $query->withCount('services');
        
        // Apply sorting based on sort parameter
        $sort = $request->get('sort', 'usage_desc'); // Default to usage descending
        
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'usage_asc':
                $query->orderBy('services_count', 'asc');
                break;
            case 'usage_desc':
                $query->orderBy('services_count', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('services_count', 'desc');
                break;
        }
        
        $tags = $query->paginate(20);
        
        return view('admin.tags.index', compact('tags'));
    }
    
    /**
     * Store a newly created tag in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            \Log::info('Tag creation attempt', [
                'data' => $request->all(),
                'expects_json' => $request->expectsJson(),
                'headers' => $request->headers->all()
            ]);
            
            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:tags,name',
                'description' => 'nullable|string|max:255',
            ]);
            
            \Log::info('Tag validation passed', ['validated_data' => $validated]);
            
            $tag = Tag::create($validated);
            
            \Log::info('Tag created successfully', ['tag_id' => $tag->id, 'tag_name' => $tag->name]);
            
            // Handle AJAX requests (from modal or other JS)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tag created successfully.',
                    'tag' => $tag
                ]);
            }
            
            // Handle regular form submissions
            return redirect()->route('admin.tags.index')
                ->with('success', 'Tag created successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            \Log::warning('Tag validation failed', ['errors' => $e->errors()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            // Handle other errors
            \Log::error('Tag creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the tag: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'An error occurred while creating the tag. Please try again.')
                ->withInput();
        }
    }
    
    /**
     * Show the form for editing the specified tag.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $tag = Tag::withCount('services')->findOrFail($id);
            
            // Calculate tag rank based on usage (simplified)
            $tagRank = 1;
            try {
                $allTagsWithCounts = Tag::withCount('services')->get();
                $tagRank = $allTagsWithCounts->where('services_count', '>', $tag->services_count)->count() + 1;
            } catch (\Exception $e) {
                \Log::warning('Failed to calculate tag rank: ' . $e->getMessage());
            }
            
            // Get related tags (simplified with better error handling)
            $relatedTags = collect();
            if ($tag->services_count > 0 && Schema::hasTable('service_tag')) {
                try {
                    $relatedTags = DB::table('service_tag as st1')
                        ->join('service_tag as st2', 'st1.service_id', '=', 'st2.service_id')
                        ->join('tags', 'st2.tag_id', '=', 'tags.id')
                        ->where('st1.tag_id', $tag->id)
                        ->where('st2.tag_id', '!=', $tag->id)
                        ->select('tags.*', DB::raw('count(*) as common_services'))
                        ->groupBy('tags.id', 'tags.name', 'tags.description', 'tags.created_at', 'tags.updated_at')
                        ->orderBy('common_services', 'desc')
                        ->limit(10)
                        ->get();
                } catch (\Exception $e) {
                    \Log::warning('Related tags query failed: ' . $e->getMessage());
                }
            }
            
            // Provider distribution (simplified and safe)
            $providerDistribution = [
                'doctors' => 0,
                'hospitals' => 0,
                'clinics' => 0,
                'laboratories' => 0
            ];
            
            // Only try to calculate distribution if tables exist
            if (Schema::hasTable('service_tag') && Schema::hasTable('doctor_services')) {
                try {
                    $doctorServices = DB::table('service_tag')
                        ->join('doctor_services', 'service_tag.service_id', '=', 'doctor_services.id')
                        ->where('service_tag.tag_id', $tag->id)
                        ->count();
                    
                    $providerDistribution['doctors'] = $doctorServices;
                } catch (\Exception $e) {
                    \Log::warning('Provider distribution calculation failed: ' . $e->getMessage());
                }
            }
            
            return view('admin.tags.edit', compact('tag', 'tagRank', 'relatedTags', 'providerDistribution'));
            
        } catch (\Exception $e) {
            \Log::error('Tag edit page failed: ' . $e->getMessage());
            
            return redirect()->route('admin.tags.index')
                ->with('error', 'Unable to load tag for editing. Please try again.');
        }
    }
    
    /**
     * Update the specified tag in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            
            \Log::info('Tag update attempt', [
                'tag_id' => $id,
                'data' => $request->all()
            ]);
            
            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:tags,name,' . $id,
                'description' => 'nullable|string|max:255',
            ]);
            
            $tag->update($validated);
            
            \Log::info('Tag updated successfully', ['tag_id' => $id, 'tag_name' => $tag->name]);
            
            return redirect()->route('admin.tags.index')
                ->with('success', 'Tag updated successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Tag update validation failed', ['tag_id' => $id, 'errors' => $e->errors()]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Tag update failed: ' . $e->getMessage(), [
                'tag_id' => $id,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'An error occurred while updating the tag. Please try again.')
                ->withInput();
        }
    }
    
    /**
     * Remove the specified tag from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            
            \Log::info('Tag delete attempt', ['tag_id' => $id, 'tag_name' => $tag->name]);
            
            // Check if tag is in use
            $servicesCount = $tag->services()->count();
            if ($servicesCount > 0) {
                \Log::warning('Cannot delete tag - still in use', [
                    'tag_id' => $id, 
                    'services_count' => $servicesCount
                ]);
                
                return redirect()->route('admin.tags.index')
                    ->with('error', "Cannot delete tag '{$tag->name}' because it is associated with {$servicesCount} services.");
            }
            
            $tagName = $tag->name;
            $tag->delete();
            
            \Log::info('Tag deleted successfully', ['tag_id' => $id, 'tag_name' => $tagName]);
            
            return redirect()->route('admin.tags.index')
                ->with('success', "Tag '{$tagName}' deleted successfully.");
                
        } catch (\Exception $e) {
            \Log::error('Tag delete failed: ' . $e->getMessage(), [
                'tag_id' => $id,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.tags.index')
                ->with('error', 'An error occurred while deleting the tag. Please try again.');
        }
    }
    
    /**
     * Assign tags to a service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $serviceId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function assignToService(Request $request, $serviceId)
    {
        try {
            $service = Service::findOrFail($serviceId);
            
            \Log::info('Tag assignment attempt', [
                'service_id' => $serviceId,
                'data' => $request->all()
            ]);
            
            $validated = $request->validate([
                'tags' => 'array',
                'tags.*' => 'exists:tags,id',
            ]);
            
            // Handle empty tags array (remove all tags)
            $tags = $validated['tags'] ?? [];
            
            $service->tags()->sync($tags);
            
            \Log::info('Tags assigned successfully', [
                'service_id' => $serviceId,
                'tags' => $tags
            ]);
            
            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tags assigned to service successfully.'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Tags assigned to service successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Tag assignment validation failed', [
                'service_id' => $serviceId,
                'errors' => $e->errors()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Tag assignment failed: ' . $e->getMessage(), [
                'service_id' => $serviceId,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while assigning tags. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'An error occurred while assigning tags. Please try again.');
        }
    }

    /**
     * Get tags for a specific service (API endpoint).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceTags($id)
    {
        try {
            $service = Service::findOrFail($id);
            
            $tagIds = [];
            try {
                $tagIds = $service->tags->pluck('id')->toArray();
            } catch (\Exception $e) {
                \Log::warning('Failed to get tags for service: ' . $e->getMessage(), [
                    'service_id' => $id
                ]);
                // Return empty array if tags relationship fails
            }
            
            \Log::info('Service tags retrieved successfully', [
                'service_id' => $id,
                'tag_count' => count($tagIds)
            ]);
            
            return response()->json([
                'success' => true,
                'tags' => $tagIds
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Service not found for tag retrieval', ['service_id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Service not found.'
            ], 404);
            
        } catch (\Exception $e) {
            \Log::error('Failed to get service tags: ' . $e->getMessage(), [
                'service_id' => $id,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving service tags.'
            ], 500);
        }
    }
}