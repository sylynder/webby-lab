<?php defined('BASEPATH') OR exit('No direct script access allowed');

use App\Middleware\AppMiddleware;
class Cron extends AppMiddleware
{

	public function __construct()
	{
		parent::__construct();
		// if (!$this->input->is_cli_request()) {
		// 	show_error('Direct access is not allowed');
		// }
	}

	public function run()
	{
		
		$this->load->library('CronRunner');
		$cron = new CronRunner();
		$cron->run();
	}

}