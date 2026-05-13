<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

/** Form Object for create/edit Product — validation + data shaping only. Persistence lives in SaveProductAction. */
class ProductForm extends Form
{
    public ?int $id = null;

    #[Validate('required|exists:categories,id')]
    public ?int $category_id = null;

    #[Validate('nullable|exists:suppliers,id')]
    public ?int $supplier_id = null;

    #[Validate('required|string|max:64')]
    public string $sku = '';

    #[Validate('nullable|string|max:64')]
    public ?string $barcode = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:1000')]
    public ?string $description = null;

    #[Validate('required|numeric|min:0')]
    public float $unit_price = 0.0;

    #[Validate('required|numeric|min:0')]
    public float $cost_price = 0.0;

    #[Validate('required|integer|min:0')]
    public int $current_stock = 0;

    #[Validate('required|integer|min:0')]
    public int $reorder_level = 0;

    public bool $is_active = true;

    public function setProduct(?Product $product): void
    {
        if (! $product) {
            return;
        }

        $this->fill([
            'id' => $product->id,
            'category_id' => $product->category_id,
            'supplier_id' => $product->supplier_id,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'name' => $product->name,
            'description' => $product->description,
            'unit_price' => (float) $product->unit_price,
            'cost_price' => (float) $product->cost_price,
            'current_stock' => $product->current_stock,
            'reorder_level' => $product->reorder_level,
            'is_active' => (bool) $product->is_active,
        ]);
    }

    public function rules(): array
    {
        return [
            'sku' => [
                'required', 'string', 'max:64',
                Rule::unique('products', 'sku')->ignore($this->id)->whereNull('deleted_at'),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function attributes(): array
    {
        $this->validate();

        return $this->except('id');
    }
}
