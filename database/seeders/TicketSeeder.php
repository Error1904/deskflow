<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id');

        Ticket::factory()->count(40)->create()->each(function (Ticket $ticket) use ($userIds) {
            TicketComment::factory()->count(random_int(0, 5))->create([
                'ticket_id' => $ticket->id,
                'user_id' => $userIds->random(),
            ]);
        });

        Ticket::factory()->count(3)->state([
            'status' => 'open',
            'created_at' => now()->subDays(3),
        ])->create();
    }
}
