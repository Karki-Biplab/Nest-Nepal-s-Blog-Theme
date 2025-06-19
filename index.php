<?php
/**
 * The main template file - Enhanced Version
 */

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
                $post_count = 0;
                while (have_posts()) :
                    the_post();
                    $post_count++;
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="blog-card-image-wrapper">
                                <a href="<?php the_permalink(); ?>">
                                    <?php 
                                    the_post_thumbnail('blog-card', [
                                        'class' => 'blog-card-image',
                                        'alt' => get_the_title()
                                    ]); 
                                    ?>
                                </a>
                                
                                <!-- Reading Time Badge -->
                                <div class="reading-time-badge">
                                    <?php 
                                    $reading_time = nest_nepal_reading_time(get_the_content());
                                    echo $reading_time . ' min read';
                                    ?>
                                </div>
                                
                                <!-- Category Badge -->
                                <?php 
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <div class="category-badge">
                                        <?php echo esc_html($categories[0]->name); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <div class="blog-card-image-wrapper">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="blog-card-placeholder">
                                        <svg viewBox="0 0 400 240" class="placeholder-icon">
                                            <rect width="400" height="240" fill="#f1f5f9"/>
                                            <circle cx="200" cy="100" r="30" fill="#cbd5e1"/>
                                            <rect x="150" y="140" width="100" height="8" rx="4" fill="#cbd5e1"/>
                                            <rect x="170" y="160" width="60" height="6" rx="3" fill="#cbd5e1"/>
                                        </svg>
                                    </div>
                                </a>
                                
                                <!-- Reading Time Badge -->
                                <div class="reading-time-badge">
                                    <?php 
                                    $reading_time = nest_nepal_reading_time(get_the_content());
                                    echo $reading_time . ' min read';
                                    ?>
                                </div>
                                
                                <!-- Category Badge -->
                                <?php 
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <div class="category-badge">
                                        <?php echo esc_html($categories[0]->name); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="blog-card-content">
                            
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="blog-card-date">
                                <?php echo get_the_date('M j, Y'); ?>
                            </time>

                            <h2 class="blog-card-title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="blog-card-excerpt">
                                <?php 
                                if (has_excerpt()) {
                                    echo wp_trim_words(get_the_excerpt(), 20, '...');
                                } else {
                                    echo wp_trim_words(get_the_content(), 20, '...');
                                }
                                ?>
                            </div>

                            <?php if (get_the_author()) : ?>
                                <span class="blog-card-author">By <?php the_author(); ?></span>
                            <?php endif; ?>

                            <div class="blog-card-footer">
                                <?php if (get_the_tags()) : ?>
                                    <div class="blog-card-tags">
                                        <?php 
                                        $tags = get_the_tags();
                                        $tag_count = 0;
                                        foreach ($tags as $tag) {
                                            if ($tag_count >= 2) break;
                                            echo '<span class="tag">' . esc_html($tag->name) . '</span>';
                                            $tag_count++;
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </article>
                <?php
                endwhile;
                ?>
            </div>

            <!-- Enhanced Pagination -->
            <div class="pagination-wrapper">
                <?php
                $pagination = paginate_links([
                    'mid_size' => 2,
                    'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Previous',
                    'next_text' => 'Next <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
                    'type' => 'array'
                ]);
                
                if ($pagination) {
                    echo '<nav class="pagination" aria-label="Posts pagination">';
                    echo '<ul class="pagination-list">';
                    foreach ($pagination as $page) {
                        echo '<li class="pagination-item">' . $page . '</li>';
                    }
                    echo '</ul>';
                    echo '</nav>';
                }
                ?>
            </div>

            <!-- Blog Stats -->
            <div class="blog-stats">
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo wp_count_posts()->publish; ?></span>
                        <span class="stat-label">Articles Published</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo wp_get_current_user()->ID ? 'Welcome Back!' : 'Join Us'; ?></span>
                        <span class="stat-label"><?php echo wp_get_current_user()->ID ? 'Reader' : 'New Readers Daily'; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo get_categories(['hide_empty' => true, 'number' => 1])[0]->count ?? '50+'; ?></span>
                        <span class="stat-label">Topics Covered</span>
                    </div>
                </div>
            </div>

        <?php else : ?>
            
            <div class="no-posts">
                <div class="no-posts-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>
                <h2><?php esc_html_e('No Posts Found', 'nest-nepal'); ?></h2>
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search or browse our categories?', 'nest-nepal'); ?></p>
                
                <div class="no-posts-actions">
                    <?php get_search_form(); ?>
                    
                    <div class="category-suggestions">
                        <h3>Browse Categories:</h3>
                        <ul class="category-list">
                            <?php
                            $categories = get_categories(['number' => 5, 'hide_empty' => true]);
                            foreach ($categories as $category) {
                                echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

        <?php endif; ?>
        
    </div>
</main>

<?php
get_footer();
?>