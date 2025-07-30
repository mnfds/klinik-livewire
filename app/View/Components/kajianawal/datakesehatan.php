<?php

namespace App\View\Components\kajianawal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class datakesehatan extends Component
{
    public array $listPenyakit;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Data dummy
        $this->listPenyakit = [
            'florida',
            'anggur',
            'jeruk',
            'apel',
            'TBC',
            'kanker',
            'sumbing',
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.kajianawal.datakesehatan', [
            'listPenyakit' => $this->listPenyakit,
        ]);
    }
}
