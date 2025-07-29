<header class="nk-header page-header is-transparent is-sticky is-shrink" id="header">
            <!-- Header @s -->
            <div class="header-main">
                <div class="header-container container">
                    <div class="header-wrap">
                        <!-- Logo @s -->
                        <div class="header-logo logo">
                            <a href="./" class="logo-link">
                                <img class="logo-dark" src="assets/website/images/logo.png" srcset="assets/website/images/logo2x.png 2x" alt="logo">
                                <img class="logo-light" src="assets/website/images/logo-full-white.png" srcset="assets/website/images/logo-full-white2x.png 2x" alt="logo">
                            </a>
                        </div>

                        <!-- Menu Toogle @s -->
                        <div class="header-nav-toggle">
                            <a href="#" class="navbar-toggle" data-menu-toggle="header-menu">
                                <div class="toggle-line">
                                    <span></span>
                                </div>
                            </a>
                        </div>

                        <!-- Menu @s -->
                        <div class="header-navbar">
                            <nav class="header-menu" id="header-menu">
                                <ul class="menu">
                                    <li class="menu-item"><a class="menu-link nav-link" href="./">Home</a></li>
                                    
                                    <li class="menu-item has-sub">
                                        <a class="menu-link nav-link menu-toggle" href="#">Who We Are</a>
                                        <ul class="menu-sub menu-drop">
                                            <li class="menu-item"><a class="menu-link nav-link" href="page-about.html">About Us</a></li>
                                            <li class="menu-item"><a class="menu-link nav-link" href="page-about.html">The Big Picture</a></li>
                                            <li class="menu-item"><a class="menu-link nav-link" href="page-team.html">Our Teams</a></li>
                                            <li class="menu-item"><a class="menu-link nav-link" href="page-wallets.html">Packages</a></li>
                                            
                                        </ul>
                                    </li>
                                    <li class="menu-item has-sub">
                                        <a class="menu-link nav-link menu-toggle" href="#">Articles</a>
                                        <ul class="menu-sub menu-drop">
                                            <li class="menu-item"><a class="menu-link nav-link" href="#">BoG</a></li>
                                            <li class="menu-item"><a class="menu-link nav-link" href="#">Agriculture</a></li>
                                            <li class="menu-item"><a class="menu-link nav-link" href="#">Sports</a></li>
                                            <li class="menu-item"><a class="menu-link nav-link" href="#">Transport Services</a></li>
                                            <li class="menu-item"><a class="menu-link nav-link" href="#">Manufacturing</a></li>
                                            <li class="menu-item"><a class="menu-link nav-link" href="#">SMEs</a></li>
                                        </ul>
                                    </li>
                                    <li class="menu-item"><a class="menu-link nav-link" href="#">Packages</a></li>
                                    <li class="menu-item"><a class="menu-link nav-link" href="#contact">Contact</a></li>
                                    
                                </ul>
                                <ul class="menu-btns">
                                    <?php if ($this->session->userdata('isLogIn')) : ?>
                                    <li><a href="<?=site_url('customer/home')?>" class="btn btn-md btn-auto btn-grad"><span>Go to Dashboard</span></a></li>
                                    <?php else: ?>
                                    <li><a href="<?=site_url('login')?>" class="btn btn-md btn-auto btn-grad"><span>Login</span></a></li>
                                    <?php endif;?>
                                </ul>
                            </nav>
                        </div><!-- .header-navbar @e -->
                    </div>                                                
                </div>
            </div><!-- .header-main @e -->

            <!-- Banner @s -->
            <div class="header-banner bg-theme-grad"></div>
            <!-- .header-banner @e -->
        </header>