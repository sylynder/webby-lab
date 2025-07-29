<?php

use Base\Controllers\WebController;
use App\Statics\Response;
use App\Statics\Request;
use App\Statics\Orm;
use App\Classes\MyClass;
use Base\Models\EasyModel;

class TestOne extends WebController
{
	public function __construct()
	{
		parent::__construct();

		use_db();
		// use services, forms, libraries etc
	}

	public function index()
	{
		$db = new EasyModel;
		use_model('Auth/UserModel');
		$this->UserModel->table = 'users';
		dd($this->UserModel->get());
		$db->table('users')->get();
		dd($db);
		$table = Orm::get('users');
dd($table);
		dd(Orm::get());

		dd(Request::has([
			'works' => 'nice'
		], 200));

		dd(Response::json([
			'works' => 'nice'
		], 200));
		// Callback response log on => {
		// 	"ResponseCode": "0000",
		// 	"Message": "success",
		// 	"Data": {
		// 	  "Amount": 0.02,
		// 	  "Charges": 0.01,
		// 	  "AmountAfterCharges": 0.01,
		// 	  "Description": "The MTN Mobile Money payment has been approved and processed successfully.",
		// 	  "ClientReference": "TSN001",
		// 	  "TransactionId": "74424be8b5064903938aec5221063149",
		// 	  "ExternalTransactionId": "31012408751",
		// 	  "AmountCharged": 0.02,
		// 	  "OrderId": "74424be8b5064903938aec5221063149"
		// 	}
		//   }

		// Mimicing Json Data
		$string = '{
			"ResponseCode": "0000",
			"Message": "success",
			"Data": {
			  "Amount": 0.02,
			  "Charges": 0.01,
			  "AmountAfterCharges": 0.01,
			  "Description": "The MTN Mobile Money payment has been approved and processed successfully.",
			  "ClientReference": "TSN001",
			  "TransactionId": "74424be8b5064903938aec5221063149",
			  "ExternalTransactionId": "31012408751",
			  "AmountCharged": 0.02,
			  "OrderId": "74424be8b5064903938aec5221063149"
			}
		  }';
		  
		// dd($string, is_json($string));

		$get = json_decode($string, true);
		
		// echo "<pre>";

		// var_export($get); 
		// var_export($get->Data); 
		
		// die();
		dd($get);


		$defdate = date('Y-m-d H:i:s');
		$original_date = new DateTime($defdate, new DateTimeZone('UTC'));
		$original_date->setTimeZone(new DateTimeZone('Africa/Accra'));
		$mydate = $original_date->format('Y-m-d H:i:s');

		$recieveddata =  file_get_contents('php://input'); //get callback response from Redde
		//include("scripts/mfin_conn.php");
		//decode the data and proccess it

		//log data into appropriate text file
		// @file_put_contents('logs.txt',"Callback response log on ". $time ."=> ". $recieveddata . "\n",FILE_APPEND);
		// @file_put_contents('Transactions.txt',"Callback response on ".$time ."=> ". $recieveddata. "\n" ,FILE_APPEND);
		/*	alllogs_log("Callback response log on " , $time , $recieveddata);  //Create the log folder and write the logs according to their date
			alllogs_log("Callback response log on " , $time , $recieveddata2);  //Create the log folder and write the logs according to their date
			alllogs_log("Callback response log on " , $time , $recieveddata3);  //Create the log folder and write the logs according to their date
		*/
		$defdate = date('Y-m-d H:i:s');
		$original_date = new DateTime($defdate, new DateTimeZone('UTC'));
		$original_date->setTimeZone(new DateTimeZone('Africa/Accra'));
		$mydate = $original_date->format('Y-m-d H:i:s');

		$recieveddata2 = json_decode($recieveddata, true); // this will give an array

		foreach ($recieveddata2 as $data) {
			$reason = $data['ResponseCode'];
			$shika = $data['Data'][0]['Amount'];
			//echo $data['weather'][0]['date'];
		}
		return view('welcome');
		// redirect('home');
	}

	public function config()
	{

		// dd((object)config()->config);
		// $d = $config::use();
		// dd($d->enable_hooks);
		// dd($config::use()->base_url);
		// dd(config()->add(['try' => 'use']));
		$use = config()->add(\App\Config\DbConfig::class);
		$use->index_page = 'index';

		dd($use);
dd($use->base_url);
		dd(config()->config['base_url']);
		// return view('welcome');
		// return view('error-view');
		redirect('home');
	}


	public function test()
	{
		dd('sshshs');
		echo 'testing cli';
	}
}
