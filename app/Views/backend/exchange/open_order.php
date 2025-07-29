<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">                
                <?php if (!empty($open_trade)) { ?>
                <table class="table table-bordered table-hover" style="font-size:12px">
                    <thead>
                        <tr> 
                            <th><?php echo display('sl_no') ?></th>
                            <th>exchange</th>
                            <th>source</th>
                            <!-- <th>destination_wallet</th> -->
                            <th>Crypto Qty</th>
                            <th>Crypto Eate</th>
                            <th>Complete Qty</th>
                            <th>Available Qty</th>
                            <th>Datetime</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl=1;  foreach ($open_trade as $key => $value) { ?>
                        <tr>
                            <td><?php echo $sl ?></td>
                            <td><?php echo $value->exchange_type ?></td>
                            <td><?php echo $value->source_wallet ?></td>
                            <!-- <td><?php echo $value->destination_wallet ?></td> -->
                            <td><?php echo $value->crypto_qty ?></td>
                            <td><?php echo $value->crypto_rate ?></td>
                            <td><?php echo $value->complete_qty ?></td>
                            <td><?php echo $value->available_qty ?></td>
                            <td><?php echo $value->datetime ?></td>
                            <td><?php echo $value->status==0?"<p class='btn btn-danger btn-xs'>Canceled</p>":($value->status==1?"<p class='btn btn-success btn-xs'>Completed</p>":"<p class='btn btn-primary btn-xs'>Running</p>") ?></td>
                        </tr>
                        <?php $sl++; } ?>                    
                    </tbody>
                </table>
                <?php  } ?>
                <?php echo $links;?>
            </div> 
        </div>
    </div>
</div>
<!-- 
    id
    exchange_type
    source_wallet
    crypto_qty
    crypto_rate
    complete_qty
    available_qty
    datetime
    status

    id
    exc_id
    exchange_type1
    source_wallet1
    destination_wallet
    crypto_qty1
    crypto_rate1
    complete_qty1
    available_qty1
    datetime1
    status1 -->