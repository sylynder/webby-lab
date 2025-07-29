        <div class="news-masonry">
            <div class="container">
                    <br>
                    <br>
                    <br>
            </div>
        </div>
        <!-- /.End of news masonry -->
        <div class="blog-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">


                            <?php  
    foreach ($blog as $news_key => $news_value) {
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



                            <div class="col-md-4">
                                <article class="post-grid">
                                    <figure>
                                        <a href="<?php echo base_url('blog/'.$cat_slug->slug."/$slug"); ?>">
                                            <img src="<?php echo base_url($news_article_image); ?>" class="img-fluid" alt="">
                                        </a>
                                    </figure>
                                    <span><a href="<?php echo base_url('blog/'.$cat_slug->slug); ?>"><?php echo $cat_name ?></a></span>
                                    <h4 class="post-title"><a href="<?php echo base_url('blog/'.$cat_slug->slug."/$slug"); ?>"><?php echo strip_tags($news_headline); ?></a></h4>
                                    <p class="post-des"><?php echo substr(strip_tags($news_article1), 0, 110); ?></p>
                                    <div class="information">admin, <?php
                                                $date=date_create($publish_date);
                                                echo date_format($date,"jS, F Y");
                                            ?></div>
                                </article>
                                <!-- /.End of post grid -->
                            </div>


<?php

    }

?>



                        


                            



                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <nav aria-label="Page navigation example">
                                    <?php echo $links; ?>                                    
                                </nav>
                            </div>
                        </div>
                    </div>
                    <?php echo (!empty($content)?$content:null) ?>
                </div>
            </div>
        </div>
        <!-- /.End of blog content -->