<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <div id="page" class="site">
        
        <header id="masthead" class="site-header">
            <div class="container">
                <div class="header-content">
                    
                    <div class="site-logo">
                        <?php if (has_custom_logo()) : ?>
                            <div class="site-logo-image">
                                <?php the_custom_logo(); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="site-branding">
                            <?php if (is_front_page() && is_home()) : ?>
                                <h1 class="site-title">
                                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                        <?php bloginfo('name'); ?>
                                    </a>
                                </h1>
                            <?php else : ?>
                                <p class="site-title">
                                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                        <?php bloginfo('name'); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            
                            <?php
                            $description = get_bloginfo('description', 'display');
                            if ($description || is_customize_preview()) :
                            ?>
                                <p class="site-tagline"><?php echo $description; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <nav id="site-navigation" class="main-navigation">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'menu-1',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'fallback_cb'    => 'nest_nepal_default_menu',
                        ]);
                        ?>
                    </nav>

                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="sr-only"><?php esc_html_e('Toggle Menu', 'nest-nepal'); ?></span>
                        <span class="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                    
                </div>
            </div>
        </header>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu">
            <div class="mobile-menu-content">
                <?php
                wp_nav_menu([
                    'theme_location' => 'menu-1',
                    'menu_id'        => 'mobile-primary-menu',
                    'container'      => false,
                    'fallback_cb'    => 'nest_nepal_default_menu',
                ]);
                ?>
            </div>
        </div>

        <div id="content" class="site-content">

<?php
/**
 * Default menu fallback
 */
function nest_nepal_default_menu() {
    echo '<ul>';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about')) . '">About</a></li>';
    echo '<li><a href="' . esc_url(home_url('/services')) . '">Services</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">Contact</a></li>';
    echo '</ul>';
}
?>