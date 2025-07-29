<?php
/**
 * PROJECT NAME
 * Version:
 * Author: Mahdi Hezaveh
 * Copyright: 2022 Mahdi Hezaveh
 *
 * Email: mahdi.hezavehei@gmail.com
 * URL: https://asapit.ca
 * License: MIT License (https://opensource.org/licenses/MIT)
 *
 * Date: 28-Sep-2022
 * Time: 7:10 PM
 *
 * Description:
 *
 *
 */

use Base\Controllers\WebController;

defined('BASEPATH') or exit('No direct script access allowed');

class Logs extends WebController {

	private $log;

	public function __construct()
	{
		parent::__construct();

		$this->use->library('CILogViewer');
		$this->log = new CILogViewer();

	}

	public function index()
	{
		// http://localhost:8064/logs?rt=5000
		// Todo: [*** hezaveh ***] add environment if ....

		$data = $this->log->getLogData();

		return view('Logs/logs_view', $data);
	}

	public function apps()
	{
		$this->log->useLog('app');

		$data = $this->log->getLogData();

		return view('Logs/logs_view', $data);
	}

	public function users()
	{
		$this->log->useLog('user');
		
		$data = $this->log->getLogData();

		return view('Logs/logs_view', $data);
	}
}