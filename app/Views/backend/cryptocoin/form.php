<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="border_preview">
                    <?php echo form_open_multipart("backend/cryptocoin/form/$cryptocoin->id") ?>
                    <?php echo form_hidden('id', $cryptocoin->id) ?> 
                        <div class="form-group row">
                            <label for="cid" class="col-sm-4 col-form-label">Coin Id<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input name="cid" value="<?php echo $cryptocoin->cid ?>" class="form-control" placeholder="Coin Id" type="text" id="cid">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="symbol" class="col-sm-4 col-form-label">Symbol<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input name="symbol" value="<?php echo $cryptocoin->symbol ?>" class="form-control" placeholder="Symbol" type="text" id="symbol">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="coin_name" class="col-sm-4 col-form-label">Coin Name<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input name="coin_name" value="<?php echo $cryptocoin->coin_name ?>" class="form-control" placeholder="Coin Name" type="text" id="coin_name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="full_name" class="col-sm-4 col-form-label">Coin Full Name</label>
                            <div class="col-sm-8">
                                <input name="full_name" value="<?php echo $cryptocoin->full_name ?>" class="form-control" placeholder="Coin Full Name" type="text" id="full_name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="algorithm" class="col-sm-4 col-form-label">Algorithm</label>
                            <div class="col-sm-8">
                                <input name="algorithm" value="<?php echo $cryptocoin->algorithm ?>" class="form-control" placeholder="Algorithm" type="number" id="algorithm">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rank" class="col-sm-4 col-form-label">Rank</label>
                            <div class="col-sm-8">
                                <input name="rank" value="<?php echo $cryptocoin->rank ?>" class="form-control" placeholder="Rank" type="number" id="rank">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="show_home" class="col-sm-4 col-form-label">Show Home</label>
                            <div class="col-sm-8">
                                <label class="radio-inline">
                                    <?php echo form_radio('show_home', '1', (($cryptocoin->show_home==1 || $cryptocoin->show_home==null)?true:false)); ?>Yes
                                 </label>
                                <label class="radio-inline">
                                    <?php echo form_radio('show_home', '0', (($cryptocoin->show_home=="0")?true:false) ); ?>No
                                 </label> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="coin_position" class="col-sm-4 col-form-label">Serial</label>
                            <div class="col-sm-8">
                                <input name="coin_position" value="<?php echo $cryptocoin->coin_position ?>" class="form-control" type="text" id="algorithm">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="image" class="col-sm-4 col-form-label">Coin Image/Icon/Logo</label>
                            <div class="col-sm-8">
                                <input name="image" value="<?php echo $cryptocoin->image ?>" class="form-control"  type="file" id="image">
                                <input type="hidden" name="image_old" value="<?php echo $cryptocoin->image ?>">
                                <?php if (!empty($cryptocoin->image)) { ?>
                                    <img src="<?php echo base_url("$cryptocoin->image") ?>" width="150">
                                 <?php } ?>                            
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
                            <div class="col-sm-8">
                                <label class="radio-inline">
                                    <?php echo form_radio('status', '1', (($cryptocoin->status==1 || $cryptocoin->status==null)?true:false)); ?>Active
                                 </label>
                                <label class="radio-inline">
                                    <?php echo form_radio('status', '0', (($cryptocoin->status=="0")?true:false) ); ?>Inactive
                                 </label> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-9 col-sm-offset-3">
                                <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                                <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo $cryptocoin->id?display("update"):display("create") ?></button>
                            </div>
                        </div>
                    <?php echo form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 