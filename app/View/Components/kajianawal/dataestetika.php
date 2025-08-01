<?php

namespace App\View\Components\kajianawal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class dataestetika extends Component
{
    public ?string $sedang_hamil = null;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.kajianawal.dataestetika',[
            'sedang_hamil' => $this->sedang_hamil,
        ]);
    }
}
