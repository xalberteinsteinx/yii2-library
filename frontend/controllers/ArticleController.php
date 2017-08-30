<?php
namespace xalberteinsteinx\library\frontend\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use xalberteinsteinx\library\common\entities\Article;
use xalberteinsteinx\library\frontend\behaviors\SeoBehavior;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ArticleController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'staticPage' => [
                'class' => SeoBehavior::className(),
                'replace_pairs_in_title' => []
            ]
        ];
    }

    /**
     * Displays Article model.
     *
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $id)
    {

        if (!empty($id)) {
            $article = Article::findOne($id);

            if (!empty($article)) {

                /**
                 * @var $this SeoBehavior
                 */
                $this->setMetaTags($article->translation);
                return $this->render('show', ['article' => $article]);
            }

        }

        throw new NotFoundHttpException();
    }

}