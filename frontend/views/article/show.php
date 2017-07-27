<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $article       \xalberteinsteinx\library\common\entities\Article
 */

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
    </div>
</article>