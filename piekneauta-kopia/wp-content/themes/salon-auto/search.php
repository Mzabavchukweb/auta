<?php
/**
 * Search Results Template
 * Wyświetla wyniki wyszukiwania
 */
get_header();
?>

<main class="min-h-screen bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="font-serif text-4xl md:text-5xl font-light text-primary mb-4 tracking-tight uppercase italic">
                Wyniki wyszukiwania
            </h1>
            <p class="text-xl text-gray-600">
                <?php if (have_posts()) : ?>
                    Znaleziono wyniki dla: <strong class="text-primary">"<?php echo esc_html(get_search_query()); ?>"</strong>
                <?php else : ?>
                    Brak wyników dla: <strong class="text-primary">"<?php echo esc_html(get_search_query()); ?>"</strong>
                <?php endif; ?>
            </p>
        </div>
        
        <?php if (have_posts()) : ?>
            
            <!-- Results Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <?php if (get_post_type() === 'car') : ?>
                        <!-- Car Result -->
                        <?php
                        $brand = salon_auto_get_car_field(get_the_ID(), 'brand');
                        $model = salon_auto_get_car_field(get_the_ID(), 'model');
                        $price = salon_auto_get_car_field(get_the_ID(), 'price');
                        $year = salon_auto_get_car_field(get_the_ID(), 'year');
                        $trim = salon_auto_get_car_field(get_the_ID(), 'trim');
                        ?>
                        <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 group">
                            <a href="<?php the_permalink(); ?>" class="block">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="aspect-[4/3] overflow-hidden">
                                        <?php the_post_thumbnail('car-card', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500')); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="aspect-[4/3] bg-gray-200 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="p-6">
                                    <span class="text-xs font-semibold text-accent uppercase tracking-wider">Samochód</span>
                                    <h2 class="font-serif text-2xl font-semibold text-gray-900 mt-2 mb-1 uppercase tracking-wide">
                                        <?php echo esc_html($brand . ' ' . $model); ?>
                                    </h2>
                                    <?php if ($trim) : ?>
                                        <p class="text-sm text-gray-500 mb-4"><?php echo esc_html($trim); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($price) : ?>
                                        <p class="text-xl font-bold text-primary">
                                            <?php echo esc_html(number_format($price, 0, ',', ' ')); ?> zł
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                        
                    <?php else : ?>
                        <!-- Page/Post Result -->
                        <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                            <a href="<?php the_permalink(); ?>" class="block p-6">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    <?php echo get_post_type() === 'page' ? 'Strona' : 'Wpis'; ?>
                                </span>
                                <h2 class="font-serif text-xl font-semibold text-gray-900 mt-2 mb-3">
                                    <?php the_title(); ?>
                                </h2>
                                <?php if (has_excerpt()) : ?>
                                    <p class="text-gray-600 text-sm line-clamp-3">
                                        <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                                    </p>
                                <?php endif; ?>
                                <span class="inline-flex items-center mt-4 text-accent font-semibold text-sm">
                                    Czytaj więcej
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </span>
                            </a>
                        </article>
                    <?php endif; ?>
                    
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-center">
                <?php
                the_posts_pagination(array(
                    'prev_text' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>',
                    'next_text' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
                    'class' => 'flex gap-2',
                ));
                ?>
            </div>
            
        <?php else : ?>
            
            <!-- No Results -->
            <div class="max-w-xl mx-auto text-center">
                <div class="bg-white rounded-2xl p-12 shadow-sm">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Nic nie znaleziono</h2>
                    <p class="text-gray-600 mb-8">
                        Niestety nie znaleźliśmy żadnych wyników pasujących do Twojego zapytania. Spróbuj użyć innych słów kluczowych.
                    </p>
                    
                    <!-- Search Form -->
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="mb-8">
                        <div class="flex gap-2">
                            <input type="search" name="s" placeholder="Szukaj..." value="<?php echo esc_attr(get_search_query()); ?>" class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent">
                            <button type="submit" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 transition-all">
                                Szukaj
                            </button>
                        </div>
                    </form>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center justify-center px-6 py-3 text-primary font-semibold hover:underline">
                            Strona główna
                        </a>
                        <a href="<?php echo esc_url(home_url('/samochody/')); ?>" class="inline-flex items-center justify-center px-6 py-3 bg-accent text-white font-semibold rounded-xl hover:bg-accent/90 transition-all">
                            Zobacz wszystkie samochody
                        </a>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</main>

<?php get_footer(); ?>

