<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterPage;
use App\Models\SocialMediaLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FooterPageManagementController extends Controller
{
    /**
     * Display a listing of footer pages.
     */
    public function index()
    {
        $pages = FooterPage::with('updatedByUser')
            ->orderBy('slug')
            ->paginate(10);

        $stats = [
            'total' => FooterPage::count(),
            'active' => FooterPage::where('is_active', true)->count(),
            'inactive' => FooterPage::where('is_active', false)->count(),
        ];

        return view('admin.footer-pages.index', compact('pages', 'stats'));
    }

    /**
     * Display the specified footer page.
     */
    public function show(FooterPage $footerPage)
    {
        $footerPage->load('updatedByUser');
        return view('admin.footer-pages.show', compact('footerPage'));
    }

    /**
     * Show the form for editing the specified footer page.
     */
    public function edit(FooterPage $footerPage)
    {
        return view('admin.footer-pages.edit', compact('footerPage'));
    }

    /**
     * Update the specified footer page.
     */
    public function update(Request $request, FooterPage $footerPage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('footer_pages', 'slug')->ignore($footerPage->id)
            ],
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $validated['updated_by_user_id'] = Auth::id();
        $validated['last_updated_by'] = now();
        $validated['is_active'] = $request->has('is_active');

        $footerPage->update($validated);

        return redirect()->route('admin.footer-pages.index')
            ->with('success', 'Footer page updated successfully!');
    }

    /**
     * Remove the specified footer page.
     */
    public function destroy(FooterPage $footerPage)
    {
        $footerPage->delete();

        return redirect()->route('admin.footer-pages.index')
            ->with('success', 'Footer page deleted successfully!');
    }

    /**
     * Toggle the status of a footer page.
     */
    public function toggleStatus(FooterPage $footerPage)
    {
        $footerPage->update([
            'is_active' => !$footerPage->is_active,
            'updated_by_user_id' => Auth::id(),
            'last_updated_by' => now()
        ]);

        $status = $footerPage->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Footer page {$status} successfully!",
            'is_active' => $footerPage->is_active
        ]);
    }

    /**
     * Generate slug from title.
     */
    public function generateSlug(Request $request)
    {
        $title = $request->input('title');
        $slug = Str::slug($title);
        
        // Check if slug exists and make it unique
        $originalSlug = $slug;
        $counter = 1;
        
        while (FooterPage::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return response()->json(['slug' => $slug]);
    }

    /**
     * Display social media links management.
     */
    public function socialMedia()
    {
        $links = SocialMediaLink::ordered()->get();
        
        $stats = [
            'total' => SocialMediaLink::count(),
            'active' => SocialMediaLink::where('is_active', true)->count(),
            'inactive' => SocialMediaLink::where('is_active', false)->count(),
        ];

        return view('admin.footer-pages.social-media', compact('links', 'stats'));
    }

    /**
     * Store a new social media link.
     */
    public function storeSocialMedia(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'icon_class' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Set default sort order if not provided
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = SocialMediaLink::max('sort_order') + 1;
        }

        SocialMediaLink::create($validated);

        return redirect()->route('admin.footer-pages.social-media')
            ->with('success', 'Social media link added successfully!');
    }

    /**
     * Update a social media link.
     */
    public function updateSocialMedia(Request $request, SocialMediaLink $socialMediaLink)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'icon_class' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $socialMediaLink->update($validated);

        return redirect()->route('admin.footer-pages.social-media')
            ->with('success', 'Social media link updated successfully!');
    }

    /**
     * Delete a social media link.
     */
    public function destroySocialMedia(SocialMediaLink $socialMediaLink)
    {
        $socialMediaLink->delete();

        return redirect()->route('admin.footer-pages.social-media')
            ->with('success', 'Social media link deleted successfully!');
    }

    /**
     * Toggle social media link status.
     */
    public function toggleSocialMediaStatus(SocialMediaLink $socialMediaLink)
    {
        $socialMediaLink->update([
            'is_active' => !$socialMediaLink->is_active
        ]);

        $status = $socialMediaLink->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Social media link {$status} successfully!",
            'is_active' => $socialMediaLink->is_active
        ]);
    }

    /**
     * Update social media links order.
     */
    public function updateSocialMediaOrder(Request $request)
    {
        $validated = $request->validate([
            'links' => 'required|array',
            'links.*.id' => 'required|exists:social_media_links,id',
            'links.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['links'] as $linkData) {
            SocialMediaLink::where('id', $linkData['id'])
                ->update(['sort_order' => $linkData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Social media links order updated successfully!'
        ]);
    }
}