<?php 

use Base\Console\Command;

class GistCommand extends Command
{

    protected $signature;

    protected $description;

    protected $aliases = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo $this->info('Some success console message here');
    }

    public function run()
    {
        // Sample Code Here ...
    }

}
/* End of GistCommand file */
