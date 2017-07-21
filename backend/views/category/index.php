<?php
use bl\multilang\entities\Language;
use rmrevin\yii\fontawesome\FA;
use xalberteinsteinx\library\backend\widgets\ManageButtons;
use xalberteinsteinx\library\common\entities\ArticleCategory;
use xalberteinsteinx\library\common\entities\ArticleCategorySearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this           yii\web\View
 * @var $searchModel    xalberteinsteinx\library\common\search\ArticleCategorySearch
 * @var $dataProvider   yii\data\ActiveDataProvider
 */

$this->title = Yii::t('library', 'List of categories');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php Pjax::begin([
    'id' => 'p-categories',
    'linkSelector' => '.pjax',
    'enablePushState' => false
]); ?>

    <div class="box">

        <!--TITLE-->
        <div class="box-title">
            <h1>
                <?= FA::i(FA::_NAVICON) . ' ' . $this->title; ?>
            </h1>
            <!--ADD BUTTON-->
            <a href="<?= Url::to(['save', 'languageId' => Language::getCurrent()->id]) ?>"
               class="btn btn-primary btn-xs">
            <span>
                <?= FA::i(FA::_USER_PLUS) . ' ' . \Yii::t('library', 'Add new'); ?>
            </span>
            </a>
        </div>

        <!--CONTENT-->
        <div class="box-content">

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => [
                    'class' => 'table table-hover'
                ],
                'pager' => ['linkOptions' => ['class' => 'pjax']],
                'summary' => "",

                'columns' => [
                    /*POSITION*/
                    [
                        'headerOptions' => ['class' => 'text-center col-md-1'],
                        'format' => 'html',
                        'label' => Yii::t('library', 'Position'),
                        'value' => function ($model) {
                            $buttonUp = Html::a(
                                FA::i(FA::_CHEVRON_UP),
                                Url::toRoute(['up', 'id' => $model->id]),
                                [
                                    'class' => 'pjax'
                                ]
                            );
                            $buttonDown = Html::a(
                                FA::i(FA::_CHEVRON_DOWN),
                                Url::toRoute(['down', 'id' => $model->id]),
                                [
                                    'class' => 'pjax'
                                ]
                            );
                            return $buttonUp . '<div>' . $model->position . '</div>' . $buttonDown;
                        },
                        'contentOptions' => ['class' => 'vote-actions'],
                    ],

                    /*TITLE*/
                    [
                        'headerOptions' => ['class' => 'text-center col-md-3'],
                        'attribute' => 'title',
                        'value' => function ($model) {
                            $content = null;
                            if (!empty($model->translation->title)) {
                                $content = Html::a(
                                    $model->translation->title,
                                    Url::toRoute(['save', 'id' => $model->id, 'languageId' => Language::getCurrent()->id])
                                );
                                $content .= '<br><small>' . Yii::t('library', 'Created') . ' ' . $model->created_at . '</small><br>';
                            }
                            return $content;
                        },
                        'label' => Yii::t('library', 'Title'),
                        'format' => 'html',
                        'contentOptions' => ['class' => ''],
                    ],

                    /*PARENT*/
                    [
                        'headerOptions' => ['class' => 'text-center col-md-2'],
                        'attribute' => 'parent',
                        'value' => 'parent.translation.title',
                        'label' => Yii::t('library', 'Parent'),
                        'format' => 'text',
                        'filter' => ArrayHelper::map(ArticleCategory::find()->joinWith('translations')
                            ->orderBy('title')->all(), 'id', 'translation.title'),
                        'contentOptions' => ['class' => ''],
                    ],

                    /*HITS*/
                    [
                        'headerOptions' => ['class' => 'text-center col-md-1'],
                        'attribute' => 'hits',
                        'value' => 'hits',
                        'label' => Yii::t('library', 'Hits'),
                        'contentOptions' => ['class' => 'text-center'],
                    ],


                    'key',
                    // 'publish_at',

                    /*ACTIONS*/
                    [
                        'headerOptions' => ['class' => 'text-center col-md-2'],
                        'attribute' => \Yii::t('library', 'Control'),
                        'value' => function ($model) {
                            return ManageButtons::widget(['model' => $model]);
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                ],
            ]); ?>
        </div>
    </div>

<?php Pjax::end(); ?>