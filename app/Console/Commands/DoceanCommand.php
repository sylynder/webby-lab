<?php 

use Base\Console\Command;

class DoceanCommand extends Command
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo $this->info("Hi everyone");
    }

}
/* End of DoceanCommand file */
