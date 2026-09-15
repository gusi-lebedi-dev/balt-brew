<?php
/** Front page template. */
get_header();
?>
    <!-- ================= PRODUCT ================= -->
    <section class="section product" role="region" aria-roledescription="карусель" aria-label="Ассортимент Балтика Brew" id="assortment" data-product-carousel data-slide="0">
        <div class="product__stage">
            <div class="product__art">
                <div class="product__scene">
                    <!-- Фоны следуют тому же порядку, что бутылки и описания. -->
                    <div class="product__track" aria-hidden="true">
                        <!-- Анти-лагер Кёльш -->
                        <div class="product__background product__background--product1">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/line/amti-lager-1-bg.webp') ); ?>" alt="" draggable="false">
                        </div>

                        <div class="product__bridge">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/bridge.png') ); ?>" alt="" draggable="false">
                        </div>

                        <!-- Техно IPA -->
                        <div class="product__background product__background--product2">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/line/lgaer-bg.webp') ); ?>" alt="" draggable="false">
                        </div>

                        <div class="product__bridge">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/bridge.png') ); ?>" alt="" draggable="false">
                        </div>

                        <!-- Аэронавт Бланш -->
                        <div class="product__background product__background--product4">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/line/aero-bg.webp') ); ?>" alt="" draggable="false">
                        </div>

                        <div class="product__bridge">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/bridge.png') ); ?>" alt="" draggable="false">
                        </div>

                        <!-- Заводной Крик -->
                        <div class="product__background product__background--product3">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/line/kriek-scene.png') ); ?>" alt="" draggable="false">
                        </div>

                        <div class="product__bridge">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/bridge.png') ); ?>" alt="" draggable="false">
                        </div>

                        <!-- Пневмо-сидр полусухой -->
                        <div class="product__background product__background--cider">
                            <img src="<?php echo esc_url( balt_brew_asset('images/product/line/cider-scene.png') ); ?>" alt="" draggable="false">
                        </div>
                    </div>

                    <!-- Бутылки анимируются отдельным слоем. -->
                    <div class="product__bottle product__bottle--product1" aria-hidden="false">
                        <img src="<?php echo esc_url( balt_brew_asset('images/product/line/anti-lager-1.png') ); ?>" alt="Анти-лагер Кёльш" draggable="false">
                    </div>
                    <div class="product__bottle product__bottle--product2" aria-hidden="true">
                        <img src="<?php echo esc_url( balt_brew_asset('images/product/line/techno-ipa-bottle.png') ); ?>" alt="Техно IPA" draggable="false">
                    </div>
                    <div class="product__bottle product__bottle--product4" aria-hidden="true">
                        <img src="<?php echo esc_url( balt_brew_asset('images/product/line/aero-bottle.png') ); ?>" alt="Аэронавт Бланш" draggable="false">
                    </div>
                    <div class="product__bottle product__bottle--product3" aria-hidden="true">
                        <img src="<?php echo esc_url( balt_brew_asset('images/product/line/kriek-bottle.png') ); ?>" alt="Заводной Крик" draggable="false">
                    </div>
                    <div class="product__bottle product__bottle--cider" aria-hidden="true">
                        <img src="<?php echo esc_url( balt_brew_asset('images/product/line/cider-bottle.png') ); ?>" alt="Пневмо-сидр полусухой" draggable="false">
                    </div>

                    <!-- CONTROLS -->
                    <div class="product__controls">
                        <button class="product__arrow product__arrow--prev" type="button" data-prev aria-label="Предыдущий напиток">
                            <img src="<?php echo esc_url( balt_brew_asset('images/left-arrow.png') ); ?>" alt="" draggable="false">
                        </button>
                        <button class="product__arrow product__arrow--next" type="button" data-next aria-label="Следующий напиток">
                            <img src="<?php echo esc_url( balt_brew_asset('images/right-arrow.png') ); ?>" alt="" draggable="false">
                        </button>
                    </div>
                </div>
            </div>

            <!-- COPY - текстовый контент (вне stage!) -->
            <div class="product__copy">
                <article class="product__slide product__slide--product1 product__info" role="group" aria-roledescription="слайд" aria-label="1 из 5" aria-hidden="false">
                    <h2 class="product__title">Анти-лагер Кёльш</h2>
                    <p class="product__description">
                        Кёльш – гибридный стиль: светлый эль с солодовым вкусом, оттенками фруктовой сладости и мягкими пшеничными полутонами, стремящийся быть похожим на лагер, оставаясь при этом элем. Одним словом: Анти-лагер.
                    </p>
                    <div class="product__stats">
                        <div class="product__stat">
                            <span class="product__stat-value">5,3 %</span>
                            <span class="product__stat-label">Алкоголь</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">12,7 %</span>
                            <span class="product__stat-label">Плотность</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">23</span>
                            <span class="product__stat-label">IBU</span>
                        </div>
                    </div>
                </article>

                <article class="product__slide product__slide--product2 product__info" role="group" aria-roledescription="слайд" aria-label="2 из 5" aria-hidden="true" inert>
                    <h2 class="product__title">Техно IPA</h2>
                    <p class="product__description">
                        В этом IPA мы соединили индустриальные традиции викторианской эпохи с триумфом современных хмелевых технологий. Хмелевая горечь ощущается легкими электрическими покалываниями на языке. Яркий хвойно-цитрусовый аромат словно разряд пробуждает ваши рецепторы.
                    </p>
                    <div class="product__stats">
                        <div class="product__stat">
                            <span class="product__stat-value">5,3 %</span>
                            <span class="product__stat-label">Алкоголь</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">12,7 %</span>
                            <span class="product__stat-label">Плотность</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">23</span>
                            <span class="product__stat-label">IBU</span>
                        </div>
                    </div>
                </article>

                <article class="product__slide product__slide--product4 product__info" role="group" aria-roledescription="слайд" aria-label="3 из 5" aria-hidden="true" inert>
                    <h2 class="product__title">Аэронавт Бланш</h2>
                    <p class="product__description">
                        Попробуйте изысканный пшеничный напиток, доведённый до совершенства в нашей лаборатории. Мы взяли за основу проверенный временем подход к стилю «Бланш» и вывернули тумблер на полную мощность.Пышный, как облако, и освежающий, как дуновение цитрусово‑кориандрового вихря.
                    </p>
                    <div class="product__stats">
                        <div class="product__stat">
                            <span class="product__stat-value">5,3 %</span>
                            <span class="product__stat-label">Алкоголь</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">12,7 %</span>
                            <span class="product__stat-label">Плотность</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">23</span>
                            <span class="product__stat-label">IBU</span>
                        </div>
                    </div>
                </article>

                <article class="product__slide product__slide--product3 product__info" role="group" aria-roledescription="слайд" aria-label="4 из 5" aria-hidden="true" inert>
                    <h2 class="product__title">Заводной Крик</h2>
                    <p class="product__description">
                        Настоящая магия начинается, когда мы добавляем в наш ламбик натуральный сок вишни. Кислые, сладкие и терпкие ноты этого рубинового эля идеально гармонизируют друга друга, образуя эталонную формулу стиля. Никакого сахара — лишь щепотка волшебства.
                    </p>
                    <div class="product__stats">
                        <div class="product__stat">
                            <span class="product__stat-value">5,3 %</span>
                            <span class="product__stat-label">Алкоголь</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">12,7 %</span>
                            <span class="product__stat-label">Плотность</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">23</span>
                            <span class="product__stat-label">IBU</span>
                        </div>
                    </div>
                </article>

                <article class="product__slide product__slide--cider product__info" role="group" aria-roledescription="слайд" aria-label="5 из 5" aria-hidden="true" inert>
                    <h2 class="product__title">Пневмо-сидр<br> Полусухой</h2>
                    <p class="product__description">
                        Внутри наших медных варочных чанов яблочный сок, добытый механическими усилиями пневомашин, превращается в искристый полусухой сидр. Баланс фруктовой сладости и освежающей приятной кислинки в нём выверен с инженерной точностью. И никакого сахара – лишь щепотка волшебства.
                    </p>
                    <div class="product__stats">
                        <div class="product__stat">
                            <span class="product__stat-value">5,3 %</span>
                            <span class="product__stat-label">Алкоголь</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">12,7 %</span>
                            <span class="product__stat-label">Плотность</span>
                        </div>
                        <div class="product__stat">
                            <span class="product__stat-value">23</span>
                            <span class="product__stat-label">IBU</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>

        <p class="product__sr-only" data-status role="status" aria-live="polite" aria-atomic="true"></p>
    </section>

    <!-- ================= ABOUT / HISTORY ================= -->
    <section class="section about" id="about">
