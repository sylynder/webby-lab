<?php

use App\Middleware\AppMiddleware;
class Token extends AppMiddleware 
{

    public function __construct()
    {
        parent::__construct();
  
        if (!$this->session->userdata('isLogIn')) 
        redirect('customer'); 

        if (!$this->session->userdata('user_id')) 
        redirect('customer');
 
        $this->load->model(array(
            'customer/auth_model',
            'customer/token_model',
            'common_model',

        ));
        
        $globdata['coininfo'] = $this->common_model->get_coin_info();
        $this->load->vars($globdata);

        $result = $this->db->select('*')->from('setting')->get()->row();
        date_default_timezone_set(@$result->time_zone);

    }


    public function token_buy()
    {
        $data['title']   = 'Token buy'; 
        $menucontrol = $this->db->select('*')->from('menu_control')->get()->row();

        $data['menucontrol'] = $menucontrol;

        //Current Round Selecting
        $date = new DateTime();
        $today = $date->format('Y-m-d H:i:s');
        $where = 0;
        $coin_release_q = $this->db->select("*")->from('dbt_release_setup')->where('status', 1)->get()->result();
        foreach ($coin_release_q as $key => $value) {
            $lastday = date('Y-m-d H:i:s', strtotime("+$value->day days", strtotime($value->start_date)));

            if ($lastday >= $today && $value->start_date <= $today) {
                $where = "start_date BETWEEN '".$value->start_date."' AND '".$lastday."'";             
            }

        }

        $released_coin = $this->db->select("*")->from('dbt_release_setup')->where($where)->where('status', 1)->get()->row();

        @$data['coin_price'] = $this->db->select("*")->from('dbt_currency')->where('symbol', $released_coin->exchange_currency)->where('status', 1)->get()->row();

        $this->form_validation->set_rules('coin_qty', 'coin_qty','required|trim');

        if ($this->form_validation->run()) 
        {

            if($menucontrol->ico_wallet==0){

                $this->session->set_flashdata('exception','Feature is disable!');
                redirect('customer/token/token_buy');
            }

            $coin_owner_wallet = $this->db->select('*')->from('coin_setup')->get()->row();

            if ($coin_owner_wallet->wallet=='') {
                $this->session->set_flashdata('exception', "Server Problem");
                redirect('customer/token/token_buy');
            }

            $source_wallet     = @$coin_owner_wallet->wallet;               //$this->input->post('source_wallet'); //Wallet address where come from crypto
            $crypto_qty        = $this->input->post('coin_qty');      //Quentity of Crypto coin
            $crypto_rate       = @$data['coin_price']->rate;          //Rate of crypto coin
            $exchange_currency = @$data['coin_price']->symbol; //$this->input->post('exchange_currency'); //Exchange crypto with Currency
            $total             = @$data['coin_price']->rate*$crypto_qty; //Crypto Price + Rate
            //$balance         = @$data['coin_price']->price*$crypto_qty; // Sum of Total
            $crypto_balance    = $crypto_qty; // Sum of Crypto Coin

            //Check Available Coin
            if (@$released_coin->target - @$released_coin->fillup_target<=0) {
                $this->session->set_flashdata('exception', "Coin sold out");
                redirect('customer/token/token_buy');
            }

            //Check Available Coin Quantity
            if ($released_coin->target - $released_coin->fillup_target < $crypto_qty) {
                $this->session->set_flashdata('exception', "This amount(quantity) is not availabe");
                redirect('customer/token/token_buy');
            }

            $balance    = $this->token_model->checkBalance();

            if ($balance >= $total) {
                //Generate Wallet
                $coinwallet = md5(hash('sha256', date('Y-m-d H:i:s').microtime().mt_rand(0, 9999999)));

                $comspdata[] = array(
                    'id'                => md5($crypto_qty.date('Y-m-d H:i:s').microtime()),
                    'source_wallet'     => $source_wallet,
                    'crypto_qty'        => $crypto_qty,
                    'crypto_rate'       => $crypto_rate,
                    'exchange_currency' => $exchange_currency,
                    'total'             => $total,
                    //'balance'         => $balance,
                    'crypto_balance'    => $crypto_balance,
                );

                $jsondata = array(
                    $coinwallet  => $comspdata,
                );

                $jsonencode = json_encode($jsondata);

                $walletdata = array(
                    'wallet'     => $coinwallet, 
                    'data'       => $jsonencode,
                    'datetime'   => date('Y-m-d H:i:s'),
                );
                $cryptowallet = array(
                    'user_id'    => $this->session->userdata('user_id'),
                    'wallet'     => $coinwallet,
                    'datetime'   => date('Y-m-d H:i:s'),
                );

                $user_wallet =  $this->token_model->retriveUserCryptoWallet();

                if ($user_wallet) {

                    $user_wallet_transaction =  $this->token_model->retriveUserCryptoTransaction();
                    $jsondecode = json_decode($user_wallet_transaction->data);

                    $datapush = array();

                    foreach ($jsondecode as $key => $value) {
                       $datapush[$key] = $value;                        
                    }

                    // Data Last value get
                    foreach ($value as $last_key => $last_value)             

                    $comspdata_ex = array(
                        'id'                => md5($crypto_qty.date('Y-m-d H:i:s').microtime()),
                        'source_wallet'     => $source_wallet,
                        'crypto_qty'        => $crypto_qty,
                        'crypto_rate'       => $crypto_rate,
                        'exchange_currency' => $exchange_currency,
                        'total'             => $total + @$last_value->total,
                        //'balance'           => $balance + $last_value->balance,
                        'crypto_balance'    => $crypto_balance + @$last_value->crypto_balance,
                    );

                    //New Transaction Push
                    array_push($datapush[$key], $comspdata_ex);

                    $jsonencode_ex = json_encode($datapush);

                    $walletdata = array(
                        'wallet'     => $user_wallet, 
                        'data'       => $jsonencode_ex,
                        'datetime'   => date('Y-m-d H:i:s'),
                    );

                    //New Transaction Update
                    $this->token_model->updateUserWalletData($walletdata);

                    //User Balance Update
                    $this->db->set('balance', $balance-$total)->where('user_id', $this->session->userdata('user_id'))->update("dbt_balance");

                    //Coin Release Balance Update
                    $this->db->set('fillup_target', $released_coin->fillup_target+$crypto_qty)->where('id', $released_coin->id)->update("dbt_release_setup");

                }else{

                    //User Balance Update
                    $this->db->set('balance', $balance-$total)->where('user_id', $this->session->userdata('user_id'))->update("dbt_balance");

                    //Coin Release Balance Update
                    $this->db->set('fillup_target', $released_coin->fillup_target+$crypto_qty)->where('id', $released_coin->id)->update("dbt_release_setup");

                    //New User Wallet
                    $this->token_model->user_cryptowallet($cryptowallet);
                    //First Transaction
                    $this->token_model->crypto_transaction($walletdata);
                }

                $balance1    = $this->token_model->checkBalance1();

                $balance_log = array(
                    'balance_id'        => $balance1->id,
                    'user_id'           => $this->session->userdata('user_id'),
                    'transaction_type'  => "BUY",
                    'transaction_amount'=> $total,
                    'transaction_fees'  => 0,
                    'ip'                => $this->input->ip_address(),
                    'date'              => date('Y-m-d H:i:s'),
                );
                //User Balance log
                $this->db->insert('dbt_balance_log', $balance_log);

                $reffereldata = $this->db->select('referral_id')->from('dbt_user')->where('user_id',$this->session->userdata('user_id'))->get()->row();
                if($reffereldata->referral_id){
                    $refferId = $reffereldata->referral_id;
                    $rcommission = $this->db->select('earning_id')->from('earnings')->where('user_id',$refferId)->where('Purchaser_id',$this->session->userdata('user_id'))->where('earning_type','REFERRAL')->get()->num_rows();
                    if($rcommission<1){
                        $commissioninfo = $this->db->select('*')->from('dbt_affiliation')->where('status',1)->get();
                        if($commissioninfo->num_rows()>0){
                            $commission = $commissioninfo->row();
                            $camount    = 0;
                            if($commission->type=="PERCENT"){
                                $camount = number_format(($total*$commission->commission)/100,8);
                            }
                            else{
                                $camount = number_format($commission->commission,8);
                            }
                            $commissiondata = array(
                                'user_id'       => $refferId,
                                'Purchaser_id'  => $this->session->userdata('user_id'),
                                'earning_type'  => 'REFERRAL',
                                'amount'        => $camount,
                                'date'          => date('Y-m-d'),
                            );
                            $this->db->insert('earnings',$commissiondata);
                            $checkbalance = $this->db->select('id,user_id,balance')->from('dbt_balance')->where('user_id',$refferId)->get()->row();
                            if($checkbalance){

                                $totalbalance= $checkbalance->balance+$camount;
                                $balancedata = array(
                                    'balance'       =>$totalbalance,
                                    'last_update'   =>date('Y-m-d H:i:s'),
                                );
                                $this->db->where('user_id',$refferId)->update("dbt_balance",$balancedata);

                            }
                            else{

                                $balancedata = array(
                                    'user_id'    =>$refferId,
                                    'balance'    =>$camount,
                                    'last_update'=>date('Y-m-d H:i:s')
                                );
                                $this->db->insert("dbt_balance",$balancedata);
                            }
                        }
                    }
                }


                 $this->session->set_flashdata('message', "Buy Successful!");

            }else{

                $this->session->set_flashdata('exception', "You don't have sufficent Balance!");
                redirect('customer/token/token_buy');
            }
            
        }

        $data['currency'] = $this->token_model->exchangeCurrency();


        $data['content'] = $this->load->view('customer/pages/token_buy', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  

    }

    public function token_list()
    {
        
        $data['title']   = 'Token list'; 

        $data['transaction'] = $this->token_model->retriveUserCryptoTransaction(); 
        $data['coin_setup'] = $this->db->select('*')->from('coin_setup')->get()->row();      


        $data['content'] = $this->load->view('customer/pages/token_list', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  

    }

}