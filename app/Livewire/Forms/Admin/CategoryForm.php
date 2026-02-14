<?php

namespace App\Livewire\Forms\Admin;

use App\Models\Category;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CategoryForm extends Form
{
    public ?Category $category;

    public string $mode = '';

    public ?string $id = null;

    #[Validate('required|string')]
    public string $name = '';

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required|boolean')]
    public bool $is_active = false;

    public function setData($mode, $id): void
    {
        $this->mode = $mode;

        if (! is_null($id)) {
            $this->id = $id;
        }

        if ($mode === 'edit' && $id) {
            $this->category = Category::find(decrypt($id));
            $this->name = $this->category->name;
            $this->description = $this->category->description;
            $this->is_active = $this->category->is_active;
        } elseif ($mode === 'delete' && $id) {
            $this->category = Category::find(decrypt($id));
        } else {
            $this->category = new Category;
            $this->reset(['name', 'description', 'is_active']);
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
        $this->category->create($validated);
    }

    public function update(): void
    {
        $validated = $this->validate();
        $this->category->update($validated);
    }

    public function delete(): void
    {
        $this->category->delete();
        $this->reset();
    }
}
