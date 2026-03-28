<?php

namespace App\Exports;

use App\Models\ProductIncoming;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductIncomingExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get product incomings with filters applied
     */
    public function collection()
    {
        $query = ProductIncoming::query();

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%")
                  ->orWhere('vendor_name', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($this->filters['product_type_id'])) {
            $query->where('product_type_id', $this->filters['product_type_id']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('created_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        if (isset($this->filters['min_price'])) {
            $query->where('product_total_price', '>=', $this->filters['min_price']);
        }

        if (isset($this->filters['max_price'])) {
            $query->where('product_total_price', '<=', $this->filters['max_price']);
        }

        if (!empty($this->filters['vendor_name'])) {
            $query->where('vendor_name', 'LIKE', "%{$this->filters['vendor_name']}%");
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Map each incoming to array
     */
    public function map($incoming): array
    {
        return [
            $incoming->created_at->format('d/m/Y H:i'),
            $incoming->product_code ?? '-',
            $incoming->product_name,
            $incoming->vendor_name,
            $incoming->product_purpose ?? '-',
            $incoming->product_quantity,
            $incoming->product_each_price,
            $incoming->product_total_price,
        ];
    }

    /**
     * Set headers
     */
    public function headings(): array
    {
        return [
            'Tanggal Pembelian',
            'Kode Obat',
            'Nama Obat',
            'Vendor',
            'Kegunaan',
            'Jumlah',
            'Harga Beli/Pcs',
            'Total Beli',
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
