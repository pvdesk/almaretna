<?php
/**
 * Template Name: Pagina Legale
 * Template Post Type: page
 *
 * Template professionale per Privacy Policy, Cookie Policy,
 * Termini e Condizioni, Diritto di Recesso.
 * Il titolo H1 è generato dal template — NON inserire H1 nel contenuto.
 *
 * @package AlmaretnaChild
 */

get_header();
?>
<style>
.legal-content h2 {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    color: var(--color-text);
    margin-top: 2.5rem;
    margin-bottom: .75rem;
    padding-bottom: .4rem;
    border-bottom: 1px solid var(--color-border);
}
.legal-content h3 {
    font-family: var(--font-heading);
    font-size: 1.05rem;
    color: var(--color-text);
    margin-top: 1.5rem;
    margin-bottom: .5rem;
}
.legal-content p  { margin-bottom: 1rem; }
.legal-content ul,
.legal-content ol { padding-left: 1.5rem; margin-bottom: 1.25rem; line-height: 1.9; }
.legal-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.25rem 0 1.5rem;
    font-size: .9rem;
}
.legal-content table th,
.legal-content table td {
    text-align: left;
    padding: .6rem .9rem;
    border: 1px solid var(--color-border);
}
.legal-content table th {
    background: var(--color-accent);
    font-weight: 600;
}
.legal-content code {
    background: var(--color-accent);
    padding: .1em .4em;
    border-radius: 3px;
    font-size: .88em;
}
.legal-content a {
    color: var(--color-secondary);
    text-decoration: underline;
    text-decoration-color: transparent;
    transition: text-decoration-color .2s;
}
.legal-content a:hover { text-decoration-color: currentColor; }
.legal-meta {
    font-size: .8rem;
    color: var(--color-text-light);
    margin-bottom: 2.5rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.legal-meta::before {
    content: '';
    display: inline-block;
    width: 16px;
    height: 16px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238C8070' stroke-width='2'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cpath d='M12 6v6l4 2'/%3E%3C/svg%3E");
    background-size: contain;
    flex-shrink: 0;
}
</style>

<main id="main" class="page-content page-legal" style="min-height:60vh;">
    <div class="container" style="padding-top:calc(var(--header-h) + 3rem); padding-bottom:4rem; max-width:820px; margin-inline:auto;">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('legal-article'); ?>>

                <header class="entry-header" style="margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:2px solid var(--color-border);">
                    <p style="font-size:.75rem;letter-spacing:.15em;text-transform:uppercase;color:var(--color-primary);font-weight:600;margin-bottom:.5rem;">
                        <?php esc_html_e('Informazioni legali', 'almaretna-child'); ?>
                    </p>
                    <h1 style="font-family:var(--font-heading);font-size:clamp(1.75rem,4vw,2.4rem);color:var(--color-text);margin:0 0 1rem;">
                        <?php the_title(); ?>
                    </h1>
                    <p class="legal-meta">
                        <?php printf(
                            esc_html__('Ultimo aggiornamento: %s', 'almaretna-child'),
                            esc_html(get_the_modified_date('d/m/Y'))
                        ); ?>
                    </p>
                </header>

                <div class="entry-content legal-content">
                    <?php the_content(); ?>
                </div>

                <footer style="margin-top:3.5rem;padding-top:1.5rem;border-top:1px solid var(--color-border);display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between;">
                    <p style="margin:0;font-size:.85rem;color:var(--color-text-light);">
                        © <?php echo esc_html(date('Y')); ?> Almaretna &mdash;
                        Via Scorciavacca Montarsi, 48 &mdash; Nunziata di Mascali (CT)
                    </p>
                    <a href="<?php echo esc_url(alm_url_with_lang(home_url('/'))); ?>" style="font-size:.85rem;color:var(--color-secondary);text-decoration:none;">
                        &larr; <?php echo esc_html(alm_t('foot.back_home')); ?>
                    </a>
                </footer>

            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
