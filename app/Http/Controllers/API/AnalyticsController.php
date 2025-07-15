<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsTraffic;
use App\Models\AnalyticsTrafficSource;
use App\Models\AnalyticsSales;
use App\Models\AnalyticsSalesProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function getTraffic(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));

        $traffic = AnalyticsTraffic::with(['sources'])->whereDate('date', $date)->first();

        if (!$traffic) {
            return response()->json([
                'message' => 'No traffic data found for this date'
            ], 404);
        }

        return $traffic;
    }

    public function getSales(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));

        $sales = AnalyticsSales::with(['products'])->whereDate('date', $date)->first();

        if (!$sales) {
            return response()->json([
                'message' => 'No sales data found for this date'
            ], 404);
        }

        return $sales;
    }

    public function getProductStats(Request $request)
    {
        $date = $request->query('date', date('Y-m-d'));
        $productId = $request->query('product_id');

        if ($productId) {
            $stats = AnalyticsSalesProducts::whereDate('created_at', $date)
                ->where('product_id', $productId)
                ->first();

            if (!$stats) {
                return response()->json([
                    'message' => 'No product stats found'
                ], 404);
            }

            return $stats;
        }

        $stats = AnalyticsSalesProducts::whereDate('created_at', $date)
            ->with('product')
            ->get();

        return $stats;
    }

    public function getTopProducts(Request $request)
    {
        $startDate = $request->query('start_date', date('Y-m-d', strtotime('-30 days')));
        $endDate = $request->query('end_date', date('Y-m-d'));

        $products = AnalyticsSalesProducts::select(
            'product_id',
            DB::raw('SUM(quantity_sold) as total_sold'),
            DB::raw('SUM(revenue) as total_revenue')
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('product_id')
        ->orderBy('total_sold', 'desc')
        ->limit(10)
        ->get();

        return $products;
    }

    public function getTrafficSources(Request $request)
    {
        $startDate = $request->query('start_date', date('Y-m-d', strtotime('-30 days')));
        $endDate = $request->query('end_date', date('Y-m-d'));

        $sources = AnalyticsTrafficSources::select(
            'source',
            DB::raw('SUM(visits) as total_visits'),
            DB::raw('AVG(percentage) as average_percentage')
        )
        ->whereHas('traffic', function($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })
        ->groupBy('source')
        ->orderBy('total_visits', 'desc')
        ->get();

        return $sources;
    }
}
