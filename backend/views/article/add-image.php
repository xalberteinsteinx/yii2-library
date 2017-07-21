<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $selectedLanguage       \bl\multilang\entities\Language
 * @var $article                Article
 * @var $image_form             ArticleImageForm
 */
use rmrevin\yii\fontawesome\FA;
use xalberteinsteinx\library\backend\components\forms\ArticleImageForm;
use xalberteinsteinx\library\common\entities\Article;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

BootstrapPluginAsset::register($this);
?>


<!--Tabs-->
<?= $this->render('_tabs', [
    'article' => $article,
    'selectedLanguage' => $selectedLanguage
]); ?>


<div class="box padding20">

    <?php Pjax::begin([
        'id' => 'p-article-image',
        'enablePushState' => false,
        'enableReplaceState' => false,
    ]); ?>

    <?php $addImageForm = ActiveForm::begin([
        'action' => [
            'add-image',
            'id' => $article->id,
            'languageId' => $selectedLanguage->id
        ],
        'method' => 'post',
        'options' => [
            'id' => 'article-image',
            'data-pjax' => true
        ]
    ]);
    ?>

    <h2><?= \Yii::t('library', 'Images'); ?></h2>

    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-3 text-center" colspan="2">
                <strong>
                    <?= \Yii::t('library', 'Add from web'); ?>
                </strong>
            </td>
            <td class="col-md-4">
                <?= $addImageForm->field($image_form, 'link')->textInput([
                    'placeholder' => Yii::t('library', 'Image link')
                ])->label(false); ?>
            </td>
            <td class="col-md-3">
                <?= $addImageForm->field($image_form, 'alt1')->textInput(['placeholder' => \Yii::t('library', 'Alternative text')])->label(false); ?>
            </td>
            <td class="col-md-2 text-center">
                <?= Html::submitButton(\Yii::t('library', 'Add'), ['class' => 'btn btn-primary pjax']) ?>
            </td>
        </tr>
        <tr>
            <td class="text-center" colspan="2">
                <strong>
                    <?= \Yii::t('library', 'Upload'); ?>
                </strong>
            </td>
            <td>
                <?= $addImageForm->field($image_form, 'image')->fileInput()->label(false); ?>
            </td>
            <td class="text-center">
                <?= $addImageForm->field($image_form, 'alt2')->textInput(['placeholder' => \Yii::t('library', 'Alternative text')])->label(false); ?>
            </td>
            <td class="text-center">
                <?= Html::submitButton(\Yii::t('library', 'Add'), ['class' => 'btn btn-primary pjax']) ?>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <p>
                    <i>
                        <?= '*' . \Yii::t('library', 'The maximum file size limit for uploads is') . ' ' .
                        (int)(ini_get('upload_max_filesize')) . 'Mb.'; ?>
                        <?= \Yii::t('library', 'Upload only optimized and lightweight images. This will speed up the website.'); ?>
                    </i>
                </p>
            </td>
        </tr>
        </tbody>
    </table>


    <?php $addImageForm->end(); ?>

    <!--List of images-->
    <table class="table">
        <tbody>
        <tr>
            <th class="col-md-1"></th>
            <th class="text-center col-md-2">
                <p>
                    <?= \Yii::t('library', 'Image preview'); ?>
                </p>
            </th>
            <th class="text-center col-md-4">
                <p>
                    <?= \Yii::t('library', 'Image URL'); ?>
                </p>
            </th>
            <th class="text-center col-md-3">
                <p>
                    <?= \Yii::t('library', 'Alt'); ?>
                </p>
            </th>
            <th class="text-center col-md-2">
                <p>
                    <?= \Yii::t('library', 'Control'); ?>
                </p>
            </th>
        </tr>
        <?php if (!empty($images = $article->images)) : ?>
            <?php foreach ($images as $image) : ?>
                <tr>
                    <td>
                        <?= Html::a(
                            '',
                            Url::toRoute(['image-up', 'id' => $image->id, 'languageId' => $selectedLanguage->id]),
                            [
                                'class' => 'pjax fa fa-chevron-up'
                            ]
                        ) .
                        $image->position .
                        Html::a(
                            '',
                            Url::toRoute(['image-down', 'id' => $image->id, 'languageId' => $selectedLanguage->id]),
                            [
                                'class' => 'pjax fa fa-chevron-down'
                            ]
                        );
                        ?>
                    </td>
                    <td class="text-center">
                        <img data-toggle="modal" data-target="#menuItemModal-<?= $image->id ?>"
                             src="<?= $image->getBySize('small'); ?>"
                             class="thumb">
                        <!-- Modal -->
                        <div id="menuItemModal-<?= $image->id ?>" class="modal fade" role="dialog">
                            <img style="display: block" class="modal-dialog"
                                 src="<?= $image->getBySize('thumb'); ?>">
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="image-link">
                            <p>
                                <i class="fa fa-external-link" aria-hidden="true"></i>
                                <?= str_replace(Yii::$app->homeUrl, '', Url::home(true)) . $image->getBySize('full-hd'); ?>
                            </p>
                        </div>
                    </td>
                    <td class="text-center">
                        <?php $editImageAltForm = ActiveForm::begin([
                            'id' => 'edit-image-alt-' . $image->id,
                            'action' => [
                                'article/edit-image',
                                'id' => $image->id,
                                'languageId' => $selectedLanguage->id
                            ],
                            'method' => 'post',
                            'options' => [
                                'id' => 'edit-article-image-' . $image->id,
                                'data-pjax' => true
                            ]
                        ]);
                        ?>

                        <?= $editImageAltForm->field($image->getTranslation($selectedLanguage->id), 'alt_text', [
                            'inputOptions' => [
                                'class' => 'form-control'
                            ],
                        ])->label(false); ?>

                        <?= Html::submitButton(FA::i(FA::_EDIT), [
                            'class' => 'btn btn-primary btn-in-input',
                        ]); ?>

                        <?php $editImageAltForm->end(); ?>
                    </td>
                    <td class="text-center">

                        <a href="<?= Url::toRoute(['delete-image', 'id' => $image->id, 'languageId' => $selectedLanguage->id]); ?>"
                           class="btn btn-xs btn-danger"><?= FA::i(FA::_REMOVE); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <?php Pjax::end(); ?>
</div>