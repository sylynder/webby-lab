<style type="text/css">
    
    .positive {
        color: #35a947;
    }
    .negative {
        color: red;
    }
    .price_updown{font-size: 26px;font-weight: normal;}
    .price_updown i{font-weight: normal;}
    .panel-one{background: #f8931f;color: #fff;}
    .panel-two{background: #1b1464;color: #fff;}
    .panel-three{color: #fff;background: #0071bd;}
</style>
<div class="row">
    <div class="col-sm-6 col-md-4">
        <div class="count_panel panel-one">
            <div class="stats-title">
                <h4>Sell Avaiable</h4>
                <i class="fa fa-university"></i>
            </div>
            <h1 class="sell_avaiable">0.00</h1>
            <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total Abaiable Sell in System."></i>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="count_panel panel-two">
            <div class="stats-title ">
                <h4>Price</h4>
                <i class="fa fa-universal-access"></i>
            </div>
            <h1 class="present_price"><span class="price_updown ">0.00</span></h1>
            <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Presents Exchange Crypto Rate."></i>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="count_panel panel-three">
            <div class="stats-title ">
                <h4>Crypto Balance</h4>
                <i class="fa fa-balance-scale"></i>
            </div>
            <h1 class="crypto_balance">0.00</h1>
            <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Total Your Crypto Balance."></i>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h3>Exchange History</h3>
                </div>
            </div>
            <div class="panel-body">
                <div id="exchangesChart" style="height:450px"></div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-6">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Buy Coin</h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('#',array('name'=>'exchange_buy','id'=>'exchange_buy')); ?>
                <?php echo form_hidden('exchange', 'BUY') ?>

                <div class="form-group row">
                    <label for="changed" class="col-sm-1 col-form-label"></label>
                    <div class="col-sm-11">
                        <center><span id="exceptionorbuy" class="text-success"></span></center>
                    </div>
                </div>
                <div id="buy_coin_mainloader">
                    <div id="buy_coin_subloader">
                        <div class="form-group row">
                            <label for="buyqty" class="col-sm-4 col-form-label">Quantity<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input class="form-control" name="qty" type="text" id="buyqty" value="1" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="buyrate" class="col-sm-4 col-form-label">Rate (<?php echo $coininfo->pair_with; ?>)<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input class="form-control" onkeyup="Fee('exchange_buy','buyfee')" name="rate" type="text" id="buyrate" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="changed" class="col-sm-4 col-form-label">Fees</label>
                            <div class="col-sm-8">
                                <?php echo $coininfo->pair_with; ?> <span id="buyfee" class="text-success">0.0</span> (<?php echo @$buyfees->fees?@$buyfees->fees:0; ?>%)
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="buytotal" class="col-sm-4 col-form-label">Total</label>
                            <div class="col-sm-8">
                                <span id="buytotal">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row m-b-15">
                    <div class="col-sm-8 col-sm-offset-4">
                        <button type="button" onclick="Exchange('exchange_buy','exceptionorbuy')" class="btn btn-success w-md m-b-5 col-sm-12"><?php echo display('buy')?></button>
                    </div>
                </div>
                        <input type="hidden" name="level" value="buy">

                <?php echo form_close();?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Sell Coin</h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('#',array('name'=>'exchange_sell','id'=>'exchange_sell')); ?>
                <?php echo form_hidden('exchange', 'SELL') ?>

                <div class="form-group row">
                    <label for="changed" class="col-sm-1 col-form-label"></label>
                    <div class="col-sm-11">
                        <center><span id="exceptionorsell" class="text-success"></span></center>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="sellqty" class="col-sm-4 col-form-label">Quantity<i class="text-danger">*</i></label>
                    <div class="col-sm-8">
                        <input class="form-control" name="qty" type="text" id="sellqty" value="1" autocomplete="off" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="sellrate" class="col-sm-4 col-form-label">Rate (<?php echo $coininfo->pair_with; ?>)<i class="text-danger">*</i></label>
                    <div class="col-sm-8">
                        <input class="form-control" onkeyup="Fee('exchange_sell','sellfee')" name="rate" type="text" id="sellrate" autocomplete="off" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="changed" class="col-sm-4 col-form-label">Fees</label>
                    <div class="col-sm-8">
                        <?php echo $coininfo->pair_with; ?> <span id="sellfee" class="text-success">0.0</span> (<?php echo @$sellfees->fees?@$sellfees->fees:0; ?>%)
                    </div>
                </div>
                <div class="form-group row">
                    <label for="selltotal" class="col-sm-4 col-form-label">Total</label>
                    <div class="col-sm-8">
                        <span id="selltotal">0.00</span>
                    </div>
                </div>

                <div class="row m-b-15">
                    <div class="col-sm-8 col-sm-offset-4">
                        <button type="button" onclick="Exchange('exchange_sell','exceptionorsell')" class="btn btn-danger w-md m-b-5 col-sm-12"><?php echo display('sell')?></button>
                    </div>
                </div>
                <input type="hidden" name="level" value="sell">
                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Market Depth</h4>
                </div>
            </div>
            <div class="panel-body">
               <div id="marketDepth" style="height:450px;"></div>
            </div>
        </div>
    </div>
</div>



<br>
<br>
<br>
    <!-- Chart -->
    <link href="<?php echo base_url('assets/amcharts/export.css'); ?>" rel="stylesheet">
    <script src="<?php echo base_url('assets/amcharts/amcharts.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/amcharts/serial.js'); ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/amcharts/amstock.js'); ?>" type="text/javascript"></script>

    <!-- Amchats js -->
    <script src="<?php echo base_url('assets/amcharts/plugins/dataloader/dataloader.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/amcharts/plugins/export/export.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/amcharts/patterns.js') ?>"></script>
    <script src="<?php echo base_url('assets/amcharts/dark.js') ?>"></script>


<script type="text/javascript">

    function Exchange(form_name,exceptionid)
    {
        var exchange,qty,rate;
        exchange = document.forms[form_name].elements['exchange'].value;
        qty      = document.forms[form_name].elements['qty'].value;
        rate     = document.forms[form_name].elements['rate'].value;
        var csrf_test_name = document.forms[form_name].elements['csrf_test_name'].value;

        $('#'+exceptionid).html("<font color='green'>Please Wait......</font>");

        if(qty=="" || rate==""){
            $('#'+exceptionid).html("<font color='red'>Please fill up required field!</font>");
        }
        else{

            $.ajax({
                'url': '<?php echo base_url("customer/exchange");?>',
                'type': 'POST', //the way you want to send data to your URL
                'data': {'exchange':exchange,'qty':qty,'rate':rate,'csrf_test_name':csrf_test_name },
                'dataType': "JSON",
                'success': function(data) {

                    if(data.type==1){

                        if(form_name=="exchange_buy"){

                            $('#'+form_name)[0].reset();
                            $("#buytotal").html("");
                        }
                        else{
                            $('#'+form_name)[0].reset();
                            $("#selltotal").html("");
                        }

                        $('#'+exceptionid).html("<div class='alert alert-info alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"+data.message+"</div>");
                        
                    }else {

                        if(form_name=="exchange_buy"){

                            $('#'+form_name)[0].reset();
                            $("#buytotal").html("");
                        }
                        else{

                            $('#'+form_name)[0].reset();
                            $("#selltotal").html("");
                        }

                        $('#'+exceptionid).html("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"+data.message+"</div>");
                    }

                }
            });
        }

    }

    function Fee(form_name,fees_id){
        
        var amount  = document.forms[form_name].elements['rate'].value;
        var qty     = document.forms[form_name].elements['qty'].value;
        amount      = amount*qty;
        var level   = document.forms[form_name].elements['level'].value;

        var csrf_test_name = document.forms[form_name].elements['csrf_test_name'].value;

        if (amount=="" || amount==0) {
            $('#'+fees_id).text(0);
        }
        if (amount!=""){
            $.ajax({
                'url': '<?php echo base_url("customer/ajaxload/fees_load");?>',
                'type': 'POST', //the way you want to send data to your URL
                'data': {'level':level,'amount':amount,'csrf_test_name':csrf_test_name },
                'dataType': "JSON",
                'success': function(data) { 
                    if(data){
                        $('#'+fees_id).text(data.fees);                    
                    } else {
                        alert('Error!');
                    }  
                }
            });
        } 
    }

    $(document).ready( function() {

        done01();
        done02();
    });

    function done01() {
        setTimeout( function() {         
        sellavaible(); 
        done01();
        }, 10000);
    }

    function done02() {
        setTimeout( function() {         
        exchangehistoryupdates(); 
        done02();
        }, 10000);
    }
    //Historycal data load
    function exchangehistoryupdates() {
        $.getJSON('<?php echo base_url("customer/exchange/getExchangRate") ?>', function(data) {

            var lastprice;
            if (data.coinhistory) {

                var change = data.coinhistory.price_change_24h/data.coinhistory.last_price;
                var lastprice = parseFloat(parseFloat(data.coinhistory.last_price).toString()).toFixed(2);                
                var lastprice1 = $('#buyrate').val();
                var lastprice2 = $('#sellrate').val();

                if (lastprice1=='') {
                    $('#buyrate').val(lastprice);
                    $('#buytotal').val(lastprice*1);
                };
                if (lastprice2=='') {
                    $('#sellrate').val(lastprice);
                    $('#selltotal').val(lastprice*1);
                };


                if (change>0) {
                    $(".price_updown").html(parseFloat(parseFloat(data.coinhistory.last_price).toString()).toFixed(2)+' <i class="fa fa-arrow-up" aria-hidden="true"></i>');
                    $('.price_updown').addClass("positive");
                    $('.price_updown').removeClass("negative");
                }
                else if(change<0) {
                    $(".price_updown").html(parseFloat(parseFloat(data.coinhistory.last_price).toString()).toFixed(2)+' <i class="fa fa-arrow-down" aria-hidden="true"></i>');

                    $('.price_updown').addClass("negative");
                    $('.price_updown').removeClass("positive");
                }else{

                    $(".price_updown").html(parseFloat(parseFloat(data.coinhistory.last_price).toString()).toFixed(2)+' <i class="fa fa-arrow-up" aria-hidden="true"></i>');

                    $('.price_updown').addClass('positive');
                    $('.price_updown').removeClass('negative');
                }
            };
        });
    }

    function sellavaible(){
        $.getJSON('<?php echo base_url("customer/exchange/getSellavaible") ?>',function(data){

            $(".sell_avaiable").html(Number(data.sellavaiable).toFixed(2));
            $(".crypto_balance").html(Number(data.cryptobalance).toFixed(2));
        });
    }

</script>

<script type="text/javascript">
    $("#buyqty, #buyrate").on("keyup", function(event) {
        event.preventDefault();

        var qty = parseFloat($('#buyqty').val())||0;
        var rate = parseFloat($('#buyrate').val())||0;

        var total = qty*rate;

        $("#buytotal").html("<span><?php echo @$coin_owner_wallet->pair_with ?> " + total + "</span>");

    });
    $("#sellqty, #sellrate").on("keyup", function(event) {
        event.preventDefault();

        var qty = parseFloat($('#sellqty').val())||0;
        var rate = parseFloat($('#sellrate').val())||0;

        var total = qty*rate;

        $("#selltotal").html("<span><?php echo @$coin_owner_wallet->pair_with ?> " + total + "</span>");

    });
</script>

    <script>
      var chart = AmCharts.makeChart("exchangesChart", {
        "type": "stock",
        "theme": "black",
        "categoryAxesSettings": {
            "minPeriod": "mm"
        },
        "dataSets": [{
        "color": "#b0de09",
        "fieldMappings": [ {
            "fromField": "last_price",
            "toField": "value"
            }, {
            "fromField": "total_coin_supply",
            "toField": "volume"
        } ],
        "categoryField": "date",
          /**
           * data loader for data set data
           */
        "dataLoader": {
            "url": '<?php echo base_url("customer/exchange/trade_charthistory"); ?>',
            "format": "json",
            "showCurtain": true,
            "showErrors": false,
            "async": true,
            "reverse": true,
            "delimiter": ",",
            "useColumnNames": true
        },
        }],
        "panels": [ {
            "showCategoryAxis": false,
            "title": "Value",
            "percentHeight": 70,

            "stockGraphs": [ {
              "id": "g1",
              "valueField": "value",
              "type": "smoothedLine",
              "lineThickness": 2,
              "bullet": "round"
            } ],

            "stockLegend": {
              "valueTextRegular": " ",
              "markerType": "none"
            }
          }, {
            "title": "Volume",
            "percentHeight": 30,
            "stockGraphs": [ {
              "valueField": "volume",
              "type": "column",
              "cornerRadiusTop": 2,
              "fillAlphas": 1
            } ],

            "stockLegend": {
              "valueTextRegular": " ",
              "markerType": "none"
            }
        } ],
        "chartScrollbarSettings": {
            "graph": "g1",
            "usePeriod": "10mm",
            "position": "bottom"
        },
        "chartCursorSettings": {
            "valueBalloonsEnabled": true
        },
        "periodSelector": {
        "position": "top",
        "dateFormat": "YYYY-MM-DD JJ:NN",
        "inputFieldWidth": 150,
        "periods": [ {
          "period": "hh",
          "count": 1,
          "label": "1 hour"
        }, {
          "period": "hh",
          "count": 2,
          "label": "2 hours"
        }, {
          "period": "hh",
          "count": 5,
          "selected": true,
          "label": "5 hour"
        }, {
          "period": "hh",
          "count": 12,
          "label": "12 hours"
        }, {
          "period": "MAX",
          "label": "MAX"
        } ]
      },
        "panelsSettings": {
            "usePrefixes": true
          },
        "export": {
            "enabled": true,
            "position": "bottom-right"
        }
    });

    </script>

<script type="text/javascript">

        //Market Depth
        var chart = AmCharts.makeChart("marketDepth", {
            "type": "serial",
            "theme": "patterns",
            "dataLoader": {
                "url": '<?php echo base_url("customer/exchange/market_depth"); ?>',
                "format": "json",
                "reload": 120,
                "showErrors": false,
                "postProcess": function (data) {

                    // Function to process (sort and calculate cummulative volume)
                    function processData(list, type, desc) {

                        // Convert to data points
                        for (var i = 0; i < list.length; i++) {
                            list[i] = {
                                value: Number(list[i][0]),
                                volume: Number(list[i][1])
                            };
                        }

                        // Sort list just in case
                        list.sort(function (a, b) {
                            if (a.value > b.value) {
                                return 1;
                            } else if (a.value < b.value) {
                                return -1;
                            } else {
                                return 0;
                            }
                        });

                        // Calculate cummulative volume
                        if (desc) {
                            for (var i = list.length - 1; i >= 0; i--) {
                                if (i < (list.length - 1)) {
                                    list[i].totalvolume = list[i + 1].totalvolume + list[i].volume;
                                } else {
                                    list[i].totalvolume = list[i].volume;
                                }
                                var dp = {};
                                dp["value"] = list[i].value;
                                dp[type + "volume"] = list[i].volume;
                                dp[type + "totalvolume"] = list[i].totalvolume;
                                res.unshift(dp);
                            }
                        } else {
                            for (var i = 0; i < list.length; i++) {
                                if (i > 0) {
                                    list[i].totalvolume = list[i - 1].totalvolume + list[i].volume;
                                } else {
                                    list[i].totalvolume = list[i].volume;
                                }
                                var dp = {};
                                dp["value"] = list[i].value;
                                dp[type + "volume"] = list[i].volume;
                                dp[type + "totalvolume"] = list[i].totalvolume;
                                res.push(dp);
                            }
                        }

                    }

                    // Init
                    var res = [];
                    processData(data.bids, "bids", true);
                    processData(data.asks, "asks", false);

                    //console.log(res);
                    return res;
                }
            },
            "graphs": [{
                    "id": "bids",
                    "fillAlphas": 0.2,
                    "lineAlpha": 1,
                    "lineThickness": 2,
                    "lineColor": "#0f0",
                    "type": "step",
                    "valueField": "bidstotalvolume",
                    "balloonFunction": balloon
                }, {
                    "id": "asks",
                    "fillAlphas": 0.2,
                    "lineAlpha": 1,
                    "lineThickness": 2,
                    "lineColor": "#f00",
                    "type": "step",
                    "valueField": "askstotalvolume",
                    "balloonFunction": balloon
                }, {
                    "lineAlpha": 0,
                    "fillAlphas": 0.2,
                    "lineColor": "#0f0",
                    "type": "column",
                    "clustered": false,
                    "valueField": "bidsvolume",
                    "showBalloon": true
                }, {
                    "lineAlpha": 0,
                    "fillAlphas": 0.2,
                    "lineColor": "#f00",
                    "type": "column",
                    "clustered": false,
                    "valueField": "asksvolume",
                    "showBalloon": true
                }],
            "categoryField": "value",
            "chartCursor": {},
            "balloon": {
                "textAlign": "left"
            },
            "valueAxes": [{
                    "title": "Volume"
                }],
            "categoryAxis": {
                "title": "Price (<?php echo @$coin_owner_wallet->pair_with ?>/<?php echo @$coin_owner_wallet->symbol ?>)",
                "minHorizontalGap": 100,
                "startOnAxis": true,
                "showFirstLabel": false,
                "showLastLabel": false
            },
            "export": {
                "enabled": true
            }
        });

        function balloon(item, graph) {
            var txt;
            if (graph.id === "asks") {
                txt = "Ask: <strong>" + formatNumber(item.dataContext.value, graph.chart, 4) + "</strong><br />"
                        + "Total volume: <strong>" + formatNumber(item.dataContext.askstotalvolume, graph.chart, 4) + "</strong><br />"
                        + "Volume: <strong>" + formatNumber(item.dataContext.asksvolume, graph.chart, 4) + "</strong>";
            } else {
                txt = "Bid: <strong>" + formatNumber(item.dataContext.value, graph.chart, 4) + "</strong><br />"
                        + "Total volume: <strong>" + formatNumber(item.dataContext.bidstotalvolume, graph.chart, 4) + "</strong><br />"
                        + "Volume: <strong>" + formatNumber(item.dataContext.bidsvolume, graph.chart, 4) + "</strong>";
            }
            return txt;
        }

        function formatNumber(val, chart, precision) {
            return AmCharts.formatNumber(
                val,
                {
                    precision: precision ? precision : chart.precision,
                    decimalSeparator: chart.decimalSeparator,
                    thousandsSeparator: chart.thousandsSeparator
                }
            );
        }

        var exchange = <?php echo $menucontrol->exchange;?>

        if(exchange==0){

            $('#buyqty,#buyrate,.w-md,#sellqty,#sellrate').prop('disabled',true);
        }

    </script>
