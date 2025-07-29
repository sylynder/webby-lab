<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                    <div class="col-sm-3 col-md-3 pull-right">
                        <a class="btn btn-success w-md m-b-5 pull-right" href="<?php echo base_url("backend/coin_release/form") ?>"><i class="fa fa-plus" aria-hidden="true"></i> Coin Release</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <table class="datatable2 table table-bordered table-hover">
                    <thead>
                        <tr> 
                            <th><?php echo display('sl_no') ?></th>
                            <th>Round Name</th>
                            <th>Day</th>
                            <th>Target</th>
                            <th>Fillup Target</th>
                            <th class="hide">Exchange Currency</th>
                            <th>Date</th>
                            <th><?php echo display('status') ?></th>
                            <th><?php echo display('action') ?></th> 
                        </tr>
                    </thead>    
                    <tbody>
                        <?php if (!empty($coin_release)) ?>
                        <?php $sl = 1; ?>
                        <?php foreach ($coin_release as $value) { 

                            $percent  = ($value->fillup_target*100)/$value->target;
                        ?>
                        <tr>
                            <td><?php echo $sl++; ?></td>
                            <td><?php echo $value->round_name; ?></td>
                            <td><?php echo $value->day; ?></td>
                            <td><?php echo $value->target; ?></td>
                            <td><div class="progress progress-lg" style="background: darkgray;">
                                    <div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $percent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percent ?>%">
                                        <?php echo $percent ?>%
                                    </div>
                                </div>
                            </td>
                            <td class="hide"><?php echo $value->exchange_currency; ?></td>
                            <td><?php echo $value->start_date; ?></td>
                            <td><?php echo (($value->status==1)?display('active'):display('inactive')); ?></td>
                            <td>
                                <a href="<?php echo base_url("backend/coin_release/form/$value->id") ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="fa fa-pencil" aria-hidden="true"></i></a>
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

 