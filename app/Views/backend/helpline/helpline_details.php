<div class="row">
    <div class="col-sm-12">
        <div class="mailbox">
            <div class="mailbox-body">
                <div class="mailbox-body">
                    <div class="row m-0">
                        <div class="col-xs-12 col-sm-12 col-md-12 p-0 inbox-mail">
                            <div class="inbox-avatar p-20 border-btm">
                                <img src="<?php echo base_url(!empty($settings->logo)?$settings->logo:"assets/images/icons/sms_user.png"); ?>" class="border-green hidden-xs hidden-sm" alt="">
                                <div class="inbox-avatar-text">
                                    <div class="avatar-name"><strong>From: </strong>
                                        <?php echo $usermessege->email; ?>
                                    </div>
                                    <div><small><strong>Subject: </strong> <?php echo $usermessege->subject;?></small></div>
                                </div>
                                <div class="inbox-date text-right hidden-xs hidden-sm">
                                    <div><small><?php echo $usermessege->date_time;?></small></div>
                                </div>
                            </div>

                            <div class="inbox-mail-details p-20 border-btm">
                                <p><strong><?php echo $usermessege->first_name." ".$usermessege->last_name; ?></strong></p>
                                <p><span><?php echo $usermessege->messege;?></span></p>
                               
                            </div>
                            <?php foreach ($adminmessege as $key => $value) { ?>
                                <div class="inbox-mail-details p-20 border-btm">
                                    <p><strong>Admin</strong></p>
                                    <p><span><?php echo $value->messege;?></span></p>
                                </div>        
                            <?php } ?>
                            <div class="inbox-mail-details p-20">
                                <?php echo form_open(base_url('backend/helpline/send_message/'.$usermessege->id),'id="textform" name="textform"');?>
                                    <div class="form-group row">
                                        <label for="subject" class="col-sm-2 col-form-label">Subject <i class="text-danger">*</i></label>
                                        <div class="col-sm-7">
                                            <input name="subject" class="form-control" placeholder="Subject" type="text" id="subject">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="message" class="col-sm-2 col-form-label">Message <i class="text-danger">*</i></label>
                                        <div class="col-sm-7">
                                            <textarea rows="7" class="form-control" name="message" id="message" placeholder="Message"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-sm-offset-5">
                                            <button type="submit" class="btn btn-primary"><?php echo display('send')?></button>
                                        </div>
                                    </div>
                                <?php form_close();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>