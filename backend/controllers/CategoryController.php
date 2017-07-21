<?php

namespace xalberteinsteinx\library\backend\controllers;

use bl\multilang\entities\Language;
use xalberteinsteinx\library\backend\components\forms\CategoryImageForm;
use xalberteinsteinx\library\common\entities\ArticleCategoryImage;
use xalberteinsteinx\library\common\entities\ArticleCategoryImageTranslation;
use xalberteinsteinx\library\common\entities\ArticleCategoryTranslation;
use Yii;
use xalberteinsteinx\library\common\entities\ArticleCategory;
use xalberteinsteinx\library\common\search\ArticleCategorySearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii2tech\ar\position\PositionBehavior;

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
                }
                if (!$articleCategory->validate()) {
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


    /**
     * Changes article position to up
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUp($id)
    {
        $category = ArticleCategory::findOne($id);
        if (!empty($category)) {
            /**
             * @var $category PositionBehavior
             */
            $category->movePrev();
        }
        if (\Yii::$app->request->isPjax) return $this->actionIndex();
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Changes category position to down
     *
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDown($id)
    {
        $category = ArticleCategory::findOne($id);
        if (!empty($category)) {
            /**
             * @var $category PositionBehavior
             */
            $category->moveNext();
        }
        if (\Yii::$app->request->isPjax) return $this->actionIndex();
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Deletes an existing ArticleCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $category = ArticleCategory::findOne($id);
        if (empty($category)) throw new NotFoundHttpException();
        $category->delete();

        \Yii::$app->session->setFlash('success', Yii::t('library', 'You have successfully removed this category'));

        if (\Yii::$app->request->isPjax) return $this->actionIndex();
        return $this->redirect('index');
    }


    /**
     * Generates seo Url from title on add-basic page
     *
     * @param string $title
     * @return string
     */
    public function actionGenerateSeoUrl($title)
    {
        $newAlias = ArticleCategoryTranslation::generateAlias($title);
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
        $category = ArticleCategory::findOne($id);

        if (empty($category)) throw new NotFoundHttpException();

        $image_form = new CategoryImageForm();
        $image = new ArticleCategoryImage();
        $imageTranslation = new ArticleCategoryImageTranslation();

            if (Yii::$app->request->isPost) {
                $image_form->load(Yii::$app->request->post());
                $image_form->image = UploadedFile::getInstance($image_form, 'image');
                if (!empty($image_form->image)) {
                    if ($uploadedImageName = $image_form->upload()) {
                        $image->image_name = $uploadedImageName;
                        $imageTranslation->alt_text = $image_form->alt2;
                        $image->article_category_id = $id;

                        if ($image->validate()) {
                            $image->save();
                            $imageTranslation->image_id = $image->id;
                            $imageTranslation->language_id = $languageId;
                            if ($imageTranslation->validate()) {
                                $imageTranslation->save();
                            }
                        }
                        else Yii::$app->session->setFlash('error', Yii::t('library', 'Error during the saving image'));
                    }
                }
                if (!empty($image_form->link)) {
                    $image_name = $image_form->copy($image_form->link);
                    $image->image_name = $image_name;
                    $imageTranslation->alt_text = $image_form->alt1;
                    $image->article_category_id = $id;
                    if ($image->validate()) {
                        $image->save();
                        $imageTranslation->image_id = $image->id;
                        $imageTranslation->language_id = $languageId;
                        if ($imageTranslation->validate()) {
                            $imageTranslation->save();
                        }
                    }
                    else Yii::$app->session->setFlash('error', Yii::t('library', 'Error during the saving image'));
                }
            }
            $params = [
                'selectedLanguage' => Language::findOne($languageId),
                'category' => $category,
                'image_form' => new CategoryImageForm(),
            ];
            if (Yii::$app->request->isPjax) {
                return $this->renderPartial('add-image', $params);
            }
            return $this->render('save', [
                'category' => $category,
                'viewName' => 'add-image',
                'params' => $params
            ]);
    }

    /**
     * @param $id
     * @param $languageId
     * @return string|\yii\web\Response
     */
    public function actionEditImage($id, $languageId)
    {
        if (Yii::$app->request->isPost) {
            $image = ArticleCategoryImage::findOne($id);
            $imageTranslation = ArticleCategoryImageTranslation::find()->where([
                'image_id' => $id,
                'language_id' => $languageId
            ])->one();
            if (empty($imageTranslation)) {
                $imageTranslation = new ArticleCategoryImageTranslation();
            }
            $imageTranslation->load(Yii::$app->request->post());
            $imageTranslation->image_id = $id;
            $imageTranslation->language_id = $languageId;
            if ($imageTranslation->validate()) {
                $imageTranslation->save();
                if (Yii::$app->request->isPjax) {
                    $category = $image->category;
                    return $this->renderPartial('add-image', [
                        'selectedLanguage' => Language::findOne($languageId),
                        'category' => $category,
                        'image_form' => new ArticleCategoryImage(),
                    ]);
                }
            } else \Yii::$app->session->setFlash('error', \Yii::t('library', 'Edit image error'));
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Changes ArticleCategoryImage model position property to down
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionImageDown($id, $languageId)
    {
        $image = ArticleCategoryImage::findOne($id);
        if (!empty($image)) {

            /**
             * @var $image PositionBehavior|ArticleCategoryImage
             */
            $image->moveNext();
        }
        return $this->actionAddImage($image->article_category_id, $languageId);
    }

    /**
     * Changes ArticleCategoryImage model position property to up
     *
     * @param integer $id
     * @param integer $languageId
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionImageUp($id, $languageId)
    {
        $image = ArticleCategoryImage::findOne($id);
        if (!empty($image)) {

            /**
             * @var $image PositionBehavior|ArticleCategoryImage
             */
            $image->movePrev();
        }
        return $this->actionAddImage($image->article_category_id, $languageId);
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
            $image = ArticleCategoryImage::findOne($id);

            if (!empty($image)) {
                $category = ArticleCategory::findOne($image->article_category_id);

                $image->delete();
                \Yii::$app->get('library_imagable')->delete('category', $image->image_name);

                if (Yii::$app->request->isPjax) {
                    return $this->renderPartial('add-image', [
                        'selectedLanguage' => Language::findOne($languageId),
                        'category' => $category,
                        'image_form' => new CategoryImageForm(),
                    ]);
                }
                return $this->redirect(\Yii::$app->request->referrer);
            }
        }
        throw new NotFoundHttpException();
    }

}
