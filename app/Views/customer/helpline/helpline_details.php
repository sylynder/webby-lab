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
                                <?php $read_unread = $value->status==1?"new-message":""?>
                                <div class="inbox-mail-details p-20 border-btm">
                                    <p><strong>Admin</strong></p>
                                    <p <?php echo $read_unread;?>><span><?php echo $value->messege;?></span></p>
                                </div>        
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>