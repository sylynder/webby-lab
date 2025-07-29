<div class="row">
	<div class="col-sm-7 col-md-7">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2>Upload Document For Profile Verification</h2>
                </div>
            </div>

            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php echo form_open_multipart("backend/user/user/pending_user_verification/".@$user->user_id) ?>
				<?php echo form_hidden('user_id', @$user->user_id) ?>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Verification Type</label>
                        <div class="col-sm-8">
                            <?php echo @$user->verify_type ?>
                        </div>
                    </div>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <?php echo @$user->first_name ?></span>
                        </div>
                    </div>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Surname</label>
                        <div class="col-sm-8">
                            <?php echo @$user->last_name ?></span>
                        </div>
                    </div>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Gender</label>
                        <div class="col-sm-8">
                            <?php echo (@$user->gender==1)?'Male':'Female' ?></span>
                        </div>
                    </div>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">NID</label>
                        <div class="col-sm-8">
                            <?php echo @$user->id_number ?>
                        </div>
                    </div>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Doc I</label>
                        <div class="col-sm-8">
                        <?php if (@$user->document1) { ?>
                            <img src="<?php echo base_url(@$user->document1); ?>" class="img-responsive"/>
                            <a href="<?php echo base_url(@$user->document1); ?>" class="btn btn-success" download="<?php echo @$user->first_name."_".@$user->user_id."_1"; ?>">Download File</a>
                        <?php } ?>
                        </div>
                    </div>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Doc II</label>
                        <div class="col-sm-8">
                        <?php if (@$user->document2) { ?>
                            <img src="<?php echo base_url(@$user->document2); ?>" class="img-responsive"/>
                            <a href="<?php echo base_url(@$user->document2); ?>" class="btn btn-success" download="<?php echo @$user->first_name."_".@$user->user_id."_2"; ?>">Download File</a>	
                        <?php } ?>
                        </div>
                    </div>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Upload Document</label>
                        <div class="col-sm-8">
                            <?php 
                                $date=date_create(@$user->date);
                                echo date_format(@$date,"jS F Y");  
                            ?>
                        </div>
                    </div>
                	<div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Status</label>
                        <div class="col-sm-8">
                        	<h3>
                            <?php if (@$user->verified==0) { echo "Not Submited"; } ?>
                            <?php if (@$user->verified==1) { echo "Verified"; } ?>
                            <?php if (@$user->verified==2) { echo "Cancel"; } ?>
                            <?php if (@$user->verified==3) { echo "Processing"; } ?>
                            </h3>
                        </div>
                    </div>

                    <?php if (@$user->verified==3) { ?>

					<div>
                        <button type="submit" name="cancel" class="btn btn-primary" ><?php echo display("cancel") ?></button>
                        <button type="submit" name="approve" class="btn btn-success">Approve</button>
                    </div>
                    <?php } ?>

                <?php echo form_close() ?>

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-5 col-md-5">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo display('user_info') ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('user_id') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->user_id ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('username') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->username ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('referral_id') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->referral_id ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('language') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->language ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('firstname') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->first_name ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('lastname') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->last_name ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('email') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->email ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('mobile') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->phone ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('registered_ip') ?></label>
                        <div class="col-sm-8">
                            <?php echo @$user->ip ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
                        <div class="col-sm-8">
                            <?php echo (@$user->status==1)?display('active'):display('inactive'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Registered Date</label>
                        <div class="col-sm-8">
                            <?php 
                                $date=date_create(@$user->created);
                                echo date_format(@$date,"jS F Y");  
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>