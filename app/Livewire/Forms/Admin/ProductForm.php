<?php

namespace App\Livewire\Forms\Admin;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product;

    public string $mode = '';

    public ?string $id = null;

    #[Validate('required|string')]
    public ?string $category_id = null;

    #[Validate('required|string')]
    public string $name = '';

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required')]
    public ?string $price = null;

    #[Validate('required')]
    public ?string $capital_price = null;

    #[Validate('nullable|image|max:2048')]
    public $image = null;

    #[Validate('required|boolean')]
    public bool $is_available = false;

    #[Validate('required')]
    public ?int $stock = null;

    public function setData($mode, $id): void
    {
        $this->mode = $mode;

        if (! is_null($id)) {
            $this->id = $id;
        }

        if ($mode === 'edit' && $id) {
            $this->product = Product::find(decrypt($id));
            $this->category_id = Category::find($this->product->category_id)->id ?? null;
            ! is_null($this->category_id) ? $this->category_id = encrypt($this->category_id) : $this->category_id = null;
            $this->name = $this->product->name;
            $this->description = $this->product->description;
            $this->is_available = $this->product->is_available;
            $this->stock = $this->product->stock;
            $this->price = intval($this->product->price);
            $this->capital_price = intval($this->product->capital_price);
        } elseif ($mode === 'delete' && $id) {
            $this->product = Product::find(decrypt($id));
        } else {
            $this->product = new Product;
            $this->reset(['category_id', 'name', 'description', 'is_available', 'image', 'stock', 'price', 'capital_price']);
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
        $validated['price'] = floatval(str_replace(',', '', $validated['price']));
        $validated['capital_price'] = floatval(str_replace(',', '', $validated['capital_price']));

        if (! is_null($this->image)) {
            $validated['image'] = $this->storeImage();
        }

        $this->product->create($validated);
    }

    public function update(): void
    {
        $validated = $this->validate();
        $validated['category_id'] = decrypt($this->category_id);
        $validated['price'] = floatval(str_replace(',', '', $validated['price']));
        $validated['capital_price'] = floatval(str_replace(',', '', $validated['capital_price']));

        if (! is_null($validated['image'])) {
            if (! is_null($this->product->image)) {
                $this->removeExistingImage($this->product->image);
            }
            $validated['image'] = $this->storeImage();
        } else {
            unset($validated['image']);
        }

        $this->product->update($validated);
    }

    public function delete(): void
    {
        $this->removeExistingImage($this->product->image);
        $this->product->delete();
        $this->reset();
    }

    private function storeImage()
    {
        $fileName = 'products-'.time().'.'.$this->image->extension();

        return $this->image->storeAs(path: 'products', name: $fileName);
    }

    private function removeExistingImage($path): void
    {
        $imagePath = storage_path($path);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
