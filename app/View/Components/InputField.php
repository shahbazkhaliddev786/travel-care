<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputField extends Component
{
    public $name;
    public $type;
    public $icon;
    public $placeholder;
    public $extraClass;
    public $options;
    public $label;
    public $hasSelect;
    public $pattern;
    public $id;
    public $useCountryCodes;

    public function __construct(
        $name, 
        $type = 'text', 
        $icon = null, 
        $placeholder = '', 
        $extraClass = '', 
        $options = [], 
        $label = null, 
        $hasSelect = false, 
        $pattern = null,
        $id = null,
        $useCountryCodes = false
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->icon = $icon;
        $this->placeholder = $placeholder;
        $this->extraClass = $extraClass;
        
        // If useCountryCodes is true, load all country codes
        if ($useCountryCodes) {
            $this->options = array_keys(config('countries.country_codes'));
            $this->hasSelect = true;
        } else {
            $this->options = $options;
            $this->hasSelect = $hasSelect;
        }
        
        $this->label = $label;
        $this->pattern = $pattern;
        $this->id = $id;
    }

    public function render()
    {
        return view('components.input-field');
    }
}