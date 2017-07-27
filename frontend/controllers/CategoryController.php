<?php
namespace xalberteinsteinx\library\frontend\controllers;

use xalberteinsteinx\library\common\entities\ArticleCategory;
use xalberteinsteinx\library\common\search\ArticleCategorySearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CategoryController implements the CRUD actions for ArticleCategory model.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryController extends Controller
{

    /**
     * Lists all ArticleCategory models.
     * @return mixed
     */
    public function actionIndex()
    {

        if ($this->module->enableIndexCategoryAction) {
            $searchModel = new ArticleCategorySearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        else return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * Displays article category
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionShow(int $id)
    {
        if (!empty($id)) {
            $category = ArticleCategory::find()->with('articles')->where(['id' => $id])->one();
            if (!empty($category)) {
                return $this->render('show', ['category' => $category]);
            }

        }
        throw new NotFoundHttpException();
    }
}