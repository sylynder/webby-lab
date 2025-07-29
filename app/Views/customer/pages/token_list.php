<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">                
                <?php if (!empty($transaction)) { ?>
                <?php $data = json_decode($transaction->data);  ?>
                <?php if (!empty($data)) { ?>

                <?php foreach ($data as $key => $value) { ?> 
                    <h3 class="text-center">Address:- &nbsp; &nbsp; <?php echo $key; ?></h3>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr> 
                            <th><?php echo display('sl_no') ?></th>
                            <th>Transaction ID</th>
                            <th>Source Address</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Price in</th>
                            <th><?php echo display('total') ?></th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sl = 1;
                            $total_balance = 0;
                            
                            foreach ($value as $keys => $values) { 
                                if (!empty($values)) {
                        ?>
                        <tr>
                            <td><?php echo $sl++; ?></td>                            
                            <td><?php echo $values->id; ?></td>
                            <td><?php echo @$values->source_wallet; ?></td>
                            <td><?php echo @$values->crypto_qty; ?></td>
                            <td><?php echo @$values->crypto_rate; ?></td>
                            <td><?php echo @$values->exchange_currency; ?></td>
                            <td><?php echo @$values->total; ?></td>
                            <td><?php echo @$values->crypto_balance; ?></td>
                        </tr>
                        <?php $total_balance =  @$values->crypto_balance; ?>                        
                        <?php } } ?>
                        <tr>
                            <td colspan="7" class="text-right"><b>Total <?php echo $coin_setup->symbol; ?></b></td>
                            <td><b><?php echo $total_balance; ?></b></td>
                        </tr>                     
                    </tbody>
                </table>
                <?php  } } } ?>

            </div> 
        </div>
    </div>
</div>

 