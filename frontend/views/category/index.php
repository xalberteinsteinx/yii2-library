<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */


use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel xalberteinsteinx\library\common\search\ArticleCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('library', 'Article Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('library', 'Create Article Category'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'parent_id',
            'user_id',
            'key',
            'view_name',
            // 'article_view_name',
            // 'position',
            // 'hits',
            // 'show',
            // 'created_at',
            // 'updated_at',
            // 'publish_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>