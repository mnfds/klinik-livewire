<?php

namespace App\Livewire\Kajianawal;

use Livewire\Component;

class Tandavital extends Component
{
    public string $tanda_vital = '';

    public function updatedTandaVital($value)
    {
        $this->dispatch('tandaVitalUpdated', tanda_vital: $value);
    }

    public function render()
    {
        return view('livewire.kajianawal.tandavital');
    }
}
