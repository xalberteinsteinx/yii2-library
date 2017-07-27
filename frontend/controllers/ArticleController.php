<?php
namespace xalberteinsteinx\library\frontend\controllers;

use xalberteinsteinx\library\common\entities\Article;
use xalberteinsteinx\library\common\search\ArticleSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ArticleController extends Controller
{
    /**
     * Lists all ArticleCategory models.
     * @return mixed
     */
    public function actionIndex()
    {

        if ($this->module->enableIndexArticleAction) {
            $searchModel = new ArticleSearch();
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
            $article = Article::findOne($id);
            if (!empty($article)) {
                return $this->render('show', ['article' => $article]);
            }

        }
        throw new NotFoundHttpException();
    }
}