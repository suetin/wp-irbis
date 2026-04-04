<?php

declare(strict_types=1);

$request = $context['request'] ?? [];
$result = $context['result'] ?? ['items' => [], 'error' => null];
?>
<div class="wp-irbis-catalog">
    <?php if (! empty($request['show_form'])) : ?>
        <?php echo irbis_catalog_template('search-form', ['request' => $request]); ?>
    <?php endif; ?>

    <?php if (! empty($request['show_results'])) : ?>
        <?php echo irbis_catalog_template('results', ['request' => $request, 'result' => $result]); ?>
    <?php endif; ?>
</div>
