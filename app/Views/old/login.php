        <div class="reg-wrapper">
            <div class="container">
                <div class="col-sm-7 col-md-6">
                    <div class="">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Register</a>
                                <a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Log in</a>
                            </div>
                        </nav>
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- alert message -->
                                <?php if ($this->session->flashdata('message') != null) {  ?>
                                <div class="alert alert-info alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <?php echo $this->session->flashdata('message'); ?>
                                </div> 
                                <?php } ?>
                                    
                                <?php if ($this->session->flashdata('exception') != null) {  ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <?php echo $this->session->flashdata('exception'); ?>
                                </div>
                                <?php } ?>
                                    
                                <?php if (validation_errors()) {  ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <?php echo validation_errors(); ?>
                                </div>
                                <?php } ?> 
                            </div>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <?php echo form_open('register','id="registerForm" name="registerForm" onsubmit="return validateForm()" '); ?>
                                    <div class="row">    
                                    <div class="col-sm-6">
                                        <div class="input">
                                            <input class="input__field" type="text"  name="rf_name" id="f_name" value="<?php echo $this->session->userData['f_name']; ?>" autocomplete="off" required>
                                            <label class="input__label" for="f_name">
                                                <span class="input__label-content" data-content="<?php echo display('firstname'); ?>"><?php echo display('firstname'); ?></span>
                                            </label>
                                        </div>
                                    </div>    
                                    <div class="col-sm-6">
                                        <div class="input">
                                            <input class="input__field" type="text"  name="rl_name" id="l_name" value="<?php echo $this->session->userData['l_name']; ?>" autocomplete="off" required>
                                            <label class="input__label" for="l_name">
                                                <span class="input__label-content" data-content="<?php echo display('lastname'); ?>"><?php echo display('lastname'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-sm-12">
                                        <div class="input">
                                            <input class="input__field" type="text" name="rusername" id="username" required>
                                            <label class="input__label" for="username">
                                                <span class="input__label-content" data-content="<?php echo display('username'); ?>"><?php echo display('username'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row">    
                                    <div class="col-sm-6">
                                        <div class="input">
                                            <select  class="selectpicker" data-width="100%" class="country input__field" id="country" name="country">
                                                <option value="" selected>Select Country</option>
                                                <?php
                                                    foreach($countryArray as $code => $country){
                                                        $countryName = ucwords(strtolower($country["name"]));
                                                ?>
                                                <option value="<?=$country["code"]?>"><?=$countryName." (+".$country["code"].")"?></option>
                                                <?php } ?>
                                            </select>
                                            <label class="input__label" for="country">
                                                <span class="input__label-content" data-content="<?php echo display('country'); ?>"><?php echo display('country'); ?></span>
                                            </label>
                                        </div>
                                    </div>    
                                    <div class="col-sm-6">
                                        <div class="input">
                                            <input class="input__field" type="number" name="phone" id="phone" autocomplete="off" required>
                                            <label class="input__label" for="phone">
                                                <span class="input__label-content" data-content="<?php echo display('phone'); ?>"><?php echo display('phone'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="row">    
                                    <div class="col-sm-12">
                                        <div class="input">
                                            <input class="input__field" type="email" id="email" name="remail" id="email" onkeydown="checkEmail()" value="<?php echo $this->session->userData['email']; ?>" autocomplete="off" required>
                                            <label class="input__label" for="email">
                                                <span class="input__label-content" data-content="<?php echo display('email'); ?>"><?php echo display('email'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-sm-12">
                                        <div class="input">
                                            <input class="input__field" type="password" name="rpass" id="pass" onkeyup="strongPassword()" required>
                                            <label class="input__label" for="pass">
                                                <span class="input__label-content" data-content="<?php echo display('password'); ?>"><?php echo display('password'); ?></span>
                                            </label>
                                            <div id="message">
                                              <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                                              <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                                              <p id="special" class="invalid">A <b>special</b></p>
                                              <p id="number" class="invalid">A <b>number</b></p>
                                              <p id="length" class="invalid">Minimum <b>8 characters</b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-sm-12">
                                        <div class="input">
                                            <input class="input__field" type="password" name="rr_pass" id="r_pass" onkeyup="rePassword()" required>
                                            <label class="input__label" for="r_pass">
                                                <span class="input__label-content" data-content="<?php echo display('conf_password'); ?>"><?php echo display('conf_password'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-sm-12">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="checkbox" name="raccept_terms" value="ptConfirm"> 
                                            
                                                <?php echo display('your_password_at_global_crypto_are_encrypted_and_secured'); ?> <a target="_blank" href="<?php echo base_url(@$article_image[0]); ?>" class="checkbox-link">Privacy policy</a> and 
                                                <a target="_blank" href="<?php echo base_url(@$article_image[0]); ?>" class="checkbox-link">Terms of Use</a>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-reg"><?php echo display('sign_up'); ?></button>
                                <?php echo form_close() ?>
                            </div>
                            <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <?php echo form_open('home/login','id="loginForm" '); ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input">
                                            <input class="input__field" type="text" name="luseremail" id="useremail" autocomplete="off" required>
                                            <label class="input__label" for="input">
                                                <span class="input__label-content" data-content="<?php echo display('email'); ?>"><?php echo display('email'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-sm-12">
                                        <div class="input">
                                            <input class="input__field" type="password" name="lpassword" id="password" required>
                                            <label class="input__label" for="password">
                                                <span class="input__label-content" data-content="<?php echo display('password'); ?>"><?php echo display('password'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-sm-12">
                                        <div class="checkbox">
                                            <label>
                                                <a href="#" data-toggle="modal" data-target="#forgotModal" class="forgot"><?php echo display('forgot_password'); ?>?</a> <?php echo display('dont_have_an_account'); ?>?&nbsp;<span id="sign_up_now"><?php echo display('sign_up_now'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-reg"><?php echo display('login'); ?></button>
                                <?php echo form_close() ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>



        <!-- Modal -->
<div id="forgotModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo display('forgot_password'); ?></h4>
      </div>
      <div class="modal-body">
        <?php echo form_open('home/forgotPassword','id="forgotPassword"'); ?>
            <div class="form-group">
                <input class="form-control" name="email" id="email" placeholder="<?php echo display('email'); ?>" type="text" autocomplete="off">
            </div>
            <button  type="submit" class="btn btn-success btn-block"><?php echo display('send_code'); ?></button>
        <?php echo form_close();?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo display('close'); ?></button>
      </div>
    </div>

  </div>
</div>

        <style>
            #message {
                display:none;
                position: relative;
                padding: 20px;
                margin-top: 10px;
            }
            #message p {
                margin-bottom: 0;
            }
            .input .valid {
                color: green;
            }
            .input .valid:before {
                position: relative;
                left: -10px;
                content: "✔";
            }
            .input .invalid {
                color: red;
            }
            .input .invalid:before {
                position: relative;
                left: -10px;
                content: "✖";
            }
        </style>
        <script type="text/javascript">
            var sign_up_now = document.getElementById("sign_up_now");
            var nav_home_tab = document.getElementById("nav-home-tab");
            sign_up_now.onclick = function() {
                nav_home_tab.click();
            }

            var myInput = document.getElementById("pass");
            var letter  = document.getElementById("letter");
            var capital = document.getElementById("capital");
            var special = document.getElementById("special");
            var number  = document.getElementById("number");
            var length  = document.getElementById("length");

            myInput.onfocus = function() {
                document.getElementById("message").style.display = "block";
            }
            myInput.onblur = function() {
                document.getElementById("message").style.display = "none";
            }

            myInput.onkeyup = function() {

              var lowerCaseLetters = /[a-z]/g;
              if(myInput.value.match(lowerCaseLetters)) {  
                letter.classList.remove("invalid");
                letter.classList.add("valid");
              } else {
                letter.classList.remove("valid");
                letter.classList.add("invalid");
              }

              var upperCaseLetters = /[A-Z]/g;
              if(myInput.value.match(upperCaseLetters)) {  
                capital.classList.remove("invalid");
                capital.classList.add("valid");
              } else {
                capital.classList.remove("valid");
                capital.classList.add("invalid");
              }

              var specialCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g;
              if(myInput.value.match(specialCharacter)) {  
                special.classList.remove("invalid");
                special.classList.add("valid");
              } else {
                special.classList.remove("valid");
                special.classList.add("invalid");
              }

              var numbers = /[0-9]/g;
              if(myInput.value.match(numbers)) {  
                number.classList.remove("invalid");
                number.classList.add("valid");
              } else {
                number.classList.remove("valid");
                number.classList.add("invalid");
              }

              if(myInput.value.length >= 8) {
                length.classList.remove("invalid");
                length.classList.add("valid");
              } else {
                length.classList.remove("valid");
                length.classList.add("invalid");
              }
            }

            //Confirm Password check
            function rePassword() {
                var pass = document.getElementById("pass").value;
                var r_pass = document.getElementById("r_pass").value;

                if (pass !== r_pass) {
                    document.getElementById("r_pass").style.borderColor = '#f00';
                    return false;
                }
                else{
                    document.getElementById("r_pass").style.borderColor = 'unset';
                    return true;
                }
            }
            //Valid Email Address Check
            function checkEmail() {
                var email = document.getElementById('email');
                var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                if (!filter.test(email.value)) {
                    document.getElementById("email").style.borderColor = '#f00';
                    return false;
                }
                else{
                    document.getElementById("email").style.borderColor = 'unset';
                    return true;
                }
            }
            //Registration From validation check
            function validateForm() {
                var f_name    = document.forms["registerForm"]["f_name"].value;
                var l_name    = document.forms["registerForm"]["l_name"].value;
                var username  = document.forms["registerForm"]["username"].value;
                // var sponsor_id= document.forms["registerForm"]["sponsor_id"].value;
                var email     = document.forms["registerForm"]["email"].value;
                var phone     = document.forms["registerForm"]["phone"].value;
                var country   = document.forms["registerForm"]["country"].value;
                var pass      = document.forms["registerForm"]["pass"].value;
                var r_pass    = document.forms["registerForm"]["r_pass"].value;
                var checkbox  = document.forms["registerForm"]["accept_terms"].value;

                if (f_name == "") {
                    alert("First Name Required");
                    return false;
                }
                if (l_name == "") {
                    alert("Last Name Required");
                    return false;
                }
                if (username == "") {
                    alert("User Name Required");
                    return false;
                }
                if (country == "") {
                    alert("Country Required");
                    return false;
                }
                if (phone == "") {
                    alert("Phone Required");
                    return false;
                }
                if (email == "") {
                    alert("Email Required");
                    return false;
                }
                if (pass == "") {
                    alert("Password Required.");
                    return false;
                }
                if (pass.length < 8) {
                    alert("Please Enter at least 8 Characters input");
                    return false;
                }
                if (r_pass == "") {
                    alert("Confirm Password must be filled out");
                    return false;
                }
                if (checkbox == "") {
                    alert("Must Confirm Privacy Policy and Terms and Conditions");
                    return false;
                }
            }
        </script>