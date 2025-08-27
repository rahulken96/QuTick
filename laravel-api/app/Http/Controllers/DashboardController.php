<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboardStatistic()
    {
        $currentMonth    = Carbon::now()->startOfMonth();
        $endOfMonth      = $currentMonth->copy()->endOfMonth();
        $ticketThisMonth = Ticket::query()->whereBetween('created_at', [$currentMonth, $endOfMonth]);

        $totalTickets       = (clone $ticketThisMonth)->count();
        $activeTickets      = (clone $ticketThisMonth)->whereIn('status', [0,1])->count();
        $completedTickets   = (clone $ticketThisMonth)->where('status', 2)->count();
        $rejectedTickets    = (clone $ticketThisMonth)->where('status', 3)->count();

        $avgCompletionTime = (clone $ticketThisMonth)
            ->where('status', 2)
            ->whereNotNull('completed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_completion_time'))
            ->value('avg_completion_time');

        $dataPerStatus = [
            'open'          => (clone $ticketThisMonth)->where('status', 0)->count(),
            'inProgress'    => (clone $ticketThisMonth)->where('status', 1)->count(),
            'completed'     => (clone $ticketThisMonth)->where('status', 2)->count(),
            'rejected'      => (clone $ticketThisMonth)->where('status', 3)->count(),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'totalTickets'      => $totalTickets,
                'activeTickets'     => $activeTickets,
                'completedTickets'  => $completedTickets,
                'rejectedTickets'   => $rejectedTickets,
                'avgCompletionTime' => $avgCompletionTime ? round($avgCompletionTime, 2) : 0,
                'dataPerStatus'     => $dataPerStatus,
            ]
        ]);
    }
}
