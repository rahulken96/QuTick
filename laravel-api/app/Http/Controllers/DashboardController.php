<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ticket;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

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

    public function getLogs(Request $request)
    {
        try {
            $limit = $request->get('limit', 15);
            $query = UserLog::with(['user']);

            if (!isThisAdmin()) {
                $query->where('user_id', Auth::id());
            }

            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('activity', 'like', '%' . $keyword . '%')
                      ->orWhereHas('user', function($uq) use ($keyword) {
                          $uq->where('name', 'like', '%' . $keyword . '%');
                      });
                });
            }

            $logs = $query->orderBy('created_at', 'desc')->paginate($limit);

            return response()->json([
                'status' => true,
                'data' => $logs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function exportReportPDF(Request $request)
    {
        if (!isThisAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $ticketsQuery = Ticket::with(['user']);
            if ($startDate) {
                $ticketsQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $ticketsQuery->whereDate('created_at', '<=', $endDate);
            }
            $tickets = $ticketsQuery->orderBy('created_at', 'desc')->get();

            $total = $tickets->count();
            $open = $tickets->where('status', 0)->count();
            $inProgress = $tickets->where('status', 1)->count();
            $resolved = $tickets->where('status', 2)->count();
            $rejected = $tickets->where('status', 3)->count();

            $html = view('report.tickets', compact('tickets', 'startDate', 'endDate', 'total', 'open', 'inProgress', 'resolved', 'rejected'))->render();

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
            ]);

            $mpdf->WriteHTML($html);

            return response($mpdf->Output('qutick_report.pdf', 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="qutick_report.pdf"',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

