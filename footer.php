</div><!-- #content -->

        <footer id="colophon" class="site-footer">
            <div class="container">
                <div class="footer-content">
                    
                    <div class="footer-section">
                        <h3><?php bloginfo('name'); ?></h3>
                        <p>
                            <?php 
                            $footer_description = get_theme_mod('footer_description', 
                                'Leading IT services provider in Nepal, specializing in web hosting, domain registration, and comprehensive digital solutions for businesses of all sizes.'
                            );
                            echo esc_html($footer_description);
                            ?>
                        </p>
                    </div>

                    <div class="footer-section">
                        <h4><?php esc_html_e('Services', 'nest-nepal'); ?></h4>
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'footer-services',
                            'container'      => false,
                            'menu_class'     => '',
                            'fallback_cb'    => 'nest_nepal_default_services_menu',
                        ]);
                        ?>
                    </div>

                    <div class="footer-section">
                        <h4><?php esc_html_e('Company', 'nest-nepal'); ?></h4>
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'footer-company',
                            'container'      => false,
                            'menu_class'     => '',
                            'fallback_cb'    => 'nest_nepal_default_company_menu',
                        ]);
                        ?>
                    </div>

                </div>

                <div class="footer-bottom">
                    <p>
                        &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. 
                        <?php esc_html_e('All rights reserved.', 'nest-nepal'); ?>
                        <?php if (get_theme_mod('show_wordpress_credit', true)) : ?>
                            | <?php esc_html_e('Proudly powered by', 'nest-nepal'); ?> 
                            <a href="<?php echo esc_url('https://wordpress.org/'); ?>" target="_blank">WordPress</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </footer>

    </div><!-- #page -->

    <?php wp_footer(); ?>

    <!-- Mobile Menu JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('#mobile-menu');
        
        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                this.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.classList.toggle('active');
                document.body.classList.toggle('mobile-menu-open');
            });
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileToggle.setAttribute('aria-expanded', 'false');
                mobileMenu.classList.remove('active');
                document.body.classList.remove('mobile-menu-open');
            }
        });
    });
    </script>

</body>
</html>

<?php
/**
 * Default footer menu fallbacks
 */
function nest_nepal_default_services_menu() {
    echo '<ul>';
    echo '<li><a href="#">Web Hosting</a></li>';
    echo '<li><a href="#">Domain Registration</a></li>';
    echo '<li><a href="#">Cloud Services</a></li>';
    echo '<li><a href="#">Website Development</a></li>';
    echo '</ul>';
}

function nest_nepal_default_company_menu() {
    echo '<ul>';
    echo '<li><a href="' . esc_url(home_url('/about')) . '">About Us</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">Contact</a></li>';
    echo '<li><a href="' . esc_url(home_url('/support')) . '">Support</a></li>';
    echo '<li><a href="' . esc_url(home_url('/blog')) . '">Blog</a></li>';
    echo '</ul>';
}
?>