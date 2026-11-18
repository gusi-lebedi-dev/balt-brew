<?php
/**
 * WordPress product panorama.
 */

if (!defined('ABSPATH')) {
    exit;
}

$products = baltic_home_products();
$count = count($products);

if (!$count) {
    return;
}

$art_url = static function (string $name): string {
    return baltic_asset(baltic_product_panorama_asset($name));
};
?>
<section class="section product-panorama" role="region" aria-roledescription="карусель" aria-label="Ассортимент Балтика Brew" id="<?php echo esc_attr(baltic_nav_home_anchor('products')); ?>" data-product-panorama data-slide="0">
    <div class="product-panorama__art">
        <div class="product-panorama__track" data-panorama-track aria-hidden="true">
            <?php foreach ($products as $index => $product) : ?>
                <?php
                $scene = $product['scene'];
                [$background_left, $background_width, $background_height] = $scene['background'];
                [$drink_width, $drink_height, $drink_top] = $scene['drink'];
                $frame_style = sprintf(
                    '--advance:%s;--background-left:%s;--background-width:%s;--background-height:%s;--drink-width:%s;--drink-height:%s;--drink-top:%s;--hall-left:%s',
                    $scene['advance'],
                    $background_left,
                    $background_width,
                    $background_height,
                    $drink_width,
                    $drink_height,
                    $drink_top,
                    $scene['hall']
                );
                $frame_style .= ';--background-top:' . ($scene['background_top'] ?? 0);
                if ($index === $count - 1) {
                    $first_background_left = $products[0]['scene']['background'][0];
                    $frame_style .= ';--hall-end:' . ($scene['advance'] + $first_background_left - $scene['hall']);
                }
                $loading = $index === 0 ? 'eager' : 'lazy';
                ?>
                <div class="product-panorama__frame" data-panorama-frame data-advance="<?php echo esc_attr($scene['advance']); ?>" style="<?php echo esc_attr($frame_style); ?>">
                    <div class="product-panorama__background">
                        <img<?php echo empty($scene['flip']) ? '' : ' class="is-mirrored"'; ?> src="<?php echo esc_url($product['background']); ?>" alt="" loading="<?php echo esc_attr($loading); ?>" decoding="async" draggable="false">
                    </div>
                    <?php if ($count > 1) : ?>
                        <div class="product-panorama__hall<?php echo $index === $count - 1 ? ' product-panorama__hall--terminal' : ''; ?>">
                            <img class="product-panorama__hall-art" src="<?php echo esc_url($art_url('hall')); ?>" alt="" decoding="async" draggable="false">
                            <img class="product-panorama__column product-panorama__column--left" src="<?php echo esc_url($art_url('column')); ?>" alt="" decoding="async" draggable="false">
                            <img class="product-panorama__column product-panorama__column--right" src="<?php echo esc_url($art_url('column')); ?>" alt="" decoding="async" draggable="false">
                        </div>
                    <?php endif; ?>
                    <?php if (isset($scene['reflection'])) : ?>
                        <?php
                        [$reflection_top, $opacity, $blend] = $scene['reflection'];
                        $reflection_style = sprintf('--reflection-top:%s;--reflection-opacity:%s;--reflection-blend:%s', $reflection_top, $opacity, $blend);
                        ?>
                        <div class="product-panorama__reflection" style="<?php echo esc_attr($reflection_style); ?>">
                            <img src="<?php echo esc_url($product['bottle']); ?>" alt="" loading="<?php echo esc_attr($loading); ?>" decoding="async" draggable="false">
                        </div>
                    <?php endif; ?>
                    <div class="product-panorama__drink<?php echo empty($scene['contact_shadow']) ? '' : ' product-panorama__drink--grounded'; ?>">
                        <img src="<?php echo esc_url($product['bottle']); ?>" alt="" loading="<?php echo esc_attr($loading); ?>" decoding="async" draggable="false">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($count > 1) : ?>
            <div class="product-panorama__controls">
                <button class="product-panorama__button product-panorama__button--prev" type="button" data-prev aria-label="Предыдущий напиток">
                    <img src="<?php echo esc_url($art_url('arrow')); ?>" alt="" draggable="false">
                </button>
                <button class="product-panorama__button" type="button" data-next aria-label="Следующий напиток">
                    <img src="<?php echo esc_url($art_url('arrow')); ?>" alt="" draggable="false">
                </button>
            </div>
        <?php endif; ?>
    </div>

    <div class="product-panorama__cards">
        <?php foreach ($products as $index => $product) : ?>
            <?php
            $title = (string) $product['title'];
            $description = (string) $product['description'];
            $stats = array_filter([
                'Алкоголь' => $product['alcohol'],
                'Плотность' => $product['density'],
                'IBU' => $product['ibu'],
            ], static function ($value): bool {
                return trim(wp_strip_all_tags((string) $value)) !== '';
            });
            $scene = $product['scene'];
            ?>
            <article class="product-panorama__card<?php echo empty($scene['border']) ? '' : ' product-panorama__card--outlined'; ?>" style="<?php echo esc_attr('--card-left:' . $scene['card']); ?>" role="group" aria-roledescription="слайд" aria-label="<?php echo esc_attr($scene['label'] . ', ' . ($index + 1) . ' из ' . $count); ?>" aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>"<?php echo $index === 0 ? '' : ' inert'; ?>>
                <?php if (trim(wp_strip_all_tags($title)) !== '') : ?>
                    <h2 class="product-panorama__title<?php echo preg_match('/.{25}/u', wp_strip_all_tags($title)) ? ' product-panorama__title--compact' : ''; ?>"><?php echo wp_kses_post($title); ?></h2>
                <?php endif; ?>
                <?php if (trim(wp_strip_all_tags($description)) !== '') : ?>
                    <p class="product-panorama__description"><?php echo wp_kses_post(nl2br($description)); ?></p>
                <?php endif; ?>
                <?php if ($stats) : ?>
                    <dl class="product-panorama__stats">
                        <?php foreach ($stats as $label => $value) : ?>
                            <div class="product-panorama__stat">
                                <dt><?php echo esc_html($label); ?></dt>
                                <dd><?php echo esc_html($value); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
    <p class="product-panorama__status" data-status role="status" aria-live="polite" aria-atomic="true"></p>
</section>
