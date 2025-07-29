<?php 

use Base\Controllers\WebController;

class Hx extends WebController
{
    public function __construct()
    {
        parent::__construct();
        
        // $this->use->database(); // enable to use database

    }

    public function index()
    {
        return view('hx.index');
    }

    public function users()
    {

        // dd(request()->getPost('limit'));

        $users = [
            [
                'id' => 1,
                'name' => "Oteng Kwame",
            ],
            [
                'id' => 2,
                'name' => "Afia Yeboah",
            ],
            [
                'id' => 3,
                'name' => "Jose Amuni Kwame",
            ]
            ];

        // return response()->json($users);
        sleep(2);

        return view('hx.users', compact('users'), true);
    }
}
/* End of Hx file */
