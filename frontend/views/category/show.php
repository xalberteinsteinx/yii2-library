<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $category       \xalberteinsteinx\library\common\entities\ArticleCategory
 */

use bl\imagable\helpers\FileHelper;
use yii\helpers\Url;

$this->title = $category->translation->title;

$this->params['breadcrumbs'][] = $this->title;
?>

<article>
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

        <?php if ($category->hasArticles()) : ?>
            <!--Articles-->
            <section class="category-articles">
                <div class="articles-grid blue">
                    <?php foreach ($category->articles as $service) : ?>
                        <?php $link = Url::to(['/library/article/show', 'id' => $service->id]); ?>
                        <section class="article">
                            <a href="<?= $link; ?>">
                                <?php $imageName = $service->getFirstImage()->image_name; ?>
                                <?php if (!empty($imageName)): ?>
                                    <img align="left" src="/images/library/article/<?= FileHelper::getFullName(
                                        \Yii::$app->library_imagable->get('article', 'thumb', $imageName)
                                    ); ?>" alt="<?= $staticPage->translation->title; ?>">
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

    </div>
</article>
