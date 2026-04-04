<?php

declare(strict_types=1);

namespace WpIrbis\Admin;

final class Settings
{
    public const OPTION = 'wp_irbis_settings';
    private const PAGE_SLUG = 'wp-irbis';
    private const SECTION_ID = 'wp_irbis_settings_section';

    public function register(): void
    {
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', [$this, 'registerMenu']);
    }

    public function registerSettings(): void
    {
        register_setting(
            self::PAGE_SLUG,
            self::OPTION,
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
                'default' => [],
            ]
        );

        add_settings_section(
            self::SECTION_ID,
            __('Настройки подключения к ИРБИС', 'wp-irbis'),
            [$this, 'renderSection'],
            self::PAGE_SLUG
        );

        $this->addField('host', __('IP сервера ИРБИС', 'wp-irbis'));
        $this->addField('login', __('Логин', 'wp-irbis'));
        $this->addField('password', __('Пароль', 'wp-irbis'), 'password');
        $this->addField('database', __('База данных', 'wp-irbis'));
    }

    public function registerMenu(): void
    {
        add_options_page(
            __('ИРБИС', 'wp-irbis'),
            __('ИРБИС', 'wp-irbis'),
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('ИРБИС', 'wp-irbis'); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields(self::PAGE_SLUG);
                do_settings_sections(self::PAGE_SLUG);
                submit_button(__('Сохранить изменения', 'wp-irbis'));
                ?>
            </form>
        </div>
        <?php
    }

    public function renderSection(): void
    {
        echo '<p>' . esc_html__('Укажите параметры подключения к серверу ИРБИС.', 'wp-irbis') . '</p>';
    }

    public function sanitize($value): array
    {
        $value = is_array($value) ? $value : [];

        return [
            'host' => sanitize_text_field((string) ($value['host'] ?? '')),
            'login' => sanitize_text_field((string) ($value['login'] ?? '')),
            'password' => sanitize_text_field((string) ($value['password'] ?? '')),
            'database' => sanitize_text_field((string) ($value['database'] ?? '')),
        ];
    }

    private function addField(string $key, string $label, string $type = 'text'): void
    {
        add_settings_field(
            'wp_irbis_' . $key,
            $label,
            function () use ($key, $type): void {
                $options = get_option(self::OPTION, []);
                $value = is_array($options) ? (string) ($options[$key] ?? '') : '';
                ?>
                <input
                    type="<?php echo esc_attr($type); ?>"
                    name="<?php echo esc_attr(self::OPTION); ?>[<?php echo esc_attr($key); ?>]"
                    value="<?php echo esc_attr($value); ?>"
                    class="regular-text"
                >
                <?php
            },
            self::PAGE_SLUG,
            self::SECTION_ID
        );
    }
}
