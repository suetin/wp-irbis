# WP IRBIS

Плагин для интеграции WordPress с библиотечным каталогом ИРБИС.

Текущая версия не использует `old` на рантайме. Актуальный код и библиотека лежат в:

- `src/` — код плагина
- `templates/` — встроенные PHP-шаблоны
- `libs/Irbis/` — встроенная библиотека клиента ИРБИС
- `assets/` — стили, JS и placeholder-обложка

## Что внутри

- Composer PSR-4 автозагрузка
- DTO для запроса и результата
- отдельный сервис поиска
- REST API
- рендер через Sage Blade или PHP-шаблоны темы
- фолбек на встроенные шаблоны плагина

## Установка

1. Установить зависимости:

```bash
composer install
```

2. Подключить плагин в WordPress.
3. В `Настройки > Общие` заполнить:

- IP сервера ИРБИС
- логин
- пароль
- базу данных

Важно:

- для работы библиотеки ИРБИС нужно PHP-расширение `mbstring`
- без `vendor/autoload.php` плагин не загрузится

## Структура

```text
wp-irbis.php
src/
templates/
libs/Irbis/
assets/
```

Основные классы:

- `WpIrbis\Plugin` — bootstrap и регистрация
- `WpIrbis\Api\Catalog` — фасад для рендера и поиска
- `WpIrbis\Api\SearchService` — поиск и сбор результата
- `WpIrbis\Http\RequestResolver` — нормализация входных параметров
- `WpIrbis\Rendering\TemplateRenderer` — оркестратор рендера
- `WpIrbis\Rest\SearchController` — REST endpoint

## Шорткод

```text
[irbis_catalog]
[irbis_catalog limit="20" show_form="true" show_results="true"]
[irbis-catalog]
```

Поддерживаемые атрибуты:

- `search_by`
- `search_string`
- `search_category`
- `limit`
- `show_form`
- `show_results`

## PHP API

Рендер каталога:

```php
echo irbis_catalog([
    'search_by' => 'author',
    'search_string' => 'Пушкин',
]);
```

Поиск с массивом результата:

```php
$result = irbis_catalog_search([
    'search_by' => 'title',
    'search_string' => 'Война и мир',
]);
```

Поиск с DTO:

```php
$result = irbis_catalog_search_result([
    'search_by' => 'keywords',
    'search_string' => 'фольклор',
]);
```

Вспомогательный рендер шаблона:

```php
echo irbis_catalog_template('book-card', [
    'book' => $book,
]);
```

Контракт:

- `irbis_catalog()` использует текущий HTTP request
- `irbis_catalog_search()` возвращает массив
- `irbis_catalog_search_result()` возвращает `\WpIrbis\Domain\CatalogResult`

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

Ответ:

- `items`
- `error`
- `request`
- `has_query`

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

Встроенные шаблоны плагина:

```text
templates/catalog.php
templates/search-form.php
templates/results.php
templates/book-card.php
```

Контекст шаблонов:

- `$context['request']` — `\WpIrbis\Domain\CatalogRequest`
- `$context['result']` — `\WpIrbis\Domain\CatalogResult`
- `$context['book']` — `\WpIrbis\Domain\Book`

## Хуки

Изменить request:

```php
add_filter('wp_irbis/request', function ($request) {
    if (! $request instanceof \WpIrbis\Domain\CatalogRequest) {
        return $request;
    }

    return new \WpIrbis\Domain\CatalogRequest(
        $request->searchBy,
        $request->searchString,
        $request->searchCategory,
        20,
        $request->baseUrl,
        $request->showForm,
        $request->showResults
    );
});
```

Переопределить Blade view:

```php
add_filter('wp_irbis/template_blade_views', function (array $views, string $template) {
    array_unshift($views, 'components.irbis.' . str_replace('/', '.', $template));
    return $views;
}, 10, 2);
```

Переопределить PHP-пути шаблонов:

```php
add_filter('wp_irbis/template_php_paths', function (array $paths) {
    array_unshift($paths, get_stylesheet_directory() . '/views/catalog');
    return $paths;
});
```

Изменить параметры поиска:

```php
add_filter('wp_irbis/search_parameters', function ($parameters, $request) {
    $parameters->numberOfRecords = 20;
    return $parameters;
}, 10, 2);
```

Изменить книгу после маппинга:

```php
add_filter('wp_irbis/book_data', function ($book) {
    return $book;
});
```
