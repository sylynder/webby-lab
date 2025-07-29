<?php 

use Base\Controllers\ConsoleController;
use Base\Seeder\Seeder;

class Tools extends ConsoleController
{
    public $seeder;

    public function __construct()
    {
        parent::__construct();

        // // dd('here');
        $this->seeder = new Seeder();
        // dd($this->seeder);

        // // can only be called from the command line
        // if (!is_cli()) {
        //     exit('Direct access is not allowed. This is a command line tool, use the terminal');
        // }

        // $this->use->dbforge();

        // // initiate faker
        $this->faker = Faker\Factory::create();

    }

    public function message($to = 'World')
    {
        echo "Hello {$to}!" . PHP_EOL;
    }

    public function help() {
		$result = "The following are the available command line interface commands\n\n";
		$result .= "php index.php tools migration \"file_name\"         Create new migration file\n";
		$result .= "php index.php tools migrate [\"version_number\"]    Run all migrations. The version number is optional.\n";
		$result .= "php index.php tools seeder \"file_name\"            Creates a new seed file.\n";
		$result .= "php index.php tools seed \"file_name\"              Run the specified seed file.\n";

		echo $result . PHP_EOL;
	}

    public function index($name)
    {
        // Sample Code Here ...
        $this->create_seeder($name);
    }

    public function seeder($name) {
		$this->create_seeder($name);
	}

	public function seed($name) {

		// $this->seeder->call($name);
	}

    protected function create_seeder($name)
    {
		
        $path = ROOTPATH . "database/seeders/" . ucfirst($name) . EXT;

		$seeder = fopen($path, "w") or die("Unable to create seeder file!");

		$content = "<?php

use Base\Seeder\Seeder;

class ". ucfirst($name)." extends Seeder 
{

    private \$table = '';

    public function run() 
    {

        \$this->db->truncate(\$this->table);

        //seed records manually

        \$data = [
            'user_name' => 'admin',
            'password' => '9871'
        ];

        \$this->db->insert(\$this->table, \$data);

        //seed many records using faker
        \$limit = 33;
        echo \"seeding \$limit user accounts\";

        for (\$i = 0; \$i < \$limit; \$i++) {
            echo \".\";

            \$data = [
                'user_name' => \$this->faker->unique()->userName,
                'password' => '1234',
            ];

            \$this->db->insert(\$this->table, \$data);
        }

        echo PHP_EOL;
    }
}
";

		fwrite($seeder, $content);

		fclose($seeder);

        echo $this->success(ucwords($name) . EXT . " seeder created successfully.");
		// echo "$path seeder has successfully been created." . PHP_EOL;
	}

}
/* End of Tools file */
