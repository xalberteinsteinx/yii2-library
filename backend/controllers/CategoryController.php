<?php

namespace xalberteinsteinx\library\backend\controllers;

use bl\multilang\entities\Language;
use xalberteinsteinx\library\common\entities\ArticleCategoryTranslation;
use Yii;
use xalberteinsteinx\library\common\entities\ArticleCategory;
use xalberteinsteinx\library\common\search\ArticleCategorySearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArticleCategoryController implements the CRUD actions for ArticleCategory model.
 */
class CategoryController extends Controller
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
                        'roles' => ['viewListOfCategories'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'save',
                            'up', 'down', 'generate-seo-url',
                            'add-image', 'delete-image', 'edit-image',
                            'image-up', 'image-down'
                        ],
                        'roles' => ['createCategory', 'updateCategory'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['delete'],
                        'roles' => ['deleteCategory'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ArticleCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates new or updates existing ArticleCategory and ArticleCategoryTranslation models.
     * @param null|integer $id
     * @param null|integer $languageId
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionSave($id = null, $languageId = null)
    {
        $selectedLanguage = (!empty($languageId)) ? Language::findOne($languageId) : Language::getCurrent();

        //Getting or creating ArticleCategory and ArticleCategoryTranslation models
        if (!empty($id)) {
            $articleCategory = ArticleCategory::findOne($id);
            if (!empty($articleCategory)) {
                $articleCategoryTranslation = ArticleCategoryTranslation::find()->where([
                        'article_category_id' => $id,
                        'language_id' => $languageId
                    ])->one() ?? new ArticleCategoryTranslation();
            } else throw new NotFoundHttpException();
        } else {
            $articleCategory = new ArticleCategory();
            $articleCategoryTranslation = new ArticleCategoryTranslation();
        }

        /*POST*/
        if (Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            if ($articleCategory->load($post)) {

                /*Position*/
                if ($articleCategory->parent_id == null) {
                    $articleCategory->position = ArticleCategory::find()
                            ->select('position')->where(['parent_id' => null])->max('position') + 1;
                }

                if ($articleCategory->isNewRecord) {
                    $articleCategory->user_id = Yii::$app->user->id;

                    if ($articleCategory->validate()) $articleCategory->save();
                    else Yii::$app->session->setFlash(
                        'error',
                        \Yii::t('library', 'An error occurred during the save of the category'));
                } else {
                    if (!$articleCategory->validate())
                        Yii::$app->session->setFlash(
                            'error',
                            \Yii::t('library', 'An error occurred during the save of the category'));
                }

                if ($articleCategoryTranslation->load($post)) {

                    /*Sets alias*/
                    if (empty($articleCategoryTranslation->alias)) {
                        $articleCategoryTranslation->alias = ArticleCategoryTranslation::generateAlias($articleCategoryTranslation->title);
                    }

                    $articleCategory->save();
                    $articleCategoryTranslation->article_category_id = $articleCategory->id;
                    $articleCategoryTranslation->language_id = $selectedLanguage->id;

                    if ($articleCategoryTranslation->validate()) {
                        $articleCategoryTranslation->save();

                        Yii::$app->session->setFlash(
                            'success',
                            \Yii::t('library', 'You have successfully save this article'));
                        return $this->redirect(Url::to(['save', 'id' => $articleCategory->id, 'languageId' => $selectedLanguage->id]));
                    } else Yii::$app->session->setFlash(
                        'error',
                        \Yii::t('library', 'An error occurred during the save of the article'));
                }
            }
        }
        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('add-basic', [
                'selectedLanguage' => $selectedLanguage,
                'category' => $articleCategory,
                'categoryTranslation' => $articleCategoryTranslation,
            ]);
        } else {
            return $this->render('save', [
                'category' => $articleCategory,
                'viewName' => 'add-basic',
                'params' => [
                    'selectedLanguage' => $selectedLanguage,
                    'category' => $articleCategory,
                    'categoryTranslation' => $articleCategoryTranslation,
                ]
            ]);
        }
    }

}
