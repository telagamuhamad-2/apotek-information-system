<?php

namespace App\Exports;

use App\Models\ProductOutgoing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductOutgoingExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get product outgoings with filters applied
     */
    public function collection()
    {
        $query = ProductOutgoing::query();

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

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Map each outgoing to array
     */
    public function map($outgoing): array
    {
        return [
            $outgoing->created_at->format('d/m/Y H:i'),
            $outgoing->product_code ?? '-',
            $outgoing->product_name,
            $outgoing->product_purpose ?? '-',
            $outgoing->product_quantity,
            $outgoing->product_each_price,
            $outgoing->product_total_price,
        ];
    }

    /**
     * Set headers
     */
    public function headings(): array
    {
        return [
            'Tanggal Penjualan',
            'Kode Obat',
            'Nama Obat',
            'Kegunaan',
            'Jumlah',
            'Harga Jual/Pcs',
            'Total Jual',
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
