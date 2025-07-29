<?php

use App\Middleware\AppMiddleware;
class Exchange extends AppMiddleware {
    
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('isLogIn')) 
        redirect('login');

        if (!$this->session->userdata('user_id')) 
        redirect('login');

        // $menucontrol = $this->db->select('*')->from('menu_control')->get()->row();
        // if($menucontrol->exchange==0){
        //     redirect('customer/pageexcept');
        // }
    
        $this->load->model(array(

            'customer/exchange_model',
            'customer/token_model',
            'common_model',  
        ));

        $globdata['coininfo'] = $this->common_model->get_coin_info();
        $this->load->vars($globdata);
        
    }

    public function index(){

        $menucontrol = $this->db->select('*')->from('menu_control')->get()->row();
        $seller_wallet_transaction =  $this->exchange_model->retriveUserCryptoTransaction();

        $data['buyfees']  = $this->db->select('fees')->from('dbt_fees')->where('level','BUY')->get()->row();
        $data['sellfees'] = $this->db->select('fees')->from('dbt_fees')->where('level','SELL')->get()->row();

        //Seller All Transaction data
        if($seller_wallet_transaction){
            $jsondecode_seller = json_decode(@$seller_wallet_transaction->data);
        }
        else{
            $jsondecode_seller = '';
        }

        $data['title'] = "Exchange Coin";
        $data['menucontrol'] = $menucontrol;

        $exchange = $this->input->post('exchange');
        $qty      = $this->input->post('qty');
        $rate     = $this->input->post('rate');
        $user_id  = $this->session->userdata('user_id');

        $this->form_validation->set_rules('exchange', 'exchange','required|trim');
        $this->form_validation->set_rules('qty', 'qty','required|trim');
        $this->form_validation->set_rules('rate', 'rate','required|trim');


        //Check Fees
        $fees = $this->db->select('fees')->from('dbt_fees')->where('level', $exchange)->get()->row();
        if ($fees) {
            $feesamount = ($qty*$rate * $fees->fees)/100;

        }else{
            $feesamount = 0;
        }


        if ($this->form_validation->run()) {

            if($menucontrol->exchange==0){
                $this->session->set_flashdata('exception','Feature is disable!');
                redirect('customer/exchange/wrightException');
            }

            $source_wallet = $this->exchange_model->retriveUserCryptoWallet($user_id);

            $coin_owner_wallet = $this->db->select('*')->from('coin_setup')->get()->row();

            $exchangedata = array(
                'exchange_type' => $exchange,
                'source_wallet' => $source_wallet,
                'crypto_qty'    => $qty,
                'crypto_rate'   => $rate,
                'complete_qty'  => 0,
                'available_qty' => $qty,
                // 'total_amount'  => $qty*$rate,
                // 'amount_available'  => $qty*$rate,
                'fees_amount'   => $feesamount,
                'datetime'      => date('Y-m-d H:i:s'),
                'status'        => 2,
            );

            $exchange_id = '';
            
            if ($exchange=='SELL') {

                $seller_wallet_transaction =  $this->exchange_model->retriveUserCryptoTransaction();
                if(!empty($seller_wallet_transaction)){
                    //Seller All Transaction data 
                    $jsondecode_seller = json_decode($seller_wallet_transaction->data);
                    //Seller Last Transaction data
                    $transaction_last_value_seller = end($jsondecode_seller->$source_wallet);
    
                    //Available for Sell
                    $available_qty = $this->db->select_sum('available_qty')->from('dbt_exchange')->where('source_wallet', $source_wallet)->where('exchange_type', 'SELL')->where('status', 2)->get()->row();
    
                    // Check Crypto Quantity available
                    if ((@$transaction_last_value_seller->crypto_balance - @$available_qty->available_qty) >= $qty) {
    
                        //Exchange Data Insert
                        if ($exchange_id = $this->exchange_model->exchangeCreate($exchangedata))
                        {
                                                    
    
                            //Search all Buy data
                            $where = "(crypto_rate >= '".$rate."' AND status = 2 AND exchange_type = 'BUY')";                   
                            $buy_exchange_query = $this->db->select('*')->from('dbt_exchange')->where($where)->order_by('datetime', 'asc')->get()->result();
    
                            $seller_total           = 0;
                            $seller_crypto_balance  = 0;
                            $buyer_total            = 0;
                            $buyer_crypto_balance   = 0;
    
                            if ($buy_exchange_query) {
                                foreach ($buy_exchange_query as $key => $buyexchange) {
    
    
                                    $seller_available_qty     = 0;
                                    $seller_complete_qty      = 0;
                                    $buyer_available_qty      = 0;
                                    $buyer_complete_qty       = 0;
                                    $seller_available_qty_log = 0;
                                    $seller_complete_qty_log  = 0;
                                    $buyer_available_qty_log  = 0;
                                    $buyer_complete_qty_log   = 0;
    
    
                                    $last_exchange = $this->exchange_model->singleExchange($exchange_id);
                                    if ($last_exchange->status==2) {
    
                                        //Seller+Buyer Quantity Complete Master table
                                        $seller_available_qty   = $last_exchange->available_qty-$buyexchange->available_qty<0?0:$last_exchange->available_qty-$buyexchange->available_qty;
                                        $seller_complete_qty    = $last_exchange->complete_qty+((($last_exchange->available_qty-$seller_available_qty)<0)?0:$last_exchange->available_qty-$seller_available_qty);
                                        $buyer_available_qty   = $buyexchange->available_qty-$last_exchange->available_qty<0?0:$buyexchange->available_qty-$last_exchange->available_qty;
                                        $buyer_complete_qty    = $buyexchange->complete_qty+((($buyexchange->available_qty-$buyer_available_qty)<0)?0:$buyexchange->available_qty-$buyer_available_qty);
    
    
                                        // Seller+Buyer Quantity Complete log table
                                        $seller_available_qty_log   = $last_exchange->available_qty-$buyexchange->available_qty<0?0:$last_exchange->available_qty-$buyexchange->available_qty;
                                        $seller_complete_qty_log    = ((($last_exchange->available_qty-$seller_available_qty)<0)?0:$last_exchange->available_qty-$seller_available_qty);
    
                                        $buyer_available_qty_log   = $buyexchange->available_qty-$last_exchange->available_qty<0?0:$buyexchange->available_qty-$last_exchange->available_qty;
                                        $buyer_complete_qty_log    = ((($buyexchange->available_qty-$buyer_available_qty)<0)?0:$buyexchange->available_qty-$buyer_available_qty);
    
    
    
    
                                        //Seller Money/Quantity Transaction
                                        $seller_total           = ($transaction_last_value_seller->total-($seller_complete_qty_log*$last_exchange->crypto_rate))<0?(($seller_complete_qty_log*$last_exchange->crypto_rate)-$transaction_last_value_seller->total):($transaction_last_value_seller->total-($seller_complete_qty_log*$last_exchange->crypto_rate));
                                        $seller_crypto_balance  = $transaction_last_value_seller->crypto_balance-$seller_complete_qty_log;
    
    
                                        // Seller data update on transaction tbl
                                        $selltransaction = (object)array(
                                            'id'                => md5($seller_complete_qty_log.date('Y-m-d H:i:s').microtime()),
                                            'source_wallet'     => $source_wallet, //Seller own wallet
                                            'crypto_qty'        => (-$seller_complete_qty_log), //For Sell this will be minus
                                            'crypto_rate'       => $last_exchange->crypto_rate,
                                            'exchange_currency' => $coin_owner_wallet->pair_with,
                                            'total'             => $seller_total, //If positive SMILE else CRY
                                            'crypto_balance'    => $seller_crypto_balance,
                                        );
    
                                        array_push($jsondecode_seller->$source_wallet, $selltransaction);
                                        $walletdata_seller = array(
                                            'wallet'     => $source_wallet, 
                                            'data'       => json_encode($jsondecode_seller),
                                            'datetime'   => date('Y-m-d H:i:s'),
                                        );
    
                                        $transaction_last_value_seller->total          =  $seller_total;
                                        $transaction_last_value_seller->crypto_balance =  $seller_crypto_balance;
    
    
    
    
                                        $buyer_wallet = $buyexchange->source_wallet;
                                        //Check Buyer Wallet
                                        $buyer_wallet_transaction =  $this->exchange_model->retriveUserWallet($buyer_wallet);
    
                                        if ($buyer_wallet_transaction) {
    
                                            // Buyer All Transaction data 
                                            $jsondecode_buyer = json_decode($buyer_wallet_transaction->data);
                                            //Buyer Last Transaction data
                                            $transaction_last_value_buyer = end($jsondecode_buyer->$buyer_wallet); 
    
                                            if ($transaction_last_value_buyer==NULL) {
    
                                                $transaction_last_value_buyer   = (object)array('total'=>0, 'crypto_balance'=>0);
    
                                            }
    
                                            //Buyer Money/Quantity Transaction
                                            $buyer_total           = $transaction_last_value_buyer->total+($buyer_complete_qty_log*$last_exchange->crypto_rate);
                                            $buyer_crypto_balance  = $transaction_last_value_buyer->crypto_balance+$buyer_complete_qty_log;
    
                                            // Buyer data update on transaction tbl
                                            $buytransaction = (object)array(
                                                'id'                => md5($buyer_complete_qty_log.date('Y-m-d H:i:s').microtime()),
                                                'source_wallet'     => $source_wallet, //Seller own wallet
                                                'crypto_qty'        => $buyer_complete_qty_log, //For Sell this will be minus
                                                'crypto_rate'       => $last_exchange->crypto_rate,
                                                'exchange_currency' => $coin_owner_wallet->pair_with,
                                                'total'             => $buyer_total, //If negative SMILE else CRY
                                                'crypto_balance'    => $buyer_crypto_balance,
                                            );
    
    
                                            array_push($jsondecode_buyer->$buyer_wallet, $buytransaction);
                                            $walletdata_buyer = array(
                                                'wallet'     => $buyer_wallet, 
                                                'data'       => json_encode($jsondecode_buyer),
                                                'datetime'   => date('Y-m-d H:i:s'),
                                            );
    
    
                                            $transaction_last_value_buyer->total          =  $buyer_total;
                                            $transaction_last_value_buyer->crypto_balance =  $buyer_crypto_balance;
    
    
                                        }else{
    
                                            $this->session->set_flashdata('exception', "There is something wrong!");
                                            redirect('customer/exchange/wrightException');
                                        }
    
    
    
                                        //Buy Check Fees
                                        $buy_fees = $this->db->select('fees')->from('dbt_fees')->where('level', 'BUY')->get()->row();
                                        if ($buy_fees) {
                                            $buy_feesamount = ($buyer_complete_qty_log * $last_exchange->crypto_rate * $buy_fees->fees)/100;
    
                                        }else{
                                            $buy_feesamount = 0;
    
                                        }
    
    
                                        //Exchange Data =>Sell
                                        $exchangeselldata = array(
                                            'id'             => $last_exchange->id,
                                            'exchange_type'  => $last_exchange->exchange_type,
                                            'source_wallet'  => $last_exchange->source_wallet,
                                            'crypto_qty'     => $last_exchange->crypto_qty,
                                            'crypto_rate'    => $last_exchange->crypto_rate,
                                            'complete_qty'   => $seller_complete_qty,
                                            'available_qty'  => $seller_available_qty,
                                            'datetime'       => date('Y-m-d H:i:s'),
                                            'status'         => $seller_available_qty==0?1:2,
                                        );
    
                                        //Exchange Data =>Buyer
                                        $exchangebuydata = array(
                                            'id'             => $buyexchange->id,
                                            'exchange_type'  => $buyexchange->exchange_type,
                                            'source_wallet'  => $buyexchange->source_wallet,
                                            'crypto_qty'     => $buyexchange->crypto_qty,
                                            'crypto_rate'    => $buyexchange->crypto_rate,
                                            'complete_qty'   => $buyer_complete_qty, 
                                            'available_qty'  => $buyer_available_qty,
                                            'datetime'       => date('Y-m-d H:i:s'), 
                                            'status'         => $buyer_available_qty==0?1:2,
                                        );
    
    
                                        //Exchange Sell+Buy Update
                                        $this->exchange_model->exchangeUpdate($exchangeselldata);
                                        $this->exchange_model->exchangeUpdate($exchangebuydata);
    
    
    
    
                                        // Exchange Log Data =>Seller
                                        $exchangelogdata_seller = array(
                                            'exc_id'            => $last_exchange->id,
                                            'exchange_type'     => $last_exchange->exchange_type,
                                            'source_wallet'     => $last_exchange->source_wallet,
                                            'destination_wallet'=> $last_exchange->source_wallet,
                                            'crypto_qty'        => $last_exchange->crypto_qty,
                                            'crypto_rate'       => $last_exchange->crypto_rate,
                                            'complete_qty'      => $seller_complete_qty_log,
                                            'available_qty'     => $seller_available_qty_log,
                                            'fees_amount'       => $feesamount,
                                            'datetime'          => date('Y-m-d H:i:s'),
                                            'status'            => $seller_available_qty_log==0?1:2,
                                        );
    
                                        //Exchange Log Data =>Buyer
                                        $exchangelogdata_buyer = array(
                                            'exc_id'            => $last_exchange->id,
                                            'exchange_type'     => $buyexchange->exchange_type,
                                            'source_wallet'     => $buyexchange->source_wallet,
                                            'destination_wallet'=> $buyexchange->source_wallet,
                                            'crypto_qty'        => $buyexchange->crypto_qty,
                                            'crypto_rate'       => $last_exchange->crypto_rate,
                                            'complete_qty'      => $buyer_complete_qty_log,
                                            'available_qty'     => $buyer_available_qty_log,
                                            'fees_amount'       => $buy_feesamount,
                                            'datetime'          => date('Y-m-d H:i:s'),
                                            'status'            => $buyer_available_qty_log==0?1:2,
                                        );
    
                                        //Exchange Sell+Buy Log data
                                        $this->exchange_model->exchangeLogCreate($exchangelogdata_seller);
                                        $this->exchange_model->exchangeLogCreate($exchangelogdata_buyer);
    
                                            
                                        //Seller+Buyer Balance Update
                                        $balance = $this->exchange_model->checkBalance();
                                        if ($balance) {
                                            $total = ($seller_complete_qty_log*$last_exchange->crypto_rate)+@$balance->balance-$feesamount;
                                            $this->db->set('balance', $total)->where('user_id', $user_id)->update("dbt_balance");
    
                                        }else{
    
                                            $balance_insert = array(
                                                'user_id'       => $user_id,
                                                'balance'       => ($seller_complete_qty_log*$last_exchange->crypto_rate)-$feesamount,
                                                'last_update'   => date('Y-m-d H:i:s'),
                                            );
    
                                            $this->db->insert('dbt_balance', $balance_insert);
    
                                        }
    
                                        $balance = $this->exchange_model->checkBalance();
    
                                        $balance_log = array(
                                            'balance_id'        => $balance->id,
                                            'user_id'           => $user_id,
                                            'transaction_type'  => "SELL",
                                            'transaction_amount'=> $seller_complete_qty_log*$last_exchange->crypto_rate,
                                            'transaction_fees'  => $feesamount,
                                            'ip'                => $this->input->ip_address(),
                                            'date'              => date('Y-m-d H:i:s'),
                                        );
                                        //User Balance log
                                        $this->db->insert('dbt_balance_log', $balance_log);                                    
    
                                        // New Transaction Update
                                        $this->exchange_model->updateUserWalletData($walletdata_seller);
                                        $this->exchange_model->updateUserWalletData($walletdata_buyer);
                                        
    
                                        //Adjustment when buyer rate is high
                                        if(@$buyexchange->crypto_rate>$rate){
    
                                            // $totalexchanceqty = $last_exchange->available_qty-$buyexchange->available_qty<0?$buyexchange->available_qty-$last_exchange->available_qty:$last_exchange->available_qty-$buyexchange->available_qty;

                                            $totalexchanceqty = $buyer_available_qty_log;
    
                                            $buyremeaningrate = $buyexchange->crypto_rate-$rate;
                                            $buyerbalence     = $buyremeaningrate*$totalexchanceqty;
    
                                            //*********** Fees when Adjustment ***********

                                            $returnfees = 0;
                                            $byerfees       = ($totalexchanceqty*$buyexchange->crypto_rate*@$fees->fees)/100;
                                            $sellerrfees    = ($totalexchanceqty*$rate*@$fees->fees)/100;

                                            $buyerreturnfees  = $byerfees-$sellerrfees;

                                            if($buyerreturnfees>0){

                                                $returnfees = $buyerreturnfees;
                                            }

                                            //$fees_adjust = ($buyerbalence * $buyexchange->fees_amount)/$buyexchange->crypto_qty;
    
                                            $buyeruserid      = $this->exchange_model->retriveUserIdByCryptoWallet($buyexchange->source_wallet);
    
                                            $balance_data = array(
                                                'user_id'    => $buyeruserid,
                                                'amount'     => $buyerbalence,
                                                'return_fees'=> $returnfees,
                                                'ip'         => $this->input->ip_address()
                                            );
    
                                            $this->exchange_model->balanceReturn($balance_data);
    
                                        }
    
    
    
                                        //Historycal data generate
                                        $where      = "(datetime >= DATE_SUB(NOW(), INTERVAL 1 hour))"; 
                                        $where1    = "(exchange_type='BUY')";
                                        $where2     = "(datetime >= DATE_SUB(DATE_SUB(NOW(), INTERVAL 1 hour), INTERVAL 1 hour)) AND (datetime <= DATE_SUB(NOW(), INTERVAL 1 hour))";

                                        $total_coin_supply = $this->db->select_sum('complete_qty')->from('dbt_exchange_details')->where($where1)->order_by('datetime', 'desc')->get()->row();
                                        
                                        $h1_bid_high_price = $this->db->select_max('crypto_rate')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->get()->row();
                                        
                                        $h1_bid_low_price = $this->db->select_min('crypto_rate')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->get()->row();
                                        
                                        $h1_coin_supply = $this->db->select_sum('complete_qty')->from('dbt_exchange_details')->where($where1)->order_by('datetime', 'desc')->get()->row();
                                        //Price change value in up/down
                                        $last_price_query = $this->db->select('*')->from('dbt_exchange_details')->order_by('datetime', 'desc')->get()->row();
                                        
                                        if($h1_bid_high_price->crypto_rate==''){
                                            $high1 = $last_exchange->crypto_rate;
                                        
                                        }else{
                                        
                                            if ($h1_bid_high_price->crypto_rate<$last_exchange->crypto_rate) {
                                                $high1 = $last_exchange->crypto_rate;
                                        
                                            }else{
                                                $high1 = $h1_bid_high_price->crypto_rate;
                                        
                                            }
                                        
                                        }
                                        
                                        if($h1_bid_low_price->crypto_rate==''){
                                             $low1 = $last_exchange->crypto_rate;
                                        
                                        }else{
                                        
                                            if ($h1_bid_low_price->crypto_rate<$last_exchange->crypto_rate) {
                                                $low1 = $last_exchange->crypto_rate;
                                        
                                            }else{
                                                $low1 = $h1_bid_low_price->crypto_rate;
                                        
                                            }
                                        
                                        }
                                        
                                        if ($last_exchange->crypto_rate<@$last_price_query->last_price) {
                                            $price_change_1h = -($high1 - $low1);
                                        
                                        }else{
                                            $price_change_1h = $high1 - $low1;
                                        
                                        }
                                        
                                        $where = "(datetime >= DATE_SUB(NOW(), INTERVAL 24 hour))"; 
                                        $where1 = "(exchange_type='BUY')";
                                        $where2 = "(datetime >= DATE_SUB(NOW(), INTERVAL 24 hour) AND exchange_type='BUY')"; 
                                        $where3 = "(datetime >= DATE_SUB(DATE_SUB(NOW(), INTERVAL 24 hour), INTERVAL 24 hour)) AND (datetime <= DATE_SUB(NOW(), INTERVAL 24 hour))";
                                        
                                        
                                        
                                        
                                        
                                        $h24_last_price_avg = $this->db->select_avg('crypto_rate')->from('dbt_exchange_details')->where($where2)->order_by('datetime', 'desc')->get()->row();
                                        $pre24h_last_price = $this->db->select('crypto_rate')->from('dbt_exchange_details')->where($where3)->order_by('datetime', 'desc')->get()->row();
                                        $pre24h_last_price_avg = $this->db->select_avg('crypto_rate')->from('dbt_exchange_details')->where($where3)->order_by('datetime', 'desc')->get()->row();
                                        $h24_coin_supply = $this->db->select_sum('complete_qty')->from('dbt_exchange_details')->where($where1)->order_by('datetime', 'desc')->get()->row();
                                        $h24_bid_high_price = $this->db->select_max('crypto_rate')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->get()->row();
                                        $h24_bid_low_price = $this->db->select_min('crypto_rate')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->get()->row();
                                        
                                        
                                        
                                        
                                        if($h24_bid_high_price->crypto_rate==''){
                                            $high24 = $last_exchange->crypto_rate;
                                        
                                        }else{
                                        
                                            if ($h24_bid_high_price->crypto_rate<$last_exchange->crypto_rate) {
                                                $high24 = $last_exchange->crypto_rate;
                                        
                                            }else{
                                                $high24 = $h24_bid_high_price->crypto_rate;
                                        
                                            }
                                        
                                        }
                                        
                                        if($h24_bid_low_price->crypto_rate==''){
                                            $low24 = $last_exchange->crypto_rate;
                                        
                                        }else{
                                        
                                            if ($h24_bid_low_price->crypto_rate<$last_exchange->crypto_rate) {
                                                $low24 = $last_exchange->crypto_rate;
                                        
                                            }else{
                                                $low24 = $h24_bid_low_price->crypto_rate;
                                        
                                            }
                                        
                                        }
                                        
                                        if ($last_exchange->crypto_rate<@$last_price_query->last_price) {
                                            $price_change_24h = -($high24 - $low24);
                                        
                                        }else{
                                            $price_change_24h = $high24 - $low24;
                                        
                                        }
                                        
                                        $exchange_history = array(
                                            //'coin_symbol'       => $buytrade->currency_symbol,
                                            //'market_symbol'     => $buytrade->market_symbol,
                                            'last_price'          => $last_exchange->crypto_rate,
                                            'total_coin_supply'   => @$buyer_complete_qty_log+@$total_coin_supply->complete_qty,
                                            'price_high_1h'       => ($h1_bid_high_price->crypto_rate=='')?$last_exchange->crypto_rate:(($h1_bid_high_price->crypto_rate<$last_exchange->crypto_rate)?$last_exchange->crypto_rate:$h1_bid_high_price->crypto_rate),
                                            'price_low_1h'      => ($h1_bid_low_price->crypto_rate=='')?$last_exchange->crypto_rate:(($h1_bid_low_price->crypto_rate>$last_exchange->crypto_rate)?$last_exchange->crypto_rate:$h1_bid_low_price->crypto_rate),
                                            'price_change_1h'   => ($price_change_1h=='')?0:$price_change_1h,
                                            'volume_1h'         => ($h1_coin_supply->complete_qty=='')?0:$h1_coin_supply->complete_qty,
                                            'price_high_24h'     => ($h24_bid_high_price->crypto_rate=='')?$last_exchange->crypto_rate:(($h24_bid_high_price->crypto_rate<$last_exchange->crypto_rate)?$last_exchange->crypto_rate:$h24_bid_high_price->crypto_rate),
                                            'price_low_24h'      => ($h24_bid_low_price->crypto_rate=='')?$last_exchange->crypto_rate:(($h24_bid_low_price->crypto_rate>$last_exchange->crypto_rate)?$last_exchange->crypto_rate:$h24_bid_low_price->crypto_rate),
                                            'price_change_24h'   => ($price_change_24h=='')?0:$price_change_24h,
                                            'volume_24h'         => ($h24_coin_supply->complete_qty=='')?0:$h24_coin_supply->complete_qty,
                                            'open'              => $last_exchange->crypto_rate,
                                            'close'             => $last_exchange->crypto_rate,
                                            'volumefrom'        => @$buyer_complete_qty_log+@$total_coin_supply->complete_qty,
                                            'volumeto'          => ($h24_coin_supply->complete_qty=='')?0:$h24_coin_supply->complete_qty,
                                            'date'              => date('Y-m-d H:i:s'),
                                        );
                                        
                                        $this->db->insert('dbt_exchange_history', $exchange_history);
    
                                    }                            
    
                                }
                            }

                            $this->session->set_flashdata('message','Sell request successfully!');
                            redirect('customer/exchange/wrightMessage');

                        }
    
                    }else{
    
                        $this->session->set_flashdata('exception', "You don't have sufficent Crypto Coin!");
                        redirect('customer/exchange/wrightException');
                    }
                }
                else{
                    $this->session->set_flashdata('exception', "You don't have any Cryptocoin!");
                        redirect('customer/exchange/wrightException');
                }
                        
            }else{


                $buyer_wallet_transaction =  $this->exchange_model->retriveUserCryptoTransaction();

                if (!$buyer_wallet_transaction) {
                    
                    //Generate Wallet
                    $coinwallet = md5(hash('sha256', date('Y-m-d H:i:s').microtime().mt_rand(0, 9999999)));

                    $comspdata = array();

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


                    //New User Wallet
                    $this->exchange_model->user_cryptowallet($cryptowallet);
                    //First Transaction
                    $this->exchange_model->crypto_transaction($walletdata);
                    

                    $buyer_wallet_transaction =  $this->exchange_model->retriveUserCryptoTransaction();

                    $source_wallet = $this->exchange_model->retriveUserCryptoWallet($user_id);

                    //Source wallet not found so create wallet and add to exchange data
                    $exchangedata = array(
                        'exchange_type' => $exchange,
                        'source_wallet' => $source_wallet,
                        'crypto_qty'    => $qty,
                        'crypto_rate'   => $rate,
                        'complete_qty'  => 0,
                        'available_qty' => $qty,
                        'fees_amount'   => $feesamount,
                        'datetime'      => date('Y-m-d H:i:s'),
                        'status'        => 2,
                    );

                }


                //Buyer All Transaction data 
                $jsondecode_buyer = json_decode($buyer_wallet_transaction->data);
                //Buyer Last Transaction data
                $transaction_last_value_buyer = end($jsondecode_buyer->$source_wallet);

                //Available Balance for Buy
                $user_balance = $this->exchange_model->checkBalance();

                // Check Balance for Buy
                if ((@$user_balance->balance+$feesamount) >= ($qty*$rate)) {

                    //Exchange Data Insert
                    if ($exchange_id = $this->exchange_model->exchangeCreate($exchangedata)) {
                        

                        
                        //Money Discut from Buyer account
                        $balance_data = array(
                            'user_id'   => $user_id,
                            'amount'    => $qty*$rate,
                            'feesamount'=> $feesamount
                        );
                        $this->exchange_model->balanceDebit($balance_data);

                        $balance_log = array(
                            'balance_id'        => $user_balance->id,
                            'user_id'           => $this->session->userdata('user_id'),
                            'transaction_type'  => "BUY",
                            'transaction_amount'=> $qty*$rate,
                            'transaction_fees'  => $feesamount,
                            'ip'                => $this->input->ip_address(),
                            'date'              => date('Y-m-d H:i:s'),
                        );
                        //User Balance log
                        $this->db->insert('dbt_balance_log', $balance_log);

                        //Search all Buy data
                        $where = "(crypto_rate <= '".$rate."' AND status = 2 AND exchange_type = 'SELL')";                   
                        $sell_exchange_query = $this->db->select('*')->from('dbt_exchange')->where($where)->order_by('datetime', 'asc')->get()->result();

                        $seller_total           = 0;
                        $seller_crypto_balance  = 0;
                        $buyer_total            = 0;
                        $buyer_crypto_balance   = 0;

                        if ($sell_exchange_query) {
                            foreach ($sell_exchange_query as $key => $sellexchange) {

                                $seller_available_qty     = 0;
                                $seller_complete_qty      = 0;
                                $buyer_available_qty      = 0;
                                $buyer_complete_qty       = 0;
                                $seller_available_qty_log = 0;
                                $seller_complete_qty_log  = 0;
                                $buyer_available_qty_log  = 0;
                                $buyer_complete_qty_log   = 0;


                                $last_exchange = $this->exchange_model->singleExchange($exchange_id);


                                if (@$last_exchange->status==2) {

                                    //Seller+Buyer Quantity Complete Master table
                                    $seller_available_qty   = $sellexchange->available_qty-$last_exchange->available_qty<0?0:$sellexchange->available_qty-$last_exchange->available_qty;
                                    $seller_complete_qty    = $sellexchange->complete_qty+((($sellexchange->available_qty-$seller_available_qty)<0)?0:$sellexchange->available_qty-$seller_available_qty);
                                    $buyer_available_qty   = $last_exchange->available_qty-$sellexchange->available_qty<0?0:$last_exchange->available_qty-$sellexchange->available_qty;
                                    $buyer_complete_qty    = $last_exchange->complete_qty+$seller_complete_qty;


                                    // Seller+Buyer Quantity Complete log table
                                    $seller_available_qty_log   = $sellexchange->available_qty-$last_exchange->available_qty<0?0:$sellexchange->available_qty-$last_exchange->available_qty;
                                    $seller_complete_qty_log    = ((($sellexchange->available_qty-$seller_available_qty)<0)?0:$sellexchange->available_qty-$seller_available_qty);

                                    $buyer_available_qty_log   = $last_exchange->available_qty-$sellexchange->available_qty<0?0:$last_exchange->available_qty-$sellexchange->available_qty;
                                    $buyer_complete_qty_log    = $seller_complete_qty;



                                    if ($transaction_last_value_buyer==NULL) {
                                        $transaction_last_value_buyer   = (object)array('total'=>0, 'crypto_balance'=>0);

                                    }

                                    //Buyer Money/Quantity Transaction
                                    $buyer_total           = $transaction_last_value_buyer->total+($buyer_complete_qty_log*$sellexchange->crypto_rate);
                                    $buyer_crypto_balance  = $transaction_last_value_buyer->crypto_balance+$buyer_complete_qty_log;

                                    // Buyer data update on transaction tbl
                                    $buytransaction = (object)array(
                                        'id'                => md5($buyer_complete_qty_log.date('Y-m-d H:i:s').microtime()),
                                        'source_wallet'     => $source_wallet, //Seller own wallet
                                        'crypto_qty'        => $buyer_complete_qty_log, //For Sell this will be minus
                                        'crypto_rate'       => $sellexchange->crypto_rate,
                                        'exchange_currency' => $coin_owner_wallet->pair_with,
                                        'total'             => $buyer_total, //If negative SMILE else CRY
                                        'crypto_balance'    => $buyer_crypto_balance,
                                    );


                                    array_push($jsondecode_buyer->$source_wallet, $buytransaction);
                                    $walletdata_buyer = array(
                                        'wallet'     => $source_wallet, 
                                        'data'       => json_encode($jsondecode_buyer),
                                        'datetime'   => date('Y-m-d H:i:s'),
                                    );


                                    $transaction_last_value_buyer->total          =  $buyer_total;
                                    $transaction_last_value_buyer->crypto_balance =  $buyer_crypto_balance;


                                    $seller_wallet = $sellexchange->source_wallet;
                                    //Check Seller Wallet
                                    $seller_wallet_transaction =  $this->exchange_model->retriveUserWallet($seller_wallet);

                                    // Seller All Transaction data 
                                    $jsondecode_seller = json_decode($seller_wallet_transaction->data);
                                    //Seller Last Transaction data
                                    $transaction_last_value_seller = end($jsondecode_seller->$seller_wallet); 


                                    //Seller Money/Quantity Transaction
                                    $seller_total           = (($buyer_complete_qty_log*$sellexchange->crypto_rate)-$transaction_last_value_seller->total)<0?($transaction_last_value_seller->total-($buyer_complete_qty_log*$sellexchange->crypto_rate)):(($buyer_complete_qty_log*$sellexchange->crypto_rate)-$transaction_last_value_seller->total);
                                    $seller_crypto_balance  = $transaction_last_value_seller->crypto_balance-$buyer_complete_qty_log;


                                    // Seller data update on transaction tbl
                                    $selltransaction = (object)array(
                                        'id'                => md5($seller_complete_qty_log.date('Y-m-d H:i:s').microtime()),
                                        'source_wallet'     => $source_wallet, //Seller own wallet
                                        'crypto_qty'        => -$seller_complete_qty_log, //For Sell this will be minus
                                        'crypto_rate'       => $sellexchange->crypto_rate,
                                        'exchange_currency' => $coin_owner_wallet->pair_with,
                                        'total'             => $seller_total, //If negative SMILE else CRY
                                        'crypto_balance'    => $seller_crypto_balance,
                                    );

                                    array_push($jsondecode_seller->$seller_wallet, $selltransaction);
                                    $walletdata_seller = array(
                                        'wallet'     => $seller_wallet, 
                                        'data'       => json_encode($jsondecode_seller),
                                        'datetime'   => date('Y-m-d H:i:s'),
                                    );

                                    $transaction_last_value_seller->total          =  $seller_total;
                                    $transaction_last_value_seller->crypto_balance =  $seller_crypto_balance;


                                    //BUY Check Fees
                                    $buy_fees = $this->db->select('fees')->from('dbt_fees')->where('level', 'BUY')->get()->row();
                                    if ($buy_fees) {
                                        $buy_feesamount = ($buyer_complete_qty_log * $sellexchange->crypto_rate * $buy_fees->fees)/100;

                                    }else{
                                        $buy_feesamount = 0;
                                        
                                    }
                                    //SELL Check Fees
                                    $sell_fees = $this->db->select('fees')->from('dbt_fees')->where('level', 'SELL')->get()->row();
                                    if ($sell_fees) {
                                        $sell_feesamount = ($seller_complete_qty_log * $sellexchange->crypto_rate * $sell_fees->fees)/100;

                                    }else{
                                        $sell_feesamount = 0;
                                        
                                    }


                                    //Exchange Data =>Sell
                                    $exchangeselldata = array(
                                        'id'             => $sellexchange->id,
                                        'exchange_type'  => $sellexchange->exchange_type,
                                        'source_wallet'  => $sellexchange->source_wallet,
                                        'crypto_qty'     => $sellexchange->crypto_qty,
                                        'crypto_rate'    => $sellexchange->crypto_rate,
                                        'complete_qty'   => $seller_complete_qty,
                                        'available_qty'  => $seller_available_qty,
                                        'datetime'       => date('Y-m-d H:i:s'),
                                        'status'         => $seller_available_qty==0?1:2,
                                    );

                                    //Exchange Data =>Buyer
                                    $exchangebuydata = array(
                                        'id'             => $last_exchange->id,
                                        'exchange_type'  => $last_exchange->exchange_type,
                                        'source_wallet'  => $last_exchange->source_wallet,
                                        'crypto_qty'     => $last_exchange->crypto_qty,
                                        'crypto_rate'    => $sellexchange->crypto_rate,
                                        'complete_qty'   => $buyer_complete_qty, 
                                        'available_qty'  => $buyer_available_qty,
                                        'datetime'       => date('Y-m-d H:i:s'), 
                                        'status'         => $buyer_available_qty==0?1:2,
                                    );



                                    //Exchange Sell+Buy Update
                                    $this->exchange_model->exchangeUpdate($exchangeselldata);
                                    $this->exchange_model->exchangeUpdate($exchangebuydata);


                                    // Exchange Log Data =>Seller
                                    $exchangelogdata_seller = array(
                                        'exc_id'            => $last_exchange->id,
                                        'exchange_type'     => $sellexchange->exchange_type,
                                        'source_wallet'     => $sellexchange->source_wallet,
                                        'destination_wallet'=> $sellexchange->source_wallet,
                                        'crypto_qty'        => $sellexchange->crypto_qty,
                                        'crypto_rate'       => $sellexchange->crypto_rate,
                                        'complete_qty'      => $seller_complete_qty_log,
                                        'available_qty'     => $seller_available_qty_log,
                                        'fees_amount'       => $sell_feesamount,
                                        'datetime'          => date('Y-m-d H:i:s'),
                                        'status'            => $seller_available_qty_log==0?1:2,
                                    );

                                    //Exchange Log Data =>Buyer
                                    $exchangelogdata_buyer = array(
                                        'exc_id'            => $last_exchange->id,
                                        'exchange_type'     => $last_exchange->exchange_type,
                                        'source_wallet'     => $last_exchange->source_wallet,
                                        'destination_wallet'=> $last_exchange->source_wallet,
                                        'crypto_qty'        => $last_exchange->crypto_qty,
                                        'crypto_rate'       => $sellexchange->crypto_rate,
                                        'complete_qty'      => $buyer_complete_qty_log,
                                        'available_qty'     => $buyer_available_qty_log,
                                        'fees_amount'       => $buy_feesamount,
                                        'datetime'          => date('Y-m-d H:i:s'),
                                        'status'            => $buyer_available_qty_log==0?1:2,
                                    );

                                    //Exchange Sell+Buy Log data
                                    $this->exchange_model->exchangeLogCreate($exchangelogdata_seller);
                                    $this->exchange_model->exchangeLogCreate($exchangelogdata_buyer); 

                                    $seller_id = $this->exchange_model->retriveUserIdByCryptoWallet($sellexchange->source_wallet);
                                    //Seller Balance Update
                                    $balance = $this->exchange_model->checkBalance($seller_id);
                                    if ($balance) {
                                        $total = ($buyer_complete_qty_log*$sellexchange->crypto_rate)+@$balance->balance-$sell_feesamount;
                                        $this->db->set('balance', $total)->where('user_id', $seller_id)->update("dbt_balance");

                                        $balance_log = array(
                                            'balance_id'        => $balance->id,
                                            'user_id'           => $seller_id,
                                            'transaction_type'  => "SELL",
                                            'transaction_amount'=> $buyer_complete_qty_log*$sellexchange->crypto_rate,
                                            'transaction_fees'  => $sell_feesamount,
                                            'ip'                => $this->input->ip_address(),
                                            'date'              => date('Y-m-d H:i:s'),
                                        );
                                        //User Balance log
                                        $this->db->insert('dbt_balance_log', $balance_log); 


                                    }else{

                                        $balance_insert = array(
                                            'user_id'       => $seller_id,
                                            'balance'       => ($buyer_complete_qty_log*$sellexchange->crypto_rate)-$sell_feesamount,
                                            'last_update'   => date('Y-m-d H:i:s'),
                                        );

                                        $this->db->insert('dbt_balance', $balance_insert);

                                        $balance = $this->exchange_model->checkBalance();

                                    }                                    

                                    
                                    // New Transaction Update
                                    $this->exchange_model->updateUserWalletData($walletdata_seller);
                                    $this->exchange_model->updateUserWalletData($walletdata_buyer);
                                    
                                    if($sellexchange->crypto_rate<$rate){

                                        // $totalexchanceqty = $last_exchange->available_qty-$sellexchange->available_qty<0?$sellexchange->available_qty-$last_exchange->available_qty:$last_exchange->available_qty-$sellexchange->available_qty;
                                        $totalexchanceqty = $buyer_complete_qty_log;

                                        $buyremeaningrate = $rate-$sellexchange->crypto_rate;
                                        $buyerbalence     = $buyremeaningrate*$totalexchanceqty;


                                        //*********** Fees when Adjustment ***********

                                        $returnfees = 0;
                                        $byerfees       = ($totalexchanceqty*$rate*@$fees->fees)/100;
                                        $sellerrfees    = ($totalexchanceqty*$sellexchange->crypto_rate*@$fees->fees)/100;

                                        $buyerreturnfees  = $byerfees-$sellerrfees;

                                        if($buyerreturnfees>0){

                                            $returnfees = $buyerreturnfees;
                                        }

                                        

                                        //$fees_adjust = ($buyerbalence * $last_exchange->fees_amount)/$last_exchange->crypto_qty;
                                        
                                        $buyeruserid      = $this->exchange_model->retriveUserIdByCryptoWallet($last_exchange->source_wallet);

                                        $balance_data = array(
                                            'user_id'    => $buyeruserid,
                                            'amount'     => $buyerbalence,
                                            'return_fees'=> $returnfees,
                                            'ip'         => $this->input->ip_address()
                                        );

                                        $this->exchange_model->balanceReturn($balance_data);

                                    }

                                    
                                    //Historycal data generate
                                    $where      = "(datetime >= DATE_SUB(NOW(), INTERVAL 1 hour))"; 
                                    $where1     = "(exchange_type='BUY')";
                                    $where2     = "(datetime >= DATE_SUB(DATE_SUB(NOW(), INTERVAL 1 hour), INTERVAL 1 hour)) AND (datetime <= DATE_SUB(NOW(), INTERVAL 1 hour))";



                                    $total_coin_supply = $this->db->select_sum('complete_qty')->from('dbt_exchange_details')->where($where1)->order_by('datetime', 'desc')->get()->row();

                                    $h1_bid_high_price = $this->db->select_max('crypto_rate')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->get()->row();

                                    $h1_bid_low_price = $this->db->select_min('crypto_rate')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->get()->row();

                                    $h1_coin_supply = $this->db->select_sum('complete_qty')->from('dbt_exchange_details')->where($where1)->order_by('datetime', 'desc')->get()->row();
                                    //Price change value in up/down
                                    $last_price_query = $this->db->select('*')->from('dbt_exchange_details')->order_by('datetime', 'desc')->get()->row();

                                    if($h1_bid_high_price->crypto_rate==''){
                                        $high1 = $sellexchange->crypto_rate;

                                    }else{

                                        if ($h1_bid_high_price->crypto_rate<$sellexchange->crypto_rate) {
                                            $high1 = $sellexchange->crypto_rate;

                                        }else{
                                            $high1 = $h1_bid_high_price->crypto_rate;

                                        }

                                    }

                                    if($h1_bid_low_price->crypto_rate==''){
                                         $low1 = $sellexchange->crypto_rate;

                                    }else{

                                        if ($h1_bid_low_price->crypto_rate<$sellexchange->crypto_rate) {
                                            $low1 = $sellexchange->crypto_rate;

                                        }else{
                                            $low1 = $h1_bid_low_price->crypto_rate;

                                        }

                                    }

                                    if ($sellexchange->crypto_rate<@$last_price_query->last_price) {
                                        $price_change_1h = -($high1 - $low1);

                                    }else{
                                        $price_change_1h = $high1 - $low1;

                                    }

                                    $where = "(datetime >= DATE_SUB(NOW(), INTERVAL 24 hour))"; 
                                    $where1 = "(exchange_type='BUY')";
                                    $where2 = "(datetime >= DATE_SUB(NOW(), INTERVAL 24 hour) AND exchange_type='BUY')"; 
                                    $where3 = "(datetime >= DATE_SUB(DATE_SUB(NOW(), INTERVAL 24 hour), INTERVAL 24 hour)) AND (datetime <= DATE_SUB(NOW(), INTERVAL 24 hour))";

                                    $h24_last_price_avg = $this->db->select_avg('crypto_rate')->from('dbt_exchange_details')->where($where2)->order_by('datetime', 'desc')->get()->row();
                                    $pre24h_last_price = $this->db->select('crypto_rate')->from('dbt_exchange_details')->where($where3)->order_by('datetime', 'desc')->get()->row();
                                    $pre24h_last_price_avg = $this->db->select_avg('crypto_rate')->from('dbt_exchange_details')->where($where3)->order_by('datetime', 'desc')->get()->row();
                                    $h24_coin_supply = $this->db->select_sum('complete_qty')->from('dbt_exchange_details')->where($where1)->order_by('datetime', 'desc')->get()->row();
                                    $h24_bid_high_price = $this->db->select_max('crypto_rate')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->get()->row();
                                    $h24_bid_low_price = $this->db->select_min('crypto_rate')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->get()->row();

                                    if($h24_bid_high_price->crypto_rate==''){
                                        $high24 = $sellexchange->crypto_rate;

                                    }else{

                                        if ($h24_bid_high_price->crypto_rate<$sellexchange->crypto_rate) {
                                            $high24 = $sellexchange->crypto_rate;

                                        }else{
                                            $high24 = $h24_bid_high_price->crypto_rate;

                                        }

                                    }

                                    if($h24_bid_low_price->crypto_rate==''){
                                        $low24 = $sellexchange->crypto_rate;

                                    }else{

                                        if ($h24_bid_low_price->crypto_rate<$sellexchange->crypto_rate) {
                                            $low24 = $sellexchange->crypto_rate;

                                        }else{
                                            $low24 = $h24_bid_low_price->crypto_rate;

                                        }

                                    }

                                    if ($sellexchange->crypto_rate<@$last_price_query->last_price) {
                                        $price_change_24h = -($high24 - $low24);

                                    }else{
                                        $price_change_24h = $high24 - $low24;

                                    }

                                    $exchange_history = array(
                                        //'coin_symbol'       => $buytrade->currency_symbol,
                                        //'market_symbol'     => $buytrade->market_symbol,
                                        'last_price'          => $sellexchange->crypto_rate,
                                        'total_coin_supply'   => @$buyer_complete_qty_log+@$total_coin_supply->complete_qty,
                                        'price_high_1h'       => ($h1_bid_high_price->crypto_rate=='')?$sellexchange->crypto_rate:(($h1_bid_high_price->crypto_rate<$sellexchange->crypto_rate)?$sellexchange->crypto_rate:$h1_bid_high_price->crypto_rate),
                                        'price_low_1h'      => ($h1_bid_low_price->crypto_rate=='')?$sellexchange->crypto_rate:(($h1_bid_low_price->crypto_rate>$sellexchange->crypto_rate)?$sellexchange->crypto_rate:$h1_bid_low_price->crypto_rate),
                                        'price_change_1h'   => ($price_change_1h=='')?0:$price_change_1h,
                                        'volume_1h'         => ($h1_coin_supply->complete_qty=='')?0:$h1_coin_supply->complete_qty,
                                        'price_high_24h'     => ($h24_bid_high_price->crypto_rate=='')?$sellexchange->crypto_rate:(($h24_bid_high_price->crypto_rate<$sellexchange->crypto_rate)?$sellexchange->crypto_rate:$h24_bid_high_price->crypto_rate),
                                        'price_low_24h'      => ($h24_bid_low_price->crypto_rate=='')?$sellexchange->crypto_rate:(($h24_bid_low_price->crypto_rate>$sellexchange->crypto_rate)?$sellexchange->crypto_rate:$h24_bid_low_price->crypto_rate),
                                        'price_change_24h'   => ($price_change_24h=='')?0:$price_change_24h,
                                        'volume_24h'         => ($h24_coin_supply->complete_qty=='')?0:$h24_coin_supply->complete_qty,
                                        'open'              => $sellexchange->crypto_rate,
                                        'close'             => $sellexchange->crypto_rate,
                                        'volumefrom'        => @$buyer_complete_qty_log+@$total_coin_supply->complete_qty,
                                        'volumeto'          => ($h24_coin_supply->complete_qty=='')?0:$h24_coin_supply->complete_qty,
                                        'date'              => date('Y-m-d H:i:s'),
                                    );

                                    $this->db->insert('dbt_exchange_history', $exchange_history);
                                }                            

                            }
                            //foreach loop
                        }                      

                        $this->session->set_flashdata('message','Buy request successfully!');
                            redirect('customer/exchange/wrightMessage');
                    }

                }else{

                    $this->session->set_flashdata('exception', "You don't have sufficent Balance!");
                    redirect('customer/exchange/wrightException');

                }
                //Balance check
                
            }
            //Buy + Sell

        }
        //From Valid

        $data['coin_owner_wallet'] = $this->db->select('*')->from('coin_setup')->get()->row();

        $data['content'] = $this->load->view('customer/pages/exchange', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data);  
    }

    public function exchange_history(){

        $source_wallet = $this->exchange_model->retriveUserCryptoWallet();
       
        $data['exchange'] = $this->exchange_model->userSingelExchnageHistory($source_wallet);

        $data['content'] = $this->load->view('customer/pages/exchange_history', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data); 

    }

    public function exchange_opened(){

        $source_wallet = $this->exchange_model->retriveUserCryptoWallet();
       
        $data['exchange'] = $this->exchange_model->userExchangeOpened($source_wallet);

        $data['content'] = $this->load->view('customer/pages/exchange_opened', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data); 

    }

    public function exchange_canceled(){

        $source_wallet = $this->exchange_model->retriveUserCryptoWallet();
       
        $data['exchange'] = $this->exchange_model->userExchangeCanceled($source_wallet);

        $data['content'] = $this->load->view('customer/pages/exchange_canceled', $data, true);
        $this->load->view('customer/layout/main_wrapper', $data); 

    }

    public function exchange_cancel($id = null){

        $exchange = $this->exchange_model->single($id);

        $user_id = $this->exchange_model->retriveUserIdByCryptoWallet($exchange->source_wallet);

        if (!($this->session->userdata('user_id') == $user_id)){
            $this->session->set_flashdata('exception', "There is no exchange for cancel");
            redirect("customer/exchange/exchange_history");

        }else{

            if ($exchange->status==2) {
                $cancelexc = array(
                    'status' => 0
                );

                if ($exchange->exchange_type=='BUY') {

                    $balance = $this->exchange_model->checkBalance();


                    //BUY Check Fees
                    $buy_fees = $this->db->select('fees')->from('dbt_fees')->where('level', 'BUY')->get()->row();
                    if ($buy_fees) {
                        $buy_feesamount = ($exchange->available_qty*$exchange->crypto_rate * $buy_fees->fees)/100;

                    }else{
                        $buy_feesamount = 0;
                        
                    }

                    //User Financial Log
                    $exccanceldata = array(
                        'user_id'            => $user_id,
                        'balance_id'         => @$balance->id,
                        'transaction_type'   => 'EXCHANGE_CANCEL',
                        'transaction_amount' => $exchange->available_qty*$exchange->crypto_rate,
                        'transaction_fees'   => $buy_feesamount, 
                        'ip'                 => $this->input->ip_address(),
                        'date'               => date('Y-m-d H:i:s')
                    );

                    $this->exchange_model->balancelog($exccanceldata);

                    $new_balance = @$balance->balance+($exchange->available_qty*$exchange->crypto_rate)+$buy_feesamount;
                    $this->db->set('balance', $new_balance)->where('user_id', $user_id)->update("dbt_balance");

                }

                $this->db->where('id', $id)->update("dbt_exchange", $cancelexc);                                

                $traderlog = array(
                    'exc_id'            => $exchange->id,
                    'exchange_type'     => $exchange->exchange_type,
                    'source_wallet'     => $exchange->source_wallet,
                    'destination_wallet'=> $exchange->source_wallet,
                    'crypto_qty'        => $exchange->crypto_qty,
                    'crypto_rate'       => $exchange->crypto_rate,
                    'complete_qty'      => $exchange->complete_qty,
                    'available_qty'     => $exchange->available_qty,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'status'            => 0,
                );

                $this->db->insert('dbt_exchange_details', $traderlog);

                $this->session->set_flashdata('message', "Request Canceled");
                redirect("customer/exchange/exchange_history");

            }else{
                $this->session->set_flashdata('exception', "Something went wrong!!!");
                redirect("customer/exchange/exchange_history");
            }
            
        }

    }


    public function trade_charthistory()
    {
        
        $coinhistory = $this->db->select('*')->from('dbt_exchange_history')->order_by('date', 'asc')->get()->result();

        echo json_encode($coinhistory);

    }

    public function market_depth()
    {    

        $asks = array();
        $bids = array();

        $where = "exchange_type = 'SELL'"; 
        $coinhistory = $this->db->select('*')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->limit(100)->get()->result();
        $x = 0;
        $y = 0;
        foreach ($coinhistory as $key => $value) {
            array_push($asks, array($x,$y));
            $x = $value->crypto_rate;
            $y = $value->complete_qty;

        }

        $where = "exchange_type = 'BUY'"; 
        $coinhistory = $this->db->select('*')->from('dbt_exchange_details')->where($where)->order_by('datetime', 'desc')->limit(100)->get()->result();
        foreach ($coinhistory as $key => $value) {
            $x = $value->crypto_rate;
            $y = $value->complete_qty;
            array_push($bids, array($x,$y));

        }

        echo json_encode(
            array('asks' => $asks,
                  'bids'  => $bids,
            )
        );

    }

    public function getPercentOfNumber($number, $percent){
        return ($percent / 100) * $number;

    }

    public function getExchangRate()
    {
        $coinhistory = $this->db->select('*')->from('dbt_exchange_history')->order_by('date', 'desc')->get()->row();

        echo json_encode(
            array('coinhistory' => @$coinhistory)
        );

    }

    public function getSellavaible()
    {
        $sellavaiable = $this->db->select_sum('available_qty')
                            ->from('dbt_exchange')
                            ->where('exchange_type','SELL')
                            ->get()
                            ->row();

        $transaction = $this->token_model->retriveUserCryptoTransaction();
        $total_balance = 0;
        if (!empty($transaction)) {
            $data = json_decode($transaction->data); 
            foreach ($data as $key => $value) { 
                foreach ($value as $keys => $values) { 
                    $total_balance =  @$values->crypto_balance;
                }
            }
        }

        echo json_encode(
            array('sellavaiable' => @$sellavaiable->available_qty,'cryptobalance' => @$total_balance)
        );

    }

    public function wrightException()
    {
        echo json_encode(
            array('type'    => 0,
                  'message' => $this->session->flashdata('exception'),
            )
        );
    }


    public function wrightMessage()
    {
        echo json_encode(
            array('type'    => 1,
                  'message' => $this->session->flashdata('message'),
            )
        );
    }


}