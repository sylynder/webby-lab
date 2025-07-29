<?php
$settings = $this->db->select("*")
    ->get('setting')
    ->row();
?>

        <footer>
            <div class="main_footer">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="widget-contact">
                                <ul class="list-icon">
                                    <li><?php echo $settings->description ?></li>
                                    <li><?php echo $settings->phone ?></li>
                                    <li><a href="mailto:<?php echo $settings->email ?>"><?php echo $settings->email ?></a></li>
                                    <li> <br>
                                        <?php echo $settings->office_time ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-3 col-lg-4">
                            <div class="row">                            
                                <div class="col-6 col-sm-12 col-md-12 col-lg-6">
                                    <div class="footer-box">
                                        <h3 class="footer-title"><?php echo display('our_company'); ?></h3>
                                        <ul class="footer-list">
                                        <?php
                                            foreach ($category as $cat_key => $cat_value) {                                
                                                if ($cat_value->menu==2 || $cat_value->menu==4) {
                                                    $cat_name = isset($lang) && $lang =="french"?$cat_value->cat_name_fr:$cat_value->cat_name_en;
                                                
                                        ?>
                                            <li><a href="<?php echo base_url()?>#<?php echo $cat_value->slug ?>" class="js-scroll-trigger"><?php echo $cat_name ?></a></li>

                                        <?php
                                                }

                                            }
                                        ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6 d-sm-none d-md-none d-lg-block">
                                    <div class="footer-box">
                                        <h3 class="footer-title"><?php echo display('services'); ?></h3>
                                        <ul class="footer-list">
                                            <?php
                                                foreach ($category as $cat_key => $cat_value) {                                
                                                if ($cat_value->menu==3 || $cat_value->menu==5) {
                                                    $cat_name = isset($lang) && $lang =="french"?$cat_value->cat_name_fr:$cat_value->cat_name_en;
                                                
                                            ?>
                                                <li><a href="#<?php echo $cat_value->slug ?>" class="js-scroll-trigger"><?php echo $cat_name ?></a></li>

                                            <?php
                                                    }

                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-5 col-lg-3 offset-lg-1">
                            <div class="newsletter-box">
                                <h3 class="footer-title"><?php echo display('email_newslatter'); ?></h3>
                                <p><?php echo display('subscribe_to_our_newsletter'); ?></p>
                                <?php echo form_open('#','class="newsletter-form" id="subscribeForm" name="subscribeForm"'); ?>
                                    <input name="subscribe_email" placeholder="<?php echo display('email'); ?>" type="text">
                                    <button type="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                                    <div class="envelope"> <i class="fa fa-envelope" aria-hidden="true"></i> </div>
                                <?php echo form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.End of main footer -->
            <div class="sub-footer">
                <div class="container">
                    <p class="footer-copyright"><?php echo $settings->footer_text; ?></p>
                </div>
            </div>
        </footer>
        <!-- /.End of footer --> 
        <!-- Optional JavaScript --> 
        <!-- jQuery first, then Popper.js, then Bootstrap JS --> 
        <script src="<?php echo base_url('assets/website/js/jquery-3.3.1.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/js/popper.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/js/bootstrap.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/js/jquery.dd.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/js/metisMenu.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/js/jquery.easing.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/js/jquery.mCustomScrollbar.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/js/jquery.magnific-popup.min.js')?>"></script>
        <script src="<?php echo base_url('assets/website/js/flipclock.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/slick/slick.min.js')?>"></script> 
        <script src="<?php echo base_url('assets/website/js/echarts-en.min.js')?>"></script>
        <script src="<?php echo base_url('assets/website/js/echarts-liquidfill.min.js')?>"></script>
        <script src="<?php echo base_url('assets/website/js/classie.min.js')?>"></script>
        <script src="<?php echo base_url('assets/website/js/script.js')?>"></script>
<?php if ($this->uri->segment(1)=='' || $this->uri->segment(1)=='home') { ?>
        <?php

            $color0 ='';
            $data0 ='';
            foreach ($chart0 as $key0 => $value0) {
                $color0.= "'".$value0->article1_fr."',";
                $data0.= "{value: $value0->article1_en, name: '".$value0->headline_en."'},";
                
            }

            $color0 = rtrim($color0, ',');
            $data0 = rtrim($data0, ',');

            $color1 ='';
            $data1 ='';
            foreach ($chart1 as $key1 => $value1) {            
                $color1.= "'".$value1->article1_fr."',";
                $data1.= "{value: $value1->article1_en, name: '".$value1->headline_en."'},";
            }

            $color1 = rtrim($color1, ',');
            $data1 = rtrim($data1, ',');

        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                "use strict"; // Start of use strict
                //eChart js
                var echartsConfig = function () {
                    if ($('#eChart_1').length > 0) {
                        var eChart_1 = echarts.init(document.getElementById('eChart_1'));
                        var option1 = {
                            tooltip: {
                                trigger: 'item',
                                formatter: "{a} <br/>{b} : {c} ({d}%)",
                                backgroundColor: 'rgba(33,33,33,1)',
                                borderRadius: 0,
                                padding: 10,
                                textStyle: {
                                    color: '#fff',
                                    fontStyle: 'normal',
                                    fontWeight: 'normal',
                                    fontFamily: "'Poppins', sans-serif",
                                    fontSize: 12
                                }
                            },
                            calculable: true,
                            itemStyle: {
                                normal: {
                                    shadowBlur: 5,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            },
                            series: [
                                {
                                    name: 'Advertising',
                                    type: 'pie',
                                    radius: ['10%', '80%'],
                                    center: ['50%', '50%'],
                                    roseType: 'area',
                                    color: [<?php echo $color0 ?>],
                                    data: [
                                        <?php echo $data0 ?>
                                    ]
                                }
                            ]
                        };
                        eChart_1.setOption(option1);
                        eChart_1.resize();
                    }
                    if ($('#eChart_2').length > 0) {
                        var eChart_2 = echarts.init(document.getElementById('eChart_2'));
                        var option2 = {
                            tooltip: {
                                trigger: 'item',
                                formatter: "{a} <br/>{b} : {c} ({d}%)",
                                backgroundColor: 'rgba(33,33,33,1)',
                                borderRadius: 0,
                                padding: 10,
                                textStyle: {
                                    color: '#fff',
                                    fontStyle: 'normal',
                                    fontWeight: 'normal',
                                    fontFamily: "'Poppins', sans-serif",
                                    fontSize: 12
                                }
                            },
                            calculable: true,
                            itemStyle: {
                                normal: {
                                    shadowBlur: 5,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            },
                            series: [
                                {
                                    name: 'Advertising',
                                    type: 'pie',
                                    radius: ['10%', '80%'],
                                    center: ['50%', '50%'],
                                    roseType: 'radius',
                                    color: [<?php echo $color1 ?>],
                                    label: {
                                        normal: {
                                            fontFamily: "'Poppins', sans-serif",
                                            fontSize: 12
                                        }
                                    },
                                    data: [
                                        <?php echo $data1 ?>
                                    ].sort(function (a, b) {
                                        return a.value - b.value;
                                    })
                                }
                            ]
                        };
                        eChart_2.setOption(option2);
                        eChart_2.resize();
                    }
                };

                //Resize function start
                var echartResize;
                $(window).on("resize", function () {
                    /*E-Chart Resize*/
                    clearTimeout(echartResize);
                    echartResize = setTimeout(echartsConfig, 200);
                }).resize();
                //Function Call
                echartsConfig();

            });

        </script>
        <!-- Ajax Language Change -->
        <script type="text/javascript">
            $(function(){
                $("#lng_select").on("change", function(event) {
                    event.preventDefault();

                    var lang = $("#lng_select").val();

                    var token   = "<?php echo $this->security->get_csrf_hash(); ?>";
                    var inputdata = "lang="+lang+"&<?php echo $this->security->get_csrf_token_name(); ?>="+token;
                    $.ajax({
                        url: "<?php echo base_url('home/langChange'); ?>",
                        type: "post",
                        data: inputdata,
                        success: function(result,status,xhr) {
                            location.reload();
                        },
                        error: function(xhr,status,error){
                            location.reload();
                        }
                    });
                });
            });
        </script>
        <!-- Ajax Subscription -->
        <script type="text/javascript">
            function isValidEmailAddress(emailAddress) {
                var pattern = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return pattern.test(emailAddress);
            }

            $(function(){
                $("#subscribeForm").on("submit", function(event) {
                    event.preventDefault();
                    var inputdata = $("#subscribeForm").serialize();
                    var email = $('input[name=subscribe_email]').val();

                    if (email == "") {
                        alert("Please Input Your Email !!!");
                        return false;
                    }
                    if (!isValidEmailAddress(email)) {
                        alert("Please Enter Valid Email !!!");
                        return false;
                    }

                    $.ajax({
                        url: "<?php echo base_url('home/subscribe'); ?>",
                        type: "post",
                        data: inputdata,
                        success: function(result,status,xhr) {
                            alert("Subscribtion complete");
                            location.reload();
                        },
                        error: function (xhr,status,error) {
                            if (xhr.status===500) {
                                alert("This Email Address already subscribed");
                            }
                        }
                    });
                });
            }); 
        </script>
        <!-- Ajax Contract From -->
        <script type="text/javascript">
            $(function(){
                $("#contactForm").on("submit", function(event) {
                    event.preventDefault();
                    var inputdata = $("#contactForm").serialize();
                    $.ajax({
                        url: "<?php echo base_url('home/contactMsg'); ?>",
                        type: "post",
                        data: inputdata,
                        success: function(d) {
                            alert("Message send successfuly");
                            location.reload();
                        },
                        error: function(){
                            alert("Message send Fail");
                        }
                    });
                });
            }); 
        </script>
<?php } ?>
<?php if ($this->uri->segment(1)=='register') { ?>
<script type="text/javascript">
            var url = window.location.href;
            var tab = url.substring(url.lastIndexOf('#') + 1);
            var logintab = url.substring(url.lastIndexOf('login'));

            if (tab == 'tab2') {
              $("#btntab2").addClass("active");
              $("#tab2").addClass("in active");
              $("#btntab1").removeClass("active");
              $("#tab1").removeClass("in active");
            }
            if (logintab == 'login') {
              $("#btntab2").addClass("active");
              $("#tab2").addClass("in active");
              $("#btntab1").removeClass("active");
              $("#tab1").removeClass("in active");
            }
        </script>
        <script>
            (function () {
                // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
                if (!String.prototype.trim) {
                    (function () {
                        // Make sure we trim BOM and NBSP
                        var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
                        String.prototype.trim = function () {
                            return this.replace(rtrim, '');
                        };
                    })();
                }

                [].slice.call(document.querySelectorAll('input.input__field')).forEach(function (inputEl) {
                    // in case the input is already filled..
                    if (inputEl.value.trim() !== '') {
                        classie.add(inputEl.parentNode, 'input--filled');
                    }

                    // events:
                    inputEl.addEventListener('focus', onInputFocus);
                    inputEl.addEventListener('blur', onInputBlur);
                });

                function onInputFocus(ev) {
                    classie.add(ev.target.parentNode, 'input--filled');
                }

                function onInputBlur(ev) {
                    if (ev.target.value.trim() === '') {
                        classie.remove(ev.target.parentNode, 'input--filled');
                    }
                }
            })();
        </script>
        <!-- Select Mobile -->
        <script type="text/javascript">
            $(function(){
                $("#country").on("change", function(event) {
                    event.preventDefault();
                    $( "#phone").val(this.value);
                });
            });
        </script>
<?php } ?>
    </body>
            <script type="text/javascript">
            //Countdown
            var clock;

            clock = $('.clock').FlipClock({
                clockFace: 'DailyCounter',
                autoStart: false,
                callbacks: {
                    stop: function () {
                        $('.message').html('The clock has stopped!');
                    }
                }
            });

            clock.setTime(<?php echo $fliptime;?>);
            clock.setCountdown(true);
            clock.start();

        </script>
</html>