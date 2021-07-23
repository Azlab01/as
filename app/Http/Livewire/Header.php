<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Header extends Component
{
    protected $listeners = ['loadUrl'];
    
    public function render()
    {
        return view('livewire.header');
    }
    
    public function loadUrl(string $viewPath = "")
    {
        return redirect()->to("/$viewPath");
    }
}
