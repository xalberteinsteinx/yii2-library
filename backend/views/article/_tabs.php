<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $article            \xalberteinsteinx\library\common\entities\Article
 * @var $selectedLanguage   \bl\multilang\entities\Language
 */
use yii\helpers\Html;
use yii\helpers\Url;

$newArticleMessage = Yii::t('library', 'You must save new article before this action');
$selectedLanguageId = $selectedLanguage->id;
?>

<header class="tabs">
    <ul>

        <!--BASIC-->
        <li class="<?= Yii::$app->controller->action->id == 'save' ? 'active' : ''; ?>">
            <?= Html::a(\Yii::t('library', 'Basic'), Url::to([
                'save', 'id' => $article->id, 'languageId' => $selectedLanguageId
            ]));
            ?>
        </li>

        <!--IMAGES-->
        <li class="<?= (empty($article->translation)) ? 'disabled' : ''; ?>
            <?= Yii::$app->controller->action->id == 'add-image' ? 'active' : ''; ?>">

            <?= ($article->isNewRecord) ?
                Html::a(\Yii::t('library', 'Images'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newArticleMessage
                ]) :
                Html::a(
                    \Yii::t('library', 'Images'),
                    Url::to(['add-image', 'id' => $article->id, 'languageId' => $selectedLanguageId]),
                    [
                        'class' => 'pjax'
                    ]); ?>
        </li>

        <!--VIDEOS-->
        <li class="<?= (empty($article->translation)) ? 'disabled' : ''; ?>
            <?= Yii::$app->controller->action->id == 'add-video' ? 'active' : ''; ?>">
            <?= ($article->isNewRecord) ?
                Html::a(\Yii::t('library', 'Video'), null, [
                    'data-toggle' => 'tooltip',
                    'title' => $newArticleMessage
                ]) :
                Html::a(
                    \Yii::t('library', 'Video'),
                        Url::to(['add-video', 'id' => $article->id, 'languageId' => $selectedLanguageId]),
                    [
                        'class' => 'pjax'
                    ]); ?>
        </li>
    </ul>
</header>