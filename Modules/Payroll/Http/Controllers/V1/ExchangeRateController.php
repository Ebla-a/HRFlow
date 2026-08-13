<?php
declare(strict_types=1);

namespace Modules\Payroll\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payroll\Contracts\ExchangeRateFetcherInterface;

final class ExchangeRateController extends Controller
{
    public function __construct(
        private readonly ExchangeRateFetcherInterface $exchangeRateFetcher
    ) {}

    public function index(Request $request): JsonResponse
    {
       
        $baseCurrency = $request->input('base', 'USD');

    
        $rates = $this->exchangeRateFetcher->fetchLatestRates($baseCurrency);

      return $this->success(['success' => true,
            'base' => $baseCurrency,
            'rates' => $rates,]);
          
    }
}