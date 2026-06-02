<?php
/**
 * Almaretna Child — page.php
 * Template per le pagine WordPress standard.
 *
 * @package AlmaretnaChild
 */

get_header();
?>

<main id="main" class="page-content" style="padding-top: var(--header-h); min-height: 60vh;">
    <div class="container" style="padding-top: 3rem; padding-bottom: 4rem; max-width: 860px; margin-inline: auto;">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header" style="margin-bottom: 2rem;">
                    <h1 style="font-family: var(--font-heading); font-size: clamp(1.75rem,4vw,2.5rem); color: var(--color-text);">
                        <?php the_title(); ?>
                    </h1>
                    <div style="width:50px;height:3px;background:var(--color-secondary);border-radius:2px;margin-top:1rem;"></div>
                </header>
                <div class="entry-content" style="font-size:1rem;line-height:1.8;color:var(--color-text);">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
