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
                <?php echo form_open_multipart("backend/cms/notice/form/$article->article_id") ?>
                <?php echo form_hidden('article_id', $article->article_id) ?> 
                   
                    <div class="form-group row">
                        <label for="article1_en" class="col-sm-2 col-form-label">Notice En<i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <textarea name="article1_en" class="form-control editor" placeholder="<?php echo display('answer_en') ?>" type="text" id="article1_en"><?php echo esc($article->article1_en) ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="article1_fr" class="col-sm-2 col-form-label"><?php echo "Notice"." ".$web_language->name ?></label>
                        <div class="col-sm-10">
                            <textarea name="article1_fr" class="form-control" placeholder="<?php echo display('answer')." ".$web_language->name ?>" type="text" id="article1_fr"><?php echo esc($article->article1_fr) ?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <a href="<?php echo base_url('admin'); ?>" class="btn btn-primary  w-md m-b-5"><?php echo display("cancel") ?></a>
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo $article->article_id?display("update"):display("create") ?></button>
                        </div>
                    </div>
                    
                <?php echo form_close() ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>