<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $selectedLanguage       \bl\multilang\entities\Language
 * @var $article                \xalberteinsteinx\library\common\entities\Article
 * @var $articleTranslation     \xalberteinsteinx\library\common\entities\ArticleTranslation
 */

use marqu3s\summernote\Summernote;
use rmrevin\yii\fontawesome\FA;
use xalberteinsteinx\library\backend\widgets\LanguageSwitcher;
use xalberteinsteinx\library\common\entities\ArticleCategory;
use yii\helpers\{
    Html, Url
};
use yii\widgets\ActiveForm;

$selectedLanguageId = $selectedLanguage->id;
?>

<!--Tabs-->
<?= $this->render('_tabs', [
    'article' => $article,
    'selectedLanguage' => $selectedLanguage
]); ?>

<div class="box padding20">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'enableClientValidation' => true,
        'action' => [
            'save',
            'id' => $article->id,
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
            <?php if (!empty($article->translation)) : ?>
                <?= Html::a(
                    Html::tag('span', FA::i(FA::_EXTERNAL_LINK) . Yii::t('shop', 'View on website')),
                    (Yii::$app->get('urlManagerFrontend'))->createAbsoluteUrl(['/library/article/show', 'id' => $article->id], true), [
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
        <?= $form->field($articleTranslation, 'title', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ]); ?>

        <div class="row">
            <div class="col-md-6">
                <!--CATEGORY-->
                <label><?= \Yii::t('library', 'Category'); ?></label>
                <?= \xalberteinsteinx\library\backend\widgets\InputTree::widget([
                    'className' => ArticleCategory::className(),
                    'form' => $form,
                    'model' => $article,
                    'attribute' => 'category_id',
                    'languageId' => $selectedLanguageId
                ]);
                ?>
            </div>

            <div class="col-md-6">
                <!--KEY-->
                <?= $form->field($article, 'key', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]);
                ?>
                <!--VIEW NAME-->
                <?= $form->field($article, 'view_name', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]);
                ?>

                <!--SHOW-->
                <div style="display: inline-block;">
                    <?php $article->show = ($article->isNewRecord) ? true : $article->show; ?>
                    <?= $form->field($article, 'show', [
                        'inputOptions' => [
                            'class' => '']
                    ])->checkbox(); ?>
                </div>
            </div>
        </div>


        <h2><?= \Yii::t('library', 'Texts'); ?></h2>

        <!--INTRO TEXT-->
        <?= $form->field($articleTranslation, 'intro_text', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ]);
        ?>

        <!--FULL TEXT-->
        <?= $form->field($articleTranslation, 'full_text', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ]);
        ?>

        <!--SEO-->
        <h2><?= \Yii::t('library', 'SEO options'); ?></h2>

        <div class="seo-url">
            <?= $form->field($articleTranslation, 'alias', [
                'inputOptions' => [
                    'class' => 'form-control'
                ],
            ]); ?>
            <?= Html::button(\Yii::t('library', 'Generate'), [
                'id' => 'generate-seo-url',
                'class' => 'btn btn-primary btn-in-input',
                'url' => Url::to('generate-seo-url')
            ]); ?>
        </div>

        <?= $form->field($articleTranslation, 'seo_title', [
            'inputOptions' => [
                'class' => 'form-control'
            ]
        ]); ?>

        <div class="row">

            <div class="col-md-6">
                <?= $form->field($articleTranslation, 'meta_robots', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($articleTranslation, 'meta_author', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($articleTranslation, 'meta_keywords', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->textarea(['rows' => 3]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($articleTranslation, 'meta_description', [
                    'inputOptions' => [
                        'class' => 'form-control'
                    ]
                ])->textarea(['rows' => 3]); ?>
            </div>
        </div>

        </div>
            <div class="row">
            <div class="col-md-6">
                <?= $form->field($articleTranslation, 'meta_copyright', [
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
    </div>

    <?php $form::end(); ?>

    <?php if (!$article->isNewRecord): ?>
        <div class="created-by">
            <p>
                <b>
                    <?= \Yii::t('library', 'Created by'); ?>:
                </b>
                <?= $article->user->email ?? ''; ?>
            </p>
            <p>
                <b>
                    <?= \Yii::t('library', 'Created at'); ?>:
                </b>
                <?= $article->created_at; ?>
            </p>
            <p>
                <b>
                    <?= \Yii::t('library', 'Updated at'); ?>:
                </b>
                <?= $article->updated_at; ?>
            </p>
            <p>
                <b>
                    <?= \Yii::t('library', 'Shows'); ?>:
                </b>
                <?= $article->hits; ?>
            </p>
        </div>
    <?php endif; ?>

</div>