<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $viewName   string
 * @var $params     array
 * @var $category   \xalberteinsteinx\library\common\entities\ArticleCategory
 */
use yii\widgets\Pjax;

$this->title = \Yii::t('library', ($category->isNewRecord) ? 'Creating a new category' : 'Changing the category');

$this->params['breadcrumbs'] = [
    Yii::t('library', 'Library'),
    [
        'label' => Yii::t('library', 'Categories'),
        'url' => ['index'],
        'itemprop' => 'url'
    ]
];
$this->params['breadcrumbs'][] = (!empty($category->translation)) ?
    $category->translation->title :
    \Yii::t('library', 'New category');

?>

<!--BODY PANEL-->
<?php //Pjax::begin([
//    'id' => 'p-category-save',
//    'linkSelector' => '.p-category-save',
//    'submitEvent' => 'change-product-page',
//]); ?>

    <!--CONTENT-->
    <?= $this->render($viewName, $params); ?>

<?php //Pjax::end(); ?>

