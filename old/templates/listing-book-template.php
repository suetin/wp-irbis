<?php
/**
 * Template for book listing item
 */

$data           = $data ?? new stdClass();
$title          = $data->title ?? '';
$bib_desc       = $data->bib_desc ?? '';
$category       = $data->category ?? '';
$category_link  = $data->category_link ?? '';
$cover          = $data->cover ?? null;
$mfn            = $data->mfn ?? '';
$hue_rotate_deg = rand( 0, 36 ) * 10;
?>

<div id="irbis-catalog-listing-item-<?php echo esc_html( $mfn ); ?>" class="irbis-catalog-listing-item">

    <div class="irbis-catalog-listing-item__col-left">
        <img
                src="<?php echo esc_url( $cover ) ? $cover : SUETIN_IRBIS_PATH_TO_ASSETS . '/img/book-placeholder.png'; ?>" <?php echo 'style="filter:hue-rotate(' . $hue_rotate_deg . 'deg)"' ?>
                alt="Обложка книги" class="img-responsive">
        <span class="irbis-catalog-listing-item__id"><?php echo esc_html( $mfn ); ?></span>
    </div>

    <div class="irbis-catalog-listing-item__col-right">
        <h2 class="irbis-catalog-listing-item__title"><?php echo esc_html( $title ); ?></h2>

	    <?php if ( '' !== $category ): ?>
            <a href="<?php echo esc_url( $category_link ); ?>" class="irbis-catalog-listing-item__cat"
               title="Перейти в категорию <?php echo esc_html( $category ); ?>"><?php echo esc_html( $category ); ?></a>
	    <?php endif ?>

        <div class="irbis-catalog-listing-item__bib-desc"><?php echo esc_html( $bib_desc ); ?></div>

		<?php do_action( 'irbis-catalog/book-card-bottom' ); ?>
    </div>

</div>
