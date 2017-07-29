<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $selectedLanguage           \bl\multilang\entities\Language
 * @var $category                   \xalberteinsteinx\library\common\entities\ArticleCategory
 * @var $categoryTranslation        \xalberteinsteinx\library\common\entities\ArticleCategoryTranslation
 */

use marqu3s\summernote\Summernote;
use rmrevin\yii\fontawesome\FA;
use xalberteinsteinx\library\backend\assets\LibraryAsset;
use xalberteinsteinx\library\backend\widgets\LanguageSwitcher;
use xalberteinsteinx\library\common\entities\ArticleCategory;
use yii\helpers\{
    Html, Url
};
use yii\widgets\ActiveForm;
$selectedLanguageId = $selectedLanguage->id;

LibraryAsset::register($this);
?>

<!--Tabs-->
<?= $this->render('_tabs', [
    'category' => $category,
    'selectedLanguage' => $selectedLanguage
]); ?>

<div class="box padding20">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'enableClientValidation' => true,
        'action' => [
            'save',
            'id' => $category->id,
            'languageId' => $selectedLanguageId
        ]]);
    ?>

    <header>
        <section class="title">
            <h2><?= \Yii::t('library', 'Basic options'); ?></h2>
        </section>

        <section class="buttons">
            <!--SAVE BUTTON-->
            <?= Html::submitButton(
                Html::tag('span', FA::i(FA::_SAVE) . ' ' . \Yii::t('library', 'Save')),
                ['class' => 'btn btn-xs']); ?>

            <!--CANCEL BUTTON-->
            <?= Html::a(
                Html::tag('span', FA::i(FA::_STOP_CIRCLE) . ' ' . \Yii::t('library', 'Cancel')),
                Url::to(['index']), [
                'class' => 'btn btn-danger btn-xs'
            ]); ?>

            <!--VIEW ON SITE-->
            <?php if (!empty($category->translation)) : ?>
                <?= Html::a(
                    Html::tag('span', FA::i(FA::_EXTERNAL_LINK) . Yii::t('shop', 'View on website')),
                    (Yii::$app->get('urlManagerFrontend'))
                        ->createAbsoluteUrl(['/library/category/index', 'id' => $category->id, 'languageId' => $selectedLanguage->id], true), [
                    'class' => 'btn btn-info btn-xs',
                    'target' => '_blank'
                ]); ?>
            <?php endif; ?>

            <!--LANGUAGES-->
            <?= LanguageSwitcher::widget([
                'selectedLanguage' => $selectedLanguage,
            ]); ?>
        </section>
    </header>

    <!--BASIC-->
    <div id="basic">

        <!--TITLE-->
        <?= $form->field($categoryTranslation, 'title', [
            'inputOptions' => [
                'id' => 'title-input',
                'class' => 'form-control'
            ]
        ]); ?>

        <div class="row">
            <div class="col-md-6">
                <!--CATEGORY-->
                <label><?= \Yii::t('library', 'Parent'); ?></label>
                <?= \xalberteinsteinx\library\backend\widgets\InputTree::widget([
                    'className' => ArticleCategory::className(),
                    'form' => $form,
                    'model' => $category,
                    'attribute' => 'parent_id',
                    'languageId' => $selectedLanguageId
                ]);
                ?>
            </div>

            <div class="col-md-6">
                <!--KEY-->
                <?= $form->field($category, 'key', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]);
                ?>
                <!--VIEW NAME-->
                <?= $form->field($category, 'view_name', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]);
                ?>
                <!--VIEW NAME-->
                <?= $form->field($category, 'article_view_name', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]);
                ?>

                <!--PUBLISH AT-->
                <?= $form->field($category, 'publish_at', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->input('datetime-local', ['value' => date('Y-m-d\TH:i')]); ?>

                <!--SHOW-->
                <div style="display: inline-block;">
                    <?php $category->show = ($category->isNewRecord) ? true : $category->show; ?>
                    <?= $form->field($category, 'show', [
                        'inputOptions' => [
                            'class' => '']
                    ])->checkbox(); ?>
                </div>
            </div>
        </div>


        <h2><?= \Yii::t('library', 'Texts'); ?></h2>

        <!--INTRO TEXT-->
        <?= $form->field($categoryTranslation, 'intro_text', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->widget(Summernote::className());
        ?>

        <!--FULL TEXT-->
        <?= $form->field($categoryTranslation, 'full_text', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ])->widget(Summernote::className());
        ?>

        <!--SEO-->
        <h2><?= \Yii::t('library', 'SEO options'); ?></h2>

        <div class="seo-url">
            <?= $form->field($categoryTranslation, 'alias', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'id' => 'alias-input'
                ],
            ]); ?>
            <?= Html::button(\Yii::t('library', 'Generate'), [
                'id' => 'generate-alias-button',
                'class' => 'btn btn-primary btn-in-input',
                'data-url' => Url::to('generate-alias')
            ]); ?>
        </div>

        <?= $form->field($categoryTranslation, 'seo_title', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ]); ?>

        <div class="row">

            <div class="col-md-6">
                <?= $form->field($categoryTranslation, 'meta_robots', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($categoryTranslation, 'meta_author', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($categoryTranslation, 'meta_keywords', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->textarea(['rows' => 3]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($categoryTranslation, 'meta_description', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->textarea(['rows' => 3]); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($categoryTranslation, 'meta_copyright', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]); ?>
            </div>
        </div>

        <section class="buttons">
            <!--SAVE BUTTON-->
            <?= Html::submitButton(FA::i(FA::_SAVE) . ' ' . \Yii::t('library', 'Save'), ['class' => 'btn btn-xs']); ?>

            <!--CANCEL BUTTON-->
            <?= Html::a(FA::i(FA::_STOP_CIRCLE) . ' ' . \Yii::t('library', 'Cancel'), Url::to(['index']), [
                'class' => 'btn btn-danger btn-xs'
            ]); ?>
        </section>

        <?php if (!$category->isNewRecord): ?>
            <div class="created-by">
                <p>
                    <b>
                        <?= \Yii::t('library', 'Created by'); ?>:
                    </b>
                    <?= $category->user->email ?? ''; ?>
                </p>
                <p>
                    <b>
                        <?= \Yii::t('library', 'Created at'); ?>:
                    </b>
                    <?= $category->created_at; ?>
                </p>
                <p>
                    <b>
                        <?= \Yii::t('library', 'Updated at'); ?>:
                    </b>
                    <?= $category->updated_at; ?>
                </p>
                <p>
                    <b>
                        <?= \Yii::t('library', 'Shows'); ?>:
                    </b>
                    <?= $category->hits ?? 0; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <?php $form::end(); ?>
</div>
