<?php

use Illuminate\Database\Seeder;
use App\CalendarEvent;

class CalendarEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
        	['title'=>'Client Event-1', 'start_date'=>'2018-04-11', 'end_date'=>'2018-04-12'],
        	['title'=>'Client Event-2', 'start_date'=>'2018-04-11', 'end_date'=>'2018-04-13'],
        	['title'=>'Client Event-3', 'start_date'=>'2018-04-14', 'end_date'=>'2018-04-14'],
        	['title'=>'Client Event-4', 'start_date'=>'2018-04-17', 'end_date'=>'2018-04-17'],
                ['title'=>'Client Event-5', 'start_date'=>'2018-04-26', 'end_date'=>'2018-04-27'],
        ];
        foreach ($data as $key => $value) {
        	CalendarEvent::create($value);
        }
    }
}
