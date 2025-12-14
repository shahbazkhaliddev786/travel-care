<footer class="footer">
    <div class="footer-content">
        <div class="social-links">
            @php
                $socialLinks = \App\Models\SocialMediaLink::active()->ordered()->get();
            @endphp
            @forelse($socialLinks as $link)
                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" title="{{ $link->formatted_platform }}">
                    <i class="{{ $link->icon }}"></i>
                </a>
            @empty
                {{-- Fallback to hardcoded links if no social media links are configured --}}
                <a href="https://www.linkedin.com/company/travelcare" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin"></i></a>
                <a href="https://www.facebook.com/travelcare" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/travelcare" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                <a href="https://www.youtube.com/channel/travelcare" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a>
            @endforelse
        </div>
        <div class="footer-links">
            <a href="{{ url('/terms-conditions') }}">Terms & Conditions</a>
            <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>
            <a href="{{ url('/cookies-policy') }}">Cookies Policy</a>
            <a href="{{ url('/support') }}">Support</a>
            <a href="{{ url('/contact-us') }}">Contact Us</a>
        </div>
        <p class="copyright">Copyright &copy; {{ date('Y') }} TravelCare. All rights reserved</p>
    </div>
</footer>