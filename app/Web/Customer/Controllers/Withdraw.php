<?php

use App\Middleware\AppMiddleware;

class Withdraw extends AppMiddleware
{
    public function __construct()
    {
        parent::__construct();
  
        if (!$this->session->userdata('isLogIn')) 
        redirect('login');

        if (!$this->session->userdata('user_id')) 
        redirect('login'); 
 
        $this->load->model(array(
            'customer/profile_model',
            'customer/withdraw_model',
            'customer/transections_model',
            'customer/transfer_model',
            'common_model',
            'payment_model',
        ));
        
        $this->load->library("payment");

        $globdata['coininfo'] = $this->common_model->get_coin_info();
        $this->load->vars($globdata);

    }


    public function index()
    {   

        $data['title']   = display('withdraw');


        $this->form_validation->set_rules('amount', display('amount'), 'required');
        $this->form_validation->set_rules('varify_media', display('otp_send_to'), 'required');
        if($this->input->post('method')=='coinpayment')
        {
            $this->form_validation->set_rules('walet_address', 'Your Address', 'required');
        }
        else{
            $this->form_validation->set_rules('walletid', display('wallet_id'), 'required');
        }

        if($this->form_validation->run()){

            $amount         = $this->input->post('amount');
            $varify_media   = $this->input->post('varify_media');
            $walletid       = $this->input->post('walletid');

            $appSetting = $this->common_model->get_setting();

            $varify_media = $this->input->post('varify_media');
            $varify_code = $this->randomID();
            
            $userinfo = $this->withdraw_model->retriveUserInfo();

            // check balance            
            $fees_val = $this->withdraw_model->checkFees('WITHDRAW');
            $blance = $this->withdraw_model->checkBalance();

            //Fees in Percent
            $fees = ($amount/100)*@$fees_val->fees;

            #----------------------------
            if($blance->balance < $amount+$fees){

                $this->session->set_flashdata('exception', display('balance_is_unavailable'));
                redirect('customer/withdraw');

            } else {

                if($varify_media==2){

                #----------------------------
                #      email verify smtp
                #----------------------------
                $post = array(
                    'title'             => $appSetting->title,
                    'subject'           => 'Withdraw Verification!',
                    'to'                => $this->session->userdata('email'),
                    'message'           => 'You Withdraw The Amount Is '.$this->input->post('amount').'. The Verification Code is <h1>'.$varify_code.'</h1>',
                );
                $code_send = $this->common_model->send_email($post);

               
                } else {
                    #----------------------------
                    #      Sms verify
                    #----------------------------                    
                    $this->load->library('sms_lib');

                    $template = array( 
                        'name'      => $this->session->userdata('fullname'),
                        'amount'    => $this->input->post('amount'),
                        'code'      => $varify_code,
                        'date'      => date('d F Y')
                    );

                    if ($userinfo->phone) {
                        $code_send = $this->sms_lib->send(array(
                            'to'       => $userinfo->phone, 
                            'template' => 'Hello! %name% You Withdraw The Amount Is %amount%, The Verification Code is %code% ', 
                            'template_config' => $template, 
                        ));

                    }else{

                        $this->session->set_flashdata('exception', "There is no Phone number!!!");

                    }
 
                }


                if(isset($code_send)){

                    // get withdraw fees                    

                    if($this->input->post('method')=="coinpayment"){
                        $wallet_id = $this->input->post('walet_address');
                    }
                    else{
                        $wallet_id = $this->input->post('walletid');
                    }

                    $withdraw = array(
                        'user_id'   => $this->session->userdata('user_id'),
                        'wallet_id' => $wallet_id,
                        'amount'    => $this->input->post('amount'),
                        'method' => $this->input->post('method'),
                        'fees_amount' => $fees,                        
                        'comment' => '',    
                        'request_date' => date('Y-m-d h:i:s'),                    
                        'status' => 2,                    
                        'ip' => $this->input->ip_address(),
                    );


                    $varify_data = array(

                        'ip_address' => $this->input->ip_address(),
                        'user_id' => $this->session->userdata('user_id'),
                        'session_id' => $this->session->userdata('isLogIn'),
                        'verify_code' => $varify_code,
                        'data' => json_encode($withdraw)

                    );

                    $result = $this->withdraw_model->verify($varify_data);
                 
                    redirect('customer/withdraw/withdraw_confirm/'.$result['id']);
                }  

            }     

        }


        $data['payment_gateway'] = $this->common_model->payment_gateway_common();
        $data['content'] = $this->load->view('customer/pages/withdraw', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  
    }


    public function withdraw_list()
    {
        $user_id = $this->session->userdata('user_id');

        $data['title']   = display('withdraw_list'); 

       /*******************************
        *pagination starts
        ******************************/

        $config["base_url"]         = base_url('customer/withdraw/withdraw_list');
        $config["total_rows"]       = $this->db->get_where('dbt_withdraw', array('user_id'=>$user_id))->num_rows();
        $config["per_page"]         = 25;
        $config["uri_segment"]      = 4;
        $config["last_link"]        = "Last"; 
        $config["first_link"]       = "First"; 
        $config['next_link']        = 'Next';
        $config['prev_link']        = 'Prev';  
        $config['full_tag_open']    = "<ul class='pagination col-xs pull-right'>";
        $config['full_tag_close']   = "</ul>";
        $config['num_tag_open']     = '<li>';
        $config['num_tag_close']    = '</li>';
        $config['cur_tag_open']     = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close']    = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open']    = "<li>";
        $config['next_tag_close']   = "</li>";
        $config['prev_tag_open']    = "<li>";
        $config['prev_tagl_close']  = "</li>";
        $config['first_tag_open']   = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open']    = "<li>";
        $config['last_tagl_close']  = "</li>";
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data['withdraw'] = $this->db->select("*")
                            ->from('dbt_withdraw')
                            ->where('user_id',$user_id)
                            ->limit($config["per_page"], $page)
                            ->get()
                            ->result();
        $data["links"] = $this->pagination->create_links();
        /*******************************
        *pagination ends
        ******************************/

        $data['content'] = $this->load->view('customer/withdraw/withdraw_list', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  

    }


    // public function store()
    // {

        

    //     $data['payment_gateway'] = $this->common_model->payment_gateway_common();

    //     $data['title']   = display('withdraw'); 
    //     $data['content'] = $this->load->view('customer/pages/withdraw', $data, true);
    //     $this->load->view('customer/layout/main_wrapper', $data);  

    // }


    /*
    |-----------------------------------
    |   confirm_withdraw
    |-----------------------------------
    */

    public function withdraw_confirm($id = null)
    {

       $data['v'] = $this->withdraw_model->get_verify_data($id);

        if($data['v']!=NULL){

            $data['title']   = display('confirm_withdraw');
            
        } else {
            redirect('customer/withdraw');
        }

        $data['title']   = display('withdraw'); 
        $data['content'] = $this->load->view('customer/pages/confirm_withdraw', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);

    }    

    /*
    |-----------------------------------
    |   verify the code
    |-----------------------------------
    */
        public function withdraw_verify()
    {

        $code   = $this->input->post('code');
        $id     = $this->input->post('id');

        // check verify code
        $data = $this->db->select('*')
        ->from('dbt_verify')
        ->where('verify_code',$code)
        ->where('id', $id)
        ->where('session_id',$this->session->userdata('isLogIn'))
        ->where('status',1)
        ->get()
        ->row();

        $userinfo = $this->withdraw_model->retriveUserInfo();


        if($data!=NULL) {


            $t_data = ((array) json_decode($data->data));

            $result = $this->withdraw_model->withdraw($t_data);

            $wdstatus  = $this->withdraw_model->coinpayment_withdraw();
            if($t_data['method']== "coinpayment" && $wdstatus==1){
                 
                 $method = $t_data['method'];
                 $this->payment->payment_withdraw($t_data,$method);
            }
            else{

                //$set = $this->common_model->email_sms('email');
                $appSetting = $this->common_model->get_setting();

                //if($set->withdraw!=NULL){
                    
                    $check_user_balance = $this->db->select('*')->from('dbt_balance')->where('user_id', $this->session->userdata('user_id'))->get()->row();
                    $new_balance = $check_user_balance->balance-$t_data['amount'];


                    $this->db->set('balance', $new_balance)->where('user_id', $this->session->userdata('user_id'))->update("dbt_balance");

                    //User Financial Log
                    if ($check_user_balance) {

                        $depositdata = array(
                            'user_id'            => $t_data['user_id'],
                            'balance_id'         => $check_user_balance->id,
                            'transaction_type'   => 'WITHDRAW',
                            'transaction_amount' => $t_data['amount'],
                            'transaction_fees'   => $t_data['fees_amount'],
                            'ip'                 => $t_data['ip'],
                            'date'               => $t_data['request_date']
                        );

                        $this->payment_model->balancelog($depositdata);

                    }


                    #----------------------------
                    #      email verify smtp
                    #----------------------------
                    $post = array(
                        'title'             => $appSetting->title,
                        'subject'           => 'Withdraw',
                        'to'                => $this->session->userdata('email'),
                        'message'           => 'You successfully withdraw the amount Is $'.$t_data['amount'].'. from your account. Your new balance is $'.$new_balance,
                    );
                    $send = $this->common_model->send_email($post);
                    
                    if($send){
                            $n = array(
                            'user_id'                => $this->session->userdata('user_id'),
                            'subject'                => display('withdraw'),
                            'notification_type'      => 'withdraw',
                            'details'                => 'You successfully withdraw the amount Is $'.$t_data['amount'].'. from your account. Your new balance is $'.$new_balance,
                            'date'                   => date('Y-m-d h:i:s'),
                            'status'                 => '0'
                        );
                        $this->db->insert('notifications',$n);    
                    }

                    #----------------------------
                    #      Sms verify
                    #----------------------------
                        
                    $this->load->library('sms_lib');

                        $template = array( 
                            'name'      => $this->session->userdata('fullname'),
                            'amount'    => $t_data['amount'],
                            'new_balance' => $new_balance,
                            'date'      => date('d F Y')
                        );
        
                    if ($userinfo->phone) {
                        $send_sms = $this->sms_lib->send(array(
                            'to'       => $userinfo->phone, 
                            'subject'         => 'Withdraw',
                            'template'         => 'You successfully withdraw the amount is $%amount% from your account. Your new balance is $%new_balance%', 
                            'template_config' => $template, 
                        ));

                    }else{

                        $this->session->set_flashdata('exception', "There is no Phone number!!!");
                    }

                    if($send_sms){
                        $message_data = array(
                            'sender_id' =>1,
                            'receiver_id' => $this->session->userdata('user_id'),
                            'subject' => 'Withdraw',
                            'message' => 'You successfully withdraw the amount is $'.$t_data['amount'].'. from your account. Your new balance is $'.$new_balance,
                            'datetime' => date('Y-m-d h:i:s'),
                        );

                        $this->db->insert('message',$message_data);
                    }
                //}

            }


                

            // $transections_data = array(
            //     'user_id'                   => $this->session->userdata('user_id'),
            //     'transection_category'      => 'withdraw',
            //     'releted_id'                => $result['id'],
            //     'amount'                    => $t_data['amount'],
            //     'transection_date_timestamp'=> date('Y-m-d h:i:s')
            // );

            // $this->transections_model->save_transections($transections_data);

            $this->db->set('status',0)
            ->where('id',$this->input->post('id'))
            ->where('session_id',$this->session->userdata('isLogIn'))
            ->update('dbt_verify');

            $this->session->set_flashdata('message', display('withdraw_successfull'));

            echo $result['id'];

        } else {
            echo '';
        }
 
    }



public function withdraw_details($id=NULL)
{
    $user_id = $this->session->userdata('user_id');
    $data['title']   = display('withdraw_details');

    $data['my_info'] = $this->withdraw_model->retriveUserInfo();
    $data['withdraw'] = $this->withdraw_model->get_withdraw_by_id($id);


    $data['content'] = $this->load->view('customer/withdraw/withdraw_details', $data, true);
    $this->load->view('customer/layout/main_wrapper', $data);  

}



    public function withdraw_recite($id=NULL)
    {
        $data['v'] = $this->withdraw_model->get_verify_data($id);

        $data['title']   = display('withdraw_recite'); 
        $data['my_info'] = $this->Profile_model->my_info();

        $data['content'] = $this->load->view('customer/pages/withdraw_recite', $data, true);
        
        $this->load->view('customer/layout/main_wrapper', $data);
    }

    /*
|---------------------------------
|   Fees Load and deposit amount 
|---------------------------------
*/
public function fees_load($amount=null,$level = "", )
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
    |   Balance Check
    |-----------------------------------
    */

    public function check_balance($amount=NULL)
    {
        $data = $this->transections_model->get_cata_wais_transections();
        $amount += $this->fees_load($amount,'withdraw');
        
        if($amount < $data['balance']){
            return true;
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
