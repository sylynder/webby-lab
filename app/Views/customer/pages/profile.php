<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">

                <?php echo form_open_multipart("customer/profile/update") ?>
                <?php echo form_hidden('uid', $profile->id) ?>
  
                    <div class="row">

                        <div class="form-group col-lg-6">
                            <label><?php echo display("username") ?> <span class="text-danger">*</span></label>
                            <input type="text" value="<?php echo $profile->username ?>" class="form-control" name="username" placeholder="<?php echo display("username") ?>" disabled>
                        </div>

                        <div class="form-group col-lg-6">
                            <label><?php echo display("referral_id") ?> <span class="text-danger">*</span></label>
                            <input type="text" value="<?php echo $profile->referral_id ?>" class="form-control" name="referral_id" placeholder="<?php echo display("sponsor_name") ?>" disabled>
                        </div>

                        <div class="form-group col-lg-6">
                            <label><?php echo display("firstname") ?> <span class="text-danger">*</span></label>
                            <input type="text" value="<?php echo $profile->first_name ?>" class="form-control" name="first_name" placeholder="<?php echo display("firstname") ?>">
                        </div>

                        <div class="form-group col-lg-6">
                            <label><?php echo display("lastname") ?> <span class="text-danger">*</span></label>
                            <input type="text" value="<?php echo $profile->last_name ?>" class="form-control" name="last_name" placeholder="<?php echo display("lastname") ?>">
                        </div>

                        <div class="form-group col-lg-6">
                            <label><?php echo display("email") ?> <span class="text-danger">*</span></label>
                            <input type="text" value="<?php echo $profile->email ?>" class="form-control" name="email" placeholder="<?php echo display("email") ?>">
                        </div>

                        <div class="form-group col-lg-6">
                            <label><?php echo display("mobile") ?> <span class="text-danger">*</span></label>
                            <input type="text" value="<?php echo $profile->phone ?>" id="mobile" class="form-control" name="mobile" placeholder="<?php echo display("mobile") ?>">
                        </div>

                        <div class="form-group col-lg-6">
                                <label><?php echo display('language') ?></label>
                                
                                <select name="language" class="form-control">
                                    <?php 
                                        foreach($languageList as $key => $val){
                                            echo '<option '.($profile->language==$key?'selected':'').' value="'.$key.'">'.$val.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        
                    </div> 

                    <div>
                        <button type="submit" class="btn btn-success"><?php echo display("update") ?></button>
                    </div>
                <?php echo form_close() ?>

            </div>
        </div>
    </div>
</div>

 