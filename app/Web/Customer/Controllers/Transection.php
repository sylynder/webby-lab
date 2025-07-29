<?php

use App\Middleware\AppMiddleware;

class Transection extends AppMiddleware
{
	public function __construct()
	{
		parent::__construct();
  
        if (!$this->session->userdata('isLogIn')) 
        redirect('login');

        if (!$this->session->userdata('user_id')) 
        redirect('login');  
 
		$this->load->model(array(
            'customer/transections_model', 
            'customer/Profile_model', 
        ));

	}

    public function index()
    { 
        $data['title']   = display('transection');
        $data = $this->transections_model->transections_all_sums();

        $config["base_url"] = base_url('customer/transection/index');
        $config["total_rows"] = $this->db->select('*')
        ->from('dbt_balance_log')
        ->where('user_id', $this->session->userdata('user_id'))
        ->get()->num_rows();
        $config["per_page"] = 15;
        $config["uri_segment"] = 4;
        $config["last_link"] = "Last"; 
        $config["first_link"] = "First"; 
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';  
        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

        $data['transection'] = $this->transections_model->all_transection($config["per_page"], $page);
        $data["links"]      = $this->pagination->create_links();
        $data['coin_setup'] = $this->transections_model->get_coin_info();
        $data['content']    = $this->load->view('customer/pages/transection', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);
    
    }

    public function transection_details($id=NULL,$table=NULL)
    { 


        $data['title']   = display('transection_details');
        //$data['my_info'] = $this->Profile_model->my_info(); 
        //$data['transection'] = $this->transections_model->transection_by_id($id,$table);
        $data['content'] = $this->load->view('customer/pages/transection', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  
    
    }    

}
