# WP IRBIS

Плагин использует legacy-библиотеку ИРБИС из `old/libs/Irbis`, но поверх неё собран новый слой:

- Composer PSR-4 автозагрузка для кода плагина
- DTO для запроса и результата
- сервис поиска
- REST API
- стратегия рендера Blade/PHP с фолбеком на шаблоны плагина

## Шорткод

```text
[irbis_catalog]
[irbis_catalog limit="20" show_form="true" show_results="true"]
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

$dto = irbis_catalog_search_result([
    'search_by' => 'keywords',
    'search_string' => 'фольклор',
]);
```

`irbis_catalog_search()` возвращает массив.

`irbis_catalog_search_result()` возвращает `\WpIrbis\Domain\CatalogResult`.

## REST API

Публичный endpoint:

```text
/wp-json/wp-irbis/v1/search
```

Пример:

```text
/wp-json/wp-irbis/v1/search?search_by=title&search_string=Война%20и%20мир
```

Параметры:

- `search_by`: `title`, `author`, `keywords`
- `search_string`
- `search_category`
- `limit`
- `base_url`

## Шаблоны

Порядок рендера:

1. Sage Blade view
2. PHP-шаблон из темы
3. встроенный PHP-шаблон плагина

Blade view по умолчанию:

```text
irbis.catalog
irbis.search-form
irbis.results
irbis.book-card
```

Соответствующие файлы Sage:

```text
your-theme/resources/views/irbis/catalog.blade.php
your-theme/resources/views/irbis/search-form.blade.php
your-theme/resources/views/irbis/results.blade.php
your-theme/resources/views/irbis/book-card.blade.php
```

PHP override в теме:

```text
your-theme/irbis/catalog.php
your-theme/irbis/search-form.php
your-theme/irbis/results.php
your-theme/irbis/book-card.php
```

Контекст шаблонов:

- `$context['request']` это `\WpIrbis\Domain\CatalogRequest`
- `$context['result']` это `\WpIrbis\Domain\CatalogResult`
- `$context['book']` это `\WpIrbis\Domain\Book`

## Хуки

```php
add_filter('wp_irbis/request', function ($request) {
    if ($request instanceof \WpIrbis\Domain\CatalogRequest) {
        return new \WpIrbis\Domain\CatalogRequest(
            $request->searchBy,
            $request->searchString,
            $request->searchCategory,
            20,
            $request->baseUrl,
            $request->showForm,
            $request->showResults
        );
    }

    return $request;
});

add_filter('wp_irbis/template_blade_views', function (array $views, string $template) {
    array_unshift($views, 'components.irbis.' . str_replace('/', '.', $template));
    return $views;
}, 10, 2);

add_filter('wp_irbis/template_php_paths', function (array $paths) {
    array_unshift($paths, get_stylesheet_directory() . '/views/catalog');
    return $paths;
});
```
