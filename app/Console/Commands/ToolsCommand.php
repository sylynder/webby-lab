<?php 

use Base\Console\Command;
use Base\Seeder\Seeder;

class ToolsCommand extends Command
{

    public $seeder;

    public function __construct()
    {
        parent::__construct();

        dd('here');
        $this->seeder =  new Seeder();

        // can only be called from the command line
        if (!is_cli()) {
            exit('Direct access is not allowed. This is a command line tool, use the terminal');
        }

        $this->use->dbforge();

        // initiate faker
        $this->faker = Faker\Factory::create();

    }

    public function message($to = 'World')
    {
        echo "Hello {$to}!" . PHP_EOL;
    }


    public function index()
    {
        // Sample Code Here ...
    }

}
/* End of ToolsCommand file */
