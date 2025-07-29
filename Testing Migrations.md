Testing Migraton Two
content_copy
<?php

use Base\Migrations\Tables;
use Base\Migrations\Schema;
use Base\Migrations\Table;
use Base\Migrations\Migration;


class Migration_Add_users extends Migration
{
    public function tableExists($table)
    {
        return $this->db->table_exists($table) ? true : false;
    }

    private function create_users_table()
	{
		$this->load->dbforge();
		$this->dbforge->add_field('id');
		$this->dbforge->add_field('email VARCHAR(200) NOT NULL');
		$this->dbforge->add_field('password VARCHAR(200) NOT NULL');
		$this->dbforge->add_field('created DATETIME NOT NULL');
		$this->dbforge->add_field('last_login DATETIME NULL');
		$this->dbforge->create_table($this->users_table);
	}

    public function createTable($table = '', array $array = [])
    {

        dd();
        // Schema::renameColumn('clients', 'content', 'contents');
        // dd();
        // Schema::create('clients', function (Table $column) {
        //     $column->autoincrement('id');
        //     $column->field('content', 'varchar', ['constraint' => '40','null' => true]);
        //     $column->string('email');
        //     $column->string('password');
        //     $column->string('first_name');
        //     $column->string('last_name');
        //     $column->text('biography');
        //     $column->boolean('enabled');
        //     $column->bys();
        //     $column->datetimes();
        // });

        // Schema::create('words', function ($column) {
        //     $column->autoincrement('id');
        //     $column->string('email');
        //     $column->string('password');
        //     $column->string('first_name');
        //     $column->string('last_name');
        //     $column->text('biography');
        //     $column->boolean('enabled');
        //     $column->timestamps();
        // });

        // Schema::create('mans', function ($column) {
        //     $column->autoincrement('id');
        //     $column->string('email');
        //     $column->string('password');
        //     $column->string('first_name');
        //     $column->string('last_name');
        //     $column->text('biography');
        //     $column->boolean('enabled');
        //     $column->timestamps();
        // });

        // Schema::create('books', function ($column) {

            // $column->autoincrement('id');
            // $column->string('title', 180);
            // $column->define('author VARCHAR(200) NULL');
            // $column->integer('pages');
            // $column->boolean('status');
            // $column->datetimes();
            // $column->useractions();
            // $column->key('title');

            // $column->define('id');
            // $column->define('book_id VARCHAR(20) NOT NULL');
            // $column->define('title VARCHAR(180) NOT NULL');
            // $column->define('author VARCHAR(200) NULL');
            // $column->define('pages tinyint(6) NULL');
            // $column->define('created DATETIME NOT NULL');
            // $column->boolean('status');
            // $column->datetimes();
            // $column->useractions();
            // $column->key('title');
            
        // });

        dd();
        // $table = new Tables($this->db, $this->dbforge);
        $query = $table::query('Select * from notes')->result_array();

        // $this->db->result($query);
// dd($query);
// dd('here');
        // $check = Tables::create('users', function($column){
        //     $column->primaryKey();
        //     $column->varchar(20);
        //     $column->description();
        // });

        // $table->column('user_id');
        // dd($table->column('user_id'));
        // $table->column('user_id')
        //     ->varchar('255')
        //     ->key('primary')
        //     ->autoincrement();
        
        // $table->db->query();
        $exists = $this->tableExists($table);

        if ($exists) {
            return "Table Exists Already";
        }

        $this->db->query("CREATE TABLE {$table}(user_id int PRIMARY KEY,
                username varchar(25) NOT NULL,
                password varchar(30) NOT NULL)"
        );

