<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.admin-layout', ['title' => 'Panel de Administración'])]
class AdminPanel extends Component
{
    public $activeTab = 'usuarios'; // pestaña activa por defecto

    public function render()
    {
        return view('livewire.admin-panel');
    }
}
