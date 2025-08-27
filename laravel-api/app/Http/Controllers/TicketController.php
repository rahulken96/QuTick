<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketReplyStoreRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TicketResource;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketReplyResource;
use App\Models\TicketReply;

class TicketController extends Controller
{
    /*=====================
       Data Ticket
    ==========================*/
    public function allTickets(Request $request)
    {
        if (!isThisAdmin()) {
            return response()->json([
                'status'  => false,
                'message' => 'Eaaa.. mau ngapain mazzehh. ANDA TIDAK MEMILIKI AKSES INI hehehe... :D.'
            ], 403);
        }

        try {
            $limit = $request->get('limit', 10);
            $page  = $request->get('page', 1);
            $offset = ($page - 1) * $limit;

            $tickets = Ticket::query();

            if ($request->keyword != null && $request->keyword != '') {
                $tickets->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('code', 'like', '%' . $request->keyword . '%');
            }

            if ($request->filled('status')) {
                $tickets->where('status', $request->status);
            }

            if ($request->filled('priority')) {
                $tickets->where('priority', $request->priority);
            }

            $tickets = $tickets
                ->limit($limit)
                ->offset($offset)
                ->orderByRaw("
                    CASE
                        WHEN priority = 2 THEN 0
                        WHEN priority = 1 THEN 1
                        ELSE 2
                    END
                ")
                ->orderBy('created_at', 'asc')
                ->get();

            if ($tickets->isEmpty()) {
                return response()->json([
                    'status'    => false,
                    'totalRows' => 0,
                    'data'      => []
                ], 200);
            }

            return response()->json([
                'status'    => true,
                'totalRows' => $tickets->count(),
                'data'      => TicketResource::collection($tickets)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function myTicket(Request $request)
    {
        try {
            $limit = $request->get('limit', 6);
            $page  = $request->get('page', 1);
            $offset = ($page - 1) * $limit;

            $tickets = Ticket::query()->where('user_id', Auth::user()->id);

            if ($request->keyword != null && $request->keyword != '') {
                $tickets->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('code', 'like', '%' . $request->keyword . '%');
            }

            if ($request->filled('status')) {
                $tickets->where('status', $request->status);
            }

            if ($request->filled('priority')) {
                $tickets->where('priority', $request->priority);
            }

            $tickets = $tickets->limit($limit)
                ->offset($offset)
                ->latest()
                ->get();

            if ($tickets->isEmpty()) {
                return response()->json([
                    'status'    => false,
                    'totalRows' => 0,
                    'data'      => []
                ], 200);
            }

            return response()->json([
                'status'    => true,
                'totalRows' => $tickets->count(),
                'data'      => TicketResource::collection($tickets)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function detailTicket(Request $request)
    {
        try {
            $ticket = Ticket::with(['replies'])->firstWhere('code', $request->ticket_code);

            if (!$ticket) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tiket tidak ditemukan.'
                ], 500);
            }

            if (Auth::user()->role != 'admin' && (Auth::user()->id != $ticket->user_id)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Eaaa.. mau ngapain mazzehh. ANDA TIDAK MEMILIKI AKSES INI hehehe... :D.'
                ], 403);
            }

            return response()->json([
                'status'    => true,
                'data'      => [
                    'ticket'   => new TicketResource($ticket),
                    'replies'  => TicketReplyResource::collection($ticket->replies),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*=====================
       CRUD Ticket
    ==========================*/
    public function storeTicket(TicketStoreRequest $request)
    {
        $request->validated();
        try {
            DB::beginTransaction();

            $ticket = new Ticket();
            $ticket->user_id     = Auth::user()->id;
            $ticket->code        = generateTicketCode();
            $ticket->title       = $request->title;
            $ticket->description = $request->description;
            $ticket->priority    = $request->priority;
            $ticket->save();

            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Data tiket berhasil dibuat.',
                'data'    => new TicketResource($ticket)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTicket(TicketStoreRequest $request)
    {
        $request->validated();
        try {
            DB::beginTransaction();

            $ticket = Ticket::firstWhere('code', $request->ticket_code);

            if (!$ticket) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tiket tidak ditemukan.'
                ], 500);
            }

            if (Auth::user()->role != 'admin' && (Auth::user()->id != $ticket->user_id)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Eaaa.. mau ngapain mazzehh. ANDA TIDAK MEMILIKI AKSES INI hehehe... :D.'
                ], 403);
            }

            $ticket->title       = $request->title;
            $ticket->description = $request->description;
            $ticket->priority    = $request->priority;
            $ticket->save();

            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Data tiket berhasil diperbarui.',
                'data'    => new TicketResource($ticket)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*=====================
       Reply Ticket
    ==========================*/
    public function storeReplyTicket(TicketReplyStoreRequest $request)
    {
        $request->validated();
        try {
            DB::beginTransaction();

            $ticket = Ticket::firstWhere('code', $request->ticket_code);

            if (!$ticket) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tiket tidak ditemukan.'
                ], 500);
            }

            if (Auth::user()->role != 'admin' && (Auth::user()->id != $ticket->user_id)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Eaaa.. mau ngapain mazzehh. ANDA TIDAK MEMILIKI AKSES INI hehehe... :D.'
                ], 403);
            }

            $reply = new TicketReply();
            $reply->ticket_id   = $ticket->id;
            $reply->user_id     = Auth::user()->id;
            $reply->message     = $request->message;
            $reply->save();

            if (isThisAdmin()) {
                $ticket->status = $request->status;

                if ($request->status == 2) { // Resolved
                    $ticket->completed_at = now();
                }

                $ticket->save();
            }

            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Reply tiket berhasil dikirim.',
                'data'    => new TicketReplyResource($reply)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
