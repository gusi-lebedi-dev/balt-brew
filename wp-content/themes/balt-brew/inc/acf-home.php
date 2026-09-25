<?php
/**
 * ACF fields for the home page editor.
 */

if (!defined('ABSPATH')) {
    exit;
}

function baltic_design_copy(string $key): string
{
    static $copy;
    if ($copy === null) {
        $copy = json_decode(file_get_contents(__DIR__ . '/design-copy.json'), true) ?: [];
    }
    return (string) ($copy[$key] ?? '');
}

add_action('acf/init', 'baltic_register_home_acf');
add_action('init', 'baltic_register_home_acf', 20);

function baltic_register_home_acf(): void
{
    static $registered = false;

    if ($registered) {
        return;
    }

    if (!function_exists('acf_add_options_page') || !function_exists('acf_add_local_field_group')) {
        return;
    }

    $registered = true;

    acf_add_options_page([
        'page_title' => 'Футер',
        'menu_title' => 'Футер',
        'menu_slug'  => 'baltic-home',
        'capability' => 'edit_theme_options',
        'redirect'   => false,
        'position'   => 21,
        'icon_url'   => 'dashicons-editor-kitchensink',
    ]);

    $fields = [
        [
            'key' => 'field_baltic_tab_hero',
            'label' => 'Первый экран',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ],
        baltic_acf_hide_section_field('hero'),
        baltic_acf_file_field('hero_desktop_intro', 'Видео для компьютера: начало', 'mp4,webm'),
        baltic_acf_file_field('hero_desktop_loop', 'Видео для компьютера: повтор', 'mp4,webm'),
        baltic_acf_file_field('hero_mobile_intro', 'Видео для телефона: начало', 'mp4,webm'),
        baltic_acf_file_field('hero_mobile_loop', 'Видео для телефона: повтор', 'mp4,webm'),

        [
            'key' => 'field_baltic_tab_products',
            'label' => 'Ассортимент',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ],
        baltic_acf_hide_section_field('products'),
        [
            'key' => 'field_baltic_product_items',
            'label' => 'Товары',
            'name' => 'product_items',
            'type' => 'repeater',
            'instructions' => 'Добавляйте позиции ассортимента в том порядке, в котором они должны идти в карусели.',
            'layout' => 'block',
            'button_label' => 'Добавить товар',
            'min' => 1,
            'collapsed' => 'field_baltic_product_item_title',
            'sub_fields' => [
                [
                    'key' => 'field_baltic_product_item_title',
                    'label' => 'Название',
                    'name' => 'title',
                    'type' => 'text',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_baltic_product_item_modifier',
                    'label' => 'Тип анимации',
                    'name' => 'modifier',
                    'type' => 'select',
                    'instructions' => 'Оставьте подходящий тип из существующей анимации. Для новых товаров обычно подойдет "Обычный".',
                    'choices' => [
                        'product1' => 'Обычный',
                        'product2' => 'IPA',
                        'product4' => 'Бланш',
                        'product3' => 'Крик',
                        'cider' => 'Сидр',
                    ],
                    'default_value' => 'product1',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_baltic_product_item_description',
                    'label' => 'Описание',
                    'name' => 'description',
                    'type' => 'textarea',
                    'rows' => 5,
                    'new_lines' => '',
                ],
                [
                    'key' => 'field_baltic_product_item_alcohol',
                    'label' => 'Алкоголь',
                    'name' => 'alcohol',
                    'type' => 'text',
                    'wrapper' => ['width' => '33'],
                ],
                [
                    'key' => 'field_baltic_product_item_density',
                    'label' => 'Плотность',
                    'name' => 'density',
                    'type' => 'text',
                    'wrapper' => ['width' => '33'],
                ],
                [
                    'key' => 'field_baltic_product_item_ibu',
                    'label' => 'IBU',
                    'name' => 'ibu',
                    'type' => 'text',
                    'wrapper' => ['width' => '34'],
                ],
                [
                    'key' => 'field_baltic_product_item_background',
                    'label' => 'Фон слайда',
                    'name' => 'background',
                    'type' => 'image',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'wrapper' => ['width' => '50'],
                ],
                [
                    'key' => 'field_baltic_product_item_bottle',
                    'label' => 'Бутылка',
                    'name' => 'bottle',
                    'type' => 'image',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'wrapper' => ['width' => '50'],
                ],
            ],
        ],

        [
            'key' => 'field_baltic_tab_about',
            'label' => 'О бренде',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ],
        baltic_acf_hide_section_field('about'),
        baltic_acf_text_field('about_title', 'Заголовок блока', 'о нас'),
        baltic_acf_image_field('about_background', 'Фон блока (компьютер)', 'Рекомендуемый размер: 1765 × 891 px. Если изображение не выбрано, используется встроенный фон.'),
        baltic_acf_image_field('about_background_mobile', 'Фон блока (телефон)', 'Рекомендуемый размер: 460 × 560 px. Если изображение не выбрано, используется встроенный фон.'),
        [
            'key' => 'field_baltic_about_tabs',
            'label' => 'Вкладки блока',
            'name' => 'about_tabs',
            'type' => 'repeater',
            'instructions' => 'Редактируйте содержимое вкладок. Стандартные вкладки выводятся в порядке: «Концепция», «История», «Пивовары», «Пивоварни»; дополнительные — после них. Чекбокс «Скрыть вкладку» убирает её с сайта, сохраняя содержимое. Отметьте одну вкладку как активную после загрузки; если отмечено несколько, используется первая видимая.',
            'layout' => 'block',
            'button_label' => 'Добавить вкладку',
            'min' => 0,
            'max' => 0,
            'collapsed' => 'field_baltic_about_tab_label',
            'sub_fields' => [
                [
                    'key' => 'field_baltic_about_tab_hidden',
                    'label' => 'Скрыть вкладку',
                    'name' => 'hidden',
                    'type' => 'true_false',
                    'message' => 'Не показывать эту вкладку на сайте',
                    'default_value' => 0,
                ],
                [
                    'key' => 'field_baltic_about_tab_initially_active',
                    'label' => 'Активная вкладка после загрузки',
                    'name' => 'initially_active',
                    'type' => 'true_false',
                    'message' => 'Показывать эту вкладку активной после загрузки страницы',
                    'default_value' => 0,
                ],
                [
                    'key' => 'field_baltic_about_tab_kind',
                    'label' => 'Тип вкладки',
                    'name' => 'kind',
                    'type' => 'select',
                    'instructions' => 'Тип определяет поведение вкладки. Таймлайн доступен только для типа «История».',
                    'choices' => [
                        'concept' => 'Концепция',
                        'history' => 'История с таймлайном',
                        'brewers' => 'Пивовары',
                        'breweries' => 'Пивоварни',
                        'custom' => 'Обычная вкладка',
                    ],
                    'default_value' => 'custom',
                    'return_format' => 'value',
                    'wrapper' => ['width' => '25'],
                ],
                [
                    'key' => 'field_baltic_about_tab_label',
                    'label' => 'Название вкладки',
                    'name' => 'label',
                    'type' => 'text',
                    'wrapper' => ['width' => '35'],
                ],
                [
                    'key' => 'field_baltic_about_tab_heading',
                    'label' => 'Заголовок текста',
                    'name' => 'heading',
                    'type' => 'text',
                    'wrapper' => ['width' => '40'],
                ],
                [
                    'key' => 'field_baltic_about_tab_text',
                    'label' => 'Текст вкладки',
                    'name' => 'text',
                    'type' => 'textarea',
                    'rows' => 7,
                    'new_lines' => '',
                ],
                [
                    'key' => 'field_baltic_about_tab_active_timeline_item',
                    'label' => 'Активное событие по умолчанию',
                    'name' => 'active_timeline_item',
                    'type' => 'number',
                    'instructions' => 'Укажите номер события из списка ниже: 1, 2, 3 и так далее.',
                    'default_value' => 1,
                    'min' => 1,
                    'step' => 1,
                    'wrapper' => ['width' => '50'],
                    'conditional_logic' => [[[
                        'field' => 'field_baltic_about_tab_kind',
                        'operator' => '==',
                        'value' => 'history',
                    ]]],
                ],
                [
                    'key' => 'field_baltic_about_tab_timeline',
                    'label' => 'События таймлайна',
                    'name' => 'timeline_items',
                    'type' => 'repeater',
                    'instructions' => 'Каждое событие может иметь свой большой текст. При выборе даты на сайте этот заголовок и текст подставятся в основной блок.',
                    'layout' => 'block',
                    'button_label' => 'Добавить событие',
                    'min' => 0,
                    'collapsed' => 'field_baltic_about_timeline_heading',
                    'conditional_logic' => [[[
                        'field' => 'field_baltic_about_tab_kind',
                        'operator' => '==',
                        'value' => 'history',
                    ]]],
                    'sub_fields' => [
                        [
                            'key' => 'field_baltic_about_timeline_year',
                            'label' => 'Год',
                            'name' => 'year',
                            'type' => 'text',
                            'wrapper' => ['width' => '25'],
                        ],
                        [
                            'key' => 'field_baltic_about_timeline_month',
                            'label' => 'Месяц',
                            'name' => 'month',
                            'type' => 'text',
                            'wrapper' => ['width' => '25'],
                        ],
                        [
                            'key' => 'field_baltic_about_timeline_heading',
                            'label' => 'Заголовок события',
                            'name' => 'heading',
                            'type' => 'text',
                            'wrapper' => ['width' => '50'],
                        ],
                        [
                            'key' => 'field_baltic_about_timeline_text',
                            'label' => 'Большой текст события',
                            'name' => 'text',
                            'type' => 'textarea',
                            'rows' => 10,
                            'new_lines' => '',
                        ],
                    ],
                ],
            ],
        ],

        [
            'key' => 'field_baltic_tab_news',
            'label' => 'Блок новостей',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ],
        baltic_acf_hide_section_field('news'),
        [
            'key' => 'field_baltic_news_admin_hint',
            'label' => 'Содержание новостей',
            'name' => '',
            'type' => 'message',
            'message' => 'Добавление и редактирование новостей находится в отдельном разделе «Новости» в левом меню. На главной автоматически показываются до трёх последних опубликованных новостей.',
        ],
        baltic_acf_text_field('news_title', 'Название секции', 'новости'),
        baltic_acf_text_field('news_all_label', 'Текст ссылки на все новости', 'Все новости'),
        baltic_acf_image_field('news_background', 'Фон блока (компьютер)', 'Рекомендуемая ширина: 1920 px. Пустое поле оставляет встроенный фон, подходящий под количество новостей.'),
        baltic_acf_image_field('news_background_mobile', 'Фон блока (телефон)', 'Рекомендуемая ширина: 460 px. Пустое поле оставляет встроенный фон, подходящий под количество новостей.'),

        [
            'key' => 'field_baltic_tab_video',
            'label' => 'Видео',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ],
        baltic_acf_hide_section_field('video'),
        baltic_acf_image_field('video_background', 'Фон блока (компьютер)', 'Рекомендуемый размер: 1535 × 1024 px. Если изображение не выбрано, используется встроенный фон.'),
        baltic_acf_image_field('video_background_mobile', 'Фон блока (телефон)', 'Рекомендуемый размер: 375 × 566 px. Если изображение не выбрано, используется встроенный фон.'),
        baltic_acf_text_field('video_title', 'Название видео', 'Название видео'),
        baltic_acf_url_field(
            'video_iframe_src',
            'Ссылка на видео для iframe',
            'https://vk.com/video_ext.php?oid=-206889227&id=456240392&hash=fbe8cad821c65ff9&hd=3',
            'Вставьте только адрес из атрибута src, а не весь HTML-код iframe. Ссылки vkvideo.ru автоматически открываются через совместимый с мобильными устройствами домен vk.com.'
        ),
        baltic_acf_textarea_field('video_description', 'Описание', baltic_design_copy('video_description'), 4),

        [
            'key' => 'field_baltic_tab_author',
            'label' => 'Слово автора',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ],
        baltic_acf_hide_section_field('author'),
        baltic_acf_image_field('author_background', 'Фон блока (компьютер)', 'Рекомендуемая ширина: 1920 px. Если изображение не выбрано, используется встроенный фон.'),
        baltic_acf_image_field('author_background_mobile', 'Фон блока (телефон)', 'Рекомендуемая ширина: 460 px. Если изображение не выбрано, используется встроенный фон.'),
        baltic_acf_text_field('author_title', 'Название секции', 'Слово автора'),
        baltic_acf_text_field('author_all_label', 'Текст ссылки на все записи', 'Все записи'),
        [
            'key' => 'field_baltic_author_admin_hint',
            'label' => 'Публикации автора',
            'name' => '',
            'type' => 'message',
            'message' => 'Добавляйте публикации в разделе «Записи». На главной автоматически показываются до трёх последних опубликованных записей. Поля ниже служат запасным текстом, если записей пока нет.',
        ],
        baltic_acf_text_field('author_heading', 'Заголовок', 'С ДНЁМ ГОРОДА, КАЛИНИНГРАД'),
        baltic_acf_textarea_field('author_text', 'Текст', baltic_design_copy('author_text'), 8),
    ];

    $footer_fields = [
        baltic_acf_image_field('footer_logo', 'Логотип футера'),
        baltic_acf_url_field('footer_vk_url', 'Ссылка VK', 'https://vk.ru/baltikabrew'),
        baltic_acf_text_field('footer_phone', 'Телефон', '8 (800) 700 28 80'),
        baltic_acf_text_field('footer_phone_href', 'Телефон для ссылки', '+78007002880', 'Только цифры и плюс: +78007002880'),
        baltic_acf_text_field('footer_phone_subtitle', 'Подпись телефона', 'Бесплатно по всей России'),
        baltic_acf_text_field('footer_company', 'Юридическое название', 'ООО «ПИВОВАРЕННАЯ КОМПАНИЯ «БАЛТИКА»'),
        baltic_acf_text_field('footer_feedback_label', 'Текст кнопки обратной связи', 'Оставить обращение'),
        [
            'key' => 'field_baltic_feedback_recipient_notice',
            'label' => 'Получатель обращений',
            'name' => '',
            'type' => 'message',
            'message' => 'Заявки из формы отправляются на <strong>bsite.robot@dialogforce.tech</strong>.',
        ],
        baltic_acf_file_field('personal_pdf', 'PDF: персональные данные', 'pdf'),
        baltic_acf_file_field('cookie_pdf', 'PDF: cookie', 'pdf'),
        baltic_acf_textarea_field('cookie_text', 'Текст cookie-баннера', 'Мы используем файлы cookie, чтобы сайт работал лучше.', 3),
        baltic_acf_text_field('cookie_button', 'Кнопка cookie-баннера', 'ПРИНЯТЬ'),
    ];

    acf_add_local_field_group([
        'key' => 'group_baltic_home',
        'title' => 'Редактирование главной страницы',
        'fields' => $fields,
        'location' => [[[
            'param' => 'page_type',
            'operator' => '==',
            'value' => 'front_page',
        ]]],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ]);

    acf_add_local_field_group([
        'key' => 'group_baltic_footer',
        'title' => 'Футер',
        'fields' => $footer_fields,
        'location' => [[[
            'param' => 'options_page',
            'operator' => '==',
            'value' => 'baltic-home',
        ]]],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'active' => true,
    ]);

    baltic_migrate_home_fields($fields);
    baltic_migrate_about_tab_kinds();
}

