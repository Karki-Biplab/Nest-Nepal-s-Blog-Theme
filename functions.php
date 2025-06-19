<?php
/**
 * Nest Nepal Theme Functions - Enhanced Version
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
 * Custom Image Sizes - Hostinger Style
 */
function nest_nepal_custom_image_sizes() {
    // Blog card image - perfect for grid layout
    add_image_size('blog-card', 400, 240, true);
    
    // Featured image for single posts
    add_image_size('post-featured', 1200, 600, true);
    
    // Thumbnail for related posts
    add_image_size('post-thumbnail-small', 300, 180, true);
    
    // Medium size for content
    add_image_size('content-medium', 800, 450, true);
}
add_action('after_setup_theme', 'nest_nepal_custom_image_sizes');

/**
 * Set posts per page to 9
 */
function nest_nepal_posts_per_page($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home() || is_category() || is_tag()) {
            $query->set('posts_per_page', 9);
        }
    }
}
add_action('pre_get_posts', 'nest_nepal_posts_per_page');

/**
 * Table of Contents Generator
 */
function nest_nepal_generate_toc($content) {
    // Only add TOC to single posts
    if (!is_single()) {
        return $content;
    }

    // Extract headings
    preg_match_all('/<h([2-6])[^>]*>(.*?)<\/h[2-6]>/i', $content, $matches);
    
    if (empty($matches[0])) {
        return $content;
    }

    $toc = '<div class="table-of-contents">';
    $toc .= '<h3 class="toc-title">Table of Contents</h3>';
    $toc .= '<ul class="toc-list">';
    
    foreach ($matches[0] as $index => $heading) {
        $level = $matches[1][$index];
        $title = strip_tags($matches[2][$index]);
        $anchor = sanitize_title($title) . '-' . $index;
        
        // Add ID to the original heading
        $content = str_replace($heading, str_replace('>', ' id="' . $anchor . '">', $heading), $content);
        
        $toc .= '<li class="toc-level-' . $level . '">';
        $toc .= '<a href="#' . $anchor . '">' . $title . '</a>';
        $toc .= '</li>';
    }
    
    $toc .= '</ul></div>';
    
    // Insert TOC after first paragraph
    $content = preg_replace('/(<p>.*?<\/p>)/', '$1' . $toc, $content, 1);
    
    return $content;
}
add_filter('the_content', 'nest_nepal_generate_toc');

/**
 * Enhanced Code Block Styling
 */
function nest_nepal_enhance_code_blocks($content) {
    // Wrap code blocks in special containers
    $content = preg_replace_callback(
        '/<pre><code([^>]*)>(.*?)<\/code><\/pre>/s',
        function($matches) {
            $attributes = $matches[1];
            $code = $matches[2];
            
            // Extract language if present
            $language = '';
            if (preg_match('/class="[^"]*language-([^"\s]+)/', $attributes, $lang_match)) {
                $language = $lang_match[1];
            }
            
            $output = '<div class="code-block-container">';
            if ($language) {
                $output .= '<div class="code-language">' . strtoupper($language) . '</div>';
            }
            $output .= '<div class="code-actions">';
            $output .= '<button class="copy-code-btn" onclick="copyCode(this)">Copy</button>';
            $output .= '</div>';
            $output .= '<pre class="code-block"><code' . $attributes . '>' . $code . '</code></pre>';
            $output .= '</div>';
            
            return $output;
        },
        $content
    );
    
    // Also handle inline code
    $content = preg_replace(
        '/<code([^>]*)>(.*?)<\/code>/',
        '<code class="inline-code"$1>$2</code>',
        $content
    );
    
    return $content;
}
add_filter('the_content', 'nest_nepal_enhance_code_blocks', 20);

/**
 * Enqueue scripts and styles
 */
function nest_nepal_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('nest-nepal-style', get_stylesheet_uri(), [], '1.2.0');
    
    // Enqueue Prism.js for syntax highlighting
    wp_enqueue_style('prism-css', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css', [], '1.29.0');
    wp_enqueue_script('prism-js', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js', [], '1.29.0', true);
    wp_enqueue_script('prism-autoloader', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js', ['prism-js'], '1.29.0', true);
    
    // Enqueue navigation script
    wp_enqueue_script('nest-nepal-navigation', get_template_directory_uri() . '/js/navigation.js', [], '1.0.0', true);

    // Enqueue comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // TOC smooth scrolling and code copy functionality
    wp_enqueue_script('nest-nepal-enhancements', get_template_directory_uri() . '/js/enhancements.js', ['jquery'], '1.0.0', true);
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
 * Add Reading Time Estimation
 */
function nest_nepal_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return $reading_time;
}

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