<?php

use App\Middleware\AppMiddleware;

class Deposit extends AppMiddleware
{
	public function __construct()
	{
		parent::__construct();
  
        if (!$this->session->userdata('isLogIn')) 
        redirect('login');

        if (!$this->session->userdata('user_id'))
        redirect('login');  
 
		$this->load->model(array(
            
            'customer/deposit_model',
            'customer/transections_model',
            'common_model',
        ));

        $this->load->library('payment');

        $globdata['coininfo'] = $this->common_model->get_coin_info();
        $this->load->vars($globdata);
	}


    public function index()
    {   
        if (!$this->session->userdata('isLogIn'))
            redirect('login');

        if ($this->session->userdata('deposit')) {
            $this->session->unset_userdata('deposit');
        }


        $data['title'] = "Deposit";

        $this->form_validation->set_rules('deposit_amount', 'deposit_amount','required|trim');
        $this->form_validation->set_rules('method', 'method','required|trim');

        $date          = new DateTime();
        $deposit_date  = $date->format('Y-m-d H:i:s');
        $coinInfo      = $this->common_model->get_coin_info();

        if ($this->form_validation->run()) 
        {

            if ($this->input->post('method')=='phone') {
                $mobiledata =  array(
                    'om_name'         => $this->input->post('om_name'),
                    'om_mobile'       => $this->input->post('om_mobile'),
                    'transaction_no'  => $this->input->post('transaction_no'),
                    'idcard_no'       => $this->input->post('idcard_no'),
                );
                $comment = json_encode($mobiledata);

            }else{
                $comment = $this->input->post('comment');

            }

            $sdata['deposit']   = (object)$userdata = array(
                'user_id'        => $this->session->userdata('user_id'),
                'amount'         => $this->input->post('deposit_amount'),
                'currency_symbol'=> $coinInfo->pair_with,
                'method'         => $this->input->post('method'),
                'fees_amount'    => $this->input->post('fees'),
                'comment'        => $comment,
                'status'         => 0,
                'deposit_date'   => $deposit_date,
                'ip'             => $this->input->ip_address()
            );

            //Store Deposit Session Data
            $this->session->set_userdata($sdata);

            redirect("customer/deposit/payment_process");
        }

        $data['coin_setup'] = $coinInfo;
        $data['payment_gateway'] = $this->common_model->payment_gateway_common();

        $data['content'] = $this->load->view('customer/pages/deposit', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  


    }

    public function payment_process()
    {
        $data['deposit'] = $this->session->userdata('deposit');

        //Payment Type specify for callback (deposit/buy/sell etc )
        $this->session->set_userdata('payment_type', 'deposit');

        $method = $data['deposit']->method;

        $data['deposit_data'] = $this->payment->payment_process($data['deposit'], $method);

        if (!$data['deposit_data']) {
            $this->session->set_flashdata('exception', 'This Gateway Deactivated');
            redirect('customer/deposit');
        }

        $data['content'] = $this->load->view('customer/pages/payment_process', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);

    }

/*
|-----------------------------------
|   View deposit list
|-----------------------------------
*/

    public function show()
    {   
        $data['title']   = display('deposit_list');
        #-------------------------------#
        #
        #pagination starts
        #
        $config["base_url"] = base_url('customer/deposit/show');
        $config["total_rows"] = $this->db->get_where('dbt_deposit', array('user_id'=>$this->session->userdata('user_id')))->num_rows();
        $config["per_page"] = 25;
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
        $data['deposit'] = $this->deposit_model->read($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
        #
        #pagination ends
        #
        $data['content'] = $this->load->view('customer/pages/deposit_list', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  
    }

}
