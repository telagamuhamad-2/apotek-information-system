<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get products with filters applied
     */
    public function collection()
    {
        $query = Product::with('productType');

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($this->filters['product_type_id'])) {
            $query->where('product_type_id', $this->filters['product_type_id']);
        }

        if (!empty($this->filters['expiration_from'])) {
            $query->where('product_expiration_date', '>=', $this->filters['expiration_from']);
        }

        if (!empty($this->filters['expiration_to'])) {
            $query->where('product_expiration_date', '<=', $this->filters['expiration_to']);
        }

        if (isset($this->filters['min_quantity'])) {
            $query->where('product_quantity', '>=', $this->filters['min_quantity']);
        }

        if (isset($this->filters['max_quantity'])) {
            $query->where('product_quantity', '<=', $this->filters['max_quantity']);
        }

        if (isset($this->filters['min_price'])) {
            $query->where('product_price', '>=', $this->filters['min_price']);
        }

        if (isset($this->filters['max_price'])) {
            $query->where('product_price', '<=', $this->filters['max_price']);
        }

        if (isset($this->filters['low_stock']) && $this->filters['low_stock'] === '1') {
            $query->where('product_quantity', '<', 10);
        }

        if (isset($this->filters['expired']) && $this->filters['expired'] === '1') {
            $query->where('product_expiration_date', '<', now()->format('Y-m-d'));
        }

        return $query->orderBy('product_code')->get();
    }

    /**
     * Map each product to array
     */
    public function map($product): array
    {
        return [
            $product->product_code ?? '-',
            $product->product_name,
            $product->productType->product_type_name ?? '-',
            $product->product_purpose ?? '-',
            $product->product_quantity,
            $product->purchase_price ?? $product->product_price ?? 0,
            $product->selling_price ?? $product->product_price ?? 0,
            $product->product_expiration_date ?? '-',
            $product->vendor_name ?? '-',
            $product->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Set headers
     */
    public function headings(): array
    {
        return [
            'Kode',
            'Nama Obat',
            'Jenis',
            'Kegunaan',
            'Stok',
            'Harga Beli',
            'Harga Jual',
            'Kadaluwarsa',
            'Vendor',
            'Tanggal Input',
        ];
    }

    /**
     * Apply styles to header row
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E8F5E9']],
            ],
        ];
    }
}