add_action('admin_enqueue_scripts', static function (string $hook): void {
    $front_page_id = (int) get_option('page_on_front');
    $is_front_page_editor = $front_page_id > 0
        && in_array($hook, ['post.php', 'post-new.php'], true)
        && isset($_GET['post']) && (int) $_GET['post'] === $front_page_id;

    if ($hook !== 'toplevel_page_baltic-home' && !$is_front_page_editor) {
        return;
    }

    wp_add_inline_style('acf-global', '
        .acf-field[data-type="tab"] .acf-tab-wrap { margin-top: 8px; }
        .acf-postbox.seamless > .acf-fields { border: 0; }
        .acf-field-tab .acf-tab-group li a {
            border-radius: 8px 8px 0 0;
            font-weight: 600;
        }
        .acf-field-accordion .acf-accordion-title {
            background: #f7f3ea;
            border-radius: 8px;
        }
        .acf-field-accordion .acf-accordion-title label {
            font-size: 15px;
        }
        .acf-fields > .acf-field {
            padding: 18px 20px;
        }
        .acf-field .description {
            color: #6b6254;
        }
    ');
});

function baltic_acf_products(): array
{
    return [
        [
            'label' => 'Продукт 1: Анти-лагер Кёльш',
            'title' => 'Анти-лагер Кёльш',
            'description' => 'Кёльш - гибридный стиль: светлый эль с солодовым вкусом, фруктовой сладостью и мягкими пшеничными полутонами.',
            'modifier' => 'product1',
            'background' => 'images/product/line/amti-lager-1-bg.webp',
            'bottle' => 'images/product/line/anti-lager-1.webp',
        ],
        [
            'label' => 'Продукт 2: Техно IPA',
            'title' => 'Техно IPA',
            'description' => 'IPA с ярким хвойно-цитрусовым ароматом и выразительной хмелевой горечью.',
            'modifier' => 'product2',
            'background' => 'images/product/line/lgaer-bg.webp',
            'bottle' => 'images/product/line/lgaer-bg-new.webp',
        ],
        [
            'label' => 'Продукт 3: Аэронавт Бланш',
            'title' => 'Аэронавт Бланш',
            'description' => 'Пшеничный напиток с освежающим цитрусово-кориандровым профилем.',
            'modifier' => 'product4',
            'background' => 'images/product/line/aero-bg.webp',
            'bottle' => 'images/product/line/aero-bottle.png',
        ],
        [
            'label' => 'Продукт 4: Заводной Крик',
            'title' => 'Заводной Крик',
            'description' => 'Ягодный ламбик с насыщенным характером и выразительной кислинкой.',
            'modifier' => 'product3',
            'background' => 'images/product/line/kriek-scene.png',
            'bottle' => 'images/product/line/kriek-bottle.png',
        ],
        [
            'label' => 'Продукт 5: Пневмо-сидр',
            'title' => 'Пневмо-сидр<br> Полусухой',
            'description' => baltic_design_copy('cider_description'),
            'modifier' => 'cider',
            'background' => 'images/product/line/cider-scene.png',
            'bottle' => 'images/product/line/cider-bottle.png',
        ],
    ];
}

function baltic_home_products(): array
{
    $front_page_id = (int) get_option('page_on_front');
    $rows = [];

    if (function_exists('get_field') && $front_page_id > 0) {
        $rows = get_field('product_items', $front_page_id) ?: [];
    }

    if (!$rows && function_exists('get_field')) {
        $rows = get_field('product_items', 'option') ?: [];
    }

    if (!$rows) {
        $rows = baltic_acf_products();
    }

    $products = [];
    foreach ($rows as $index => $row) {
        $defaults = baltic_acf_products()[$index % count(baltic_acf_products())];
        $modifier = $row['modifier'] ?? $defaults['modifier'];

        if (!in_array($modifier, ['product1', 'product2', 'product3', 'product4', 'cider'], true)) {
            $modifier = 'product1';
        }

        $products[] = [
            'title' => $row['title'] ?? $defaults['title'],
            'description' => $row['description'] ?? $defaults['description'],
            'alcohol' => $row['alcohol'] ?? '5,3 %',
            'density' => $row['density'] ?? '12,7 %',
            'ibu' => $row['ibu'] ?? '23',
            'modifier' => $modifier,
            'background' => baltic_product_media_url($row['background'] ?? '', $defaults['background']),
            'bottle' => baltic_product_media_url($row['bottle'] ?? '', $defaults['bottle']),
        ];
    }

    return $products;
}

function baltic_acf_about_tabs(): array
{
    $concept_text = 'В нашей пивоваренной лаборатории многолетний опыт соединяется с технологическим прогрессом и щепоткой пивоваренного волшебства. При помощи магических машин мы довели рецептурные формулы до совершенства. Так, в союзе мастерства и волшебства рождается вкус Балтики Brew.' . "\n" . 'Это магия пива. Это Балтика Brew.';

    return [
        [
            'label' => 'Концепция',
            'kind' => 'concept',
            'heading' => 'Концепция пивоварни',
            'text' => $concept_text,
            'initially_active' => true,
            'active_timeline_item' => 1,
            'timeline_items' => [],
        ],
        [
            'label' => 'История',
            'kind' => 'history',
            'heading' => baltic_design_copy('history_heading'),
            'text' => baltic_design_copy('history_text'),
            'initially_active' => false,
            'active_timeline_item' => 4,
            'timeline_items' => array_map(static function (array $date): array {
                return [
                    'year' => $date[0],
                    'month' => $date[1],
                    'heading' => baltic_design_copy('history_heading'),
                    'text' => baltic_design_copy('history_text'),
                ];
            }, [['2010', 'декабрь'], ['2011', 'май'], ['2011', 'июнь'], ['2011', 'декабрь'], ['2012', 'апрель'], ['2012', 'август']]),
        ],
        [
            'label' => 'Пивовары',
            'kind' => 'brewers',
            'heading' => 'Пивовары',
            'text' => 'Расскажите о команде пивоваров, их опыте, подходе к рецептурам и авторском взгляде на линейку Балтики Brew.',
            'initially_active' => false,
            'active_timeline_item' => 1,
            'timeline_items' => [],
        ],
        [
            'label' => 'Пивоварни',
            'kind' => 'breweries',
            'heading' => 'Наши пивоварни',
            'text' => 'Добавьте описание пивоварен, производственных площадок и особенностей технологического процесса.',
            'initially_active' => false,
            'active_timeline_item' => 1,
            'timeline_items' => [],
        ],
    ];
}

function baltic_home_about_tabs(): array
{
    $front_page_id = (int) get_option('page_on_front');
    $rows = null;

    if (function_exists('get_field') && $front_page_id > 0 && metadata_exists('post', $front_page_id, 'about_tabs')) {
        $rows = get_field('about_tabs', $front_page_id) ?: [];
    }

    if ($rows === null && function_exists('get_field') && get_option('options_about_tabs', null) !== null) {
        $rows = get_field('about_tabs', 'option') ?: [];
    }

    if ($rows === null) {
        $rows = baltic_acf_about_tabs();
    }

    $tabs = [];
    $label_aliases = [
        'Наши пивовары' => 'Пивовары',
        'Наши пивоварни' => 'Пивоварни',
    ];

    foreach ($rows as $row) {
        if (!is_array($row) || !empty($row['hidden'])) {
            continue;
        }

        $label = trim((string) ($row['label'] ?? ''));
        $label = $label_aliases[$label] ?? $label;
        $kind_from_label = [
            'Концепция' => 'concept',
            'История' => 'history',
            'Пивовары' => 'brewers',
            'Пивоварни' => 'breweries',
        ][$label] ?? 'custom';
        $kind = (string) ($row['kind'] ?? '');
        if (
            !in_array($kind, ['concept', 'history', 'brewers', 'breweries', 'custom'], true)
            || ($kind === 'custom' && $kind_from_label !== 'custom')
        ) {
            $kind = $kind_from_label;
        }

        $events = [];
        $event_rows = $kind === 'history' ? ($row['timeline_items'] ?? []) : [];

        foreach ((array) $event_rows as $event) {
            if (!is_array($event)) {
                continue;
            }
            $events[] = [
                'year' => (string) ($event['year'] ?? ''),
                'month' => (string) ($event['month'] ?? ''),
                'heading' => (string) ($event['heading'] ?? ''),
                'text' => (string) ($event['text'] ?? ''),
            ];
        }

        $active_timeline_item = max(1, (int) ($row['active_timeline_item'] ?? 1));
        if ($events) {
            $active_timeline_item = min($active_timeline_item, count($events));
        }

        $tabs[] = [
            'label' => $label,
            'kind' => $kind,
            'heading' => (string) ($row['heading'] ?? ''),
            'text' => (string) ($row['text'] ?? ''),
            'initially_active' => !empty($row['initially_active']),
            'active_timeline_item' => $active_timeline_item,
            'timeline_items' => $events,
        ];
    }

    $tab_order = ['concept' => 0, 'history' => 1, 'brewers' => 2, 'breweries' => 3, 'custom' => 4];
    usort($tabs, static function (array $left, array $right) use ($tab_order): int {
        return ($tab_order[$left['kind']] ?? 4) <=> ($tab_order[$right['kind']] ?? 4);
    });

    $active_tab_index = array_search(true, array_column($tabs, 'initially_active'), true);
    $active_tab_index = $active_tab_index === false ? 0 : $active_tab_index;

    foreach ($tabs as $tab_index => &$tab) {
        $tab['initially_active'] = $tab_index === $active_tab_index;
    }
    unset($tab);

    return $tabs;
}

function baltic_product_media_url($value, string $fallback_path): string
{
    if (is_array($value)) {
        $value = $value['url'] ?? '';
    }

    if ($value) {
        return esc_url((string) $value);
    }

    return esc_url(baltic_asset($fallback_path));
}

function baltic_product_default(int $number, string $key): string
{
    $products = baltic_acf_products();
    $index = $number - 1;

    if (!isset($products[$index][$key])) {
        return '';
    }

    return (string) $products[$index][$key];
}

/**
 * Copy fields from the old options screen to the actual front page once.
 * Existing page values, including intentionally empty values, always win.
 */
function baltic_migrate_home_fields(array $fields): void
{
    $front_page_id = (int) get_option('page_on_front');
    if ($front_page_id <= 0 || get_option('baltic_front_page_acf_migrated_' . $front_page_id)) {
        return;
    }

    foreach ($fields as $field) {
        $name = $field['name'] ?? '';
        if ($name === '' || metadata_exists('post', $front_page_id, $name)) {
            continue;
        }

        if (get_option('options_' . $name, null) !== null) {
            update_field($field['key'], get_field($field['key'], 'option', false), $front_page_id);
        } elseif ($name === 'product_items') {
            update_field($field['key'], baltic_default_product_rows(), $front_page_id);
        } elseif ($name === 'about_tabs') {
            update_field($field['key'], baltic_acf_about_tabs(), $front_page_id);
        }
    }

    update_option('baltic_front_page_acf_migrated_' . $front_page_id, 1, false);
}

/**
 * Add stable behavior types to About rows created before the field existed.
 */
function baltic_migrate_about_tab_kinds(): void
{
    $front_page_id = (int) get_option('page_on_front');
    $migration_key = 'baltic_about_tab_kinds_migrated_1_' . $front_page_id;

    if ($front_page_id <= 0 || get_option($migration_key)) {
        return;
    }

    $rows = get_field('about_tabs', $front_page_id);
    if (!is_array($rows) || !$rows) {
        return;
    }

    $kind_by_label = [
        'Концепция' => 'concept',
        'История' => 'history',
        'Пивовары' => 'brewers',
        'Наши пивовары' => 'brewers',
        'Пивоварни' => 'breweries',
        'Наши пивоварни' => 'breweries',
    ];
    $valid_kinds = ['concept', 'history', 'brewers', 'breweries', 'custom'];
    $changed = false;

    foreach ($rows as $row_index => &$row) {
        if (!is_array($row)) {
            continue;
        }

        $kind = (string) ($row['kind'] ?? '');
        $kind_was_saved = metadata_exists('post', $front_page_id, 'about_tabs_' . $row_index . '_kind');
        if ($kind_was_saved && in_array($kind, $valid_kinds, true)) {
            continue;
        }

        $label = trim((string) ($row['label'] ?? ''));
        $row['kind'] = $kind_by_label[$label] ?? 'custom';
        $changed = true;
    }
    unset($row);

    if ($changed) {
        update_field('field_baltic_about_tabs', $rows, $front_page_id);
    }

    update_option($migration_key, 1, false);
}

function baltic_default_product_rows(): array
{
    return array_map(static function (array $product): array {
        return [
            'title' => $product['title'],
            'description' => $product['description'],
            'alcohol' => '5,3 %',
            'density' => '12,7 %',
            'ibu' => '23',
            'modifier' => $product['modifier'],
            'background' => '',
            'bottle' => '',
        ];
    }, baltic_acf_products());
}

function baltic_acf_hide_section_field(string $section): array
{
    return [
        'key' => 'field_baltic_hide_' . $section,
        'label' => 'Скрыть с главной страницы',
        'name' => 'hide_' . $section,
        'type' => 'true_false',
        'message' => 'Не показывать эту секцию на главной',
        'default_value' => 0,
    ];
}

function baltic_acf_text_field(string $name, string $label, string $default = '', string $instructions = ''): array
{
    return [
        'key' => 'field_baltic_' . $name,
        'label' => $label,
        'name' => $name,
        'type' => 'text',
        'instructions' => $instructions,
        'default_value' => $default,
        'wrapper' => ['width' => '50'],
    ];
}

function baltic_acf_textarea_field(string $name, string $label, string $default = '', int $rows = 4): array
{
    return [
        'key' => 'field_baltic_' . $name,
        'label' => $label,
        'name' => $name,
        'type' => 'textarea',
        'default_value' => $default,
        'rows' => $rows,
        'new_lines' => 'br',
    ];
}

function baltic_acf_url_field(string $name, string $label, string $default = '', string $instructions = ''): array
{
    return [
        'key' => 'field_baltic_' . $name,
        'label' => $label,
        'name' => $name,
        'type' => 'url',
        'instructions' => $instructions,
        'default_value' => $default,
        'wrapper' => ['width' => '50'],
    ];
}

function baltic_acf_image_field(string $name, string $label, string $instructions = ''): array
{
    return [
        'key' => 'field_baltic_' . $name,
        'label' => $label,
        'name' => $name,
        'type' => 'image',
        'instructions' => $instructions,
        'return_format' => 'url',
        'preview_size' => 'medium',
        'library' => 'all',
        'wrapper' => ['width' => '50'],
    ];
}

function baltic_acf_file_field(string $name, string $label, string $mime_types = ''): array
{
    return [
        'key' => 'field_baltic_' . $name,
        'label' => $label,
        'name' => $name,
        'type' => 'file',
        'mime_types' => $mime_types,
        'return_format' => 'url',
        'library' => 'all',
        'wrapper' => ['width' => '50'],
    ];
}
