<?php

use App\Middleware\AppMiddleware;
class Pageexcept extends AppMiddleware {
 	
 	public function __construct()
 	{
    	parent::__construct();
    	if (!$this->session->userdata('isLogIn')) 
            redirect('login');

            if (!$this->session->userdata('user_id')) 
            redirect('login');
 	}

	public function index()
	{
		$data['title']	 = "Page Exception";
		$data['content'] = $this->load->view("customer/pages/pageexcept", $data, true);
		$this->load->view("customer/layout/main_wrapper", $data);
	}

}