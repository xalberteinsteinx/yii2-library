<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $article       \xalberteinsteinx\library\common\entities\Article
 */

use xalberteinsteinx\library\frontend\widgets\ImageSlider;
use yii\helpers\Url;

$this->title = $article->translation->title;

$category = $article->category;
$this->params['breadcrumbs'][] = [
    'label' => $category->translation->title,
    'url' => ['/library/category/show', 'id' => $category->id]
];
$this->params['breadcrumbs'][] = $this->title;

?>

<article class="flex">
    <aside>
        <p class="h2">
            <?= $category->translation->title; ?>
        </p>
        <?php foreach ($category->articles as $categoryArticle) : ?>
            <p>
                <a href="<?= Url::to(['/library/article/show', 'id' => $categoryArticle->id]); ?>">
                    <?= $categoryArticle->translation->title; ?>
                </a>
            </p>
        <?php endforeach; ?>
    </aside>
    <div class="article-content">
        <h1 class="text-center">
            <?= $article->translation->title; ?>
        </h1>

        <?= $article->translation->intro_text; ?>
        <?= $article->translation->full_text; ?>


        <?php if (count($article->images) > 1) : ?>

            <section class="slider">
                <!-- Images slides -->
                <div class="slides">
                    <?= ImageSlider::widget([
                        'article' => $article,
                        'fancyBox' => true,
                        'defaultImage' => \Yii::$app->params['defaultImage'],
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
                        'article' => $article,
                        'imagesSize' => 'hd',
                        'defaultImage' => \Yii::$app->params['defaultImage'],
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
