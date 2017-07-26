<?php

namespace xalberteinsteinx\library\backend\controllers;

use bl\multilang\entities\Language;
use xalberteinsteinx\library\backend\components\forms\ArticleImageForm;
use xalberteinsteinx\library\backend\components\forms\ArticleVideoForm;
use xalberteinsteinx\library\common\entities\ArticleImage;
use xalberteinsteinx\library\common\entities\ArticleImageTranslation;
use xalberteinsteinx\library\common\entities\ArticleTranslation;
use xalberteinsteinx\library\common\entities\ArticleVideo;
use Yii;
use xalberteinsteinx\library\common\entities\Article;
use xalberteinsteinx\library\common\search\ArticleSearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii2tech\ar\position\PositionBehavior;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'roles' => ['viewListOfArticles'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'save',
                            'up', 'down', 'generate-seo-url',
                            'add-image', 'delete-image', 'edit-image',
                            'image-up', 'image-down',
                            'add-video', 'delete-video',
                            'video-up', 'video-down',
                        ],
                        'roles' => ['createArticle', 'createArticleWithoutModeration',
                            'updateArticle', 'updateOwnArticle'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['delete'],
                        'roles' => ['deleteArticle', 'deleteOwnArticle'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['change-article-status'],
                        'roles' => ['moderateArticleCreation'],
                        'allow' => true,
                    ]
                ],
            ],
        ];
    }


    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates new or updates existing Article and ArticleTranslation models.
     * @param null|integer $id
     * @param null|integer $languageId
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionSave($id = null, $languageId = null)
    {
        $selectedLanguage = (!empty($languageId)) ? Language::findOne($languageId) : Language::getCurrent();

        //Getting or creating Article and ArticleTranslation models
        if (!empty($id)) {
            $article = Article::findOne($id);
            if (!empty($article)) {
                if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user])) {
                    $articleTranslation = ArticleTranslation::find()->where([
                            'article_id' => $id,
                            'language_id' => $languageId
                        ])->one() ?? new ArticleTranslation();
                } else throw new ForbiddenHttpException();
            } else throw new NotFoundHttpException();
        } else {
            if (\Yii::$app->user->can('createArticle')) {
                $article = new Article();
                $articleTranslation = new ArticleTranslation();
            } else throw new ForbiddenHttpException();
        }

        /*POST*/
        if (Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            if ($article->load($post)) {

                $article->category_id = (!empty($article->category_id)) ? $article->category_id : NULL;

                /*Position*/
                $positionQuery = Article::find()->select('position');
                if ($article->category_id == null) {
                    $positionQuery->where(['category_id' => null]);
                }
                else {
                    $positionQuery->where(['category_id' => $article->category_id]);
                }
                $article->position = $positionQuery->max('position') + 1;

                if ($article->isNewRecord) {
                    $article->user_id = Yii::$app->user->id;
                }
                if (!$article->validate())
                    Yii::$app->session->setFlash(
                        'error',
                        \Yii::t('library', 'An error occurred during the save of the article'));

                if ($articleTranslation->load($post)) {

                    /*Sets alias*/
                    if (empty($articleTranslation->alias)) {
                        $articleTranslation->alias = ArticleTranslation::generateAlias($articleTranslation->title);
                    }

                    $article->save();
                    $articleTranslation->article_id = $article->id;
                    $articleTranslation->language_id = $selectedLanguage->id;

                    if ($articleTranslation->validate()) {
                        $articleTranslation->save();

                        Yii::$app->session->setFlash(
                            'success',
                            \Yii::t('library', 'You have successfully save this article'));
                        return $this->redirect(Url::to(['save', 'id' => $article->id, 'languageId' => $selectedLanguage->id]));
                    } else Yii::$app->session->setFlash(
                        'error',
                        \Yii::t('library', 'An error occurred during the save of the article'));
                }
            }
        }
        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('add-basic', [
                'selectedLanguage' => $selectedLanguage,
                'article' => $article,
                'articleTranslation' => $articleTranslation,
            ]);
        } else {
            return $this->render('save', [
                'article' => $article,
                'viewName' => 'add-basic',
                'params' => [
                    'selectedLanguage' => $selectedLanguage,
                    'article' => $article,
                    'articleTranslation' => $articleTranslation,
                ]
            ]);
        }
    }

    /**
     * Changes article position to up
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUp($id)
    {
        $article = Article::findOne($id);
        if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user_id])) {
            if (!empty($article)) {
                /**
                 * @var $article PositionBehavior
                 */
                $article->movePrev();
            }
            if (\Yii::$app->request->isPjax) return $this->actionIndex();
            return $this->redirect(\Yii::$app->request->referrer);
        } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
    }

    /**
     * Changes article position to down
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDown($id)
    {
        $article = Article::findOne($id);
        if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user_id])) {
            if (!empty($article)) {
                /**
                 * @var $article PositionBehavior
                 */
                $article->moveNext();
            }
            if (\Yii::$app->request->isPjax) return $this->actionIndex();
            return $this->redirect(\Yii::$app->request->referrer);
        } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $article = Article::findOne($id);
        if (empty($article)) throw new NotFoundHttpException();
        if (\Yii::$app->user->can('deleteArticle', ['articleOwner' => $article->user_id])) {
            $article->delete();

            \Yii::$app->session->setFlash('success', Yii::t('library', 'You have successfully removed this article'));

            if (\Yii::$app->request->isPjax) return $this->actionIndex();
            return $this->redirect('index');
        } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to delete this article.'));
    }

    /**
     * Generates seo Url from title on add-basic page
     *
     * @param string $title
     * @return string
     */
    public function actionGenerateSeoUrl($title)
    {
        $newAlias = ArticleTranslation::generateAlias($title);
        return $newAlias;
    }

    /**
     * @param $id
     * @param $languageId
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAddImage($id, $languageId)
    {
        $article = Article::findOne($id);

        if (empty($article)) throw new NotFoundHttpException();
        if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user_id])) {
            $image_form = new ArticleImageForm();
            $image = new ArticleImage();
            $imageTranslation = new ArticleImageTranslation();

            if (Yii::$app->request->isPost) {
                $image_form->load(Yii::$app->request->post());
                $image_form->image = UploadedFile::getInstance($image_form, 'image');
                if (!empty($image_form->image)) {
                    if ($uploadedImageName = $image_form->upload()) {
                        $image->image_name = $uploadedImageName;
                        $imageTranslation->alt_text = $image_form->alt2;
                        $image->article_id = $id;

                        if ($image->validate()) {
                            $image->save();
                            $imageTranslation->article_image_id = $image->id;
                            $imageTranslation->language_id = $languageId;
                            if ($imageTranslation->validate()) {
                                $imageTranslation->save();
                            }
                        }
                    }
                }
                if (!empty($image_form->link)) {
                    $image_name = $image_form->copy($image_form->link);
                    $image->image_name = $image_name;
                    $imageTranslation->alt_text = $image_form->alt1;
                    $image->article_id = $id;
                    if ($image->validate()) {
                        $image->save();
                        $imageTranslation->article_image_id = $image->id;
                        $imageTranslation->language_id = $languageId;
                        if ($imageTranslation->validate()) {
                            $imageTranslation->save();
                        }
                    }
                }
            }
            $params = [
                'selectedLanguage' => Language::findOne($languageId),
                'article' => $article,
                'image_form' => new ArticleImageForm(),
            ];
            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-image', $params);
            }
            return $this->render('save', [
                'article' => $article,
                'viewName' => 'add-image',
                'params' => $params
            ]);
        } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
    }

    /**
     * @param $id
     * @param $languageId
     * @return string|\yii\web\Response
     */
    public function actionEditImage($id, $languageId)
    {
        if (Yii::$app->request->isPost) {
            $image = ArticleImage::findOne($id);
            $imageTranslation = ArticleImageTranslation::find()->where([
                'article_image_id' => $id,
                'language_id' => $languageId
            ])->one();
            if (empty($imageTranslation)) {
                $imageTranslation = new ArticleImageTranslation();
            }
            $imageTranslation->load(Yii::$app->request->post());
            $imageTranslation->article_image_id = $id;
            $imageTranslation->language_id = $languageId;
            if ($imageTranslation->validate()) {
                $imageTranslation->save();
                if (Yii::$app->request->isPjax) {
                    $article = $image->article;
                    return $this->renderPartial('add-image', [
                        'selectedLanguage' => Language::findOne($languageId),
                        'article' => $article,
                        'image_form' => new ArticleImageForm(),
                    ]);
                }
            } else \Yii::$app->session->setFlash('error', \Yii::t('library', 'Edit image error'));
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Changes ArticleImage model position property to down
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionImageDown($id, $languageId)
    {
        $articleImage = ArticleImage::findOne($id);
        if (\Yii::$app->user->can('updateArticle', ['articleOwner' => Article::findOne($articleImage->article_id)->user_id])) {
            if (!empty($articleImage)) {

                /**
                 * @var $articleImage PositionBehavior|ArticleImage
                 */
                $articleImage->moveNext();
            }
            return $this->actionAddImage($articleImage->article_id, $languageId);
        } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
    }

    /**
     * Changes ArticleImage model position property to up
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionImageUp($id, $languageId)
    {
        $articleImage = ArticleImage::findOne($id);
        if (\Yii::$app->user->can('updateArticle', ['articleOwner' => Article::findOne($articleImage->article_id)->user_id])) {
            if (!empty($articleImage)) {

                /**
                 * @var $articleImage PositionBehavior|ArticleImage
                 */
                $articleImage->movePrev();
            }
            return $this->actionAddImage($articleImage->article_id, $languageId);
        } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
    }

    /**
     * Removes ArticleImage model and image files
     *
     * @param $id
     * @param $languageId
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage($id, $languageId)
    {
        if (!empty($id)) {
            $image = ArticleImage::findOne($id);

            if (!empty($image)) {
                $article = Article::findOne($image->article_id);

                if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user_id])) {
                    $image->delete();
                    \Yii::$app->get('library_imagable')->delete('article', $image->image_name);

                    if (Yii::$app->request->isPjax) {
                        return $this->renderPartial('add-image', [
                            'selectedLanguage' => Language::findOne($languageId),
                            'article' => $article,
                            'image_form' => new ArticleImageForm(),
                        ]);
                    }
                    return $this->redirect(\Yii::$app->request->referrer);
                } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
            }
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param integer $id ID of article
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAddVideo($id, $languageId)
    {
        $article = Article::findOne($id);
        if (!empty($article)) {

            if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user_id])) {

                $video = new ArticleVideo();
                $videoForm = new ArticleVideoForm();

                if (Yii::$app->request->isPost) {
                    $video->load(Yii::$app->request->post());
                    $videoForm->load(Yii::$app->request->post());
                    $videoForm->file_name = UploadedFile::getInstance($videoForm, 'file_name');

                    if ($fileName = $videoForm->upload()) {
                        $video->video_name = $fileName;
                        $video->resource = 'videofile';
                        $video->article_id = $id;
                        $video->save();
                    }
                    if ($video->resource == 'youtube') {
                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video->video_name, $match)) {
                            $video_name = $match[1];
                            $video->article_id = $id;
                            $video->video_name = $video_name;

                            if ($video->validate()) {
                                $video->save();
                            }
                        } else {
                            \Yii::$app->session->setFlash('error',
                                \Yii::t('library', 'Sorry, this format is not supported'));
                        }
                    } elseif ($video->resource == 'vimeo') {
                        $regexstr = '~
                        # Match Vimeo link and embed code
                        (?:&lt;iframe [^&gt;]*src=")?		# If iframe match up to first quote of src
                        (?:							        # Group vimeo url
                            https?:\/\/				        # Either http or https
                            (?:[\w]+\.)*			        # Optional subdomains
                            vimeo\.com				        # Match vimeo.com
                            (?:[\/\w]*\/videos?)?	        # Optional video sub directory this handles groups links also
                            \/						        # Slash before Id
                            ([0-9]+)				        # $1: VIDEO_ID is numeric
                            [^\s]*					        # Not a space
                        )							        # End group
                        "?							        # Match end quote if part of src
                        (?:[^&gt;]*&gt;&lt;/iframe&gt;)?	# Match the end of the iframe
                        (?:&lt;p&gt;.*&lt;/p&gt;)?		    # Match any title information stuff
                        ~ix';
                        if (preg_match($regexstr, $video->video_name, $match)) {
                            $video_name = $match[1];
                            $video->article_id = $id;
                            $video->video_name = $video_name;

                            if ($video->validate()) {
                                $video->save();
                            }
                        } else {
                            \Yii::$app->session->setFlash('error', \Yii::t('library', 'Sorry, this format is not supported'));
                        }
                    }
                }
                $params = [
                    'article' => $article,
                    'selectedLanguage' => Language::findOne($languageId),
                    'video_form_upload' => new ArticleVideoForm(),
                ];

                if (Yii::$app->request->isPjax) {
                    return $this->renderPartial('add-video', $params);
                }
                return $this->render('save', [
                    'article' => $article,
                    'viewName' => 'add-video',
                    'params' => $params]);
            } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
        } else throw new NotFoundHttpException();
    }

    /**
     * Changes ArticleVideo position to up
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionVideoUp($id, $languageId)
    {
        $articleVideo = ArticleVideo::findOne($id);
        $article = Article::findOne($articleVideo->article_id);
        if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user_id])) {
            if ($articleVideo) {
                /**
                 * @var $articleVideo PositionBehavior
                 */
                $articleVideo->movePrev();
            }

            if (\Yii::$app->request->isPjax) return $this->renderPartial('add-video', [
                'article' => $article,
                'selectedLanguage' => Language::findOne($languageId),
                'video_form_upload' => new ArticleVideoForm(),
            ]);
            else return $this->redirect(\Yii::$app->request->referrer);
        } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
    }

    /**
     * Changes ArticleVideo position to down
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionVideoDown($id, $languageId)
    {
        $articleVideo = ArticleVideo::findOne($id);
        $article = Article::findOne($articleVideo->article_id);
        if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user_id])) {
            if ($articleVideo) {
                /**
                 * @var $articleVideo PositionBehavior
                 */
                $articleVideo->moveNext();
            }

            if (\Yii::$app->request->isPjax) return $this->renderPartial('add-video', [
                'article' => $article,
                'selectedLanguage' => Language::findOne($languageId),
                'video_form_upload' => new ArticleVideoForm(),
            ]);
            else return $this->redirect(\Yii::$app->request->referrer);
        } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
    }

    /**
     * Deletes video
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDeleteVideo($id, $languageId)
    {
        if (!empty($id)) {
            $video = ArticleVideo::findOne($id);
            $article = Article::findOne($video->article_id);
            if (\Yii::$app->user->can('updateArticle', ['articleOwner' => $article->user_id])) {
                if ($video->resource == 'videofile') {
                    $dir = Yii::getAlias('@frontend/web/video');
                    unlink($dir . '/' . $video->video_name);
                }
                $video->delete();

                $params = [
                    'article' => $article,
                    'selectedLanguage' => Language::findOne($languageId),
                    'video_form_upload' => new ArticleVideoForm(),
                ];

                if (\Yii::$app->request->isPjax) return $this->renderPartial('add-video', $params);
                else return $this->redirect(\Yii::$app->request->referrer);
            } else throw new ForbiddenHttpException(\Yii::t('library', 'You have not permission to do this action.'));
        }
        return false;
    }
}
