<?php

use Illuminate\Database\Seeder;
use App\ClientProcess;
use League\Csv\Reader;
use League\Csv\Statement;

class ClientProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->getClientFiles('clients_processes.csv');
    }

    public function getClientFiles($fileName)
    {
        $csv = Reader::createFromPath(database_path('/data/'.$fileName, 'r'));
        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);
        $stmt = (new Statement());
        $records = $stmt->process($csv);

        foreach ($records as $record_key => $record) {
            $client_process = new ClientProcess;
            $client_process->client_id = $record['id'];
            $client_process->process_id = $record['process_id'];
            $client_process->step_id = $record['step_id'];
            $client_process->save();
        }
    }
}
