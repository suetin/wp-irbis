# WP IRBIS

Плагин для интеграции WordPress с библиотечным каталогом ИРБИС.

Плагин не рендерит интерфейс и не зависит от темы. Он только:

- подключается к серверу ИРБИС
- нормализует входные параметры поиска
- отдаёт данные через `REST API`
- предоставляет PHP helpers для поиска

## REST API

Публичный endpoint:

```text
/wp-json/wp-irbis/v1/search
```

Простой запрос:

```text
/wp-json/wp-irbis/v1/search?search_by=title&search_string=Война%20и%20мир
```

Запрос с дополнительными фильтрами:

```text
/wp-json/wp-irbis/v1/search?search_by=title&search_string=Война%20и%20мир&filters[year]=2020&filters[language]=ru
```

Параметры:

- `search_by`: `title`, `author`, `keywords`
- `search_string`
- `search_category`
- `filters` — объект или query-параметры вида `filters[field]=value`
- `limit`
- `base_url`

Ответ:

- `items` — массив найденных книг
- `error` — ошибка поиска или `null`
- `request` — нормализованный `SearchRequest`
- `has_query` — был ли передан поисковый запрос
- `debug` — отладочные данные, если debug включен

## Запросы из темы

Пример с `fetch`:

```js
const url = new URL('/wp-json/wp-irbis/v1/search', window.location.origin);

url.searchParams.set('search_by', 'title');
url.searchParams.set('search_string', 'Война и мир');
url.searchParams.set('limit', '10');
url.searchParams.set('filters[language]', 'ru');
url.searchParams.set('filters[year]', '2020');

const response = await fetch(url.toString(), {
  headers: {
    Accept: 'application/json',
  },
});

const payload = await response.json();
```

Пример с `base_url`, если тема синхронизирует фильтры с адресной строкой:

```js
const url = new URL('/wp-json/wp-irbis/v1/search', window.location.origin);

url.searchParams.set('search_by', 'author');
url.searchParams.set('search_string', 'Пушкин');
url.searchParams.set('base_url', window.location.pathname);

const response = await fetch(url.toString(), {
  headers: {
    Accept: 'application/json',
  },
});

const payload = await response.json();
```

Пример POST/axios не нужен: endpoint публичный и работает по `GET`.

## PHP helpers

Поиск с массивом результата:

```php
$result = irbis_catalog_search([
    'search_by' => 'title',
    'search_string' => 'Война и мир',
    'filters' => [
        'language' => 'ru',
    ],
]);
```

Поиск с DTO:

```php
$result = irbis_catalog_search_result([
    'search_by' => 'author',
    'search_string' => 'Пушкин',
    'filters' => [
        'year' => '2020',
    ],
]);
```

Стабильный DTO запроса:

- `\WpIrbis\Domain\SearchRequest`

## Расширение фильтров

Новые фильтры лучше добавлять в `filters`, а не в top-level параметры.

Пример:

```php
$result = irbis_catalog_search([
    'search_by' => 'title',
    'search_string' => 'Физика',
    'filters' => [
        'year' => '2024',
        'language' => 'ru',
        'publisher' => 'Наука',
    ],
]);
```

Чтобы превратить дополнительные фильтры в expression для IRBIS, используйте hook:

```php
add_filter('wp_irbis/search_expression_parts', function (array $parts, \WpIrbis\Domain\SearchRequest $request) {
    if (! empty($request->filters['year'])) {
        $parts[] = 'G=' . $request->filters['year'] . '$';
    }

    if (! empty($request->filters['language'])) {
        $parts[] = 'J=' . $request->filters['language'] . '$';
    }

    if (! empty($request->filters['publisher'])) {
        $parts[] = 'O=' . $request->filters['publisher'] . '$';
    }

    return $parts;
}, 10, 2);
```

## Хуки

Изменить request:

```php
add_filter('wp_irbis/request', function ($request) {
    if (! $request instanceof \WpIrbis\Domain\SearchRequest) {
        return $request;
    }

    return new \WpIrbis\Domain\SearchRequest(
        $request->searchBy,
        $request->searchString,
        $request->searchCategory,
        array_merge($request->filters, ['year' => '2020']),
        20,
        $request->baseUrl
    );
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
