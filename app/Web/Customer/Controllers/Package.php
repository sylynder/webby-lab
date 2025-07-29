<?php

use App\Middleware\AppMiddleware;
class Package extends AppMiddleware 
{
    public function __construct()
    {
        parent::__construct();
  
        if (!$this->session->userdata('isLogIn')) 
        redirect('login');

        if (!$this->session->userdata('user_id')) 
        redirect('login');

        // $menucontrol = $this->db->select('*')->from('menu_control')->get()->row();

        // if($menucontrol->package==0){
        //     redirect('customer/pageexcept');
        // }
 
        $this->load->model(array( 
            'customer/package_model',
            'customer/profile_model',
            'common_model', 
        ));

        $globdata['coininfo'] = $this->common_model->get_coin_info();
        $this->load->vars($globdata);
    }

    public function index()
    {   
        $data['title']   = display('package'); 
        $data['package'] = $this->package_model->allPackage();
        $data['content'] = $this->load->view('customer/pages/package', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);
    }

    public function investment()
    {
        $data['title']   = display('investment'); 
        $data['invest'] = $this->package_model->allInvestment();
        $data['content'] = $this->load->view('customer/pages/investment', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);
    }

    public function confirm_package($package_id=NULL)
    {
        $menucontrol = $this->db->select('*')->from('menu_control')->get()->row();
        $data['title']   = display('package');
        $data['menucontrol'] = $menucontrol;
        $data['my_info'] = $this->profile_model->my_info();
        $data['package'] = $this->package_model->packageInfoById($package_id);
        $data['content'] = $this->load->view('customer/pages/package_confirmation', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  
    }

    public function buy($package_id=NULL)
    {
        $menucontrol = $this->db->select('*')->from('menu_control')->get()->row();
        if($menucontrol->package==0){
            $this->session->set_flashdata('exception','Feature is disable!');
            redirect("customer/package/confirm_package/$package_id");
        }
        // balance chcck
        $balance = $this->check_balance($package_id);

        if($balance!=NULL){

            $user_id = $this->session->userdata('user_id');
            $userinfo = $this->db->select('*')->from('dbt_user')->where('user_id', @$user_id)->get()->row();
            $coin_setup = $this->db->select('*')->from('coin_setup')->get()->row();
            $buy_data = array(
                'user_id'       => $this->session->userdata('user_id'),
                'package_id'    => $package_id,
                'amount'        => $balance->package_amount,
                'invest_date'   => date('Y-m-d'),
                'day'           => date('N'),
            );
            $sell_packeage_id = $this->package_model->buyPackage($buy_data);
            $balance_id       = $this->package_model->getBalance();

            if($sell_packeage_id!=NULL){
                $balance_log_data = array(
                    'balance_id'        => @$balance_id->id,
                    'user_id'           => $this->session->userdata('user_id'),
                    'transaction_type'  => 'INVESTMENT',
                    'transaction_amount'=> $balance->package_amount,
                    'ip'                => $this->input->ip_address(),
                    'date'              => date('Y-m-d h:i:s')
                );
                $new_balance     = @$balance_id->balance-$balance->package_amount;
                $balance_data = array(
                    'user_id'        => $this->session->userdata('user_id'),
                    'new_balance'    => $new_balance
                );

                $this->package_model->saveBalanceLog($balance_log_data);
                $this->package_model->updateBalance($balance_data);

                $template = array( 
                    'name'      => $this->session->userdata('fullname'),
                    'amount'    => $balance->package_amount,
                    'date'      => date('d F Y')
                );

                if ($userinfo->phone) {                    
                    $send_sms = $this->sms_lib->send(array(
                        'to'              => $userinfo->phone, 
                        'template'        => "You bought a ".$coin_setup->pair_with." %amount% package successfully", 
                        'template_config' => $template, 
                    ));

                }else{
                    $this->session->set_flashdata('exception', "There is no Phone number!!!");

                }

                #----------------------------------
                #   sms insert to received commission
                #---------------------------------
                if(isset($send_sms)){
                    $message_data = array(
                        'sender_id' =>1,
                        'receiver_id' => $this->session->userdata('user_id'),
                        'subject' => 'Package Buy',
                        'message' => 'You bought a '.$balance->package_amount.' package successfully',
                        'datetime' => date('Y-m-d h:i:s'),
                    );
                    $this->db->insert('message',$message_data);
                }

                // $set        = $this->common_model->email_sms('email');
                $appSetting = $this->common_model->get_setting();
                #----------------------------
                #      email verify smtp
                #----------------------------
                $post = array(
                    'title'           => $appSetting->title,
                    'subject'           => 'Package Buy',
                    'to'                => $this->session->userdata('email'),
                    'message'           => 'You bought a '.$balance->package_amount.' package successfully',
                );
                $send_email = $this->common_model->send_email($post);

                if(isset($send_email)){
                    $n = array(
                        'user_id'                => $this->session->userdata('user_id'),
                        'subject'                => 'Package Buy',
                        'notification_type'      => 'package_by',
                        'details'                => 'You bought a '.$balance->package_amount.' package successfully',
                        'date'                   => date('Y-m-d h:i:s'),
                        'status'                 => '0'
                    );
                    $this->db->insert('notifications',$n);    
                }


            }

            $this->session->set_flashdata('message', display('package_buy_successfully'));
            redirect('customer/package/buy_success/'.$package_id);

        } else{
                $this->session->set_flashdata('exception', display('balance_is_unavailable'));
                redirect('customer/package/confirm_package/'.$package_id);

        }
       
    }

    public function buy_success($package_id)
    {
        $data['title']   = display('package'); 
        $data['my_info'] = $this->profile_model->my_info();
        $data['package'] = $this->package_model->packageInfoById($package_id);
        $data['content'] = $this->load->view('customer/pages/package_buy_recite', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);

    }

    public function check_balance($package_id)
    {
        $pak_info = $this->package_model->packageInfoById($package_id); 
        $data = $this->package_model->getBalance();
       
        if($pak_info->package_amount <=$data->balance){
            return $pak_info;

        }else {
            return $pak_info = array();
        }
    }

    public function package_payout()
    {
        $user_id = $this->session->userdata('user_id');
        $data['title']   = display('my_payout'); 
        #-------------------------------#
        #
        #pagination starts
        #
        $config["base_url"] = base_url('customer/package/package_payout');
        $config["total_rows"] = $this->db->get_where('earnings', array('user_id'=>$user_id,'earning_type'=>'ROI'))->num_rows();
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
        $data['my_payout'] = $this->db->select("*")
                        ->from('earnings')
                        ->where('user_id',$user_id)
                        ->where('earning_type','ROI')
                        ->limit($config["per_page"], $page)
                        ->get()
                        ->result();
        $data["links"] = $this->pagination->create_links();
        #
        #pagination ends
        #
        $data['content'] = $this->load->view('customer/pages/my_payout', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);
    }

    public function payout_receipt($id=NULL)
    {
        $user_id = $this->session->userdata('user_id');
        $data['title']   = display('receipt'); 
        $data['my_payout'] = $this->db->select("earnings.*,package.*")
                        ->from('earnings')
                        ->join('package','package.package_id=earnings.package_id')
                        ->where('earnings.user_id',$user_id)
                        ->where('earnings.earning_id',$id)
                        ->where('earnings.earning_type','ROI')
                        ->get()
                        ->row();

        $data['my_info'] = $this->profile_model->my_info();
        $data['content'] = $this->load->view('customer/pages/payout_receipt', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);
    }


}