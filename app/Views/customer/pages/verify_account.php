<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <div class="border_preview">
                    <div class="profile-verify">
                        <?php
                            if($verify_status->verified==0){
                        ?>
                            <?php echo form_open_multipart("customer/verify_account") ?>
                            <div class="form-group row">
                                <label for="verify_type" class="col-sm-4 col-form-label">Verify Type</label>
                                <div class="col-sm-8">
                                    <select class="form-control basic-single" name="verify_type" required id="verify_type">
                                        <option selected>Select Option</option>
                                        <option value="passport">Passport</option>
                                        <option value="driving_license">Driver's license</option>
                                        <option value="nid">Government-issued ID Card</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="first_name" class="col-md-4 col-form-label">Given Name <i class="text-danger">*</i></label>
                                <div class="col-md-8">
                                    <input name="first_name" type="text" class="form-control" id="first_name" placeholder="" value="" required="">
                                </div>
                            </div>                        
                            <div class="form-group row">
                                <label for="last_name" class="col-md-4 col-form-label">Surname <i class="text-danger">*</i></label>
                                <div class="col-md-8">
                                    <input name="last_name" type="text" class="form-control" id="last_name" placeholder="" value="" required="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="id_number" class="col-md-4 col-form-label">Passport/NID/License Number <i class="text-danger">*</i></label>
                                <div class="col-md-8">
                                    <input name="id_number" type="text" class="form-control" id="id_number" placeholder="Passport/NID/License Number" value="" required="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-4 pt-0">Gender <span><i class="text-danger">*</i></span></label>
                                <div class="col-sm-8">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadioInline1" name="gender" class="custom-control-input">
                                        <label class="custom-control-label" for="customRadioInline1">Male</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadioInline2" name="gender" class="custom-control-input">
                                        <label class="custom-control-label" for="customRadioInline2">Female</label>
                                    </div>
                                </div>
                            </div>
                            <span id="verify_field"></span>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                            <?php echo form_close();?>
                        <?php
                            }else{
                        ?>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <?php if($verify_status->verified==1){ ?>
                                            <center>
                                                <font color="green" size="+2">Profile is Verified!</font>
                                            </center>
                                        <?php } else if($verify_status->verified==2){ ?>
                                            <center>
                                                <font color="red" size="+2">Verification Cancel.</font>
                                            </center>
                                        <?php } else{ ?>
                                            <center><font color="brown" size="+2">Verification is Processing!</font></center>
                                        <?php } ?>
                                    </div>
                                </div>

                            <?php } ?>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verify profile -->
<script type="text/javascript">
    $(function(){
        $("#verify_type").on("change", function(event) {
            event.preventDefault();
            var verify_type = $("#verify_type").val();

            if (verify_type == 'passport') {

                $("#verify_field").html("<div class='form-group row'><label for='document1' class='col-md-4 col-form-label'>Passport Cover <span><i class='text-danger'>*</i></span></label><div class='col-md-8'><input name='document1' type='file' class='form-control' id='document1' required></div></div><div class='form-group row'><label for='document2' class='col-md-4 col-form-label'>Passport Inner <span><i class='text-danger'>*</i></span></label><div class='col-md-8'><input name='document2' type='file' class='form-control' id='document2' required></div></div>");

            }else if (verify_type == 'driving_license') {
                $("#verify_field").html("<div class='form-group row'><label for='document1' class='col-md-4 col-form-label'>Driving License <span><i class='text-danger'>*</i></span></label><div class='col-md-8'><input name='document1' type='file' class='form-control' id='document1' required></div></div>");
                
            }else if (verify_type == 'nid') {
                $("#verify_field").html("<div class='form-group row'><label for='document1' class='col-md-4 col-form-label'>NID With selfie <span><i class='text-danger'>*</i></span></label><div class='col-md-8'><input name='document1' type='file' class='form-control' id='document1' required></div></div>");
                
            }else{
                $("#verify_field").html();

            }


        });
    }); 
</script>