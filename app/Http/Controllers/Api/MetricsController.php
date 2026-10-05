<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MetricsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $metrics = Cache::remember('helpdesk.metrics', 300, function () {
            $counts = Ticket::query()
                ->selectRaw('status, COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status');

            $total = (int) $counts->sum();
            $resolved = (int) ($counts->get('resolved') ?? 0);

            return [
                'open' => (int) ($counts->get('open') ?? 0),
                'in_progress' => (int) ($counts->get('in_progress') ?? 0),
                'escalated' => (int) ($counts->get('escalated') ?? 0),
                'resolved' => $resolved,
                'total' => $total,
                'resolution_rate_percent' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $metrics,
        ]);
    }
}
