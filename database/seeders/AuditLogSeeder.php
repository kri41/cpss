<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Event;
use App\Models\Partisipasi;
use App\Models\Prasarana;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedPrasaranaLogs();
        $this->seedClubLogs();
        $this->seedEventLogs();
        $this->seedPartisipasiLogs();

        $this->command->info('Seeded audit logs for all dummy data.');
    }

    private function seedPrasaranaLogs(): void
    {
        $prasarana = Prasarana::all();
        foreach ($prasarana as $item) {
            AuditLog::create([
                'user_id' => $item->user_id,
                'action' => 'CREATE',
                'target_table' => 'prasarana',
                'target_id' => $item->id,
                'old_value' => null,
                'new_value' => $item->toArray(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder/DummyData',
                'created_at' => $item->created_at,
                'updated_at' => $item->created_at,
            ]);
        }
    }

    private function seedClubLogs(): void
    {
        $clubs = Club::all();
        foreach ($clubs as $item) {
            AuditLog::create([
                'user_id' => $item->user_id,
                'action' => 'CREATE',
                'target_table' => 'clubs',
                'target_id' => $item->id,
                'old_value' => null,
                'new_value' => $item->toArray(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder/DummyData',
                'created_at' => $item->created_at,
                'updated_at' => $item->created_at,
            ]);
        }
    }

    private function seedEventLogs(): void
    {
        $events = Event::all();
        foreach ($events as $item) {
            AuditLog::create([
                'user_id' => $item->user_id,
                'action' => 'CREATE',
                'target_table' => 'events',
                'target_id' => $item->id,
                'old_value' => null,
                'new_value' => $item->toArray(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder/DummyData',
                'created_at' => $item->created_at,
                'updated_at' => $item->created_at,
            ]);
        }
    }

    private function seedPartisipasiLogs(): void
    {
        $partisipasi = Partisipasi::all();
        foreach ($partisipasi as $item) {
            AuditLog::create([
                'user_id' => $item->user_id,
                'action' => 'CREATE',
                'target_table' => 'partisipasi',
                'target_id' => $item->id,
                'old_value' => null,
                'new_value' => $item->toArray(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder/DummyData',
                'created_at' => $item->created_at,
                'updated_at' => $item->created_at,
            ]);
        }
    }
}
