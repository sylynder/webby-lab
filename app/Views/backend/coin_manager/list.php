<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                    <div class="col-sm-3 col-md-3 pull-right">
                        <a class="btn btn-success w-md m-b-5 pull-right" href="<?php echo base_url("backend/coinpair/form") ?>"><i class="fa fa-plus" aria-hidden="true"></i> Coin Pair</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <table class="datatable2 table table-bordered table-hover">
                    <thead>
                        <tr> 
                            <th><?php echo display('sl_no') ?></th>
                            <th>Market</th>
                            <th>Coin</th>
                            <th>Name</th>
                            <th>Full Name</th>
                            <th>Symbol</th>
                            <th><?php echo display('status') ?></th>
                            <th><?php echo display('action') ?></th> 
                        </tr>
                    </thead>    
                    <tbody>
                        <?php if (!empty($coinpair)) ?>
                        <?php $sl = 1; ?>
                        <?php foreach ($coinpair as $value) { ?>
                        <tr>
                            <td><?php echo $sl++; ?></td> 
                            <td>
                                <?php foreach ($market as $mvalue) { ?>
                                <?php echo ($value->market_symbol==$mvalue->symbol)?$mvalue->full_name:'' ?>
                                <?php } ?>
                            </td>
                            <td>
                                <?php foreach ($coins as $cvalue) { ?>
                                <?php echo ($value->currency_symbol==$cvalue->symbol)?$cvalue->full_name:'' ?>
                                <?php } ?>
                            </td>
                            <td><?php echo $value->name; ?></td>
                            <td><?php echo $value->full_name; ?></td>
                            <td><?php echo $value->symbol; ?></td>
                            <td><?php echo (($value->status==1)?display('active'):display('inactive')); ?></td>
                            <td>
                                <a href="<?php echo base_url("backend/coinpair/form/$value->id") ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        <?php } ?>  
                    </tbody>
                </table>
                <?php echo $links; ?>
            </div> 
        </div>
    </div>
</div>

 