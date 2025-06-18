<?php
/**
 * The template for displaying all single posts
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        
        <?php while (have_posts()) : the_post(); ?>
            
            <div class="single-post">
                
                <!-- Back Button -->
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="back-button">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <?php esc_html_e('Back to Blog', 'nest-nepal'); ?>
                </a>

                <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post'); ?>>
                    
                    <!-- Featured Image -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-featured-image-wrapper">
                            <?php 
                            the_post_thumbnail('full', [
                                'class' => 'post-featured-image',
                                'alt' => get_the_title()
                            ]); 
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Header -->
                    <header class="post-header">
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        
                        <div class="post-meta">
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <?php echo get_the_date('F j, Y'); ?>
                            </time>
                            
                            <?php if (get_the_author()) : ?>
                                <span class="post-author">
                                    <?php esc_html_e('By', 'nest-nepal'); ?> <?php the_author(); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (get_the_category_list()) : ?>
                                <span class="post-categories">
                                    <?php esc_html_e('In', 'nest-nepal'); ?> <?php the_category(', '); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <!-- Post Content -->
                    <div class="post-content">
                        <?php
                        the_content();
                        
                        wp_link_pages([
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'nest-nepal'),
                            'after'  => '</div>',
                        ]);
                        ?>
                    </div>

                    <!-- Post Tags -->
                    <?php if (get_the_tags()) : ?>
                        <div class="post-tags">
                            <h3><?php esc_html_e('Tags:', 'nest-nepal'); ?></h3>
                            <?php the_tags('', ' '); ?>
                        </div>
                    <?php endif; ?>

                </article>

                <!-- Post Navigation -->
                <div class="post-navigation">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>
                    
                    <div class="nav-links">
                        <?php if ($prev_post) : ?>
                            <div class="nav-previous">
                                <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" rel="prev">
                                    <span class="nav-subtitle"><?php esc_html_e('Previous Post', 'nest-nepal'); ?></span>
                                    <span class="nav-title"><?php echo esc_html(get_the_title($prev_post)); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($next_post) : ?>
                            <div class="nav-next">
                                <a href="<?php echo esc_url(get_permalink($next_post)); ?>" rel="next">
                                    <span class="nav-subtitle"><?php esc_html_e('Next Post', 'nest-nepal'); ?></span>
                                    <span class="nav-title"><?php echo esc_html(get_the_title($next_post)); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Comments Section -->
                <?php
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>

            </div>

        <?php endwhile; ?>
        
    </div>
</main>

<?php
get_footer();