<?php

use Base\Controllers\WebController;
use Base\Helpers\PseudoHash;
use Base\Config\Config;
use Spatie\Fork\Fork as Async;
use App\Statics\Orm;
use Base\Statics\DB;
use App\Enums\Status;
use App\Classes\MyClass;

class App extends WebController
{
	public function __construct()
	{
		parent::__construct();

		// use services, forms, libraries etc
	}

	public function indexss()
	{
		// dd($_ENV);
	}
	public function index()
	{

		// dd((time() + 10), (time() . 10));
		$hash = PseudoHash::encode("12345", 6); // e.g., "aBc12D"
		$number = PseudoHash::decode($hash);   // "12345" (string)
		
		
		// dd($hash, $number);
		// dd(time_ago('2024-08-19 19:06:00', true));

		// dd(CI_Input::class, new CI_Input(), app());
		// $file = $this->use->jsondb(ROOTPATH . 'database/storage/notes_db/books.json');
		// $composer = $file->select('*')->asObject()->get();
		// dd($composer);
		// use_db();
		// // dd($this->db);

		// $this->db->cache_on();
		// $query = $this->db->query("SELECT * FROM dbt_user");
		// $this->db->cache_off();

		// dd($query->result());

		// $arr = [
		// 	'kusi' => '200',
		// 	'kofi' => '400'
		// ];

		// $bk = (object)[
		// 	'lucy' => '100',
		// 	'jane' => '500'
		// ];

		// dr($bk, $arr);
		// $df = ['sssss'];

		// dr();
		// pr($df);


		// use_model('App/AppModel');
// dd(MyClass::class);
		// dd($this->AppModel);
		// echo $this->country2flag('gh');
		// dd();
		// dd(Orm::table('people')->where('username', 'ehackett')->get());
		// redirect('notes');
		// Config Test
		// dd(SUBDOMAIN);

		// $jsonSettings = <<<FOOBAR
		// 	{
		// 	  "application": {
		// 		"name": "configuration",
		// 		"secret": "s3cr3t"
		// 	  },
		// 	  "host": "localhost",
		// 	  "port": 80,
		// 	  "servers": [
		// 		"host1",
		// 		"host2",
		// 		"host3"
		// 	  ]
		// 	}
		// 	FOOBAR;

		// $settings = config()->use($jsonSettings, 'json');
		// dd($settings->has('application.name'), $settings['application.name']);

		// $settings = config()->use(ROOTPATH . 'config/json/config.json');
		// dd($settings->get('users'));

		// $words = config()->use(new App\Config\Words);

		// $dbconfig = (new Config)->use(App\Config\DbConfig::class);
		// $words->driver = 'word';
		// dd($words);
		// dd(config()->all());
		// dd((new Config)->use(new App\Config\Words));

		// dd(config()->all(), $this->config->all('app_status'));
		// dd($this->config);
		// config()->set('app_name', 'Kwame');
		// dd(config()->get('app_name'));


		// Output class test
		app('output')
			->withAddedHeader('HX-Location', 'home')
			->withAddedHeader('HX-Refresh', 'about')
			->withAddedHeader('Authorization: Bearer', PseudoHash::encode(1, 10))
			->withAddedHeader('HX-Trigger', json_encode([
				'head' => 'reload',
				'body' => 'about'
			]));

		// dd(app('output')->headers);	
 
		// $output = app('output');

		// $json = $output->headers[2][1];
		// app()->output->set_header('Authorization: Bearer ' . PseudoHash::encode(1, 10));
		// $request = app('input');

		// dd($request);
		// dd($output->headers, $request->getRequestHeader('HX-Refresh'));
		// dd(is_php('8.3'), is_php83());
		// start_profiler();
		 // Test binding
        app('container')->bind('test_service', function() {
            return new stdClass();
        });

		// echo travel()->to('next week tuesday');
		// dd();

		// dd(app('database'));
		// $database = app('database');
		// dd(app('test_service'));
		// dd($database);

		// dd($output->headers[2][1], is_json($json));
		$this->data['baseURL'] = $_ENV['app.baseURL'];
		return view('welcome', $this->data);
		// return view('error-view');

		// redirect()->to('home', true);


		// redirect()->to('home')->with('done', 'You are doing great');

		// route_to('home');

	}

	public function check()
	{
		return view('check');
	}
	

	public function one()
	{
		return view('water.one');
	}

	public function two()
	{
		return view('water.two');
	}

	public function send()
	{
		$this->output->enable_profiler(true);
		// $this->use->config('email');

		// return view('welcome');
		// dd(config('mailer'));

		// dd('js');
		// $this->use->library('email');

		// $config = config('mailer');
		// $this->email->initialize($config);
		// $this->email->set_newline("\r\n");

		// start_benchmark();

		// $from = 'no-reply@developerkwame.com';
		// $to = "me@developerkwame.com"; //$this->input->post('to');
		// $this->email->useragent = 'webby-labs';
		// $this->email->from($from);
		// $this->email->to($to);
		// $this->email->subject('New email from webby-labs');
		// $this->email->message('New email received from webby-labs!');

		// if ($this->email->send()) {
		// 	echo 'Sent with success!';

		// 	end_benchmark();

		// 	dd(show_time_elasped());

		// } else {
		// 	show_error($this->email->print_debugger());
		// }


// 		start_benchmark();
// 		$start_time = microtime(TRUE);
// 		$from = 'no-reply@developerkwame.com';
// 		$to = "me@developerkwame.com"; //$this->input->post('to');
// 		$this->email->useragent = 'webby-labs';
// 		$this->email->from($from);
// 		$this->email->to($to);
// 		$this->email->subject('New email from webby-labs');
// 		$this->email->message('New email received from webby-labs!');

// 		$status = Async::new()->run(
// 			fn () => $this->email->send(),
// 			// fn () => Post::all(),
// 			// fn () => News::all(),
// 		);

// 		$end_time = microtime(TRUE);

// echo PHP_EOL . "Time: " . $end_time - $start_time;
// dd($status, $end_time - $start_time);
// 		if ($status) {
// 			echo 'Sent with success!';

// 			end_benchmark();

// 			dd(show_time_elasped());

// 		} else {
// 			show_error($this->email->print_debugger());
// 		}
	}

	public function sqlite()
	{
		include VIEWPATH . 'sqlitor.php';
	}

	public function just()
	{
		echo "Just Me";
	}
	
	private function country2flag(string $countryCode): string
	{
		return preg_replace_callback(
			'/./',
			static fn (array $m) => chr(ord($m[0]) + 0x1F1A5),
			strtoupper($countryCode)
		);
	}

	public function test()
	{
		dd('sshshs');
		echo 'testing cli';
	}

	public function dynamic()
	{
		echo "dynamic subdomain";
	}

	public function learn()
	{
		echo "learn subdomain";
	}

	public function teach()
	{
		echo "teach subdomain";
	}

	public function uses()
	{
		echo "uses subdomain";
	}

	public function talk()
	{
		echo "talk subdomain";
	}

	public function learnwith()
	{
		echo "learnwith subdomain";
	}

	public function pro($version = 'one')
	{
		return view('test.ci-profiler-'. $version);
	}
}
