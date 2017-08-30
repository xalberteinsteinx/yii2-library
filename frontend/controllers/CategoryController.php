<?php
namespace xalberteinsteinx\library\frontend\controllers;

use xalberteinsteinx\library\common\entities\ArticleCategory;
use xalberteinsteinx\library\frontend\behaviors\SeoBehavior;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CategoryController implements the CRUD actions for ArticleCategory model.
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class CategoryController extends Controller
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
     * Displays ArticleCategory model.
     *
     * @param   $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $id)
    {
        if (!empty($id)) {
            $category = ArticleCategory::find()->with('articles')->where(['id' => $id])->one();
            if (!empty($category)) {

                /**
                 * @var $this SeoBehavior
                 */
                $this->setMetaTags($category->translation);
                return $this->render('show', ['category' => $category]);
            }

        }

        throw new NotFoundHttpException();
    }
}