<?php

namespace App\Livewire\Products;

use App\Actions\Products\SaveProductAction;
use App\Livewire\Forms\ProductForm;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Edit Product')]
class Edit extends Component
{
    public ProductForm $form;

    public ?Product $product = null;

    public function mount(?Product $product = null): void
    {
        abort_unless(auth()->user()?->can('products.manage'), 403);

        if ($product?->exists) {
            $this->product = $product;
            $this->form->setProduct($product);
        }
    }

    public function save(SaveProductAction $save): mixed
    {
        $save->execute($this->form->attributes(), $this->product?->id);

        session()->flash('status', $this->product
            ? __('Product updated.')
            : __('Product created.'));

        return redirect()->route('products.index');
    }

    public function render(): View
    {
        return view('livewire.products.edit', [
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }
}
