<?php
/**
 * The template for displaying all single posts - Enhanced with TOC
 */
get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        
        <?php while (have_posts()) : the_post(); ?>
            
            <div class="single-post">
                
                <!-- Post Navigation Bar -->
                <div class="post-nav-bar">
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog')); ?>" class="back-button">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <?php esc_html_e('Back to Blog', 'nest-nepal'); ?>
                    </a>
                    
                    <div class="post-actions">
                        <button class="toc-toggle" onclick="toggleTOC()" aria-label="Toggle Table of Contents">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            TOC
                        </button>
                        
                        <button class="share-button" onclick="sharePost()" aria-label="Share this post">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                            Share
                        </button>
                        
                        <div class="reading-progress">
                            <div class="progress-bar" id="reading-progress-bar"></div>
                        </div>
                    </div>
                </div>

                <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post'); ?>>
                    
                    <!-- Featured Image -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-featured-image-wrapper">
                            <?php 
                            the_post_thumbnail('post-featured', [
                                'class' => 'post-featured-image',
                                'alt' => get_the_title()
                            ]); 
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Header -->
                    <header class="post-header">
                        <!-- Breadcrumbs -->
                        <nav class="breadcrumbs" aria-label="Breadcrumb">
                            <ol class="breadcrumb-list">
                                <li><a href="<?php echo home_url('/'); ?>">Home</a></li>
                                <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog')); ?>">Blog</a></li>
                                <?php if (get_the_category_list()) : ?>
                                    <li><?php echo get_the_category_list(', '); ?></li>
                                <?php endif; ?>
                                <li aria-current="page"><?php the_title(); ?></li>
                            </ol>
                        </nav>

                        <h1 class="post-title"><?php the_title(); ?></h1>
                        
                        <div class="post-meta">
                            <div class="meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12,6 12,12 16,14"></polyline>
                                </svg>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo get_the_date('F j, Y'); ?>
                                </time>
                            </div>
                            
                            <?php if (get_the_author()) : ?>
                                <div class="meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span class="post-author">
                                        <?php the_author(); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="meta-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                                <span class="reading-time">
                                    <?php 
                                    $reading_time = nest_nepal_reading_time(get_the_content());
                                    echo $reading_time . ' min read';
                                    ?>
                                </span>
                            </div>
                        </div>
                    </header>

                    <!-- Floating TOC -->
                    <div id="floating-toc" class="floating-toc">
                        <div class="toc-header">
                            <h3>Table of Contents</h3>
                            <button class="toc-close" onclick="toggleTOC()" aria-label="Close TOC">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                        <div class="toc-content">
                            <!-- TOC will be populated by JavaScript -->
                        </div>
                    </div>

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

                    <!-- Post Footer -->
                    <footer class="post-footer">
                        <!-- Post Tags -->
                        <?php if (get_the_tags()) : ?>
                            <div class="post-tags">
                                <h3><?php esc_html_e('Tags:', 'nest-nepal'); ?></h3>
                                <div class="tags-list">
                                    <?php the_tags('', ''); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Share Buttons -->
                        <div class="post-share">
                            <h3><?php esc_html_e('Share this article:', 'nest-nepal'); ?></h3>
                            <div class="share-buttons">
                                <a href="#" class="share-btn facebook" onclick="shareOnFacebook()" aria-label="Share on Facebook">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <a href="#" class="share-btn twitter" onclick="shareOnTwitter()" aria-label="Share on Twitter">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                </a>
                                <a href="#" class="share-btn linkedin" onclick="shareOnLinkedIn()" aria-label="Share on LinkedIn">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                                <a href="#" class="share-btn copy" onclick="copyToClipboard()" aria-label="Copy link">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </footer>

                </article>

                <!-- Post Navigation -->
                <nav class="post-navigation" aria-label="Post navigation">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>
                    
                    <div class="nav-links">
                        <?php if ($prev_post) : ?>
                            <div class="nav-previous">
                                <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" rel="prev">
                                    <div class="nav-direction">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        <span class="nav-subtitle"><?php esc_html_e('Previous Post', 'nest-nepal'); ?></span>
                                    </div>
                                    <span class="nav-title"><?php echo esc_html(get_the_title($prev_post)); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($next_post) : ?>
                            <div class="nav-next">
                                <a href="<?php echo esc_url(get_permalink($next_post)); ?>" rel="next">
                                    <div class="nav-direction">
                                        <span class="nav-subtitle"><?php esc_html_e('Next Post', 'nest-nepal'); ?></span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                    <span class="nav-title"><?php echo esc_html(get_the_title($next_post)); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </nav>

                <!-- Related Posts -->
                <?php
                $categories = get_the_category();
                if ($categories) {
                    $category_ids = array();
                    foreach ($categories as $category) {
                        $category_ids[] = $category->cat_ID;
                    }
                    
                    $related_posts = get_posts(array(
                        'category__in' => $category_ids,
                        'post__not_in' => array(get_the_ID()),
                        'posts_per_page' => 3,
                        'meta_key' => '_thumbnail_id'
                    ));
                    
                    if ($related_posts) : ?>
                        <section class="related-posts">
                            <h2><?php esc_html_e('Related Articles', 'nest-nepal'); ?></h2>
                            <div class="related-posts-grid">
                                <?php foreach ($related_posts as $post) : setup_postdata($post); ?>
                                    <article class="related-post">
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
                                <?php endforeach; wp_reset_postdata(); ?>
                            </div>
                        </section>
                    <?php endif;
                }
                ?>

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

<?php get_footer(); ?>