<div class="about_wrapper">
        <h2 class="about__title gold-title">О нас</h2>
        <!-- <div class="about_button_container">
        <button type="button" class="about__card about__card--single" data-tab="history" data-pos="center">
            <p class="about__card-label">История</p>
        </button>
        </div> -->
        <div class="about__content">
            <!-- <div class="about__panel" data-panel="breweries">
                <div class="about__brewers">
                    <button type="button" class="about__brewers-arrow about__brewers-arrow--prev" aria-label="Предыдущий пивовар">
                        <img src="<?php echo esc_url( balt_brew_asset('images/left-arrow.png') ); ?>" alt="">
                    </button>

                    <div class="about__brewers-slides">
                        <div class="about__brewers-slide about__brewers-slide--active" data-brewer-slide="1">
                            <img src="<?php echo esc_url( balt_brew_asset('images/illustration.png') ); ?>" alt="Наши пивовары" class="about__brewers-image">
                            <h3 class="about__content-title">Наши пивовары</h3>
                            <p class="about__content-text">
                                Здесь будет рассказ о людях, которые варят наше пиво — их подход, философия
                                и то, что делает каждую партию особенной.
                            </p>
                        </div>
                        <div class="about__brewers-slide" data-brewer-slide="2">
                            <img src="<?php echo esc_url( balt_brew_asset('images/illustration.png') ); ?>" alt="Наши пивовары" class="about__brewers-image">
                            <h3 class="about__content-title">Наши пивовары</h3>
                            <p class="about__content-text">
                                Здесь будет рассказ о втором пивоваре — его опыт, награды
                                и вклад в развитие пивоварни.
                            </p>
                        </div>
                    </div>

                    <button type="button" class="about__brewers-arrow about__brewers-arrow--next" aria-label="Следующий пивовар">
                        <img src="<?php echo esc_url( balt_brew_asset('images/right-arrow.png') ); ?>" alt="">
                    </button>
                </div>
            </div> -->
            <div class="about__panel about__panel--active" data-panel="history">
                <h3 class="about__content-title">О нас</h3>
                <div class="about__history-content">
                    <p class="about__content-text">
                        В нашей пивоваренной лаборатории многолетний опыт соединяется с технологическим прогрессом и щепоткой пивоваренного волшебства. При помощи магических машин мы довели рецептурные формулы до совершенства. Так, в союзе мастерства и волшебства рождается вкус Балтики Brew.
                    </p>
                    <p class="about__content-text">
                        Это магия пива.  Это Балтика Brew
                    </p>
                </div>
            </div>
            <!-- <div class="about__panel" data-panel="breweries-list">
                <h3 class="about__content-title">Наши пивоварни</h3>
                <p class="about__content-text">
                    Здесь будет список и описание пивоварен, где создаётся наш продукт — их география
                    и особенности производства.
                </p>
            </div> -->
        </div>

        <div class="timeline timeline--visible">
            <img src="<?php echo esc_url( balt_brew_asset('images/about/timeline-progress.png') ); ?>" alt="" class="timeline__track">

            <button type="button" class="timeline__item timeline__item--dec2010" data-year="dec2010">
                <img src="<?php echo esc_url( balt_brew_asset('images/about/timeline-dot.png') ); ?>" alt="" class="timeline__dot">
                <span class="timeline__date">декабрь 2010</span>
            </button>
            <button type="button" class="timeline__item timeline__item--may2011" data-year="may2011">
                <img src="<?php echo esc_url( balt_brew_asset('images/about/timeline-dot.png') ); ?>" alt="" class="timeline__dot">
                <span class="timeline__date">май 2011</span>
            </button>
            <button type="button" class="timeline__item timeline__item--jun2011" data-year="jun2011">
                <img src="<?php echo esc_url( balt_brew_asset('images/about/timeline-dot.png') ); ?>" alt="" class="timeline__dot">
                <span class="timeline__date">июнь 2011</span>
            </button>
            <button type="button" class="timeline__item timeline__item--dec2011 timeline__item--active" data-year="dec2011">
                <img src="<?php echo esc_url( balt_brew_asset('images/about/timeline-dot-active.png') ); ?>" alt="" class="timeline__dot">
                <span class="timeline__date">декабрь 2011</span>
            </button>
            <button type="button" class="timeline__item timeline__item--apr2012" data-year="apr2012">
                <img src="<?php echo esc_url( balt_brew_asset('images/about/timeline-dot.png') ); ?>" alt="" class="timeline__dot">
                <span class="timeline__date">апрель 2012</span>
            </button>
            <button type="button" class="timeline__item timeline__item--aug2012" data-year="aug2012">
                <img src="<?php echo esc_url( balt_brew_asset('images/about/timeline-dot.png') ); ?>" alt="" class="timeline__dot">
                <span class="timeline__date">август 2012</span>
            </button>
        </div>
