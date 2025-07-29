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
                <?php echo form_open_multipart("backend/coin_setup") ?>
                    <div class="form-group row">
                        <label for="coin_name" class="col-sm-3 col-form-label">Name<i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input name="coin_name" class="form-control" value="<?php echo $coin_setup->name ?>" type="text" id="coin_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="coin_symbol" class="col-sm-3 col-form-label">Symbol<i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input name="coin_symbol" class="form-control"  value="<?php echo $coin_setup->symbol ?>" type="text" id="coin_symbol">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pair_with" class="col-sm-3 col-form-label">Pair With<i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select class="form-control basic-single" name="pair_with" id="pair_with">
                                <?php
                                    //if($check_system_run>0){
                                ?>
                                        <!-- <option value="<?php //echo $coin_setup->pair_with; ?>" selected><?php //echo $coin_setup->pair_with; ?></option> -->

                                <?php //} else{ 
                                    foreach ($currency as $key => $value) { ?>
                                        <option <?php echo $coin_setup->pair_with==$value->symbol? "selected":null ?> value="<?php echo $value->symbol; ?>"><?php echo $value->symbol; ?></option>
                                <?php } //} ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo display('submit')?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <div class="notice-board">
                                <p>Note: Please don't change pairing currency, when system (or Bussines) start. Change it before start Bussines.</p>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
