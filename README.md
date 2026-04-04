# WP IRBIS

Плагин построен поверх существующей библиотеки ИРБИС из `old/`, но с новым каркасом и более удобным API.

## Шорткод

```text
[irbis_catalog]
[irbis_catalog limit="20" show_form="true" show_results="true"]
```

Поддерживается и legacy-алиас:

```text
[irbis-catalog]
```

## PHP API

```php
echo irbis_catalog([
    'search_by' => 'author',
    'search_string' => 'Пушкин',
]);

$result = irbis_catalog_search([
    'search_by' => 'title',
    'search_string' => 'Война и мир',
]);
```

## Переопределение шаблонов из темы

По умолчанию плагин сначала пытается отрендерить Blade-шаблон из Sage, затем ищет обычный PHP-шаблон в теме, и только потом использует встроенные шаблоны плагина.

Blade view по умолчанию:

```text
irbis.catalog
irbis.search-form
irbis.results
irbis.book-card
```

Это соответствует файлам:

```text
your-theme/resources/views/irbis/catalog.blade.php
your-theme/resources/views/irbis/search-form.blade.php
your-theme/resources/views/irbis/results.blade.php
your-theme/resources/views/irbis/book-card.blade.php
```

PHP-пути по умолчанию:

```text
your-theme/irbis/catalog.php
your-theme/irbis/search-form.php
your-theme/irbis/results.php
your-theme/irbis/book-card.php

```

Контекст шаблонов:

- `catalog.php`: `$context['request']`, `$context['result']`
- `search-form.php`: `$context['request']`
- `results.php`: `$context['request']`, `$context['result']`
- `book-card.php`: `$context['book']`

## Хуки

```php
add_filter('wp_irbis/request', function (array $request) {
    $request['limit'] = 20;
    return $request;
});

add_filter('wp_irbis/book_data', function (array $item) {
    $item['custom_label'] = '...';
    return $item;
});

add_filter('wp_irbis/template_blade_views', function (array $views, string $template) {
    array_unshift($views, 'components.irbis.' . str_replace('/', '.', $template));
    return $views;
}, 10, 2);

add_filter('wp_irbis/template_php_paths', function (array $paths, string $template, string $filename) {
    array_unshift($paths, get_stylesheet_directory() . '/views/catalog');
    return $paths;
}, 10, 3);
```
