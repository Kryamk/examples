<div class="page page-vacancy page--white">

    <div class="container">
        <div class="breadcrumbs">
            <ul itemscope="" itemtype="http://schema.org/BreadcrumbList">
                <?php bcn_display(); ?>
            </ul>
        </div>
        <h1 class="page-title"><?php the_title(); ?></h1>
    </div>

    <section class="part-vacancy-text">
        <div class="container">
            <p class="vacancy-text"><?php the_field('vacancy-desc'); ?></p>
        </div>
    </section>

    <section class="part-vacancy-positions">
        <?php $vacancy_arr = get_field('vacancy-arr'); ?>
        <?php $result_arr = []; ?>
        <div class="container">
            <div class="positions">

                <div class="positions-filter">
                    <span class="positions-filter__text">Сортировка по городу:</span>

                    <?php 
                        foreach( $vacancy_arr as $key => $item ) {
                            $item_arr = explode(',', trim($item['city'] ) ); 
                            $result_arr = array_merge($result_arr, $item_arr);
                        }
                        $result_arr = array_unique($result_arr);
                        sort($result_arr);
                        $first_city = $result_arr[0];
                    ?>

                    <?php foreach( $result_arr as $key => $item ): ?>
                        <?php $active_class = $key === 0 ? ' active' : ''; ?>
                        <a class="positions-filter__link anim-word<?php echo $active_class; ?>" data-tab-city="<?php echo $item; ?>" href="#"><?php echo $item; ?></a>
                    <?php endforeach; ?>
                </div>

                <?php foreach( $vacancy_arr as $key => $item ): ?>
                    <?php $check_active = strpos(trim($item['city']), $first_city); ?>

                    <div class="positions-panel" data-panel-city="<?php echo $item['city']; ?>" data-cc style="<?php if ($check_active === false) echo 'display: none'; ?>">
                        <div class="panel-head" data-cc-head>
                            <p class="panel-head__title"><?php echo $item['position']; ?></p>
                            <svg class="panel-head__icon" role="img">
                                <use xlink:href="<?php echo get_template_directory_uri(); ?>/static/images/sprite.svg#triangle-right"></use>
                            </svg>
                        </div>
                        <div class="panel-content" data-cc-body>
                            <?php echo $item['desc']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <?php if ( get_field('feedback-check') ): ?>
    <section class="part-vacancy-feedback">
        <picture class="feedback-img-wrap">
            <img class="feedback-img" src="<?php echo get_template_directory_uri(); ?>/static/images/feedback-bg.jpg" alt="Фоновая картинка"> 
        </picture>
        <div class="container">
            <div class="feedback">

                <div class="swiper">
                    <div class="swiper-wrapper">
                        <!-- Slides -->
                        <?php $feedback_arr = get_field('feedback-arr'); ?>
                        <?php foreach( $feedback_arr as $key => $item ): ?>
                            <div class="swiper-slide slide">
                                <div class="slide-head">
                                    <picture class="slide-img-wrap">
                                        <img class="slide-img" src="<?php echo $item['img']['sizes']['medium']; ?>" alt="<?php echo $item['name']; ?>"> 
                                    </picture>
                                    <span class="slide-name"><?php echo $item['name']; ?></span>
                                </div>
                                <div class="slide-content"><?php echo $item['text']; ?></div>
                            </div>
                        <?php endforeach; ?>
                        <!-- ./Slides -->
                    </div>

                </div>
                <div class="swiper-pagination"></div>

            </div>
        </div>

        <div class="arrow-prev arrow">
            <svg>
                <use xlink:href='<?php echo get_template_directory_uri(); ?>/static/images/static-sprite.svg#arr-in-circle'/>
            </svg>
        </div> 
        <div class="arrow-next arrow">
            <svg>
                <use xlink:href='<?php echo get_template_directory_uri(); ?>/static/images/static-sprite.svg#arr-in-circle'/>
            </svg>
        </div> 

    </section>
    <?php endif; ?>

    <section class="part-vacancy-forms">
        <div class="container">

            <div class="forms">

                <div class="resume">
                    <p class="resume-title">Уже есть резюме?</p>
                    <p class="resume-subtitle">Прикрепите файл ниже, допустимые форматы doc, pdf, размер не более 2 Мб</p>

                    <form class="resume-form form-event" action="#" data-action="resume" enctype="multipart/form-data">
                        <input type="hidden" name="title" value="Уже есть резюме?">

                        <label class="file-label">
                            <input class="file-input" type="file" name="file" id="file-uploader" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                            <svg class="file-label__icon">
                                <use xlink:href='<?php echo get_template_directory_uri(); ?>/static/images/static-sprite.svg#clip'/>
                            </svg>
                            <span class="file-label__text">Прикрепить резюме</span>
                            <div class="file-modal-info"></div>
                        </label>
                        <button class="resume-btn btn"><span class="btn__text">Отправить резюме</span></button>

                        <p class="personal">Нажимая на кнопку, вы соглашаетесь на&nbsp;
                            <a class="personal__link" href="<?php the_permalink(3); ?>" target="_blank">обработку персональных&nbsp;данных</a>
                        </p>
                    </form>
                    
                </div>

                <div class="questions">
                    <p class="questions-title">Остались вопросы?</p>
                    <p class="questions-subtitle">Задайте их нам, оставьте свой номер  и наш специалист свяжется с вами</p>

                    <form class="questions-form form-event" action="#" data-action="callback">
                        <input type="hidden" name="title" value="Остались вопросы">

                        <label class="field field-tel">
                            <input class="field__input" type="tel" name="phone" autocomplete="off">
                            <span class="field__text">Телефон</span>
                        </label>
                        <button class="questions-btn btn"><span class="btn__text">Отправить заявку</span></button>

                        <p class="personal">Нажимая на кнопку, вы соглашаетесь на&nbsp;
                            <a class="personal__link" href="<?php the_permalink(3); ?>" target="_blank">обработку персональных&nbsp;данных</a>
                        </p>
                    </form>
                    
                </div>

            </div>

        </div>
    </section>








</div>