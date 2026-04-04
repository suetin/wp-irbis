<?php

declare(strict_types=1);

$book = $context['book'] ?? null;
$cover = $book instanceof \WpIrbis\Domain\Book ? $book->cover : '';
$placeholder = WP_IRBIS_URL . 'old/assets/img/book-placeholder.png';
?>
<article class="wp-irbis-book-card" data-mfn="<?php echo esc_attr($book instanceof \WpIrbis\Domain\Book ? $book->mfn : ''); ?>">
    <div class="wp-irbis-book-card__media">
        <img
            src="<?php echo esc_url($cover !== '' ? $cover : $placeholder); ?>"
            alt="<?php echo esc_attr($book instanceof \WpIrbis\Domain\Book ? $book->title : ''); ?>"
        >
    </div>

    <div class="wp-irbis-book-card__content">
        <h3 class="wp-irbis-book-card__title"><?php echo esc_html($book instanceof \WpIrbis\Domain\Book ? $book->title : ''); ?></h3>

        <?php if ($book instanceof \WpIrbis\Domain\Book && $book->author !== '') : ?>
            <div class="wp-irbis-book-card__author"><?php echo esc_html($book->author); ?></div>
        <?php endif; ?>

        <?php if ($book instanceof \WpIrbis\Domain\Book && $book->category !== '' && $book->categoryLink !== '') : ?>
            <a class="wp-irbis-book-card__category" href="<?php echo esc_url($book->categoryLink); ?>">
                <?php echo esc_html($book->category); ?>
            </a>
        <?php endif; ?>

        <?php if ($book instanceof \WpIrbis\Domain\Book && $book->description !== '') : ?>
            <div class="wp-irbis-book-card__description"><?php echo esc_html($book->description); ?></div>
        <?php endif; ?>
    </div>
</article>
