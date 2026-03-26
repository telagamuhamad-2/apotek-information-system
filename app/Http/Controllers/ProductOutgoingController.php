<?php

namespace App\Http\Controllers;

use App\Services\ProductOutgoingService;
use App\Services\ProductTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductOutgoingController extends Controller
{
    protected ProductOutgoingService $productOutgoingService;
    protected ProductTypeService $productTypeService;

    public function __construct(
        ProductOutgoingService $productOutgoingService,
        ProductTypeService $productTypeService
    ) {
        $this->productOutgoingService = $productOutgoingService;
        $this->productTypeService = $productTypeService;
    }

    /**
     * Display a listing of product outgoing (sales).
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'product_code',
            'product_name',
            'product_type_id',
            'product_purpose',
            'customer_name',
            'date_from',
            'date_to',
            'min_price',
            'max_price',
            'search'
        ]);

        $perPage = $request->get('per_page', 10);

        $productOutgoings = $this->productOutgoingService->getPaginated($perPage, $filters);
        $productTypes = $this->productTypeService->getAll();

        return view('penjualan.index', compact('productOutgoings', 'productTypes', 'filters'));
    }

    /**
     * Show the form for creating a new product outgoing (sale).
     */
    public function create(): View
    {
        $productTypes = $this->productTypeService->getAll();
        $availableProducts = $this->productOutgoingService->getAvailableProducts();

        return view('penjualan.create', compact('productTypes', 'availableProducts'));
    }

    /**
     * Get product details by code (AJAX)
     */
    public function getProductDetails(Request $request)
    {
        $code = $request->get('code');
        $product = $this->productOutgoingService->getProductByCode($code);

        if ($product) {
            return response()->json([
                'success' => true,
                'product' => [
                    'code' => $product->product_code,
                    'name' => $product->product_name,
                    'type_id' => $product->product_type_id,
                    'purpose' => $product->product_purpose,
                    'price' => $product->product_price,
                    'stock' => $product->product_quantity,
                    'type_name' => $product->productType ? $product->productType->product_type_name : '',
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Obat tidak ditemukan'
        ]);
    }

    /**
     * Check if stock is sufficient (AJAX)
     */
    public function checkStock(Request $request)
    {
        $code = $request->get('code');
        $quantity = $request->get('quantity', 0);

        $product = $this->productOutgoingService->getProductByCode($code);

        if ($product) {
            $sufficient = $product->product_quantity >= $quantity;

            return response()->json([
                'success' => true,
                'sufficient' => $sufficient,
                'available_stock' => $product->product_quantity,
                'message' => $sufficient
                    ? 'Stok mencukupi'
                    : "Stok tidak mencukupi! Stok tersedia: {$product->product_quantity}"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Obat tidak ditemukan'
        ]);
    }

    /**
     * Store a newly created product outgoing (sale).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50',
            'product_quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
        ]);

        try {
            $this->productOutgoingService->create($validated);

            return redirect()
                ->route('penjualan.index')
                ->with('success', 'Penjualan berhasil ditambahkan dan stok diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product outgoing.
     */
    public function edit(int $id): View
    {
        try {
            $productOutgoing = $this->productOutgoingService->findById($id);
            $productTypes = $this->productTypeService->getAll();

            return view('penjualan.edit', compact('productOutgoing', 'productTypes'));
        } catch (\Exception $e) {
            return redirect()
                ->route('penjualan.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified product outgoing.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50',
            'product_quantity' => 'required|integer|min:1',
            'product_each_price' => 'required|numeric|min:0',
            'customer_name' => 'required|string|max:255',
        ]);

        // Calculate total price
        $validated['product_total_price'] = $validated['product_quantity'] * $validated['product_each_price'];

        try {
            $this->productOutgoingService->update($id, $validated);

            return redirect()
                ->route('penjualan.index')
                ->with('success', 'Data penjualan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product outgoing.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->productOutgoingService->delete($id);

            return redirect()
                ->route('penjualan.index')
                ->with('success', 'Data penjualan berhasil dihapus dan stok dikembalikan!');
        } catch (\Exception $e) {
            return redirect()
                ->route('penjualan.index')
                ->with('error', 'Gagal menghapus data penjualan: ' . $e->getMessage());
        }
    }
}
