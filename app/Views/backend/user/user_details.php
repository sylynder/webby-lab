<div class="row">
    <div class="col-sm-6 col-md-6">
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
                            <?php echo $user->user_id ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('referral_id') ?></label>
                        <div class="col-sm-8">
                            <?php echo $user->referral_id ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('language') ?></label>
                        <div class="col-sm-8">
                            <?php echo $user->language ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('firstname') ?></label>
                        <div class="col-sm-8">
                            <?php echo $user->first_name ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('lastname') ?></label>
                        <div class="col-sm-8">
                            <?php echo $user->last_name ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('email') ?></label>
                        <div class="col-sm-8">
                            <?php echo $user->email ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('mobile') ?></label>
                        <div class="col-sm-8">
                            <?php echo $user->phone ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('registered_ip') ?></label>
                        <div class="col-sm-8">
                            <?php echo $user->ip ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
                        <div class="col-sm-8">
                            <?php echo ($user->status==1)?display('active'):display('inactive'); ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cid" class="col-sm-4 col-form-label">Registered Date</label>
                        <div class="col-sm-8">
                            <?php 
                                $date=date_create($user->created);
                                echo date_format($date,"jS F Y");  
                            ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





</div>


 