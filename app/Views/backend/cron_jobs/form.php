<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="border_preview">
                <?php echo form_open_multipart("backend/cron_jobs/form/$cron_jobs->id") ?>
                <?php echo form_hidden('id', $cron_jobs->id) ?>
                <?php $date = explode(" ", $cron_jobs->next_run_at) ?>

                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label"><?php echo display('name') ?><i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="name" value="<?php echo $cron_jobs->name ?>" class="form-control" placeholder="<?php echo display('name') ?>" type="text" id="name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="command" class="col-sm-4 col-form-label">Command<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="command" value="<?php echo $cron_jobs->command ?>" class="form-control" placeholder="Command" type="url" pattern="https?://.+" id="command">
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="interval_sec" class="col-sm-4 col-form-label">Interval Seconds<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="interval_sec" value="<?php echo $cron_jobs->interval_sec ?>" class="form-control" placeholder="60" type="number" id="interval_sec" min="60">
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="next_run_at_date" class="col-sm-4 col-form-label">Start Cron Jobs Date<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="next_run_at_date" value="<?php echo $date[0] ?>" class="form-control"  type="date" id="next_run_at_date">
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="next_run_at_time" class="col-sm-4 col-form-label">Start Cron Jobs Time<i class="text-danger">*</i></label>
                        <div class="col-sm-8">
                            <input name="next_run_at_time" value="<?php echo $date[1] ?>" class="form-control" type="time" id="next_run_at_time">
                        </div>
                    </div>                   
                    <div class="form-group row">
                        <label for="status" class="col-sm-4 col-form-label"><?php echo display('status') ?></label>
                        <div class="col-sm-8">
                            <label class="radio-inline">
                                <?php echo form_radio('status', '1', (($cron_jobs->status==1 || $cron_jobs->status==null)?true:false)); ?>Active
                             </label>
                            <label class="radio-inline">
                                <?php echo form_radio('status', '0', (($cron_jobs->status=="0")?true:false) ); ?>Inactive
                             </label> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo $cron_jobs->id?display("update"):display("create") ?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>