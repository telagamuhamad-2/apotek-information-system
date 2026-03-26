<?php

namespace App\Http\Controllers;

use App\Services\ProductTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductTypeController extends Controller
{
    protected ProductTypeService $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    /**
     * Display a listing of product types.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['product_type_name', 'search']);
        $perPage = $request->get('per_page', 10);

        $productTypes = $this->productTypeService->getPaginated($perPage, $filters);

        return view('product-types.index', compact('productTypes', 'filters'));
    }

    /**
     * Show the form for creating a new product type.
     */
    public function create(): View
    {
        return view('product-types.create');
    }

    /**
     * Store a newly created product type.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_type_name' => 'required|string|max:255|unique:product_types,product_type_name',
        ]);

        try {
            $this->productTypeService->create($validated);

            return redirect()
                ->route('product-types.index')
                ->with('success', 'Jenis obat berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan jenis obat: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product type.
     */
    public function edit(int $id): View
    {
        try {
            $productType = $this->productTypeService->findById($id);

            return view('product-types.edit', compact('productType'));
        } catch (\Exception $e) {
            return redirect()
                ->route('product-types.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified product type.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'product_type_name' => 'required|string|max:255|unique:product_types,product_type_name,' . $id,
        ]);

        try {
            $this->productTypeService->update($id, $validated);

            return redirect()
                ->route('product-types.index')
                ->with('success', 'Jenis obat berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui jenis obat: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product type.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->productTypeService->delete($id);

            return redirect()
                ->route('product-types.index')
                ->with('success', 'Jenis obat berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()
                ->route('product-types.index')
                ->with('error', 'Gagal menghapus jenis obat: ' . $e->getMessage());
        }
    }
}
