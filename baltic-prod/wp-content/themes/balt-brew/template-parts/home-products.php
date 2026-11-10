<?php
/**
 * Home product carousel.
 */

if (!defined('ABSPATH')) {
    exit;
}

$products = baltic_home_products();
$count = count($products);

if ($count === 0) {
    return;
}
?>
<section class="section product" role="region" aria-roledescription="карусель" aria-label="Ассортимент Балтика Brew" id="assortment" data-product-carousel data-slide="0">
    <div class="product__stage">
        <div class="product__art">
            <div class="product__scene">
                <div class="product__track" aria-hidden="true">
                    <?php foreach ($products as $index => $product) : ?>
                        <div class="product__background product__background--<?php echo esc_attr($product['modifier']); ?>">
                            <img src="<?php echo esc_url($product['background']); ?>" alt="" draggable="false">
                        </div>

                        <?php if ($index < $count - 1) : ?>
                            <div class="product__bridge">
                                <img src="<?php echo esc_url(baltic_asset('images/product/bridge.png')); ?>" alt="" draggable="false">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($products as $index => $product) : ?>
                    <div class="product__bottle product__bottle--<?php echo esc_attr($product['modifier']); ?>" aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                        <img src="<?php echo esc_url($product['bottle']); ?>" alt="<?php echo esc_attr(wp_strip_all_tags($product['title'])); ?>" draggable="false">
                    </div>
                <?php endforeach; ?>

                <div class="product__controls">
                    <button class="product__arrow product__arrow--prev" type="button" data-prev aria-label="Предыдущий напиток">
                        <img src="<?php echo esc_url(baltic_asset('images/left-arrow.png')); ?>" alt="" draggable="false">
                    </button>
                    <button class="product__arrow product__arrow--next" type="button" data-next aria-label="Следующий напиток">
                        <img src="<?php echo esc_url(baltic_asset('images/right-arrow.png')); ?>" alt="" draggable="false">
                    </button>
                </div>
            </div>
        </div>

        <div class="product__copy">
            <?php foreach ($products as $index => $product) : ?>
                <article class="product__slide product__slide--<?php echo esc_attr($product['modifier']); ?> product__info" role="group" aria-roledescription="слайд" aria-label="<?php echo esc_attr(($index + 1) . ' из ' . $count); ?>" aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>"<?php echo $index === 0 ? '' : ' inert'; ?>>
                    <h2 class="product__title"><?php echo wp_kses_post($product['title']); ?></h2>
                    <p class="product__description"><?php echo wp_kses_post(nl2br((string) $product['description'])); ?></p>
                    <div class="product__stats">
                        <div class="product__stat">
                            <span class="product__stat-value"><?php echo esc_html((string) $product['alcohol']); ?></span>
                            <span class="product__stat-label">Алкоголь</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value"><?php echo esc_html((string) $product['density']); ?></span>
                            <span class="product__stat-label">Плотность</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value"><?php echo esc_html((string) $product['ibu']); ?></span>
                            <span class="product__stat-label">IBU</span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <p class="product__sr-only" data-status role="status" aria-live="polite" aria-atomic="true"></p>
</section>
