<?php

use App\Middleware\AppMiddleware;

class Verify_account extends AppMiddleware
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
            'website/web_model',
        ));

        
	}


    public function index()
    {   


    	$data['title']   = "Verify Account";

    	$date = new DateTime();
        $submit_time = $date->format('Y-m-d H:i:s');

        $this->form_validation->set_rules('verify_type', 'verify_type','required|trim');
        $this->form_validation->set_rules('first_name', 'first_name','required|trim');
        $this->form_validation->set_rules('last_name', 'last_name','required|trim');
        $this->form_validation->set_rules('gender', 'gender','required|trim');
        $this->form_validation->set_rules('id_number', 'id_numder','required|trim');

        $user_id = $this->session->userdata('user_id');
        

        //From Validation Check
        if ($this->form_validation->run()) 
        {
            //Set Upload File Config 
            $config = [
                'upload_path'       => 'upload/documents/',
                'allowed_types'     => 'jpg|png|jpeg', 
                'overwrite'         => false,
                'maintain_ratio'    => true,
                'encrypt_name'      => true,
                'remove_spaces'     => true,
                'file_ext_tolower'  => true 
            ];

            $document1="";
            $document2="";

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('document1')) {  
                $data = $this->upload->data();  
                $document1 = $config['upload_path'].$data['file_name'];

            }
            if ($this->upload->do_upload('document2')) {  
                $data = $this->upload->data();  
                $document2 = $config['upload_path'].$data['file_name'];

            }


            $data['verify_info']   = (object)$verify_info = array(
                'user_id'     => $this->session->userdata('user_id'),
                'verify_type' => $this->input->post('verify_type'), 
                'first_name'  => $this->input->post('first_name'),
                'last_name'   => $this->input->post('last_name'),
                'gender'      => $this->input->post('gender'),
                'id_number'   => $this->input->post('id_number'),
                'document1'   => $document1,
                'document2'   => $document2,
                'date'        => $submit_time
            );

            if ($this->web_model->userVerifyDataStore($verify_info)) {

                //Update User table for Verify Processing
                $this->db->set('verified', '3')->where('user_id', $this->session->userdata('user_id'))->update("dbt_user");
                $this->session->set_flashdata('message', "Verification Is being Processed");

            } else {
                $this->session->set_flashdata('exception', display('please_try_again'));

            }

            redirect("customer/verify_account");


        }

        $data['verify_status'] = $this->db->select('verified')
                                    ->from('dbt_user')
                                    ->where('user_id',$user_id)
                                    ->get()
                                    ->row();
                                    
        $data['content'] = $this->load->view('customer/pages/verify_account', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);
    
    }


}