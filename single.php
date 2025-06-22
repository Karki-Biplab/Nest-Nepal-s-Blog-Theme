<?php
get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        
        <?php while (have_posts()) : the_post(); ?>
            
            <div class="single-post">
                
                <div class="post-nav-bar">
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog')); ?>" class="back-button">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <?php esc_html_e('Back to Blog', 'nest-nepal'); ?>
                    </a>
                    
                    <div class="post-actions">
                        <button class="share-button" aria-label="Share Post">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                            <span class="tooltip"><?php esc_html_e('Share', 'nest-nepal'); ?></span>
                        </button>
                        <button class="print-button" onclick="window.print()" aria-label="Print Post">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"></path>
                                <path d="M6 14H2v7h20v-7h-4"></path>
                                <path d="M12 14v5"></path>
                            </svg>
                            <span class="tooltip"><?php esc_html_e('Print', 'nest-nepal'); ?></span>
                        </button>
                        <button class="scroll-to-top" aria-label="Scroll to Top">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            <span class="tooltip"><?php esc_html_e('Scroll to Top', 'nest-nepal'); ?></span>
                        </button>
                    </div>
                </div>

                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    <div class="post-meta">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-featured-image">
                                <?php the_post_thumbnail('full'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="post-meta-info">
                            <span class="post-author">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <?php the_author_posts_link(); ?>
                            </span>
                            <span class="post-date">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo get_the_date('M j, Y'); ?>
                                </time>
                            </span>
                            <span class="post-read-time">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span id="reading-time"></span> <?php esc_html_e('min read', 'nest-nepal'); ?>
                            </span>
                        </div>
                    </div>
                </header>

                <div class="entry-content post-content">
                    <?php the_content(); ?>
                </div>

                <?php
                $tags = get_the_tags();
                if ($tags) : ?>
                    <div class="post-tags">
                        <?php foreach ($tags as $tag) : ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag-link">
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php
                // Related Posts
                $current_post_id = get_the_ID();
                $categories = get_the_category($current_post_id);
                if ($categories) {
                    $category_ids = array();
                    foreach ($categories as $individual_category) {
                        $category_ids[] = $individual_category->term_id;
                    }
                    $args = array(
                        'category__in'        => $category_ids,
                        'post__not_in'        => array($current_post_id),
                        'posts_per_page'      => 3,
                        'ignore_sticky_posts' => 1,
                        'orderby'             => 'rand'
                    );
                    $related_posts = new WP_Query($args);

                    if ($related_posts->have_posts()) : ?>
                        <section class="related-posts">
                            <h2><?php esc_html_e('Related Posts', 'nest-nepal'); ?></h2>
                            <div class="related-posts-grid">
                                <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                                    <article class="related-post-item">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <div class="related-post-image">
                                                    <?php the_post_thumbnail('post-thumbnail-small'); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="related-post-content">
                                                <h3><?php the_title(); ?></h3>
                                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                    <?php echo get_the_date('M j, Y'); ?>
                                                </time>
                                            </div>
                                        </a>
                                    </article>
                                <?php endwhile;
                                wp_reset_postdata(); ?>
                            </div>
                        </section>
                    <?php endif;
                }
                ?>

                <?php
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>

            </div>

        <?php endwhile; ?>
        
    </div>
</main>

<?php get_footer(); ?>  