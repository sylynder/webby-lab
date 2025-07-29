<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open_multipart("backend/user/user/form/$user->id") ?>
                <?php echo form_hidden('id', $user->id) ?>
                <?php echo form_hidden('user_id', $user->user_id) ?>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label><?php echo display("firstname") ?> <span class="text-danger">*</span></label>
                            <input type="text" value="<?php echo $user->first_name ?>" class="form-control" name="first_name" placeholder="<?php echo display("firstname") ?>">
                        </div>
                        <div class="form-group col-lg-6">
                            <label><?php echo display("lastname") ?></label>
                            <input type="text" value="<?php echo $user->last_name ?>" class="form-control" name="last_name" placeholder="<?php echo display("lastname") ?>">
                        </div>
                        <div class="form-group col-lg-6">
                            <label><?php echo display("referral_id") ?></label>
                            <input type="text" value="<?php echo $user->referral_id ?>" class="form-control" <?php echo $user->id?'disabled':'' ?> name="referral_id" placeholder="<?php echo display("sponsor_name") ?>">
                        </div>                        
                        <div class="form-group col-lg-6">
                            <label><?php echo display("email") ?> <span class="text-danger">*</span></label>
                            <input type="text" value="<?php echo $user->email ?>" class="form-control" name="email" placeholder="<?php echo display("email") ?>">
                        </div>
                        <div class="form-group col-lg-6">
                            <label><?php echo display("password") ?> <span class="text-danger">*</span></label>
                            <input type="password" value="" class="form-control" name="password" placeholder="<?php echo display("password") ?>">
                        </div>
                        <div class="form-group col-lg-6">
                            <label><?php echo display("mobile") ?></label>
                            <input type="text" value="<?php echo $user->phone ?>" id="mobile" class="form-control" name="mobile" placeholder="<?php echo display("mobile") ?>">
                        </div>
                        <div class="form-group col-lg-6">
                            <label><?php echo display("conf_password") ?> <span class="text-danger">*</span></label>
                            <input type="password" value="" class="form-control" name="conf_password" placeholder="<?php echo display("conf_password") ?>">
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="status" class="col-sm-3 col-form-label"><?php echo display('status')?> <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <?php echo form_radio('status', '1', (($user->status==1 || $user->status==null)?true:false)); ?><?php echo display('active') ?>
                                </label>
                                <label class="radio-inline">
                                    <?php echo form_radio('status', '0', (($user->status=="0")?true:false) ); ?><?php echo display('inactive') ?>
                                </label> 
                            </div>
                        </div>

                    </div> 
                    <div>
                        <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary"><?php echo display("cancel") ?></a>
                        <button type="submit" class="btn btn-success"><?php echo $user->id?display("update"):display("register") ?></button>
                    </div>

                <?php echo form_close() ?>

            </div>
        </div>
    </div>
</div>

 