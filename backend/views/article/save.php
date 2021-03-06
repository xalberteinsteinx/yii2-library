<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $viewName   string
 * @var $params     array
 * @var $article    \xalberteinsteinx\library\common\entities\Article
 */
use yii\widgets\Pjax;

$this->title = \Yii::t('library', ($article->isNewRecord) ? 'Creating a new article' : 'Changing the article');

$this->params['breadcrumbs'] = [
    Yii::t('library', 'Library'),
    [
        'label' => Yii::t('library', 'Articles'),
        'url' => ['index'],
        'itemprop' => 'url'
    ]
];
$this->params['breadcrumbs'][] = (!empty($article->translation)) ?
    $article->translation->title :
    \Yii::t('library', 'New article');

?>

<!--BODY PANEL-->
<?php Pjax::begin([
    'id' => 'p-article-save',
    'linkSelector' => '.p-article-save',
    'submitEvent' => 'change-product-page',
]); ?>

    <!--CONTENT-->
<?= $this->render($viewName, $params); ?>

<?php Pjax::end(); ?>