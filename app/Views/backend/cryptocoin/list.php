<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                    <div class="col-sm-3 col-md-3 pull-right">
                        <a class="btn btn-success w-md m-b-5 pull-right" href="<?php echo base_url("backend/cryptocoin/form") ?>"><i class="fa fa-plus" aria-hidden="true"></i> Coin</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <table id="ajaxcointable" class="table table-bordered table-hover">
                    <thead>
                        <tr> 
                            <th><?php echo display('sl_no') ?></th>
                            <th>Coin Icon</th>                            
                            <th>Coin Name</th>
                            <th>Full Name</th>
                            <th>Symbol</th>
                            <th>Home Page/Serial</th>
                            <th>Rank</th>
                            <th><?php echo display('status') ?></th>
                            <th><?php echo display('action') ?></th> 
                        </tr>
                    </thead>    
                    <tbody>

                    </tbody>
                </table>

            </div> 
        </div>
    </div>
</div>

<script type="text/javascript">

var table;

$(document).ready(function() {   

    //datatables
    table = $('#ajaxcointable').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [],        //Initial no order.
        "pageLength": 10,   // Set Page Length
        "lengthMenu":[[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        // "paging": false,
        // "searching": false,

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('backend/cryptocoin/ajax_list')?>",
            "type": "POST",
            "data": {"<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>"}
        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            // "targets": [0,4,7],
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
       "fnInitComplete": function (oSettings, response) {
        //$("#id_show_total").text(response.recordsTotal);
      }

    });
    $.fn.dataTable.ext.errMode = 'none';

});

</script>

 