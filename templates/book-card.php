<?php

declare(strict_types=1);

$book = $context['book'] ?? [];
$cover = (string) ($book['cover'] ?? '');
$placeholder = WP_IRBIS_URL . 'old/assets/img/book-placeholder.png';
?>
<article class="wp-irbis-book-card" data-mfn="<?php echo esc_attr((string) ($book['mfn'] ?? '')); ?>">
    <div class="wp-irbis-book-card__media">
        <img
            src="<?php echo esc_url($cover !== '' ? $cover : $placeholder); ?>"
            alt="<?php echo esc_attr((string) ($book['title'] ?? '')); ?>"
        >
    </div>

    <div class="wp-irbis-book-card__content">
        <h3 class="wp-irbis-book-card__title"><?php echo esc_html((string) ($book['title'] ?? '')); ?></h3>

        <?php if (! empty($book['author'])) : ?>
            <div class="wp-irbis-book-card__author"><?php echo esc_html((string) $book['author']); ?></div>
        <?php endif; ?>

        <?php if (! empty($book['category']) && ! empty($book['category_link'])) : ?>
            <a class="wp-irbis-book-card__category" href="<?php echo esc_url((string) $book['category_link']); ?>">
                <?php echo esc_html((string) $book['category']); ?>
            </a>
        <?php endif; ?>

        <?php if (! empty($book['description'])) : ?>
            <div class="wp-irbis-book-card__description"><?php echo esc_html((string) $book['description']); ?></div>
        <?php endif; ?>
    </div>
</article>
