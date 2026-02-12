<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public string $mode = '';

    public ?string $id = null;

    #[Validate('required|string')]
    public ?string $category_id = null;

    #[Validate('required|string')]
    public string $name = '';

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required')]
    public float $price = 0;

    #[Validate('nullable|image|max:2048')]
    public $image = null;

    #[Validate('required|boolean')]
    public bool $is_available = false;

    #[Validate('required')]
    public int $stock = 0;

    public function setData($mode, $id): void
    {
        $this->mode = $mode;

        if (! is_null($id)) {
            $this->id = $id;
        }

        if ($mode === 'edit' && $id) {
            $product = Product::find(decrypt($id));
            $this->category_id = Category::find($product->category_id)->id ?? null;
            $this->name = $product->name;
            $this->description = $product->description;
            $this->is_available = $product->is_available;
            $this->stock = $product->stock;
            $this->price = $product->price;
        } else {
            $this->reset(['category_id', 'name', 'description', 'is_available', 'image', 'stock', 'price']);
        }
    }

    public function save(): void
    {
        if ($this->mode === 'create') {
            $this->store();
        } elseif ($this->mode === 'edit') {
            $this->update();
        }
    }

    public function store(): void
    {
        $validated = $this->validate();
        $validated['category_id'] = decrypt($this->category_id);

        if (! is_null($this->image)) {
            $validated['image'] = $this->storeImage();
        }

        dd($validated);

        Category::create($validated);
    }

    public function update(): void
    {
        $validated = $this->validate();
        if (! is_null($this->image)) {
            $validated['image'] = $this->storeImage();
        }

        $category = Category::find(decrypt($this->id));
        $category->update($validated);
    }

    public function delete(): void
    {
        Category::destroy(decrypt($this->id));
        $this->reset();
    }

    private function storeImage()
    {
        $fileName = 'products-'.time();

        return $this->image->storeAs(path: 'products', name: $fileName);
    }
}
