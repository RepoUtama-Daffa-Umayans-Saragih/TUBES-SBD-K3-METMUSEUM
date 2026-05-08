<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSystemSeeder extends Seeder
{
    public function run()
    {
        // ========================
        // 1. LOCATIONS
        // ========================
        DB::table('locations')->insert([
            [
                'location_name'  => 'The Met Fifth Avenue',
                'address'        => '1000 5th Ave, New York, NY',
                'capacity_limit' => 500,
            ],
            [
                'location_name'  => 'The Met Cloisters',
                'address'        => '99 Margaret Corbin Dr, New York, NY',
                'capacity_limit' => 200,
            ],
        ]);

        $locations = DB::table('locations')->get();

        // ========================
        // 2. TICKET TYPES
        // ========================
        DB::table('ticket_types')->insert([
            ['ticket_type_name' => 'Adult', 'base_price' => 25],
            ['ticket_type_name' => 'Child', 'base_price' => 0],
            ['ticket_type_name' => 'Student', 'base_price' => 17],
            ['ticket_type_name' => 'Senior', 'base_price' => 20],
        ]);

        $ticketTypes = DB::table('ticket_types')->get();

        // ========================
        // 3. VISIT SCHEDULES
        // ========================
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = Carbon::now()->addDays($i)->format('Y-m-d');
        }

        foreach ($locations as $location) {
            foreach ($dates as $date) {
                DB::table('visit_schedules')->insert([
                    'location_id'    => $location->location_id, // ✅ FIX
                    'visit_date'     => $date,
                    'capacity_limit' => $location->capacity_limit,
                ]);
            }
        }

        $schedules = DB::table('visit_schedules')->get();

        // ========================
        // 4. TICKET AVAILABILITY
        // ========================
        foreach ($schedules as $schedule) {
            foreach ($ticketTypes as $ticket) {
                DB::table('ticket_availability')->insert([
                    'ticket_type_id'    => $ticket->ticket_type_id,
                    'visit_schedule_id' => $schedule->visit_schedule_id,
                ]);
            }
        }
    }
}
