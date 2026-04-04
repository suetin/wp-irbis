<?php

declare(strict_types=1);

$request = $context['request'] ?? null;
$result = $context['result'] ?? null;
?>
<div class="wp-irbis-catalog">
    <?php if ($request instanceof \WpIrbis\Domain\CatalogRequest && $request->showForm) : ?>
        <?php echo irbis_catalog_template('search-form', ['request' => $request]); ?>
    <?php endif; ?>

    <?php if ($request instanceof \WpIrbis\Domain\CatalogRequest && $request->showResults) : ?>
        <?php echo irbis_catalog_template('results', ['request' => $request, 'result' => $result]); ?>
    <?php endif; ?>
</div>
