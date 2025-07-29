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
                <?php echo form_open_multipart("backend/white_paper/form") ?>

                    <?php if(!empty($white_paper->white_paper)){?>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <iframe  style="width: 100%;height: 450px;border: none;" src="<?php echo base_url($white_paper->white_paper);?>"></iframe>
                            </div>
                        </div>
                    <?php }?>
                    <div class="form-group row">
                        <label for="white_paper_pdf" class="col-sm-4 col-form-label">White Paper PDF<i class="text-danger">*</i></label>
                        <div class="col-sm-7">
                            <input name="white_paper_pdf" class="form-control" placeholder="PDF" type="file" id="white_paper_pdf" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo display("update") ?></button>
                        </div>
                    </div>
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>


