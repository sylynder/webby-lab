 <aside class="col-md-3">
<?php if ($recentblog) { ?>
                        <div class="widget">
                            <h2 class="widget-title"><?php echo display('recent_post'); ?></h2>
                            <ul class="recent-post">

<?php  
    foreach ($recentblog as $news_key => $news_value) {
        $article_id         =   $news_value->article_id;
        $cat_id             =   $news_value->cat_id;
        $slug               =   $news_value->slug;
        $news_headline      =   isset($lang) && $lang =="french"?$news_value->headline_fr:$news_value->headline_en;
        $news_article1      =   isset($lang) && $lang =="french"?$news_value->article1_fr:$news_value->article1_en;
        $news_article_image =   $news_value->article_image;
        $publish_date       =   $news_value->publish_date;

        $cat_slug = $this->db->select("slug, cat_name_en, cat_name_fr")->from('web_category')->where('cat_id', $cat_id)->get()->row();
        $cat_name      =   isset($lang) && $lang =="french"?$cat_slug->cat_name_fr:$cat_slug->cat_name_en;
?>
                                <li><a href="<?php echo base_url($this->uri->segment(1).'/'.$cat_slug->slug."/$slug"); ?>"><?php echo $news_headline; ?></a></li>
<?php } ?>
                            </ul>
                        </div>
                        <!-- /.End of recent post -->

<?php } ?>

                        <?php
                            foreach ($advertisement as $add_key => $add_value) { 
                                $ad_position   = $add_value->serial_position;
                                $ad_link       = $add_value->url;
                                $ad_script     = $add_value->script;
                                $ad_image      = $add_value->image;
                                $ad_name       = $add_value->name;                            

                        ?>

                        <?php if (@$ad_position==6) { ?>
                        <div class="widget">
                            <div class="widget_banner">
                                <?php if ($ad_script=="") { ?>
                                <a target="_blank" href="<?php echo $ad_link ?> "><img src="<?php echo base_url($ad_image) ?>" class="img-fluid" alt="<?php echo strip_tags($ad_name) ?>"></a>
                                <?php } else { echo $ad_script; } ?>
                            </div>
                        </div><!-- /.End of banner -->
                        <?php } } ?>

                        <?php if ($blogcat) { ?>
                        <div class="widget">
                            <h4 class="widget-title"><?php echo display('category'); ?></h4>
                            <ul class="widget_category categories">
                                <?php
                                    foreach ($blogcat as $key => $value) {
                                        $newscatname_list  = isset($lang) && $lang =="french"?$value->cat_name_fr:$value->cat_name_en;
                                        $newscatslug  = $value->slug;
                                ?>
                                    <li><a href="<?php echo base_url("blog/$newscatslug") ?>"><span>#</span><?php echo $newscatname_list ?></a></li>
                                <?php
                                    }
                                ?>
                            </ul>
                        </div><!-- /.End of category -->
                        <?php } ?>

                    <?php if ($social_link) { ?>
                        <div class="widget">
                            <h4 class="widget-title"><?php echo display('my_social_link'); ?></h4>
                            <div class="social_icon">
                            <?php foreach ($social_link as $key => $value) { ?>
                                <a href="<?php echo $value->link; ?>" class="<?php echo $value->icon; ?>"><i class="fab fa-<?php echo $value->icon; ?>"></i></a>
                            <?php } ?>    
                            </div>
                        </div><!-- /.End of social link -->
                    <?php } ?>

                    <?php
                            foreach ($advertisement as $add_key => $add_value) { 
                                $ad_position   = $add_value->serial_position;
                                $ad_link       = $add_value->url;
                                $ad_script     = $add_value->script;
                                $ad_image      = $add_value->image;
                                $ad_name       = $add_value->name;                            

                        ?>

                        <?php if (@$ad_position==7) { ?>
                        <div class="widget">
                            <div class="widget_banner">
                                <?php if ($ad_script=="") { ?>
                                <a target="_blank" href="<?php echo $ad_link ?> "><img src="<?php echo base_url($ad_image) ?>" class="img-fluid" alt="<?php echo strip_tags($ad_name) ?>"></a>
                                <?php } else { echo $ad_script; } ?>
                            </div>
                        </div><!-- /.End of banner -->
                        <?php } } ?>
                    </aside>
