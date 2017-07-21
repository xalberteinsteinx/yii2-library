<?php

namespace xalberteinsteinx\library\backend\controllers;

use bl\multilang\entities\Language;
use xalberteinsteinx\library\common\entities\ArticleTranslation;
use Yii;
use xalberteinsteinx\library\common\entities\Article;
use xalberteinsteinx\library\common\search\ArticleSearch;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
                        'roles' => ['viewArticlesList'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'save',
                            'add-image', 'delete-image', 'edit-image',
                            'add-video', 'delete-video',
                            'image-up', 'image-down',
                            'up', 'down', 'generate-seo-url',
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
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

    public function actionSave($id = null, $languageId = null)
    {
        $selectedLanguage = (!empty($languageId)) ? Language::findOne($languageId) : Language::getCurrent();

        //Getting or creating Product and ProductTranslation models
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

        if (Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            if ($article->load($post)) {
                $article->category_id = (!empty($article->category_id)) ? $article->category_id : NULL;
                if ($article->isNewRecord) {
                    $article->user = Yii::$app->user->id;

                    if ($article->validate()) $article->save();
                    else Yii::$app->session->setFlash(
                        'error',
                        \Yii::t('library', 'An error occurred during the creation of the article'));
                } else {
                    if (!$article->validate())
                        Yii::$app->session->setFlash(
                            'error',
                            \Yii::t('library', 'An error occurred during the creation of the article'));
                }

                if ($articleTranslation->load($post)) {
                    //Sets alias
                    if (empty($articleTranslation->alias)) {
                        $articleTranslation->alias = Inflector::slug($articleTranslation->title);
                    }
                    $article->save();
                    $articleTranslation->article_id = $article->id;
                    $articleTranslation->language_id = $selectedLanguage->id;

                    if ($articleTranslation->validate()) {
                        $articleTranslation->save();

                        return $this->redirect(Url::to(['save', 'id' => $article->id, 'languageId' => $selectedLanguage->id]));
                    }
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
     * Displays a single Article model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Article();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
