<?php

namespace App\Livewire\Forms;

use App\Models\Table;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TableForm extends Form
{
    public ?Table $table;

    public string $mode = '';

    public ?string $id = null;

    #[Validate('required|string')]
    public string $name = '';

    #[Validate('required|boolean')]
    public bool $status = false;

    public function setData($mode, $id): void
    {
        $this->mode = $mode;

        if (! is_null($id)) {
            $this->id = $id;
        }

        if ($mode === 'edit' && $id) {
            $this->table = Table::find(decrypt($id));
            $this->name = $this->table->name;
            $this->status = $this->table->status;
        } elseif ($mode === 'delete' && $id) {
            $this->table = Table::find(decrypt($id));
        } else {
            $this->table = new Table;
            $this->reset(['name',  'status']);
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
        $this->table->create($validated);
    }

    public function update(): void
    {
        $validated = $this->validate();
        $this->table->update($validated);
    }

    public function delete(): void
    {
        $this->table->delete();
        $this->reset();
    }
}
