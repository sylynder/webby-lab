<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd ">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php echo form_open_multipart("backend/menu_control/save") ?>
                    <div class="col-md-8 col-md-offset-2">
                        <fieldset>
                            <legend> Menu Control Settings </legend>
                            <div class="checkbox checkbox-primary"">
                                <input id="checkbox1" type="checkbox" value="1" name="ico_wallet" <?php echo $control->ico_wallet==1?"checked":""; ?>>
                                <label for="checkbox1">ICO Wallet</label>
                            </div>
                            <div class="checkbox checkbox-primary">
                                <input id="checkbox2" type="checkbox" value="1" name="exchange" <?php echo $control->exchange==1?"checked":""; ?>>
                                <label for="checkbox2">Exchange</label>
                            </div>
                            <div class="checkbox checkbox-primary">
                                <input id="checkbox3" type="checkbox" value="1" name="package"  <?php echo $control->package==1?"checked":""; ?>>
                                <label for="checkbox3">Package</label>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-8 col-md-offset-5">
                        <div class="mt-20">
                        <button type="submit" class="btn btn-success"><?php echo display("save") ?></button>
                        </div>
                    </div>
                    <?php echo form_close() ?>
                </div> 
            </div>
        </div>
    </div>
</div>




 