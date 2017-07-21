<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $article            Article
 * @var $selectedLanguage   Language
 * @var $video_form_upload  ArticleVideoForm
 */
use rmrevin\yii\fontawesome\FA;
use xalberteinsteinx\library\backend\components\forms\ArticleVideoForm;
use xalberteinsteinx\library\common\entities\Article;
use bl\multilang\entities\Language;
use xalberteinsteinx\library\common\entities\ArticleVideo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
?>

<!--Tabs-->
<?= $this->render('_tabs', [
    'article' => $article,
    'selectedLanguage' => $selectedLanguage
]); ?>

<div class="box padding20">

<!--    --><?php //Pjax::begin([
//        'id' => 'p-article-video-' . $article->id,
//        'enablePushState' => false,
//        'enableReplaceState' => false,
////        'submitEvent' => 'change-video-table',
//    ]); ?>

    <?php $addVideoForm = ActiveForm::begin([
        'action' => [
            'article/add-video',
            'id' => $article->id,
            'languageId' => $selectedLanguage->id
        ],
        'method' => 'post',
        'options' => [
            'data-pjax' => true
        ]
    ]);
    ?>
    <h2><?= \Yii::t('library', 'Video'); ?></h2>

    <table class="table table-hover">
        <tr class="text-center">
            <td class="col-md-2">
                <strong>
                    <?= \Yii::t('library', 'Add from service'); ?>
                </strong>
            </td>
            <td class="col-md-4">
                <?= $addVideoForm->field(new ArticleVideo(), 'resource')->dropDownList(
                    [
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo'
                    ]
                )->label(false); ?>
            </td>
            <td class="col-md-4">
                <?= $addVideoForm->field(new ArticleVideo(), 'video_name')->textInput(['placeholder' => \Yii::t('library', 'Link to video')])->label(false); ?>
            </td>
            <td class="col-md-2">
                <?= Html::submitButton(\Yii::t('library', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
    </table>
    <?php $addVideoForm->end(); ?>

    <?php $uploadVideoForm = ActiveForm::begin([
        'action' => [
            'article/add-video',
            'id' => $article->id,
            'languageId' => $selectedLanguage->id
        ],
        'method' => 'post',
        'options' => [
            'data-pjax' => true,
            'enctype' => 'multipart/form-data'
        ]
    ]);
    ?>
    <table class="table table-hover">
        <tr class="text-center">
            <td class="col-md-2">
                <strong>
                    <?= \Yii::t('library', 'Upload'); ?>
                </strong>
            </td>
            <td class="col-md-4">
            </td>
            <td class="col-md-4">
                <?= $uploadVideoForm->field($video_form_upload, 'file_name')->fileInput()->label(false); ?>
            </td>
            <td class="col-md-2">
                <?= Html::submitButton(\Yii::t('library', 'Add'), ['class' => 'btn btn-primary']) ?>
            </td>
        </tr>
    </table>
    <?php $uploadVideoForm->end(); ?>
    <p>
        <i>
            <?= '*' . \Yii::t('library', 'The maximum file size limit for uploads is') . ' ' .
            (int)(ini_get('upload_max_filesize')) . 'Mb'; ?>
        </i>
    </p>

    <table class="table table-bordered">
        <thead class="thead-inverse">

        <?php if (!empty($article->videos)) : ?>
            <tr>
                <th class="text-center col-md-2">
                    <?= \Yii::t('library', 'Position'); ?>
                </th>
                <th class="text-center col-md-2">
                    <?= \Yii::t('library', 'Resource'); ?>
                </th>
                <th class="text-center col-md-2">
                    <?= \Yii::t('library', 'ID'); ?>
                </th>
                <th class="text-center col-md-4">
                    <?= \Yii::t('library', 'Preview'); ?>
                </th>
                <th class="text-center col-md-2">
                    <?= \Yii::t('library', 'Delete'); ?>
                </th>
            </tr>
        <?php endif; ?>
        </thead>

        <tbody>
        <?php foreach ($article->videos as $video) : ?>
            <tr>
                <td class="text-center">
                    <?php $buttonUp = Html::a(
                        FA::i(FA::_CHEVRON_UP),
                        Url::toRoute(['video-up', 'id' => $video->id, 'languageId' => $selectedLanguage->id]),
                        [
                            'class' => 'pjax'
                        ]
                    );
                    $buttonDown = Html::a(
                        FA::i(FA::_CHEVRON_DOWN),
                        Url::toRoute(['video-down', 'id' => $video->id, 'languageId' => $selectedLanguage->id]),
                        [
                            'class' => 'pjax'
                        ]
                    );
                    echo $buttonUp . '<div>' . $video->position . '</div>' . $buttonDown; ?>
                </td>
                <td class="text-center">
                    <?= $video->resource; ?>
                </td>
                <td class="text-center">
                    <?= $video->video_name; ?>
                </td>
                <td class="text-center">
                    <?php if ($video->resource == 'youtube') : ?>
                        <iframe width="100%" height="200" src="https://www.youtube.com/embed/<?= $video->video_name; ?>"
                                frameborder="0" allowfullscreen></iframe>
                    <?php elseif ($video->resource == 'vimeo') : ?>
                        <iframe src="https://player.vimeo.com/video/<?= $video->video_name; ?>" width="100%" height="200"
                                frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                    <?php elseif ($video->resource == 'videofile') : ?>
                        <video width="100%" height="200" controls>
                            <source src="/video/<?= $video->video_name; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <a href="<?= Url::toRoute(['delete-video', 'id' => $video->id, 'languageId' => $selectedLanguage->id]); ?>"
                       class="btn btn-danger">
                        <?= FA::i(FA::_REMOVE); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<!--    --><?php //Pjax::end(); ?>

</div>