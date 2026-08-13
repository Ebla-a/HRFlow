<?php

namespace Modules\Report\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ReportExport
{
    public function __construct(
        private readonly array $summary,
        private readonly array $breakdown = [],
        private readonly array $raw = []
    ) {}

    public function download(string $fileName):StreamedResponse
{
    $spreadsheet = new Spreadsheet();

    // SUMMARY
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Summary');

    $sheet1->fromArray([['Metric', 'Value']], null, 'A1');
    $sheet1->getStyle('A1:B1')->getFont()->setBold(true);

    $row = 2;
    foreach ($this->summary as $key => $value) {
        $sheet1->setCellValue("A{$row}", $key);
        $sheet1->setCellValue("B{$row}", is_array($value) ? json_encode($value) : $value);
        $row++;
    }

    // BREAKDOWN
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('Breakdown');

    if (!empty($this->breakdown)) {
        $sheet2->fromArray(array_keys($this->breakdown[0]), null, 'A1');
        $sheet2->fromArray($this->breakdown, null, 'A2');
    }

    // RAW DATA
    $sheet3 = $spreadsheet->createSheet();
    $sheet3->setTitle('Raw Data');

    $rawRows = [];
    foreach ($this->raw as $key => $value) {
        $rawRows[] = [
            'Key' => $key,
            'Value' => is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value
        ];
    }

    $sheet3->fromArray($rawRows, null, 'A1');

    // DOWNLOAD
    $writer = new Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $fileName);
}
}
