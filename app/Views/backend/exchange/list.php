<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <table class="datatable2 table table-bordered table-hover">
                    <thead>
                        <tr class="table-bg">
                            <th class="date">Trade</th>
                            <th class="date">Rate</th>
                            <th class="date">Required QTY</th>
                            <th class="date">Available QTY</th>
                            <th class="date">Required Amount</th>
                            <th class="date">Available Amount</th>
                            <th class="date">Market</th>
                            <th class="date">Open</th>
                            <th class="date">Status</th>
                        </tr>
                    </thead>
                    <tbody id="useropenTrade">
                        <?php  foreach ($open_trade as $key => $value) { ?>
                            <tr>
                                <td><?php echo $value->bid_type; ?></td>
                                <td><?php echo $value->bid_price; ?></td>
                                <td><?php echo $value->bid_qty; ?></td>
                                <td><?php echo $value->bid_qty_available; ?></td>
                                <td><?php echo $value->total_amount; ?></td>
                                <td><?php echo $value->amount_available; ?></td>
                                <td><?php echo $value->market_symbol; ?></td>
                                <td><?php echo $value->open_order; ?></td>
                                <td>Running</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php echo $links; ?>
            </div> 
        </div>
    </div>
</div>

 