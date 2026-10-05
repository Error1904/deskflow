<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['open', 'open', 'in_progress', 'escalated', 'resolved']);

        return [
            'ticket_number' => 'DF-' . fake()->unique()->numerify('########'),
            'user_id' => User::factory(),
            'assigned_agent_id' => User::factory()->state(['role' => 'agent']),
            'department' => fake()->randomElement(['IT Support', 'Networking', 'Software', 'Hardware', 'Security']),
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => $status,
            'resolved_at' => $status === 'resolved' ? fake()->dateTimeThisMonth() : null,
        ];
    }
}
