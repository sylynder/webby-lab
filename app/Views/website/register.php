<?php $this->load->view('website/partials/head');?>
 
<body class="nk-body body-wider bg-light-alt">
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
	<div class="nk-wrap">
		
    	<?php $this->load->view('website/components/auth_header');?>

        <main class="nk-pages">
            <div class="section section-l">

                 <div class="nk-blocks d-flex justify-content-center">
                    
                    <!-- alert message -->
                    <?php if ($this->session->flashdata('message') != null) {  ?>
                    
                    <div class="alert alert-primary-alt alert-dismissible fade show">
                        <?php echo $this->session->flashdata('message'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <?php } ?>
                        
                    <?php if ($this->session->flashdata('exception') != null) {  ?>
                    
                    <div class="alert alert-danger-alt alert-dismissible fade show">
                        <?php echo $this->session->flashdata('exception'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 

                    <?php } ?>
                        
                    <?php if (validation_errors()) {  ?>
                    
                    <div class="alert alert-danger-alt alert-dismissible fade show">
                        <?php echo validation_errors(); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <?php } ?> 
                    <br>
                </div>

                <div class="container">
                    <div class="nk-blocks d-flex justify-content-center">
                        <div class="ath-container m-0">
                            <div class="ath-body">
                                <h5 class="ath-heading title">Sign Up<small class="tc-default">Create New Account</small></h5>
                                <?php echo form_open('register','id="registerForm" name="registerForm" '); ?>
                                    <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="text" class="input-bordered" name="rf_name" id="f_name" value="<?php echo set_value('rf_name');; ?>"  placeholder="Firstname" autocomplete="off" required>
                                            <!-- <p style="color: red; font-size: 10px; margin-left: 2px;">Field should not be empty</p> -->
                                        </div>
                                    </div>
                                    <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="text" class="input-bordered" name="rl_name" id="l_name" value="<?php echo set_value('rl_name'); ?>" placeholder="Lastname" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <!-- <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="text" class="input-bordered" placeholder="Username" name="rusername" id="username" autocomplete="off" required>
                                        </div>
                                    </div> -->
                                    <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="text" class="input-bordered" placeholder="Your Email" name="remail" id="email" onkeydown="checkEmail()" value="<?php echo set_value('remail'); ?>" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="password" class="input-bordered" name="rpass" id="pass" onkeyup="strongPassword()" placeholder="Password" autocomplete="off" required>
                                        </div>
                                        <div id="message">
                                          <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                                          <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                                          <p id="special" class="invalid">A <b>special</b></p>
                                          <p id="number" class="invalid">A <b>number</b></p>
                                          <p id="length" class="invalid">Minimum <b>8 characters</b></p>
                                        </div>
                                    </div>
                                    <div class="field-item">
                                        <div class="field-wrap">
                                            <input type="password" class="input-bordered" name="rr_pass" id="r_pass" onkeyup="rePassword()" placeholder="Repeat Password" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="field-item">
                                        <!-- <input class="input-checkbox" id="agree-term-2" type="checkbox"> -->
                                        <input class="input-checkbox" type="checkbox" id="agree-term-2" name="raccept_terms">
                                        <label for="agree-term-2">I agree to <a href="#">Privacy Policy</a> &amp; <a href="#">Terms</a>.</label>
                                    </div>
                                    <button class="btn btn-primary btn-block btn-md">Sign Up</button>
                                </form>
                                
                                <div class="ath-note text-center">
                                    Already have an account? <a href="<?=site_url('login')?>"> <strong>Sign in here</strong></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    
		<?php $this->load->view('website/components/footer')?>
		
	<div class="preloader"><span class="spinner spinner-round"></span></div>
	
	<!-- JavaScript -->
	<?php $this->load->view('website/partials/scripts');?>
    
        <script type="text/javascript">
            // var sign_up_now = document.getElementById("sign_up_now");
            // var nav_home_tab = document.getElementById("nav-home-tab");
            // sign_up_now.onclick = function() {
            //     nav_home_tab.click();
            // }

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
            // function validateForm() {
            //     var f_name    = document.forms["registerForm"]["f_name"].value;
            //     var l_name    = document.forms["registerForm"]["l_name"].value;
            //     var username  = document.forms["registerForm"]["username"].value;
            //     // var sponsor_id= document.forms["registerForm"]["sponsor_id"].value;
            //     var email     = document.forms["registerForm"]["email"].value;
            //     // var phone     = document.forms["registerForm"]["phone"].value;
            //     var country   = document.forms["registerForm"]["country"].value;
            //     var pass      = document.forms["registerForm"]["pass"].value;
            //     var r_pass    = document.forms["registerForm"]["r_pass"].value;
            //     var checkbox  = document.forms["registerForm"]["raccept_terms"].value;

            //     if (f_name == "") {
            //         alert("First Name Required");
            //         return false;
            //     }
            //     if (l_name == "") {
            //         alert("Last Name Required");
            //         return false;
            //     }
            //     if (username == "") {
            //         alert("User Name Required");
            //         return false;
            //     }
            //     if (country == "") {
            //         alert("Country Required");
            //         return false;
            //     }
            //     if (phone == "") {
            //         alert("Phone Required");
            //         return false;
            //     }
            //     if (email == "") {
            //         alert("Email Required");
            //         return false;
            //     }
            //     if (pass == "") {
            //         alert("Password Required.");
            //         return false;
            //     }
            //     if (pass.length < 8) {
            //         alert("Please Enter at least 8 Characters input");
            //         return false;
            //     }
            //     if (r_pass == "") {
            //         alert("Confirm Password must be filled out");
            //         return false;
            //     }
            //     if (checkbox == "") {
            //         alert("Must Confirm Privacy Policy and Terms and Conditions");
            //         return false;
            //     }
            // }
        </script>
</body>
</html>
