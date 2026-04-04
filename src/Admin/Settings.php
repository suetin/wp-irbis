<?php

declare(strict_types=1);

namespace WpIrbis\Admin;

final class Settings
{
    public const OPTION = 'wp_irbis_settings';

    public function register(): void
    {
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function registerSettings(): void
    {
        register_setting(
            'general',
            self::OPTION,
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
                'default' => [],
            ]
        );

        add_settings_section(
            'wp_irbis_settings_section',
            __('Настройки подключения к ИРБИС', 'wp-irbis'),
            '__return_empty_string',
            'general'
        );

        $this->addField('host', __('IP сервера ИРБИС', 'wp-irbis'));
        $this->addField('login', __('Логин', 'wp-irbis'));
        $this->addField('password', __('Пароль', 'wp-irbis'), 'password');
        $this->addField('database', __('База данных', 'wp-irbis'));
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
            'general',
            'wp_irbis_settings_section'
        );
    }
}
