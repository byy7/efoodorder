<?php

use App\Concerns\WithNotifications;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Table;
use App\Services\OrderService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-customer')]
class extends Component {
    use WithPagination;
    use WithNotifications;

    public ?Customer $customer = null;
    public ?Table $table = null;
    public string $search = '';
    public string $type = '';
    public string $customerId = '';
    public array $cart = [];
    public bool $showCart = false;
    public ?int $selectedCategoryId = null;
    public string $orderNotes = '';
    public string $paymentMethod = 'cash'; // 'cash' | 'cashless'

    public function mount(string $type, string $customerId, ?int $tableId = null): void
    {
        $this->type = $type;
        $this->customerId = decrypt($customerId);
        $this->customer = Customer::findOrFail($this->customerId);

        if ($tableId) {
            $this->table = Table::findOrFail($tableId);
        }

        // Load cart from session, back-fill missing notes key
        $this->cart = collect(session()->get("cart.{$this->customerId}", []))
            ->map(fn($item) => array_merge(['notes' => ''], $item))
            ->all();
    }

    #[On('alert-notification')]
    public function alert(string $type, string $message): void
    {
        match ($type) {
            'success' => $this->notifySuccess($message),
            'warning' => $this->notifyWarning($message),
            'error' => $this->notifyError($message),
            default => null
        };
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategoryId(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::where('is_active', true)
            ->withCount(['products' => function ($q) {
                $q->where('is_available', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function products()
    {
        $query = Product::with('category')
            ->where('is_available', true);

        // Category Filter
        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        // Search Filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        return $query->orderBy('name')->paginate(12);
    }

    #[Computed]
    public function groupedProducts()
    {
        return $this->products->groupBy('category.name');
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->selectedCategoryId = $categoryId;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->selectedCategoryId = null;
        $this->search = '';
        $this->resetPage();
    }

    #[Computed]
    public function cartTotal(): float
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    #[Computed]
    public function cartCount(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);

        if (!$product->is_available) {
            $this->notifyError('Produk tidak tersedia');
            return;
        }

        $cartKey = "product_{$productId}";

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity']++;
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'capital_price' => $product->capital_price,
                'image' => $product->image,
                'quantity' => 1,
                'notes' => '',
            ];
        }

        $this->saveCart();
        $this->notifySuccess("'{$product->name}' ditambahkan ke keranjang");
    }

    public function updateItemNotes(string $cartKey, string $notes): void
    {
        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['notes'] = $notes;
            $this->saveCart();
        }
    }

    public function updateQuantity(string $cartKey, int $quantity): void
    {
        if ($quantity < 1) {
            $this->removeFromCart($cartKey);
            return;
        }

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] = $quantity;
            $this->saveCart();
        }
    }

