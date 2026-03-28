<?php

namespace App\Http\Controllers;

use App\Exports\ProductIncomingExport;
use App\Services\ProductIncomingService;
use App\Services\ProductTypeService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductIncomingController extends Controller
{
    protected ProductIncomingService $productIncomingService;
    protected ProductTypeService $productTypeService;
    protected ProductService $productService;

    public function __construct(
        ProductIncomingService $productIncomingService,
        ProductTypeService $productTypeService,
        ProductService $productService
    ) {
        $this->productIncomingService = $productIncomingService;
        $this->productTypeService = $productTypeService;
        $this->productService = $productService;
    }

    /**
     * Display a listing of product incoming (purchases).
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'product_code',
            'product_name',
            'product_type_id',
            'product_purpose',
            'vendor_name',
            'date_from',
            'date_to',
            'min_price',
            'max_price',
            'search'
        ]);

        $perPage = $request->get('per_page', 10);

        $productIncomings = $this->productIncomingService->getPaginated($perPage, $filters);
        $productTypes = $this->productTypeService->getAll();

        return view('pembelian.index', compact('productIncomings', 'productTypes', 'filters'));
    }

    /**
     * Show the form for creating a new product incoming.
     */
    public function create(Request $request): View
    {
        $productTypes = $this->productTypeService->getAll();
        $products = $this->productService->getAll(['search' => $request->get('search')]);

        return view('pembelian.create', compact('productTypes', 'products'));
    }

    /**
     * Store a newly created product incoming.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_code' => 'nullable|string|max:50',
            'product_name' => 'required|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'product_purpose' => 'nullable|string|max:255',
            'product_quantity' => 'required|integer|min:1',
            'product_each_price' => 'required|numeric|min:0',
            'vendor_name' => 'required|string|max:255',
            'product_expiration_date' => 'nullable|date',
        ]);

        // Calculate total price
        $validated['product_total_price'] = $validated['product_quantity'] * $validated['product_each_price'];

        try {
            $this->productIncomingService->create($validated);

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil ditambahkan dan stok diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product incoming.
     */
    public function edit(int $id): View
    {
        try {
            $productIncoming = $this->productIncomingService->findById($id);
            $productTypes = $this->productTypeService->getAll();

            return view('pembelian.edit', compact('productIncoming', 'productTypes'));
        } catch (\Exception $e) {
            return redirect()
                ->route('pembelian.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified product incoming.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'product_code' => 'nullable|string|max:50',
            'product_name' => 'required|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'product_purpose' => 'nullable|string|max:255',
            'product_quantity' => 'required|integer|min:1',
            'product_each_price' => 'required|numeric|min:0',
            'vendor_name' => 'required|string|max:255',
            'product_expiration_date' => 'nullable|date',
        ]);

        // Calculate total price
        $validated['product_total_price'] = $validated['product_quantity'] * $validated['product_each_price'];

        try {
            $this->productIncomingService->update($id, $validated);

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product incoming.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->productIncomingService->delete($id);

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Data pembelian berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()
                ->route('pembelian.index')
                ->with('error', 'Gagal menghapus data pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Export product incomings to Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only([
            'search',
            'product_type_id',
            'date_from',
            'date_to',
            'min_price',
            'max_price',
            'vendor_name',
        ]);

        $fileName = 'pembelian-obat-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new ProductIncomingExport($filters), $fileName);
    }
}
