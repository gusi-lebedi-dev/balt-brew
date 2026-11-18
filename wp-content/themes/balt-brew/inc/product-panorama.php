<?php
/**
 * Scene geometry from Figma Frame 193, in design pixels at a height of 952.
 * Horizontal coordinates are relative to the centre of each drink.
 */

if (!defined('ABSPATH')) {
    exit;
}

function baltic_product_panorama_scenes(): array
{
    return [
        'product1' => [
            'asset' => 'kolsch', 'label' => 'Анти-лагер Кёльш',
            'background' => [-885.5, 1778, 1005], 'drink' => [275, 807, 107],
            'card' => -761.5, 'hall' => 791.5, 'advance' => 2622,
            'reflection' => [886, .5, 'normal'],
        ],
        'kolsch_zero' => [
            'asset' => 'kolsch-zero', 'label' => 'Анти-лагер безалкогольный',
            'background' => [-1014.5, 1916, 1083], 'background_top' => -65,
            'drink' => [275, 807, 107],
            'card' => -761.5, 'hall' => 791.5, 'advance' => 2622,
            'reflection' => [912, .5, 'normal'],
        ],
        'product2' => [
            'asset' => 'ipa', 'label' => 'Техно IPA',
            'background' => [-838.5, 1684, 952], 'drink' => [275, 806, 107],
            'card' => -761.5, 'hall' => 719.5, 'advance' => 2577,
            'reflection' => [878, .5, 'normal'],
        ],
        'product4' => [
            'asset' => 'blanche', 'label' => 'Аэронавт Бланш',
            'background' => [-862.5, 1711.832, 968], 'drink' => [275, 787, 120],
            'card' => -772.5, 'hall' => 793.5, 'advance' => 2618, 'flip' => true,
        ],
        'product3' => [
            'asset' => 'kriek', 'label' => 'Заводной Крик',
            'background' => [-838.5, 1684, 952], 'drink' => [274.757, 782, 120],
            'card' => -761.5, 'hall' => 835.5, 'advance' => 2701,
            'reflection' => [866, .4, 'normal'], 'contact_shadow' => true,
        ],
        'cider' => [
            'asset' => 'cider', 'label' => 'Пневмо-сидр',
            'background' => [-862.5, 1711.832, 968], 'drink' => [275, 795, 112],
            'card' => -772.5, 'hall' => 836.5, 'advance' => 2691,
            'reflection' => [890, .5, 'normal'],
        ],
        'blond' => [
            'asset' => 'blond', 'label' => 'Латунный Блонд',
            'background' => [-902.5, 1774, 1002], 'drink' => [275, 802, 111],
            'card' => -804.5, 'hall' => 790.5, 'advance' => 2609, 'border' => true,
        ],
        'bruin' => [
            'asset' => 'bruin', 'label' => 'Медный Брюн',
            'background' => [-827.5, 1778, 1005], 'drink' => [275, 787, 119],
            'card' => -761.5, 'hall' => 829.5, 'advance' => 2633.573, 'flip' => true,
            'reflection' => [885, .5, 'overlay'],
        ],
        'stout' => [
            'asset' => 'stout', 'label' => 'Стаут',
            'background' => [-833.073, 1684, 952], 'drink' => [319.146, 709, 177],
            'card' => -756.073, 'hall' => 859.927, 'advance' => 2777.426,
            'reflection' => [858, .7, 'hard-light'],
        ],
        'helles' => [
            'asset' => 'helles', 'label' => 'Радио-Хеллес',
            'background' => [-965.499, 1764, 996], 'drink' => [318.718, 703, 174],
            'card' => -798.499, 'hall' => 798.501, 'advance' => 2650, 'border' => true,
        ],
    ];
}

function baltic_product_panorama_asset(string $name): string
{
    return 'images/product/panorama/' . $name . '.webp';
}