        return "Done creating " . $table . " table";
    }

    public function up()
    {
        // $this->load->dbforge();
		// $this->dbforge->add_field('id');
		// $this->dbforge->add_field('email VARCHAR(200) NOT NULL');
		// $this->dbforge->add_field('password VARCHAR(200) NOT NULL');
		// $this->dbforge->add_field('created DATETIME NOT NULL');
		// $this->dbforge->add_field('last_login DATETIME NULL');
		// $this->dbforge->create_table('users_table');
        // $this->down();
        // dd();
        // Schema::withDBML();

        // Schema::withSQL("CREATE TABLE `tbl_users` (
        //     `user_id` int NOT NULL AUTO_INCREMENT,
        //     `firstname` varchar(255) NOT NULL,
        //     `lastname` varchar(255) NOT NULL,
        //     `username` varchar(255) NOT NULL,
        //     `password` varchar(255) NOT NULL,
        //     `timestamp` TIMESTAMP NOT NULL,
        //     PRIMARY KEY (`user_id`)
        // )");

        Schema::withSQL("CREATE TABLE `departments` (
            dept_id INT PRIMARY KEY,
            dept_name VARCHAR(50))
        ");

        // ALTER TABLE groups DROP FOREIGN KEY groups_user_id_fk; -- If it exists
        // ALTER TABLE groups ADD CONSTRAINT groups_user_id_fk FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE;


        Schema::withSQL("CREATE TABLE employees (
            emp_id INT PRIMARY KEY,
            emp_name VARCHAR(100),
            dept_id INT, 
            office_number VARCHAR(10),
            CONSTRAINT employees_dept_id_fk FOREIGN KEY (dept_id) REFERENCES departments (dept_id),
            CONSTRAINT employees_office_number_unique UNIQUE (office_number)  -- Office number must be unique
        )");

        // dd();
        
        Schema::create('test_words', function (Table $column) {
            $column->integer('id');
            $column->string('email', 40, ['null' => true]);
            $column->text('biography', ['length' => 2000, 'null' => true]);
            $column->field('content', 'varchar', ['constraint' => '40', 'null' => true]);
            $column->string('password');
        });
        
        // Schema::renameColumn('clients', 'content', 'contents');
        // dd();
        // Schema::create('books', function ($column) {
        //     $column->autoincrement('id');
        //     $column->string('email');
        //     $column->string('password');
        //     $column->string('first_name');
        //     $column->string('last_name');
        //     $column->text('biography');
        //     $column->boolean('enabled');
        //     $column->timestamps();
        // });

        // Schema::create('words', function ($column) {
        //     $column->autoincrement('id');
        //     $column->string('email');
        //     $column->string('password');
        //     $column->string('first_name');
        //     $column->string('last_name');
        //     $column->text('biography');
        //     $column->boolean('enabled');
        //     $column->timestamps();
        // });

        // Schema::create('clients', function (Table $column) {
        //     $column->autoincrement('id');
        //     $column->field('content', 'varchar', ['constraint' => '40', 'null' => true]);
        //     $column->string('email');
        //     $column->string('password');
        //     $column->string('first_name');
        //     $column->string('last_name');
        //     $column->text('biography');
        //     $column->boolean('enabled');
        //     // $column->bys();
        //     $column->datetimes();
        // });

        // Schema::create('words', function ($column) {
        //     $column->autoincrement('id');
        //     $column->string('email');
        //     $column->string('password');
        //     $column->string('first_name');
        //     $column->string('last_name');
        //     $column->text('biography');
        //     $column->boolean('enabled');
        //     $column->timestamps();
        // });

        // Schema::create('mans', function ($column) {
        //     $column->autoincrement('id');
        //     $column->varchar('email', 20)->index();
        //     $column->string('password');
        //     $column->string('first_name');
        //     $column->string('last_name');
        //     $column->tinyInt(2);
        //     $column->text('biography');
        //     $column->boolean('enabled');
        //     // $column->nulled()->createdBy(40);
        //     $column->createdBy(40);
        //     // $column->createdAt()->timestamp();
        //     // $column->blamable()->created();
        //     // $column->blamable()->updated();
        //     // $column->blamable()->all();
        //     $column->softDeletes();
        //     $column->timestamps();
        // });

    }

    public function down()
    {
        Schema::withSQL("DROP TABLE `tbl_users`;");
    }
}


Migration ideas

https://gist.github.com/natanfelles/4024b598f3b31db47c3e139d82dec281
https://github.com/fastworkx/ci_migrations_generator

https://stackoverflow.com/questions/29092608/how-to-use-dbforgeof-codeigniter-to-add-foreign-key

```php

$this->dbforge->add_field('id INT NOT NULL AUTO_INCREMENT PRIMARY KEY');

$this->dbforge->add_field('CONSTRAINT FOREIGN KEY (id) REFERENCES table(id)');

$this->dbforge->add_field('INDEX (deleted)');

// After table created
$this->dbforge->add_column('table',[
    'COLUMN id INT NULL AFTER field',
    'CONSTRAINT fk_id FOREIGN KEY(id) REFERENCES table(id)',
]);

```

https://stackoverflow.com/questions/21074347/create-unique-field-in-codeigniter-dbforge-migration