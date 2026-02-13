<?php

namespace App\Livewire\Forms;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Livewire\Form;

class UserForm extends Form
{
    use PasswordValidationRules;

    public ?User $user;

    public string $mode = '';

    public ?string $id = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function setData($mode, $id): void
    {
        $this->mode = $mode;

        if (! is_null($id)) {
            $this->id = $id;
        }

        if ($mode === 'edit' && $id) {
            $this->user = User::find(decrypt($id));
            $this->name = $this->user->name;
            $this->email = $this->user->email;
        } elseif ($mode === 'delete' && $id) {
            $this->user = User::find(decrypt($id));
        } else {
            $this->user = new User;
            $this->reset(['name', 'email', 'password', 'password_confirmation']);
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
        $validated = $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => $this->passwordRules(),
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $this->user->create($validated);
    }

    public function update(): void
    {
        $validated = $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.decrypt($this->id),
        ]);

        $this->user->update($validated);
    }

    public function delete(): void
    {
        $this->user->delete();
        $this->reset();
    }
}
