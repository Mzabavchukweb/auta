<?php
/**
 * Default Page Template
 * 
 * Template for regular pages (O nas, Kontakt, Leasing, etc.)
 * Uses custom options for content and gallery if available
 */

get_header();

// Get page slug
$page_slug = get_post_field('post_name', get_the_ID());
?>

<section class="py-12 bg-white">
    <div class="container mx-auto">
        <div class="max-w-4xl mx-auto">
            <?php
            while (have_posts()) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-primary mb-4"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content prose prose-lg max-w-none">
                    <?php
                    // Try to get content from options first (imported from static HTML)
                    $page_content = get_option('salon_auto_page_' . $page_slug . '_content', '');
                    
                    // Fallback to custom meta field
                    if (empty($page_content)) {
                        $page_content = get_post_meta(get_the_ID(), 'salon_auto_page_content', true);
                    }
                    
                    // Display content - if it's HTML from static site, output directly
                    if (!empty($page_content)) {
                        // Check if it's HTML (contains tags)
                        if (strip_tags($page_content) !== $page_content) {
                            // It's HTML, output directly (already sanitized in import)
                            echo $page_content;
                        } else {
                            // Plain text, use wpautop
                            echo wp_kses_post(wpautop($page_content));
                        }
                    } else {
                        // Fallback to standard WordPress content
                        the_content();
                    }

                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Strony:', 'salon-auto'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>
                
                <?php
                // Display gallery if available
                $gallery_ids = get_post_meta(get_the_ID(), 'salon_auto_page_gallery', true);
                $gallery_ids = is_array($gallery_ids) ? $gallery_ids : array();
                if (!empty($gallery_ids)) :
                ?>
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-primary mb-6"><?php esc_html_e('Galeria zdjęć', 'salon-auto'); ?></h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($gallery_ids as $img_id) : 
                            $img_url = wp_get_attachment_image_url($img_id, 'large');
                            $img_full = wp_get_attachment_image_url($img_id, 'full');
                            $img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                            if ($img_url) :
                        ?>
                        <a href="<?php echo esc_url($img_full); ?>" class="block aspect-square overflow-hidden rounded-lg hover:opacity-90 transition-opacity" data-lightbox="gallery">
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt ? $img_alt : get_the_title()); ?>" class="w-full h-full object-cover">
                        </a>
                        <?php 
                            endif;
                        endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </article>
            <?php
            endwhile;
            ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>