    public function incrementQuantity(string $cartKey): void
    {
        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity']++;
            $this->saveCart();
        }
    }

    public function decrementQuantity(string $cartKey): void
    {
        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['quantity'] > 1) {
                $this->cart[$cartKey]['quantity']--;
                $this->saveCart();
            } else {
                $this->removeFromCart($cartKey);
            }
        }
    }

    public function removeFromCart(string $cartKey): void
    {
        if (isset($this->cart[$cartKey])) {
            $productName = $this->cart[$cartKey]['name'];
            unset($this->cart[$cartKey]);
            $this->saveCart();
            $this->notifyWarning("'{$productName}' dihapus dari keranjang");
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->saveCart();
        $this->notifyWarning('Keranjang dikosongkan');
    }

    public function toggleCart(): void
    {
        $this->showCart = !$this->showCart;
    }

    public function checkout(): void
    {
        if (empty($this->cart)) {
            $this->notifyError('Keranjang kosong');
            return;
        }

        try {
            if(!is_null($this->table)){
                $this->table->update(['status' => false]);
            }

            $orderService = app(OrderService::class);

            $order = $orderService->createOrder(
                customer: $this->customer,
                cart: $this->cart,
                type: $this->type ?: 'dine_in',
                paymentMethod: $this->paymentMethod,
                notes: $this->orderNotes,
                tableId: $this->table->id ?? null
            );

            $this->clearCart();
            $this->orderNotes = '';
            $this->showCart = false;

            // Cashless → redirect to Xendit payment page
            if ($this->paymentMethod === 'cashless') {
                $this->redirect($order->payment->xendit_payment_url);
                return;
            } else {
                // Cash → go to payment confirmation page
                $this->redirectRoute('cash.payment', encrypt($order->id));
            }
        } catch (\Throwable $e) {
            $this->notifyError('Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    private function saveCart(): void
    {
        session()->put("cart.{$this->customerId}", $this->cart);
    }
};
?>

<div>
    <x-header :heading="__('MENU NEED & NOTES CAFFE')"></x-header>

    {{-- Customer Info and Cart Toggle --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
        <div class="bank-details">
            <h5 class="mb-0">
                {{ $this->customer->name }}
                @if($this->customer->email)
                    <span class="text-white">| {{ $this->customer->email }}</span>
                @endif
                | {{ str_replace('_', ' ', ucfirst($this->type)) }}
                @if(!is_null($this->table))
                    - {{ $this->table->name }}
                @endif
            </h5>
            @if($this->customer->phone_number)
                <p class="mb-0 text-white">{{ $this->customer->phone_number }}</p>
            @endif
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-primary">DAFTAR MENU</span>
            <button
                wire:click="toggleCart"
                class="btn btn-outline-primary position-relative"
                type="button">
                <i class="material-icons-outlined">shopping_cart</i>
                @if($this->cartCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $this->cartCount }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    <div class="separator mb-3">
        <div class="line"></div>
    </div>

    {{-- Search Bar --}}
    <div class="mb-3">
        <div class="input-group">
            <span class="input-group-text bg-transparent">
                <i class="material-icons-outlined">search</i>
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control"
                placeholder="Cari menu...">
            @if($search || $selectedCategoryId)
                <button
                    wire:click="clearFilters"
                    class="btn btn-outline-secondary"
                    type="button">
                    <i class="material-icons-outlined">clear</i>
                </button>
            @endif
        </div>
    </div>

    {{-- Category Filter Tabs --}}
    <div class="mb-3">
        <div class="d-flex align-items-center mb-2">
            <span class="text-white small me-2 flex-shrink-0">Filter:</span>
            <div class="category-filter-scroll flex-grow-1">
                <div class="d-flex gap-2 flex-nowrap">
                    <button
                        wire:click="selectCategory(null)"
                        class="btn {{ is_null($selectedCategoryId) ? 'btn-primary' : 'btn-outline-primary' }} btn-sm flex-shrink-0"
                        type="button">
                        <i class="material-icons-outlined" style="font-size: 16px;">apps</i>
                        Semua Menu
                        @if(is_null($selectedCategoryId))
                            <span class="badge bg-white text-primary ms-1">{{ $this->products->total() }}</span>
                        @endif
                    </button>

                    @foreach($this->categories as $category)
                        <button
                            wire:click="selectCategory({{ $category->id }})"
                            class="btn {{ $selectedCategoryId === $category->id ? 'btn-primary' : 'btn-outline-primary' }} btn-sm flex-shrink-0"
                            type="button">
                            {{ $category->name }}
                            @if($selectedCategoryId === $category->id)
                                <span class="badge bg-white text-primary ms-1">{{ $this->products->total() }}</span>
                            @else
                                <span class="badge bg-secondary ms-1">{{ $category->products_count }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Active Filters Display --}}
        @if($search || $selectedCategoryId)
            <div class="mt-2">
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <span class="text-white small flex-shrink-0">Filter aktif:</span>
                    @if($selectedCategoryId)
                        <span class="badge bg-primary d-inline-flex align-items-center gap-1">
                            {{ $this->categories->firstWhere('id', $selectedCategoryId)?->name }}
                            <i class="material-icons-outlined" style="font-size: 14px; cursor: pointer;"
                               wire:click="selectCategory(null)">close</i>
                        </span>
                    @endif
                    @if($search)
                        <span class="badge bg-info d-inline-flex align-items-center gap-1">
                            Pencarian: "{{ Str::limit($search, 20) }}"
                            <i class="material-icons-outlined" style="font-size: 14px; cursor: pointer;"
                               wire:click="$set('search', '')">close</i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Cart Sidebar --}}
    @if($showCart)
        <div class="cart-sidebar position-fixed top-0 end-0 h-100 bg-white shadow-lg"
             style="z-index: 1050; overflow-y: auto;">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center sticky-top bg-white">
                <h5 class="mb-0 text-dark fw-bold">Keranjang Belanja</h5>
                <button wire:click="toggleCart" class="btn-close"></button>
            </div>

            <div class="p-3">
                @if(empty($cart))
                    <div class="text-center py-5">
                        <i class="material-icons-outlined" style="font-size: 64px; color: #ccc;">shopping_cart</i>
                        <p class="text-white mt-2">Keranjang kosong</p>
                    </div>
                @else
                    {{-- Cart Items --}}
                    @foreach($cart as $key => $item)
                        <div class="card mb-2" wire:key="cart-{{ $key }}">
                            <div class="card-body p-2">
                                <div class="d-flex gap-2">
                                    <div class="flex-shrink-0" style="width: 60px; height: 60px;">
                                        @if($item['image'])
                                            <img src="{{ Storage::url($item['image']) }}"
                                                 class="w-100 h-100 object-fit-cover rounded"
                                                 alt="{{ $item['name'] }}">
                                        @else
                                            <img src="{{ asset('assets/img/no-image.webp') }}"
                                                 class="w-100 h-100 object-fit-cover rounded"
                                                 alt="{{ $item['name'] }}">
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 text-truncate"
                                            style="font-size: 0.9rem;">{{ $item['name'] }}</h6>
                                        <p class="mb-1 text-primary fw-bold"
                                           style="font-size: 0.85rem;">@currency($item['price'])</p>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button
                                                    wire:click="decrementQuantity('{{ $key }}')"
                                                    class="btn btn-outline-secondary"
                                                    type="button">
                                                    <i class="material-icons-outlined"
                                                       style="font-size: 14px;">remove</i>
                                                </button>
                                                <button class="btn btn-outline-secondary" disabled type="button">
                                                    <span class="fw-bold px-1">{{ $item['quantity'] }}</span>
                                                </button>
                                                <button
                                                    wire:click="incrementQuantity('{{ $key }}')"
                                                    class="btn btn-outline-secondary"
                                                    type="button">
                                                    <i class="material-icons-outlined" style="font-size: 14px;">add</i>
                                                </button>
                                            </div>
                                            <button
                                                wire:click="removeFromCart('{{ $key }}')"
                                                class="btn btn-sm btn-outline-danger ms-auto"
                                                type="button">
                                                <i class="material-icons-outlined" style="font-size: 14px;">delete</i>
                                            </button>
                                        </div>
                                        <textarea
                                            wire:change="updateItemNotes('{{ $key }}', $event.target.value)"
                                            class="form-control form-control-sm mt-2"
                                            rows="2"
                                            placeholder="Catatan item (contoh: tanpa gula, ekstra pedas...)"
                                            style="font-size: 0.8rem; resize: none;">{{ $item['notes'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Cart Total --}}
                    <div class="card bg-light mt-3 sticky-bottom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span class="fw-bold">@currency($this->cartTotal)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Total Item</span>
                                <span class="fw-bold">{{ $this->cartCount }}</span>
                            </div>

                            {{-- Payment Method --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-1">
                                    <i class="material-icons-outlined align-middle" style="font-size: 16px;">payment</i>
                                    Metode Pembayaran
                                </label>
                                <div class="d-flex gap-2">
                                    <button
                                        wire:click="$set('paymentMethod', 'cash')"
                                        type="button"
                                        class="btn btn-sm flex-fill {{ $paymentMethod === 'cash' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        <i class="material-icons-outlined align-middle" style="font-size: 16px;">payments</i>
                                        Tunai
                                    </button>
                                    <button
                                        wire:click="$set('paymentMethod', 'cashless')"
                                        type="button"
                                        class="btn btn-sm flex-fill {{ $paymentMethod === 'cashless' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        <i class="material-icons-outlined align-middle" style="font-size: 16px;">credit_card</i>
                                        Non-Tunai
                                    </button>
                                </div>
                                @if($paymentMethod === 'cashless')
                                    <p class="text-white mt-1 mb-0" style="font-size: 0.78rem;">
                                        <i class="material-icons-outlined align-middle"
                                           style="font-size: 13px;">info</i>
                                        Anda akan diarahkan ke halaman pembayaran Xendit
                                    </p>
                                @endif
                            </div>

                            {{-- Order Notes --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-1">
                                    <i class="material-icons-outlined align-middle" style="font-size: 16px;">notes</i>
                                    Catatan Pesanan
                                </label>
                                <textarea
                                    wire:model="orderNotes"
                                    class="form-control form-control-sm"
                                    rows="3"
                                    placeholder="Catatan untuk seluruh pesanan (contoh: tidak mau terlalu matang, alergi kacang...)"
                                    style="resize: none;"></textarea>
                            </div>

                            <button
                                wire:click="checkout"
                                wire:loading.attr="disabled"
                                class="btn btn-primary w-100 mb-2"
                                type="button">
                                <span wire:loading.remove wire:target="checkout">
                                    <i class="material-icons-outlined align-middle" style="font-size: 18px;">
                                        {{ $paymentMethod === 'cashless' ? 'credit_card' : 'check_circle' }}
                                    </i>
                                    {{ $paymentMethod === 'cashless' ? 'Bayar Sekarang' : 'Pesan (Bayar Tunai)' }}
                                </span>
                                <span wire:loading wire:target="checkout">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Memproses...
                                </span>
                            </button>
                            <button
                                wire:click="clearCart"
                                class="btn btn-outline-danger w-100"
                                type="button">
                                <i class="material-icons-outlined">delete_sweep</i>
                                Kosongkan Keranjang
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Overlay --}}
        <div
            wire:click="toggleCart"
            class="cart-overlay position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"
            style="z-index: 1040;">
        </div>
    @endif

    {{-- Product Grid --}}
    <div class="row g-3">
        @forelse($this->groupedProducts as $categoryName => $products)
            <div class="col-12">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="material-icons-outlined align-middle" style="font-size: 20px;">restaurant</i>
                    {{ $categoryName }}
                    <span class="badge bg-primary ms-2">{{ $products->count() }} item</span>
                </h6>
            </div>

            @foreach($products as $product)
                <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3" wire:key="product-{{ $product->id }}">
                    <div class="card h-100 shadow-sm hover-shadow-lg transition">
                        <div class="row g-0 h-100">
                            <div class="col-5 col-sm-12">
                                <div class="p-2 h-100" style="min-height: 120px;">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}"
                                             class="w-100 h-100 object-fit-cover rounded"
                                             alt="{{ $product->name }}">
                                    @else
                                        <img src="{{ asset('assets/img/no-image.webp') }}"
                                             class="w-100 h-100 object-fit-cover rounded"
                                             alt="{{ $product->name }}">
                                    @endif
                                </div>
                            </div>
                            <div class="col-7 col-sm-12">
                                <div class="card-body d-flex flex-column h-100 p-2 p-sm-3">
                                    <h6 class="card-title mb-1 mb-sm-2"
                                        style="font-size: 0.9rem;">{{ $product->name }}</h6>
                                    @if($product->description)
                                        <p class="card-text small text-white mb-1 mb-sm-2 d-sm-block"
                                           style="font-size: 0.8rem;">
                                            {{ Str::limit($product->description, 60) }}
                                        </p>
                                    @endif
                                    <div class="mt-auto">
                                        <h6 class="text-primary mb-1 mb-sm-2"
                                            style="font-size: 0.9rem;">@currency($product->price)</h6>
                                        <button
                                            wire:click="addToCart({{ $product->id }})"
                                            class="btn btn-sm btn-primary w-100"
                                            type="button">
                                            <i class="material-icons-outlined d-sm-inline" style="font-size: 16px;">add_shopping_cart</i>
                                            <span class="d-sm-inline">Tambah</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="material-icons-outlined" style="font-size: 64px; color: #ccc;">
                        {{ $search || $selectedCategoryId ? 'search_off' : 'restaurant_menu' }}
                    </i>
                    <p class="text-white mt-2 mb-1">
                        @if($search || $selectedCategoryId)
                            Tidak ada menu yang sesuai dengan filter
                        @else
                            Tidak ada menu tersedia
                        @endif
                    </p>
                    @if($search || $selectedCategoryId)
                        <button wire:click="clearFilters" class="btn btn-sm btn-outline-primary mt-2" type="button">
                            <i class="material-icons-outlined" style="font-size: 16px;">clear_all</i>
                            Hapus Filter
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($this->products->hasPages())
        <div class="mt-4">
            {{ $this->products->links() }}
        </div>
    @endif
</div>

@push('styles')
    <style>
        .object-fit-cover {
            object-fit: cover;
        }

        .transition {
            transition: all 0.3s ease;
        }

        .hover-shadow-lg:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
        }

        .badge .material-icons-outlined {
            vertical-align: middle;
            margin-left: 4px;
        }

        /* Responsive horizontal scroll for category filter */
        .category-filter-scroll {
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }

        .category-filter-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .category-filter-scroll::-webkit-scrollbar-track {
            background: transparent;
            margin: 0 8px;
        }

        .category-filter-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .category-filter-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, 0.3);
        }

        /* Cart sidebar responsive */
        .cart-sidebar {
            width: 400px;
            max-width: 100%;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .category-filter-scroll {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .category-filter-scroll::-webkit-scrollbar {
                display: none;
            }

            /* Full width cart on mobile */
            .cart-sidebar {
                width: 100% !important;
                max-width: 100% !important;
            }

            /* Adjust overlay to prevent scroll issues */
            .cart-overlay {
                position: fixed;
                overflow: hidden;
            }

            /* Prevent body scroll when cart is open */
            body.cart-open {
                overflow: hidden;
            }
        }

        /* Small mobile adjustments */
        @media (max-width: 576px) {
            .category-filter-scroll .btn-sm {
                font-size: 0.8rem;
                padding: 0.35rem 0.65rem;
            }

            .category-filter-scroll .material-icons-outlined {
                font-size: 14px !important;
            }

            .category-filter-scroll .badge {
                font-size: 0.65rem;
                padding: 0.2em 0.4em;
            }
        }

        /* Prevent text overflow in cart items */
        .min-w-0 {
            min-width: 0;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Smooth animations for cart */
        .cart-sidebar,
        .cart-overlay {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
@endpush
