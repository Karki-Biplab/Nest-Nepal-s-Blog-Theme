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
    
    <!-- Inline CSS to force logo size (highest priority) -->
    <style>
        /* FORCE LOGO SIZE - INLINE FOR MAXIMUM PRIORITY */
        .custom-logo, 
        .theme-logo,
        .site-header img[class*="logo"],
        .site-header .custom-logo-link img,
        .custom-logo-wrapper img,
        .theme-logo-wrapper img {
            width: 80px !important;
            height: 52px !important;
            max-width: 80px !important;
            max-height: 52px !important;
            object-fit: contain !important;
            object-position: center !important;
        }
        
        .custom-logo-wrapper,
        .theme-logo-wrapper {
            width: 85px !important;
            height: 55px !important;
            max-width: 85px !important;
            max-height: 55px !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <div id="page" class="site">
        
        <header id="masthead" class="site-header">
            <div class="container">
                <div class="header-content">
                    
                    <div class="site-logo">
                        <div class="site-logo-container">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link" rel="home">
                                <?php if (has_custom_logo()) : ?>
                                    <div class="custom-logo-wrapper">
                                        <?php 
                                        // Get custom logo with specific size attributes
                                        $custom_logo_id = get_theme_mod('custom_logo');
                                        if ($custom_logo_id) {
                                            $logo_image = wp_get_attachment_image($custom_logo_id, 'full', false, array(
                                                'class' => 'custom-logo',
                                                'style' => 'width: 80px !important; height: 52px !important; max-width: 80px !important; max-height: 52px !important; object-fit: contain !important;',
                                                'alt' => get_bloginfo('name')
                                            ));
                                            echo $logo_image;
                                        }
                                        ?>
                                    </div>
                                <?php elseif (file_exists(get_template_directory() . '/logo.png')) : ?>
                                    <!-- Use logo.png from theme directory if it exists -->
                                    <div class="theme-logo-wrapper">
                                        <img src="<?php echo get_template_directory_uri(); ?>/logo.png" 
                                             alt="<?php bloginfo('name'); ?>" 
                                             class="theme-logo"
                                             style="width: 80px !important; height: 52px !important; max-width: 80px !important; max-height: 52px !important; object-fit: contain !important;">
                                    </div>
                                <?php else : ?>
                                    <!-- Attractive default logo with modern design -->
                                    <div class="default-logo-wrapper">
                                        <div class="logo-icon">
                                            <svg width="48" height="48" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <defs>
                                                    <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                                        <stop offset="0%" style="stop-color:#3182ce;stop-opacity:1" />
                                                        <stop offset="100%" style="stop-color:#2563eb;stop-opacity:1" />
                                                    </linearGradient>
                                                </defs>
                                                <rect width="32" height="32" rx="8" fill="url(#logoGradient)"/>
                                                <path d="M16 8L22 12V24H10V12L16 8Z" fill="white" fill-opacity="0.9"/>
                                                <circle cx="16" cy="17" r="2.5" fill="white"/>
                                                <path d="M13 21H19" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- REMOVED SITE BRANDING TEXT -->
                            </a>
                        </div>
                    </div>

                    <!-- Blog Search Bar -->
                    <div class="header-search">
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="search-input-wrapper">
                                <input type="search" 
                                       class="search-field" 
                                       placeholder="<?php echo esc_attr_x('Search articles...', 'placeholder', 'nest-nepal'); ?>"
                                       value="<?php echo get_search_query(); ?>" 
                                       name="s" 
                                       title="<?php echo esc_attr_x('Search for:', 'label', 'nest-nepal'); ?>" />
                                <button type="submit" class="search-submit">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                    <span class="screen-reader-text"><?php echo _x('Search', 'submit button', 'nest-nepal'); ?></span>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </header>

        <div id="content" class="site-content">