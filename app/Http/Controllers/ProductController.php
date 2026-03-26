<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\ProductTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected ProductTypeService $productTypeService;

    public function __construct(ProductService $productService, ProductTypeService $productTypeService)
    {
        $this->productService = $productService;
        $this->productTypeService = $productTypeService;
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'product_code',
            'product_name',
            'product_type_id',
            'product_purpose',
            'expiration_from',
            'expiration_to',
            'min_quantity',
            'max_quantity',
            'min_price',
            'max_price',
            'low_stock',
            'expired',
            'search'
        ]);

        $perPage = $request->get('per_page', 10);

        $products = $this->productService->getPaginated($perPage, $filters);
        $productTypes = $this->productTypeService->getAll();

        return view('products.index', compact('products', 'productTypes', 'filters'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $productTypes = $this->productTypeService->getAll();

        return view('products.create', compact('productTypes'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_code' => 'nullable|string|max:50|unique:products,product_code',
            'product_name' => 'required|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'product_purpose' => 'nullable|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'product_expiration_date' => 'nullable|date',
        ]);

        try {
            $this->productService->create($validated);

            return redirect()
                ->route('products.index')
                ->with('success', 'Obat berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan obat: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(int $id): View
    {
        try {
            $product = $this->productService->findById($id);
            $productTypes = $this->productTypeService->getAll();

            return view('products.edit', compact('product', 'productTypes'));
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'product_code' => 'nullable|string|max:50|unique:products,product_code,' . $id,
            'product_name' => 'required|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'product_purpose' => 'nullable|string|max:255',
            'selling_price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'product_expiration_date' => 'nullable|date',
        ]);

        try {
            $this->productService->update($id, $validated);

            return redirect()
                ->route('products.index')
                ->with('success', 'Obat berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui obat: ' . $e->getMessage());
        }
    }

    /**
     * Update selling price only
     */
    public function updateSellingPrice(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'selling_price' => 'required|numeric|min:0',
        ]);

        try {
            $this->productService->updateSellingPrice($id, $validated['selling_price']);

            return redirect()
                ->back()
                ->with('success', 'Harga jual berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui harga jual: ' . $e->getMessage());
        }
    }

    /**
     * Update purchase price only
     */
    public function updatePurchasePrice(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_price' => 'required|numeric|min:0',
        ]);

        try {
            $this->productService->updatePurchasePrice($id, $validated['purchase_price']);

            return redirect()
                ->back()
                ->with('success', 'Harga beli berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui harga beli: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->productService->delete($id);

            return redirect()
                ->route('products.index')
                ->with('success', 'Obat berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Gagal menghapus obat: ' . $e->getMessage());
        }
    }
}
