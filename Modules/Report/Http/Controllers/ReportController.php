<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Report\Services\ReportService;
use Modules\Payroll\Entities\PayrollRun;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Generate a report summary on demand.
     *
     * @param string $type payroll|attendance|leave|performance|employees
     * @return JsonResponse
     */
public function generate(Request $request, string $type): JsonResponse
    {
        $month = (int) ($request->input('month', now()->month));
        $year = (int) ($request->input('year', now()->year));

        $data = $this->reportService->generate($type, $month, $year);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function generatePayroll(PayrollRun $run): JsonResponse
    {
    $data = $this->reportService->generatePayroll($run);

    return response()->json([
        'success' => true,
        'data' => $data,
      ]);
    }

    /**
     * Read a report summary for a given type/month/year.
     *
     * @param string $type
     * @return JsonResponse
     */
    public function show(Request $request, string $type): JsonResponse
    {
        $month = (int) ($request->input('month', now()->month));
        $year = (int) ($request->input('year', now()->year));

        $data = $this->reportService->read($type, $month, $year);

        if ($data === null) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found for this period. Generate it first.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * List all report summaries for a type.
     *
     * @param string $type
     * @return JsonResponse
     */
    public function index(Request $request, string $type): JsonResponse
    {
        $year = $request->has('year') ? (int) $request->input('year') : null;

        $data = $this->reportService->listByType($type, $year);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

   /**
     * Export report to Excel format.
     *
     * @param Request $request
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
  public function exportExcel(Request $request, string $type)
{
    $month = (int) $request->query('month', now()->month);
    $year = (int) $request->query('year', now()->year);

    $reportData = $this->reportService->generate($type, $month, $year);
    $rows = $this->reportService->toExportArray($reportData);

    $fileName = "{$type}_report_{$year}_{$month}.xlsx";


return (new FastExcel($rows))->download($fileName);
}
}