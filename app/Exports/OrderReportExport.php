<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderReportExport implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private string $dateStart,
        private string $dateEnd,
    ) {}

    public function query()
    {
        return Order::with(['customer', 'payment'])
            ->whereBetween('created_at', [
                Carbon::parse($this->dateStart)->startOfDay(),
                Carbon::parse($this->dateEnd)->endOfDay(),
            ])
            ->where('payment_status', 'completed')
            ->latest();
    }

    public function headings(): array
    {
        return [
            'No.',
            'No. Pesanan',
            'Tanggal',
            'Pelanggan',
            'Tipe',
            'Metode Pembayaran',
            'Status Pesanan',
            'Status Pembayaran',
            'Total (Rp)',
        ];
    }

    public function map($order): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $order->order_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->customer?->name ?? '-',
            $order->type === 'dine_in' ? 'Dine In' : 'Takeaway',
            $order->payment_method === 'cash' ? 'Tunai' : 'Non-Tunai',
            match ($order->status) {
                'pending' => 'Diproses',
                'confirmed' => 'Dikonfirmasi',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
                default => $order->status,
            },
            match ($order->payment_status) {
                'pending' => 'Belum Dibayar',
                'completed' => 'Lunas',
                'cancelled' => 'Dibatalkan',
                default => $order->payment_status,
            },
            (float) $order->amount,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row bold + background
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2D6A4F'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->rowNumber + 1;
                $lastCol = 'I';

                // Border around all data
                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD0D0D0'],
                        ],
                    ],
                ]);

                // Zebra striping on data rows
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF5F5F5'],
                            ],
                        ]);
                    }
                }

                // Total row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue("H{$totalRow}", 'TOTAL');
                $sheet->setCellValue("I{$totalRow}", "=SUM(I2:I{$lastRow})");
                $sheet->getStyle("H{$totalRow}:I{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE8F5E9'],
                    ],
                ]);
                $sheet->getStyle("I{$totalRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                // Freeze header row
                $sheet->freezePane('A2');
            },
        ];
    }

    public function title(): string
    {
        return 'Laporan Pesanan';
    }
}
