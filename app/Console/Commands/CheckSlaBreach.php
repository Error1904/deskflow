<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckSlaBreach extends Command
{
    protected $signature = 'sla:check
                            {--hours=24 : SLA threshold in hours before escalation}
                            {--dry-run : List breached tickets without changing their status}';

    protected $description = 'Escalate open tickets that have breached the SLA threshold without resolution';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $threshold = now()->subHours($hours);

        $query = Ticket::query()
            ->where('status', 'open')
            ->where('created_at', '<', $threshold);

        $count = $query->count();

        if ($count === 0) {
            $this->info("No open tickets have breached the {$hours}-hour SLA.");

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("DRY RUN: {$count} ticket(s) breached the {$hours}-hour SLA.");

            $this->table(
                ['Ticket #', 'Subject', 'Priority', 'Created At', 'Age (hours)'],
                $query->oldest()
                    ->get(['ticket_number', 'subject', 'priority', 'created_at'])
                    ->map(function ($ticket) {
                        return [
                            $ticket->ticket_number,
                            Str::limit($ticket->subject, 40),
                            $ticket->priority,
                            $ticket->created_at->toDateTimeString(),
                            $ticket->created_at->diffInHours(now()),
                        ];
                    })
                    ->toArray()
            );

            return self::SUCCESS;
        }

        $query->orderBy('id')->chunkById(500, function ($tickets) use ($hours) {
            foreach ($tickets as $ticket) {
                $ticket->update(['status' => 'escalated']);

                Log::info('SLA breach: ticket escalated', [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'user_id' => $ticket->user_id,
                    'assigned_agent_id' => $ticket->assigned_agent_id,
                    'department' => $ticket->department,
                    'priority' => $ticket->priority,
                    'created_at' => $ticket->created_at->toDateTimeString(),
                    'escalated_at' => now()->toDateTimeString(),
                    'sla_threshold_hours' => $hours,
                ]);
            }
        });

        $this->info("Successfully escalated {$count} ticket(s) to 'escalated' status.");

        return self::SUCCESS;
    }
}
