<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom Blade directive for country codes
        Blade::directive('countryCodes', function ($expression) {
            $params = explode(',', str_replace(['(', ')', ' ', "'", '"'], '', $expression));
            $name = $params[0] ?? 'country_code';
            $selected = $params[1] ?? 'null';
            $class = $params[2] ?? 'form-select';
            $showCountryName = $params[3] ?? 'true';
            
            return "<?php 
            \$countryCodes = config('countries.country_codes');
            \$selectedValue = {$selected};
            \$showNames = {$showCountryName} === 'true';
            echo '<select name=\"{$name}\" class=\"{$class}\">';
            if (\$showNames) {
                echo '<option value=\"\">Select Code</option>';
            }
            foreach (\$countryCodes as \$code => \$country) {
                \$selected = \$selectedValue == \$code ? 'selected' : '';
                if (\$showNames) {
                    echo '<option value=\"' . \$code . '\" ' . \$selected . '>' . \$code . ' (' . \$country . ')</option>';
                } else {
                    echo '<option value=\"' . \$code . '\" ' . \$selected . '>' . \$code . '</option>';
                }
            }
            echo '</select>';
            ?>";
        });
    }
}
