<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $category           \xalberteinsteinx\library\common\entities\ArticleCategory
 * @var $selectedLanguage   \bl\multilang\entities\Language
 */
use yii\helpers\Html;
use yii\helpers\Url;

$newCategoryMessage = Yii::t('library', 'You must save new category before this action');
$selectedLanguageId = $selectedLanguage->id;
?>

<header class="tabs">
    <ul>

        <!--BASIC-->
        <li class="<?= Yii::$app->controller->action->id == 'save' ? 'active' : ''; ?>">
            <?= Html::a(\Yii::t('library', 'Basic'), Url::to([
                'save', 'id' => $category->id, 'languageId' => $selectedLanguageId
            ]));
            ?>
        </li>

        <!--IMAGES-->
        <li class="<?= (empty($category->translation)) ? 'disabled' : ''; ?>
            <?= Yii::$app->controller->action->id == 'add-image' ? 'active' : ''; ?>">

            <?= ($category->isNewRecord) ?
                Html::a(\Yii::t('library', 'Images'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newCategoryMessage
                ]) :
                Html::a(
                    \Yii::t('library', 'Images'),
                    Url::to(['add-image', 'id' => $category->id, 'languageId' => $selectedLanguageId]),
                    [
                        'class' => 'p-category-save'
                    ]); ?>
        </li>

    </ul>
</header>