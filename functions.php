<?php
/**
 * Nest Nepal Theme Functions
 *
 * @package Nest_Nepal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function nest_nepal_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Add theme support for custom logos
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ]);

    // Add theme support for HTML5 markup
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Add theme support for custom backgrounds
    add_theme_support('custom-background', [
        'default-color' => 'f8fafc',
        'default-image' => '',
    ]);

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');

    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');

    // Register navigation menus
    register_nav_menus([
        'menu-1' => esc_html__('Primary Menu', 'nest-nepal'),
        'footer-services' => esc_html__('Footer Services', 'nest-nepal'),
        'footer-company' => esc_html__('Footer Company', 'nest-nepal'),
    ]);

    // Set content width
    $GLOBALS['content_width'] = 800;
}
add_action('after_setup_theme', 'nest_nepal_setup');

/**
 * Enqueue scripts and styles
 */
function nest_nepal_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('nest-nepal-style', get_stylesheet_uri(), [], '1.0.0');

    // Enqueue navigation script
    wp_enqueue_script('nest-nepal-navigation', get_template_directory_uri() . '/js/navigation.js', [], '1.0.0', true);

    // Enqueue comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Add smooth scrolling for anchor links
    wp_enqueue_script('nest-nepal-smooth-scroll', get_template_directory_uri() . '/js/smooth-scroll.js', [], '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'nest_nepal_scripts');

/**
 * Register widget areas
 */
function nest_nepal_widgets_init() {
    register_sidebar([
        'name'          => esc_html__('Sidebar', 'nest-nepal'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'nest-nepal'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar([
        'name'          => esc_html__('Footer 1', 'nest-nepal'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Add widgets here.', 'nest-nepal'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => esc_html__('Footer 2', 'nest-nepal'),
        'id'            => 'footer-2',
        'description'   => esc_html__('Add widgets here.', 'nest-nepal'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => esc_html__('Footer 3', 'nest-nepal'),
        'id'            => 'footer-3',
        'description'   => esc_html__('Add widgets here.', 'nest-nepal'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'nest_nepal_widgets_init');

/**
 * Custom excerpt length
 */
function nest_nepal_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'nest_nepal_excerpt_length');

/**
 * Custom excerpt more text
 */
function nest_nepal_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'nest_nepal_excerpt_more');

/**
 * Add custom image sizes
 */
function nest_nepal_custom_image_sizes() {
    add_image_size('nest-nepal-featured', 800, 400, true);
    add_image_size('nest-nepal-blog-card', 400, 250, true);
}
add_action('after_setup_theme', 'nest_nepal_custom_image_sizes');

/**
 * Customize the read more link
 */
function nest_nepal_read_more_link() {
    return '<a class="read-more" href="' . esc_url(get_permalink()) . '">' . esc_html__('Read More', 'nest-nepal') . '</a>';
}
add_filter('the_content_more_link', 'nest_nepal_read_more_link');

/**
 * Add custom body classes
 */
function nest_nepal_body_classes($classes) {
    // Add class for whether sidebar is active
    if (!is_active_sidebar('sidebar-1')) {
        $classes[] = 'no-sidebar';
    }

    // Add class for page layouts
    if (is_page_template('page-templates/full-width.php')) {
        $classes[] = 'full-width';
    }

    return $classes;
}
add_filter('body_class', 'nest_nepal_body_classes');

/**
 * Implement theme customizer
 */
function nest_nepal_customize_register($wp_customize) {
    // Blog Settings Section
    $wp_customize->add_section('nest_nepal_blog_settings', [
        'title'    => esc_html__('Blog Settings', 'nest-nepal'),
        'priority' => 130,
    ]);

    // Blog Title
    $wp_customize->add_setting('blog_title', [
        'default'           => 'Technology Insights & Updates',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('blog_title', [
        'label'    => esc_html__('Blog Title', 'nest-nepal'),
        'section'  => 'nest_nepal_blog_settings',
        'type'     => 'text',
    ]);

    // Blog Description
    $wp_customize->add_setting('blog_description', [
        'default'           => 'Expert guidance on web hosting, domains, security, and digital transformation',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    $wp_customize->add_control('blog_description', [
        'label'    => esc_html__('Blog Description', 'nest-nepal'),
        'section'  => 'nest_nepal_blog_settings',
        'type'     => 'textarea',
    ]);

    // Footer Settings Section
    $wp_customize->add_section('nest_nepal_footer_settings', [
        'title'    => esc_html__('Footer Settings', 'nest-nepal'),
        'priority' => 140,
    ]);

    // Footer Description
    $wp_customize->add_setting('footer_description', [
        'default'           => 'Leading IT services provider in Nepal, specializing in web hosting, domain registration, and comprehensive digital solutions for businesses of all sizes.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);

    $wp_customize->add_control('footer_description', [
        'label'    => esc_html__('Footer Description', 'nest-nepal'),
        'section'  => 'nest_nepal_footer_settings',
        'type'     => 'textarea',
    ]);

    // Show WordPress Credit
    $wp_customize->add_setting('show_wordpress_credit', [
        'default'           => true,
        'sanitize_callback' => 'nest_nepal_sanitize_checkbox',
    ]);

    $wp_customize->add_control('show_wordpress_credit', [
        'label'    => esc_html__('Show WordPress Credit', 'nest-nepal'),
        'section'  => 'nest_nepal_footer_settings',
        'type'     => 'checkbox',
    ]);
}
add_action('customize_register', 'nest_nepal_customize_register');

/**
 * Sanitize checkbox
 */
function nest_nepal_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * Add custom CSS for better styling
 */
function nest_nepal_custom_css() {
    ?>
    <style type="text/css">
        /* Mobile Menu Styles */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9999;
        }

        .mobile-menu.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-menu-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
        }

        .mobile-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .mobile-menu li {
            margin-bottom: 1rem;
        }

        .mobile-menu a {
            display: block;
            padding: 1rem;
            color: #2d3748;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .mobile-menu a:hover {
            background: #f7fafc;
            color: #3182ce;
        }

        /* Hamburger Menu */
        .hamburger {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            background: #4a5568;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle[aria-expanded="true"] .hamburger span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .mobile-menu-toggle[aria-expanded="true"] .hamburger span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle[aria-expanded="true"] .hamburger span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Post Navigation Styles */
        .post-navigation {
            margin: 3rem 0;
            padding: 2rem 0;
            border-top: 1px solid #e2e8f0;
        }

        .nav-links {
            display: flex;
            justify-content: space-between;
            gap: 2rem;
        }

        .nav-previous,
        .nav-next {
            flex: 1;
        }

        .nav-next {
            text-align: right;
        }

        .nav-links a {
            display: block;
            padding: 1rem;
            background: #f7fafc;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: #edf2f7;
            transform: translateY(-2px);
        }

        .nav-subtitle {
            display: block;
            font-size: 0.875rem;
            color: #718096;
            margin-bottom: 0.5rem;
        }

        .nav-title {
            display: block;
            font-weight: 600;
            color: #2d3748;
        }

        /* Post Tags */
        .post-tags {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f7fafc;
            border-radius: 8px;
        }

        .post-tags h3 {
            margin-bottom: 1rem;
            font-size: 1rem;
            color: #2d3748;
        }

        .post-tags a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #3182ce;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-size: 0.875rem;
            margin: 0.25rem;
            transition: all 0.3s ease;
        }

        .post-tags a:hover {
            background: #2c5282;
        }

        /* Responsive Design */
        @media (max-width: 767px) {
            .nav-links {
                flex-direction: column;
            }
            
            .nav-next {
                text-align: left;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'nest_nepal_custom_css');

/**
 * Add theme support for block editor
 */
function nest_nepal_gutenberg_support() {
    // Add support for wide and full alignment
    add_theme_support('align-wide');

    // Add support for custom color palette
    add_theme_support('editor-color-palette', [
        [
            'name'  => esc_html__('Primary Blue', 'nest-nepal'),
            'slug'  => 'primary-blue',
            'color' => '#3182ce',
        ],
        [
            'name'  => esc_html__('Dark Blue', 'nest-nepal'),
            'slug'  => 'dark-blue',
            'color' => '#2c5282',
        ],
        [
            'name'  => esc_html__('Dark Gray', 'nest-nepal'),
            'slug'  => 'dark-gray',
            'color' => '#2d3748',
        ],
        [
            'name'  => esc_html__('Light Gray', 'nest-nepal'),
            'slug'  => 'light-gray',
            'color' => '#f7fafc',
        ],
    ]);
}
add_action('after_setup_theme', 'nest_nepal_gutenberg_support');

/**
 * Security enhancements
 */
function nest_nepal_security_enhancements() {
    // Remove WordPress version from head
    remove_action('wp_head', 'wp_generator');
    
    // Remove Windows Live Writer manifest
    remove_action('wp_head', 'wlwmanifest_link');
    
    // Remove RSD link
    remove_action('wp_head', 'rsd_link');
}
add_action('init', 'nest_nepal_security_enhancements');

/**
 * Optimize WordPress
 */
function nest_nepal_optimize() {
    // Remove emoji scripts
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'nest_nepal_optimize');
?>