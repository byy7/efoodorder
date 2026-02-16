<?php

use App\Concerns\WithNotifications;
use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-customer')]
class extends Component {
    use WithPagination;
    use WithNotifications;

    public string $search = '';
    public string $type = '';
    public string $customerId = '';

    #[On('alert-notification')]
    public function alert(string $type, string $message): void
    {
        switch ($type) {
            case 'success':
                $this->notifySuccess($message);
                break;
            case 'warning':
                $this->notifyWarning($message);
                break;
            case 'error':
                $this->notifyError($message);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        $query = Category::with('products');

        /* Search Filter */
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%$this->search%")
                    ->orWhereRelation('products', 'name');
            });
        }

        return $query->latest()->paginate(10);
    }

    public function mount(string $type, string $customerId)
    {
        $this->type = $type;
        $this->customerId = decrypt($customerId);
    }
};
?>

<div>

</div>
