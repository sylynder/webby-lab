<?php 

namespace App\Models\School; 

use Base\Models\EasyModel;

class SeederModel extends EasyModel
{
    public $table = 'books'; // name of table to use
    
    public $primaryKey = 'id'; // name of primary key field
    
    protected $useSoftDelete = false; // set whether to use soft delete

     // Generator function to yield records
     function generateRecords($count):mixed {
        for ($i = 1; $i <= $count; $i++) {
            $record = [
                'id' => $i,
                'title' => 'title ' . $i,
                'isbn' => 'isbn ' . $i,
                // Add other fields as needed
            ];

            yield $record;
        }
    }

    
    public function insertSeed()
    {
        // SQL query to insert records
        // $sql = "INSERT INTO your_table (id, title, isbn) VALUES (?, ?)";
        start_benchmark('insert_gen');
        // Generate and insert 10,000 records
        $recordGenerator = $this->generateRecords(100); // this works
        // $recordGenerator = $this->generateRecords(10_000); // this works
        // $recordGenerator = $this->generateRecords(1000000); // this terminates

        $d = [];

        foreach ($recordGenerator as $record) {
            $d[] = $record;
        }

        end_benchmark('finish_gen');

        // $memory	= round(memory_get_usage() / 1024 / 1024, 2) . 'MB';
        // total_ti
        // dd($d, show_time_elasped('insert_gen', 'finish_gen'), $memory);

        $this->insertBatch($d);
        
        // foreach ($recordGenerator as $record) {
        //     $this->db->insert('books', $record);
        // }

    }
    
}
/* End of SeederModel file */
