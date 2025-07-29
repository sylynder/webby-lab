<?php

use Base\Controllers\WebController;
use App\Statics\Model as Book;
use App\Statics\Model as App;
use App\Statics\Orm;

class Test extends WebController
{
	public function __construct()
	{
		parent::__construct();
		// use_db();
		$this->use->database();
		// use services, forms, libraries etc
		$this->use->model('App/AppModel');
	}

	public function index()
	{
		dd('in-test');
		Book::use(\App\Models\School\BookModel::class);
		App::use('App/AppModel');
		$books = Orm::table('books')->limit(2)->get();
		dd($books, App::get(1), Book::get(2));
		// dd($books);
		// $app = Model::use('App/AppModel');

		dd(App::get(1), Book::get(2));
		// dd($books, $app, Model::get(2));
		// use_model('');
		// StaticModel::useModel('App/AdModel');
        // StaticModel::get(2);
		// dd(StaticModel::get(2));
		$books = Orm::table('books')->get();
		dd($books);
        // dd(StaticModel::get(2), AppStaticModel::get(3), $this->AppModel->get(1));
        // dd($this->app->limit(2)->get());
	}

	public function form()
	{
		// dd(ip_address());
		if (is('post')) {
			// dd(post());

			$fullname = $this->input->index('fullname');

			dd($this->input->fullname);
		}

		if (is('get')) {

			$url = current_url(true);
			// $name = $this->input->index('name');
			$name = $this->request->name;
			$job = $this->request->job;

			dd($url, $name, $job, $this->request->ip_address);
		}

		return view('form');
	}

}