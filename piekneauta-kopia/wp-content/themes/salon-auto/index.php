<?php
/**
 * Main Index Template (Fallback)
 * 
 * Fallback template if no specific template is found
 */

get_header();
?>

<section class="py-28 bg-white">
    <div class="container mx-auto">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header mb-8">
                    <h1 class="text-3xl font-bold text-primary mb-4"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content prose prose-lg max-w-none">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php
            endwhile;
        else :
        ?>
            <p class="text-center text-gray-500 py-12"><?php esc_html_e('Brak treści do wyświetlenia.', 'salon-auto'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>

