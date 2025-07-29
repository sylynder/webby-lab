<?php 

use Base\Migrations\Table;
use Base\Migrations\Schema;
use Base\Migrations\Migration;

class Migration_Create_books_table extends Migration
{
    public function up()
    {
        Schema::create('books', function ($column) {
           
            // $column->autoincrement('id');
            // $column->field('title', 'varchar');
            // $column->field('isbn', 'varchar');

            $column->define('id int NOT NULL AUTO_INCREMENT');
            $column->define('title VARCHAR(20) NOT NULL');
            $column->define('isbn VARCHAR(180) NOT NULL');
            $column->key('isbn');
            $column->define('PRIMARY KEY (id)');

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
        });
        

        
    }

    public function down()
    {
        Schema::dropTable('books');
    }
}
/* End of Migration_Create_books_table Migration file */
