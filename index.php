<?php

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        
        <?php if (is_home() || is_front_page()) : ?>
            <div class="blog-header">
                <h1 class="blog-title">
                    <?php 
                    $blog_title = get_theme_mod('blog_title', 'Technology Insights & Updates');
                    echo esc_html($blog_title); 
                    ?>
                </h1>
                <p class="blog-description">
                    <?php 
                    $blog_description = get_theme_mod('blog_description', 'Expert guidance on web hosting, domains, security, and digital transformation');
                    echo esc_html($blog_description); 
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
            
            <div class="blog-grid">
                <?php
                while (have_posts()) :
                    the_post();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="blog-card-image-wrapper">
                                <?php 
                                the_post_thumbnail('large', [
                                    'class' => 'blog-card-image',
                                    'alt' => get_the_title()
                                ]); 
                                ?>
                            </div>
                        <?php else : ?>
                            <div class="blog-card-image-wrapper">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/default-post.jpg" 
                                     alt="<?php the_title(); ?>" 
                                     class="blog-card-image">
                            </div>
                        <?php endif; ?>

                        <div class="blog-card-content">
                            
                            <div class="blog-card-date">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo get_the_date('F j, Y'); ?>
                                </time>
                            </div>

                            <h2 class="blog-card-title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="blog-card-excerpt">
                                <?php 
                                if (has_excerpt()) {
                                    the_excerpt();
                                } else {
                                    echo wp_trim_words(get_the_content(), 25, '...');
                                }
                                ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="read-more">
                                Read More
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>

                        </div>
                    </article>
                <?php
                endwhile;
                ?>
            </div>

            <div class="pagination-wrapper">
                <?php
                echo paginate_links([
                    'mid_size' => 2,
                    'prev_text' => '&larr; Previous',
                    'next_text' => 'Next &rarr;',
                    'class' => 'pagination'
                ]);
                ?>
            </div>

        <?php else : ?>
            
            <div class="no-posts">
                <h2><?php esc_html_e('Nothing Found', 'nest-nepal'); ?></h2>
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'nest-nepal'); ?></p>
                <?php get_search_form(); ?>
            </div>

        <?php endif; ?>
        
    </div>
</main>

<?php
get_footer();
?>