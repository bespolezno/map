<?php

use App\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = file_get_contents('./csv/schedule.csv', true);
        $rows = preg_split("/\r\n/", $content, -1, PREG_SPLIT_NO_EMPTY);
        $fields =  explode(',', array_shift($rows));
        collect($rows)
            ->map(function ($row) use ($fields) {
                return array_combine($fields, explode(',', $row));
            })
            ->each([Schedule::class, 'create']);
    }
}
