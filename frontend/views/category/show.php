<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $category       \xalberteinsteinx\library\common\entities\ArticleCategory
 */

use bl\imagable\helpers\FileHelper;
use xalberteinsteinx\library\frontend\widgets\ImageSlider;
use yii\helpers\Url;

$this->title = $category->translation->title;

$this->params['breadcrumbs'][] = $this->title;
?>

<article class="flex">
    <?php if (!empty($parent = $category->parent)): ?>
    <aside>

            <p class="h2">
                <?= $parent->translation->title; ?>
            </p>
            <?php foreach ($parent->childCategories as $childCategory) : ?>
                <p>
                    <a href="<?= Url::to(['/library/article/index', 'id' => $childCategory->id]); ?>">
                        <?= $childCategory->translation->title; ?>
                    </a>
                </p>
            <?php endforeach; ?>
    </aside>
    <?php endif; ?>

    <div class="article-content">
        <h1 class="text-center">
            <?= $this->title; ?>
        </h1>

        <div>
            <?= $category->translation->intro_text; ?>
        </div>
        <div>
            <?= $category->translation->full_text; ?>
        </div>

        <?php if ($category->hasChildCategories()) : ?>
            <!--Child categories-->
            <section class="category-articles">
                <div class="articles-grid blue">
                    <?php foreach ($category->childCategories as $childCategory) : ?>
                        <?php $link = Url::to(['/library/category/index', 'id' => $childCategory->id]); ?>
                        <section class="article">
                            <a href="<?= $link; ?>">
                                <?php $imageName = $childCategory->images[0]->image_name; ?>
                                <?php if (!empty($imageName)): ?>
                                    <img align="left" src="/images/library/category/<?= FileHelper::getFullName(
                                        \Yii::$app->library_imagable->get('category', 'thumb', $imageName)
                                    ); ?>" alt="<?= $childCategory->translation->title; ?>">
                                <?php endif; ?>

                                <h2>
                                    <?= $childCategory->translation->title; ?>
                                </h2>
                            </a>

                        </section>
                    <?php endforeach; ?>
                </div>

            </section>
        <?php endif; ?>

        <?php if ($category->hasArticles()) : ?>
            <!--Articles-->
            <section class="category-articles">
                <div class="articles-grid blue">
                    <?php foreach ($category->articles as $service) : ?>
                        <?php $link = Url::to(['/library/article/index', 'id' => $service->id]); ?>
                        <section class="article">
                            <a href="<?= $link; ?>">
                                <?php $imageName = $service->getFirstImage()->image_name; ?>
                                <?php if (!empty($imageName)): ?>
                                    <img align="left" src="/images/library/article/<?= FileHelper::getFullName(
                                        \Yii::$app->library_imagable->get('article', 'thumb', $imageName)
                                    ); ?>" alt="<?= $childCategory->translation->title; ?>">
                                <?php endif; ?>

                                <h2>
                                    <?= $service->translation->title; ?>
                                </h2>
                            </a>

                        </section>
                    <?php endforeach; ?>
                </div>

            </section>
        <?php endif; ?>


        <?php if (count($category->images) > 1) : ?>

            <section class="slider">
                <!-- Images slides -->
                <div class="slides">
                    <?= ImageSlider::widget([
                        'article' => $category,
                        'fancyBox' => true,
                        'defaultImage' => '/images/default-image.jpg',
                        'fancyBoxWidgetConfig' => [
                            'helpers' => true,
                            'mouse' => true,
                            'config' => [
                                'maxWidth' => '100%',
                                'maxHeight' => '100%',
                                'playSpeed' => 7000,
                                'padding' => 0,
                                'fitToView' => true,
                                'width' => '90%',
                                'height' => '90%',
                                'autoSize' => false,
                                'closeClick' => false,
                                'openEffect' => 'elastic',
                                'closeEffect' => 'elastic',
                                'prevEffect' => 'elastic',
                                'nextEffect' => 'elastic',
                                'openOpacity' => true,
                                'helpers' => [
                                    'title' => ['type' => 'float'],
                                    'buttons' => [],
                                    'thumbs' => ['width' => 70, 'height' => 70],
                                    'overlay' => [
                                        'css' => [
                                            'background' => 'rgba(0, 0, 0, 0.8)'
                                        ]
                                    ]
                                ],
                            ]
                        ],
                        'containerOptions' => [
                            'id' => 'articleImageSlider',
                        ],
                        /** @see http://kenwheeler.github.io/slick/#settings */
                        'clientOptions' => [
                            'autoplay' => true,
                            //'centerMode' => true,
                            'dots' => true,
                            'asNavFor' => '#articleImageSliderThumbs'
                        ],
                    ]); ?>
                </div>

                <!-- Slide thumbs -->
                <div class="thumbs">
                    <?= ImageSlider::widget([
                        'article' => $category,
                        'imagesSize' => 'hd',
                        'defaultImage' => '/images/default-image.jpg',
                        'containerOptions' => [
                            'id' => 'articleImageSliderThumbs',
                            'class' => 'article-image-thumbs',
                        ],
                        'itemOptions' => [
                            'class' => 'article-image-slide-thumb'
                        ],
                        /** @see http://kenwheeler.github.io/slick/#settings */
                        'clientOptions' => [
                            'autoplay' => true,
                            'asNavFor' => '#articleImageSlider',
                            'slidesToShow' => 7,
                            'slidesToScroll' => 1,
                            'dots' => false,
                            'focusOnSelect' => true,
                            'vertical' => false,
                            'responsive' => [
                                [
                                    'breakpoint' => 992,
                                    'settings' => [
                                        'slidesToShow' => 6,
                                        'vertical' => false,
                                    ]
                                ],
                                [
                                    'breakpoint' => 480,
                                    'settings' => [
                                        'slidesToShow' => 4,
                                        'vertical' => false,
                                    ]
                                ],
                                [
                                    'breakpoint' => 360,
                                    'settings' => [
                                        'slidesToShow' => 3,
                                        'vertical' => false,
                                    ]
                                ],
                            ]
                        ],
                    ]); ?>
                </div>
            </section>
        <?php endif; ?>

    </div>
</article>
