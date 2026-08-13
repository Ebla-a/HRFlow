<?php

namespace Modules\Report\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportExportSingleSheet
{
    public function __construct(
        private readonly array $rows
    ) {}

    public function download(string $fileName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report');

        // Title
        $sheet->setCellValue('A1', 'Report Summary');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4F81BD');
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        // Header
        $sheet->fromArray(
            [['Metric', 'Value']],
            null,
            'A2'
        );

        // Header Style
        $sheet->getStyle('A2:B2')->getFont()->setBold(true);
        $sheet->getStyle('A2:B2')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFB8CCE4');
        $sheet->getStyle('A2:B2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $sheet->fromArray($this->rows, null, 'A3');

        $lastRow = count($this->rows) + 2;

        // Borders
        $sheet->getStyle("A2:B{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Banded rows
        for ($row = 3; $row <= $lastRow; $row++) {
            if ($row % 2 === 1) {
                $sheet->getStyle("A{$row}:B{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFF2F2F2');
            }
        }

        // Freeze header
        $sheet->freezePane('A3');

        // Auto-size
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Alignment
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal('left');
        $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setWrapText(true);

        // Highlight numeric values
        for ($row = 3; $row <= $lastRow; $row++) {
            $value = $sheet->getCell("B{$row}")->getValue();
            if (is_numeric($value)) {
                $sheet->getStyle("B{$row}")
                    ->getFont()
                    ->getColor()
                    ->setARGB('FF1F497D');
            }
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }
}
