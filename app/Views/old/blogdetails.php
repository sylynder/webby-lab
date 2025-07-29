<?php
$cat_title1  = isset($lang) && $lang =="french"?$cat_info->cat_title1_fr:$cat_info->cat_title1_en;
$cat_title2  = isset($lang) && $lang =="french"?$cat_info->cat_title2_fr:$cat_info->cat_title2_en;

$news_headline      =   isset($lang) && $lang =="french"?$blog->headline_fr:$blog->headline_en;
$news_article1      =   isset($lang) && $lang =="french"?$blog->article1_fr:$blog->article1_en;
$news_article_image =   $blog->article_image;
$publish_date       =   $blog->publish_date;

?>

<br>
<br>
<br>
<br>
<br>
        <div class="blog_wrapper">
            <div class="container">
                <div class="row">
                    <main class="col-md-9">
                        <?php
                            foreach ($advertisement as $add_key => $add_value) { 
                                $ad_position   = $add_value->serial_position;
                                $ad_link       = $add_value->url;
                                $ad_script     = $add_value->script;
                                $ad_image      = $add_value->image;
                                $ad_name       = $add_value->name;                            

                        ?>

                        <?php if (@$ad_position==8) { ?>
                        <div class="widget">
                            <div class="widget_banner">
                                <?php if ($ad_script=="") { ?>
                                <a target="_blank" href="<?php echo $ad_link ?> "><img src="<?php echo base_url($ad_image) ?>" class="img-fluid" alt="<?php echo strip_tags($ad_name) ?>"></a>
                                <?php } else { echo $ad_script; } ?>
                            </div>
                        </div><!-- /.End of banner -->
                        <?php } } ?>
                        <div class="post_details">
                            <header class="details-header">
                                <div class="post-cat"><span class="cat-links"><a href="<?php echo base_url('blog') ?>">Blog</a></span></div>
                                <h2><?php echo $news_headline; ?></h2>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-img">
                                        <img src="<?php echo base_url($news_article_image); ?>" class="" alt="">
                                    </div>
                                    <div class="meta-info">
                                        <span class="avatar-name">admin</span>
                                        <span class="date"><?php 
                                                $date=date_create($publish_date);
                                                echo date_format($date,"jS, F Y"); 
                                            ?></span>
                                    </div>
                                </div>
                            </header>
                            <figure>
                                <img src="<?php echo base_url($news_article_image); ?>" alt="<?php echo strip_tags($news_headline); ?>" class="aligncenter img-fluid">
                            </figure>
                            <?php echo $news_article1; ?>
                          
                        </div>
                        <!-- /.End of post details -->
                        <?php
                            foreach ($advertisement as $add_key => $add_value) { 
                                $ad_position   = $add_value->serial_position;
                                $ad_link       = $add_value->url;
                                $ad_script     = $add_value->script;
                                $ad_image      = $add_value->image;
                                $ad_name       = $add_value->name;                            

                        ?>

                        <?php if (@$ad_position==9) { ?>
                        <div class="widget">
                            <div class="widget_banner">
                                <?php if ($ad_script=="") { ?>
                                <a target="_blank" href="<?php echo $ad_link ?> "><img src="<?php echo base_url($ad_image) ?>" class="img-fluid" alt="<?php echo strip_tags($ad_name) ?>"></a>
                                <?php } else { echo $ad_script; } ?>
                            </div>
                        </div><!-- /.End of banner -->
                        <?php } } ?>
                    </main>
                    <?php echo (!empty($content)?$content:null) ?>
                </div>
            </div>
        </div>
        <!-- /.End of page content -->