</div>

    </section>

    <!-- ================= NEWS ================= -->
    <section class="section news" id="news">
        <div class="container">
            <h2 class="news__title gold-title">новости</h2>
            <p class="news__date">22.08.2026</p>
            <h3 class="news__headline">С ДНЁМ ГОРОДА, КАЛИНИНГРАД</h3>
            <p class="news__text">
Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален!
Для нас большая честь представлять и прославлять Калининград и Калининградску..Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску....Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску..Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску....
            </p>
            <a href="<?php echo esc_url( balt_brew_news_url() ); ?>" class="news__more">подробнее</a>
        </div>
    </section>

    <!-- ================= VIDEO ================= -->
    <section class="section video">
        <img src="<?php echo esc_url( balt_brew_asset('images/video/bg.png') ); ?>" alt="" class="video__bg">

        <div class="container">
            <h3 class="video__title">Название видео</h3>

            <div class="video__frame">
                <iframe title="Видео Балтика Brew" loading="lazy" src="https://vkvideo.ru/video_ext.php?oid=-206889227&id=456240392&hash=fbe8cad821c65ff9&hd=3" width="1280" height="720" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" frameborder="0" allowfullscreen></iframe>
            </div>

            <p class="video__description">
                Возможно короткое описание<br>
                Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален!
                Для нас большая честь представлять и прославлять Калининград и Калининградскую область.
            </p>
        </div>
    </section>

    <!-- ================= AUTHOR ================= -->
    <section class="section author" id="author">
        <img src="<?php echo esc_url( balt_brew_asset('images/author/bg.png') ); ?>" alt="" class="author__bg">

        <h2 class="author__title gold-title">Слово автора</h2>

        <div class="container">
            <h3 class="author__heading">С ДНЁМ ГОРОДА, КАЛИНИНГРАД</h3>
            <p class="author__text">
каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален!каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален!каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален!каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградску...Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален!
            </p>
        </div>
    </section>


<?php get_footer(); ?>
