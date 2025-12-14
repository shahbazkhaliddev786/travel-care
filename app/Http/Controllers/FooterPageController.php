<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FooterPage;

class FooterPageController extends Controller
{
    /**
     * Generic method to display a footer page by slug.
     */
    private function showPage($slug)
    {
        $page = FooterPage::bySlug($slug)->first();

        $targetSlugs = [
            'terms-conditions',
            'privacy-policy',
            'cookies-policy',
            'support',
            'contact-us',
        ];

        if (!$page) {
            if (in_array($slug, $targetSlugs, true)) {
                $fallbackTitle = ucwords(str_replace('-', ' ', $slug));
                return view('footer-pages.dynamic', [
                    'page' => new FooterPage([
                        'slug' => $slug,
                        'title' => $fallbackTitle,
                        'content' => '',
                        'is_active' => false,
                    ]),
                    'isMissing' => true,
                ]);
            }
            abort(404);
        }

        if (!$page->is_active) {
            return view('footer-pages.dynamic', [
                'page' => $page,
                'isDeactivated' => true
            ]);
        }

        return view('footer-pages.dynamic', compact('page'));
    }

    /**
     * Display a dynamic footer page by slug.
     */
    public function showDynamicPage($slug)
    {
        return $this->showPage($slug);
    }

    /**
     * Handle contact form submission.
     */
    public function submitContactForm(Request $request)
    {
        // Validate the contact form data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Here you would typically:
        // 1. Save the contact form submission to database
        // 2. Send an email notification to admin
        // 3. Send a confirmation email to the user
        
        // For now, we'll just return a success response
        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}