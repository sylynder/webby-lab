<?php

use App\Middleware\AppMiddleware;

class Transfer extends AppMiddleware
{
    
	public function __construct()
	{
		parent::__construct();
  
        if (!$this->session->userdata('isLogIn')) 
        redirect('customer'); 
 
		$this->load->model(array(
            'common_model',
            'customer/Profile_model',
            'customer/transfer_model',
            'customer/transections_model',
            'payment_model'
             
        ));
        $this->load->library('sms_lib');

        $globdata['coininfo'] = $this->common_model->get_coin_info();
        $this->load->vars($globdata);

	}

    /*
    |-----------------------------------
    |   View transfer 
    |-----------------------------------
    */
    public function index()
    {   
         $data['title']   = display('transfer'); 
         $data['content'] = $this->load->view('customer/transfer/transfar', $data, true);
         $this->load->view('customer/layout/main_wrapper', $data);  
    }

    /*
    |-----------------------------------
    |   View transfer list
    |-----------------------------------
    */
    public function transfer_list()
    {
        $user_id = $this->session->userdata('user_id');
        $data['title']   = display('transfer_list');
        #-------------------------------#
        #
        #pagination starts
        #
        $config["base_url"] = base_url('customer/transfer/transfar/transfer_list');
        $config["total_rows"] = $this->db->select('dbt_transfer.*,dbt_user.*')
        ->from('dbt_transfer')
        ->join('dbt_user','dbt_user.user_id=dbt_transfer.receiver_user_id')
        ->where('dbt_transfer.sender_user_id',$user_id)
        ->or_where('dbt_transfer.receiver_user_id',$user_id)
        ->order_by('dbt_transfer.date',"DESC")
        ->get()->num_rows();
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
        $data['transfer'] = $this->db->select('dbt_transfer.id as transfer_id,dbt_transfer.*,dbt_user.*')
        ->from('dbt_transfer')
        ->join('dbt_user','dbt_user.user_id=dbt_transfer.receiver_user_id')
        ->where('dbt_transfer.sender_user_id',$user_id)
        ->or_where('dbt_transfer.receiver_user_id',$user_id)
        ->order_by('dbt_transfer.date',"DESC")
        ->limit($config["per_page"], $page)
        ->get()
        ->result();
        
        $data["links"] = $this->pagination->create_links();
        #
        #pagination ends
        #
        $data['content'] = $this->load->view('customer/transfer/transfar_list', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  
    }



    public function send_details($id=NULL)
    {

        $data['title']   = display('transfar_recite'); 
        $data['send'] = $this->transfer_model->get_send($id);
        $data['my_info'] = $this->Profile_model->my_info();
        $data['content'] = $this->load->view('customer/transfer/send_recite', $data, true);
        
        $this->load->view('customer/layout/main_wrapper', $data);

    }


    public function receive_details($id=NULL)
    {

        $data['title']   = display('transfar_recite'); 
        $data['send']    = $this->transfer_model->get_recieved($id);
        $data['my_info'] = $this->Profile_model->my_info();

        $data['content'] = $this->load->view('customer/transfer/recived_recite', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);
    
    }



    /*
    |-----------------------------------
    |   transfer  submit
    |-----------------------------------
    */

    public function store()
    {

        $this->form_validation->set_rules('receiver_id', display('receiver_id'), 'required'); 
        $this->form_validation->set_rules('amount', display('amount'), 'required'); 
        $this->form_validation->set_rules('varify_media', 'OTP Send To', 'required'); 

        if($this->form_validation->run()){

            


            $varify_media = $this->input->post('varify_media');
            $receiver_id = $this->input->post('receiver_id');
            $amount = $this->input->post('amount');
            $varify_code = $this->randomID();

            $appSetting = $this->common_model->get_setting();

            $userinfo = $this->transfer_model->retriveUserInfo();

            // check balance            
            $fees_val = $this->transfer_model->checkFees('TRANSFER');
            $blance = $this->transfer_model->checkBalance();

            //Fees in Percent
            $fees = ($amount/100)*@$fees_val->fees;


            #----------------------------
            if($blance->balance < $amount+$fees){

                $this->session->set_flashdata('exception', display('balance_is_unavailable'));
                redirect('customer/transfer');

            } else {
                
                if($varify_media==2){

                    #----------------------------
                    #      email verify smtp mail
                    #----------------------------
                    $post = array(
                        'title'             => $appSetting->title,
                        'subject'           => 'Transfer Verification!',
                        'to'                => $this->session->userdata('email'),
                        'message'           => 'You are about to transfar $'.$amount.' to the account '.$receiver_id.'. 
                        Your code is <h1>'.$varify_code.'</h1>',
                    );
                    $code_send = $this->common_model->send_email($post);

                    #-----------------------------

                } else {
                    
                    #----------------------------
                    #      SMS verify
                    #----------------------------
                    $this->load->library('sms_lib');
                    $template = array( 
                        'name'          => $this->session->userdata('fullname'),
                        'amount'        => $amount,
                        'receiver_id'   => $receiver_id,
                        'code'          => $varify_code
                    );

                    if ($userinfo->phone) {
                        $code_send = $this->sms_lib->send(array(
                            'to'                => $userinfo->phone, 
                            'template'          => 'You are about to transfar $%amount%, to the account %receiver_id%, Your code is %code%',
                            'template_config'   => $template,
                        ));

                    }else{

                        $this->session->set_flashdata('exception', "There is no Phone number!!!");

                    }

                }


                if(isset($code_send)){                    

                    $transfar = array(
                        'sender_user_id' => trim($this->session->userdata('user_id')),
                        'receiver_user_id' => trim($this->input->post('receiver_id')),
                        'amount' => $this->input->post('amount'),
                        'fees' => $fees,
                        'request_ip' => $this->input->ip_address(),
                        'date' => date('Y-m-d h:i:s'),
                        'comments' => $this->input->post('comments'),
                        'status' => 1,

                    );

                    $varify_data = array(
                        'ip_address' => $this->input->ip_address(),
                        'user_id' => $this->session->userdata('user_id'),
                        'session_id' => $this->session->userdata('isLogIn'),
                        'verify_code' => $varify_code,
                        'data' => json_encode($transfar)

                    );

                    $result = $this->transfer_model->verify($varify_data);
                                
                    redirect('customer/transfer/transfer_confirm/'.$result['id']);
                    
                } 
            }

        }



        $data['title']   = display('transfar'); 
        $data['content'] = $this->load->view('customer/transfer/transfar', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data); 

    }


/*
|---------------------------------
|   Fees Load and deposit amount 
|---------------------------------
*/
    public function fees_load($amount=null,$level = "")
    {   

        $result = $this->db->select('*')
        ->from('dbt_fees')
        ->where('level',$level)
        ->get()
        ->row();

       return $fees = ($amount/100)*$result->fees;
        
    }

    /*
    |-----------------------------------
    |   transfer verify
    |-----------------------------------
    */

    public function transfer_confirm($id = null)
    {

        
        $data['v'] = $this->transfer_model->get_verify_data($id);

        if($data['v']!=NULL){
            $data['title']   = display('confirm_transfer');

        } else {

            redirect('customer/transfer');
        }



        $receiver_id = json_decode($data['v']->data);
        $data['user'] = $this->db->select('*')->from('dbt_user')->where('user_id', $receiver_id->receiver_user_id)->get()->row();


        $data['title']   = display('transfar'); 
        $data['content'] = $this->load->view('customer/transfer/confirm_transfer', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data); 

    }


    /*
    |-----------------------------------
    |   verify the code
    |-----------------------------------
    */
    public function transfer_verify()
    {


    $code = $this->input->post('code');
    $id = $this->input->post('id');

        $data = $this->db->select('*')
        ->from('dbt_verify')
        ->where('verify_code',$code)
        ->where('id',$id)
        ->where('session_id',$this->session->userdata('isLogIn'))
        ->where('status', 1)
        ->get()
        ->row();

        $userinfo = $this->transfer_model->retriveUserInfo();

        if($data!=NULL) {

        $t_data = ((array) json_decode($data->data));

        //Sender Balance Update
        $check_user_balance = $this->db->select('*')->from('dbt_balance')->where('user_id', $this->session->userdata('user_id'))->get()->row();
        $new_balance = $check_user_balance->balance-($t_data['amount']+(float)@$t_data['fees']);
        $this->db->set('balance', $new_balance)->where('user_id', $this->session->userdata('user_id'))->update("dbt_balance");


        //User Financial Log
        $transfertdata = array(
            'user_id'            => $t_data['sender_user_id'],
            'balance_id'         => $check_user_balance->id,
            'transaction_type'   => 'TRANSFER',
            'transaction_amount' => $t_data['amount'],
            'transaction_fees'   => $t_data['fees'],
            'ip'                 => $t_data['request_ip'],
            'date'               => $t_data['date']
        );

        $this->payment_model->balancelog($transfertdata);

        //Recever Balance Update
        $check_recever_balance = $this->db->select('*')->from('dbt_balance')->where('user_id', $t_data['receiver_user_id'])->get()->row();
        if ($check_recever_balance) {
            $new_balance_recever = @$check_recever_balance->balance+$t_data['amount'];
            $this->db->set('balance', $new_balance_recever)->where('user_id', $t_data['receiver_user_id'])->update("dbt_balance");


            //Recever Financial Log
            $receiveddata = array(
                'user_id'            => $t_data['receiver_user_id'],
                'balance_id'         => $check_recever_balance->id,
                'transaction_type'   => 'RECEIVED',
                'transaction_amount' => $t_data['amount'],
                'transaction_fees'   => $t_data['fees'],
                'ip'                 => $t_data['request_ip'],
                'date'               => $t_data['date']
            );

            $this->payment_model->balancelog($receiveddata);


        }else{

             $transfar_recever = array(
                        'user_id' => $t_data['receiver_user_id'],
                        'balance' => $t_data['amount'],
                        'last_update' => date('Y-m-d h:i:s'),

                    );

            $recever_balance_id = $this->transfer_model->balanceAdd($transfar_recever);

            //Recever Financial Log
            $receiveddata = array(
                'user_id'            => $t_data['receiver_user_id'],
                'balance_id'         => $recever_balance_id,
                'transaction_type'   => 'RECEIVED',
                'transaction_amount' => $t_data['amount'],
                'transaction_fees'   => $t_data['fees'],
                'ip'                 => $t_data['request_ip'],
                'date'               => $t_data['date']
            );

            $this->payment_model->balancelog($receiveddata);

        }


        $result = $this->transfer_model->transfer($t_data);




        $appSetting = $this->common_model->get_setting();

            $transections_data = array(
                'user_id'                   => $this->session->userdata('user_id'),
                'transection_category'      => 'TRANSFER',
                'releted_id'                => $result['id'],
                'amount'                    => $t_data['amount'],
                'comments'                  => $t_data['comments'],
                'transection_date_timestamp'=> date('Y-m-d h:i:s')
            );

            $transections_reciver_data = array(
                'user_id'                   => $t_data['receiver_user_id'],
                'transection_category'      => 'RECEIVED',
                'releted_id'                => $result['id'],
                'amount'                    => $t_data['amount'],
                'comments'                  => $t_data['comments'],
                'transection_date_timestamp'=> date('Y-m-d h:i:s')
            );

            // $this->transections_model->save_transections($transections_data);
            
            // $this->transections_model->save_transections($transections_reciver_data);

            $this->db->set('status',0)
            ->where('id', $id)
            ->where('session_id',$this->session->userdata('isLogIn'))
            ->update('dbt_verify');
           


            $set_email = $this->common_model->email_sms('email');
            if($set_email->transfer!=NULL){

                //$balance = $this->transections_model->get_cata_wais_transections();
                #----------------------------
                #      email verify smtp
                #----------------------------
                 $post = array(

                    'title'           => $appSetting->title,
                    'subject'           => display('transfer'),
                    'to'                => $this->session->userdata('email'),
                    'message'           => 'You successfully transfer The amount $'.$t_data['amount'].' to the account '.$t_data['receiver_user_id'].'. Your new balance is $'.$new_balance
                   
                );
                $send_email = $this->common_model->send_email($post);

                if($send_email){

                    $n = array(
                        'user_id'                => $this->session->userdata('user_id'),
                        'subject'                => display('transfer'),
                        'notification_type'      => 'TRANSFER',
                        'details'                => 'You successfully transfer The amount $'.$t_data['amount'].' to the account '.$t_data['receiver_user_id'].'. Your new balance is $'.$new_balance,
                        'date'                   => date('Y-m-d h:i:s'),
                        'status'                 => '0'
                    );
                    $this->db->insert('notifications',$n);    
                }
            }

            $set_sms = $this->common_model->email_sms('sms');
            if($set_sms->transfer!=NULL){
                #------------------------------
                #   SMS Sending Confirmation
                #------------------------------
                $template = array( 
                    'name'      => $this->session->userdata('fullname'),
                    'amount'    =>$t_data['amount'],
                    'new_balance'=>$new_balance,
                    'receiver_id'=>$t_data['receiver_user_id'],
                    'date'      => date('d F Y')
                );

                if ($userinfo->phone) {
                    $send_sms = $this->sms_lib->send(array(
                        'to'              => $userinfo->phone, 
                        'subject'         => 'Transfer', 
                        'template'        => 'You successfully transfer the amount $%amount% to the account %receiver_id%. Your balence is $%new_balance%.', 
                        'template_config' => $template, 
                    ));

                }else{

                    $this->session->set_flashdata('exception', "There is no Phone number!!!");

                }

                if($send_sms){

                    $message_data = array(
                        'sender_id' =>1,
                        'receiver_id' => $this->session->userdata('user_id'),
                        'subject' => 'Transfer',
                        'message' => 'You successfully transfer the amount $'.$t_data['amount'].' to the account '.$t_data['receiver_user_id'].'. Your new balance is $'.$new_balance,
                        'datetime' => date('Y-m-d h:i:s'),
                    );

                    $this->db->insert('message',$message_data);    

                }                

            }

        echo $id;
        //echo $result['id'];


        } else {

            echo '';

        }
        
    }


    public function transfer_details($id=NULL)
    {

        $data['my_info'] = $this->transfer_model->retriveUserInfo();

        $data['v'] = $this->db->select('*')->from('dbt_verify')->where('id', $id)->where('session_id', $this->session->userdata('isLogIn'))->where('user_id', $this->session->userdata('user_id'))->where('status', 0)->get()->row();

        if($data['v']!=NULL){

            $datas = (json_decode($data['v']->data)); 
            $data['u'] =$this->db->select('user_id,first_name,last_name,email,phone')->from('dbt_user')->where('user_id',@$datas->receiver_user_id)->get()->row();
        }
        
        $data['coininfo'] = $this->common_model->get_coin_info();
        $data['content'] = $this->load->view('customer/transfer/transfer_details', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);

    }


    /*
    |-----------------------------------
    |   Balance Check
    |-----------------------------------
    */

    public function transfer_recite($id=NULL)
    {

       $data = $this->transfer_model->get_verify_data($id);



        $data['title']   = display('transfar_recite'); 
        $data['my_info'] = $this->Profile_model->my_info();
        $data['content'] = $this->load->view('customer/transfer/transfer_recite', $data, true);
        
        $this->load->view('customer/layout/main_wrapper', $data);
    }



    /*
    |-----------------------------------
    |   Balance Check
    |-----------------------------------
    */

    public function check_balance($amount=NULL)
    {
        $data = $this->transections_model->get_cata_wais_transections();
        $amount += $this->fees_load($amount,'transfer');

        if($amount < $data['balance']){
            $new = $data['balance']-$amount;
            return $balence = array('new_balance'=>$new,'balance'=>$data['balance']);
        } else {
            return false;
        }
    }

    /*
    |----------------------------------------------
    |        id genaretor
    |----------------------------------------------     
    */
    public function randomID($mode = 2, $len = 6)
    {
        $result = "";
        if($mode == 1):
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        elseif($mode == 2):
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        elseif($mode == 3):
            $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        elseif($mode == 4):
            $chars = "0123456789";
        endif;

        $charArray = str_split($chars);
        for($i = 0; $i < $len; $i++) {
                $randItem = array_rand($charArray);
                $result .="".$charArray[$randItem];
        }
        return $result;
    }
    /*
    |----------------------------------------------
    |         Ends of id genaretor
    |----------------------------------------------
    */

}