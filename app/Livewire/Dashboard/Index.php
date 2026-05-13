<?php

namespace App\Livewire\Dashboard;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.dashboard.index', [
            'productCount' => Product::active()->count(),
            'lowStockCount' => Product::lowStock()->active()->count(),
            'customerCount' => Customer::where('is_active', true)->count(),
            'todaySales' => Sale::completed()->whereDate('completed_at', today())->sum('total'),
        ]);
    }
}
