<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo (!empty($title)?$title:null) ?></h2>
                    <div class="col-sm-3 col-md-3 pull-right">
                        <a class="btn btn-success w-md m-b-5 pull-right" href="<?php echo base_url("backend/cms/".$this->uri->segment(3)."/form") ?>"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo display($this->uri->segment(3)); ?></a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <table class="datatable2 table table-bordered table-hover">
                    <thead>
                        <tr> 
                            <th><?php echo display('sl_no') ?></th>
                            <th><?php echo display('headline_en') ?></th>
                            <th><?php echo display('category') ?></th>
                            <th><?php echo display('sl_no') ?></th>
                            <th><?php echo display('action') ?></th> 
                        </tr>
                    </thead>    
                    <tbody>
                        <?php if (!empty($article)) ?>
                        <?php $sl = 1; ?>
                        <?php foreach ($article as $value) { ?>
                        <tr>
                            <td><?php echo $sl++; ?></td> 
                            <td><?php echo $value->headline_en; ?></td>
                            <td><?php echo $this->db->select("cat_name_en, cat_name_fr")->from('web_category')->where('cat_id', $value->cat_id)->get()->row()->cat_name_en; ?></td>
                            <td><?php echo $value->position_serial; ?></td>
                            <td>
                                <a href="<?php echo base_url("backend/cms/".$this->uri->segment(3)."/form/$value->article_id") ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                <a href="<?php echo base_url("backend/cms/".$this->uri->segment(3)."/delete/$value->article_id") ?>" onclick="return confirm('<?php echo display("are_you_sure") ?>')" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Delete "><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        <?php } ?>  
                    </tbody>
                </table>
                <?php echo $links; ?>
            </div> 
        </div>
        <?php
            if($this->uri->segment(2)=="cms" && $this->uri->segment(3)=="team"){
        ?>
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h2><?php echo display('team_overview');?></h2>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open_multipart("backend/cms/team/overview") ?>

                    <div class="form-group row">
                        <label for="overviewheaderen" class="col-sm-3 col-form-label"><?php echo display('overview_header_en')?><i class="text-danger">*</i></label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="<?php echo display('overview_header_en')?>" value="<?php echo @$team_overview->headline_en?>" name="overviewheaderen" id="overviewheaderen" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="overviewheaderfr" class="col-sm-3 col-form-label"><?php echo display('overview_header_fr')?><i class="text-danger">*</i></label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="<?php echo display('overview_header_fr')?>"  value="<?php echo @$team_overview->headline_fr?>" name="overviewheaderfr" id="overviewheaderfr" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="subheaderen" class="col-sm-3 col-form-label"><?php echo display('sub_header_en')?><i class="text-danger">*</i></label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="<?php echo display('sub_header_en')?>" name="subheaderen" id="subheaderen" value="<?php echo @$team_overview->article1_en?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="subheaderfr" class="col-sm-3 col-form-label"><?php echo display('sub_header_fr')?><i class="text-danger">*</i></label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="<?php echo display('sub_header_fr')?>" name="subheaderfr" id="subheaderfr" value="<?php echo @$team_overview->article1_fr?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="descriptionen" class="col-sm-3 col-form-label"><?php echo display('description_en')?><i class="text-danger">*</i></label>
                        <div class="col-sm-7">
                            <textarea rows="10" placeholder="<?php echo display('description_en')?>" name="descriptionen" id="descriptionen" class="form-control"><?php echo @$team_overview->article2_en?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="descriptionfr" class="col-sm-3 col-form-label"><?php echo display('description_fr')?><i class="text-danger">*</i></label>
                        <div class="col-sm-7">
                            <textarea  rows="10" placeholder="<?php echo display('description_fr')?>" name="descriptionfr" id="descriptionfr" class="form-control"><?php echo @$team_overview->article2_fr?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <button type="submit" class="btn btn-success  w-md m-b-5"><?php echo display("update") ?></button>
                        </div>
                    </div>

                <?php echo form_close()?>
            </div> 
        </div>
        <?php }?>
    </div>
</div>

 