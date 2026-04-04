<?php

namespace SuetinIrbis;

use Irbis\Search;
use Irbis\SearchParameters;
use const Irbis\BRIEF_FORMAT;

final class CatalogShortcode
{

    public function init(): string
    {
        if (isset($_GET['irbis_search_by'])) {
            set_query_var('irbis_search_by', $_GET['irbis_search_by']);
        }
        if (isset($_GET['irbis_search_string'])) {
            set_query_var('irbis_search_string', $_GET['irbis_search_string']);
        }
        if (isset($_GET['irbis_search_category'])) {
            set_query_var('irbis_search_category', $_GET['irbis_search_category']);
        }

        return $this->output();
    }

    private function output(): string
    {
        ob_start();
        /**
         * Action fires before html form
         */
        do_action('irbis-catalog/before-form');

        $this->get_html_form();

        /**
         * Actions fires after html form
         */
        do_action('irbis-catalog/after-form');

        /**
         * Actions fires before listing books
         */
        do_action('irbis-catalog/before-listing');

        $this->get_listing_books();

        /**
         * Actions fires after listing books
         */
        do_action('irbis-catalog/after-listing');

        return ob_get_clean();
    }

    private function get_html_form(): void
    {
        $checked_first = ' checked="checked" ' ??
            !checked(get_query_var('irbis_search_by'), 'author', false) && !checked(get_query_var('irbis_search_by'), 'keywords', false)
        ?>
        <form method="get" id="irbis-search-form" class="irbis-search-form mb-5" action="<?php the_permalink(); ?>"
              role="search">

            <div class="mb-4">
                <span class="d-inline-block mr-2"><strong>Поиск по:</strong></span>

                <div class="form-check form-check-inline">
                    <input class="js-search-by form-check-input" type="radio"
                           id="search-by-title" <?php echo $checked_first; ?>
                           name="irbis_search_by" value="title"
                           data-placeholder-for-search-string="Введите название книги">
                    <label class="form-check-label" for="search-by-title">Название</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="js-search-by form-check-input" type="radio"
                           id="search-by-author" <?php checked(get_query_var('irbis_search_by'), 'author'); ?>
                           name="irbis_search_by" value="author"
                           data-placeholder-for-search-string="Введите автора книги">
                    <label class="form-check-label" for="search-by-author">Автор</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="js-search-by form-check-input" type="radio"
                           id="search-by-keywords" <?php checked(get_query_var('irbis_search_by'), 'keywords'); ?>
                           name="irbis_search_by" value="keywords"
                           data-placeholder-for-search-string="Введите ключевые слова">
                    <label class="form-check-label" for="search-by-keywords">Ключевые слова</label>
                </div>
            </div>

            <label class="sr-only" for="search-string">Поиск по ИРБИС каталогу</label>
            <div class="input-group">
                <input placeholder="Введите название книги"
                       id="search-string"
                       type="text"
                       name="irbis_search_string"
                       value="<?php echo get_query_var('irbis_search_string'); ?>" class="field form-control">
                <span class="input-group-append">
                    <input class="submit btn btn-primary" id="searchsubmit" name="submit" type="submit" value="Найти">
                </span>
            </div>
        </form>

        <?php
    }

    private function get_listing_books(): void
    {
        $irbis = new Irbis();
        $connection = $irbis->make_connection();
        $parameters = new SearchParameters();
        $search_by = get_query_var('irbis_search_by');

        if ($search_by && get_query_var('irbis_search_string')) {
            switch ($search_by) {
                case 'title':
                    $parameters->expression = Search::equals('T=', get_query_var('irbis_search_string') . '$');
                    break;
                case 'author':
                    $parameters->expression = Search::equals('A=', get_query_var('irbis_search_string') . '$');
                    break;
                case 'keywords':
                    $parameters->expression = Search::equals('K=', get_query_var('irbis_search_string') . '$');
                    break;
            }
        }
        // Категория
        if (get_query_var('irbis_search_category')) {
            $parameters->expression = Search::equals('S=', get_query_var('irbis_search_category') . '$');
        }
        $parameters->format = BRIEF_FORMAT;
        $parameters->numberOfRecords = 10;

        $found_books = $connection->searchEx($parameters);

        foreach ($found_books as $book) {
            $record = $connection->readRecord($book->mfn);
            $book_title = $record->fm(200, 'A');
            $first_name = $record->fm(700, 'A');
            $last_name = $record->fm(700, 'B');
            $category = $record->fm(606, 'A');
            $cover = $record->fm(951, 'I');
            $category_link = get_the_permalink() . '?irbis_search_category=' . str_replace(' ', '+', $category);

            View::render('listing-book-template', array(
                'title' => $book_title,
                'bib_desc' => $book->description,
                'category' => $category,
                'category_link' => $category_link,
                'author' => $first_name . ' ' . $last_name,
                'cover' => $cover,
                'mfn' => $book->mfn,
            ));
        }
    }
}

