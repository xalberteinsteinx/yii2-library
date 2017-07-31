<?php
namespace xalberteinsteinx\library;

use xalberteinsteinx\library\common\entities\Article;
use xalberteinsteinx\library\common\entities\ArticleCategory;
use bl\multilang\entities\Language;
use Yii;
use yii\base\Object;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UrlManager;
use yii\web\UrlRuleInterface;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class UrlRule extends Object implements UrlRuleInterface
{

    /**
     * @var string
     */
    public $prefix = '';

    private $pathInfo;

    private $routes;

    private $routesCount;

    /**
     * @var
     */
    private $currentLanguage;

    /**
     * @var string
     */
    private $articleRoute = 'library/article/index';

    /**
     * @var string
     */
    private $categoryRoute = 'library/category/index';

    /**
     * Parses the given request and returns the corresponding route and parameters.
     * @param UrlManager $manager the URL manager
     * @param Request $request the request component
     * @return array|bool the parsing result. The route and the parameters are returned as an array.
     * If false, it means this rule cannot be used to parse this path info.
     * @throws NotFoundHttpException
     */
    public function parseRequest($manager, $request) {

        $this->currentLanguage = Language::getCurrent();
        $this->pathInfo = $request->getPathInfo();


        if($this->pathInfo == $this->categoryRoute || $this->pathInfo == $this->articleRoute) {


            Yii::$app->urlManager->language = $this->currentLanguage;

            if($this->createUrl(Yii::$app->urlManager, $this->pathInfo, $request->getQueryParams())) {
                throw new NotFoundHttpException(Yii::t('library', 'Page not found.'));
            }
            else if($this->pathInfo == $this->articleRoute && empty($request->getQueryParams()['id'])) {
                throw new NotFoundHttpException(Yii::t('library', 'Page not found.'));
            }
        }

        if(!empty($this->prefix)) {
            if(strpos($this->pathInfo, $this->prefix) === 0) {
                $this->pathInfo = substr($this->pathInfo, strlen($this->prefix));
            }
            else return false;
        }

        $this->initRoutes($this->pathInfo);

        $categoryId = null;

        for($i = 0; $i < $this->routesCount; $i++) {
            if($i === $this->routesCount - 1) {
                if($article = $this->findArticleByAlias($this->routes[$i], $categoryId, ['show' => true])) {
                    return ['/' . $this->articleRoute, ['id' => $article->id]];
                }
                else {
                    if($category = $this->findCategoryByAlias($this->routes[$i], $categoryId, ['show' => true])) {

                        return ['/' . $this->categoryRoute,['id' => $category->id]];
                    }
                    else return false;
                }
            }
            else {
                if($category = $this->findCategoryByAlias($this->routes[$i], $categoryId, ['show' => true])) {
                    $categoryId = $category->id;
                }
                else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @param $pathInfo
     */
    private function initRoutes($pathInfo) {
        $this->routes = explode('/', $pathInfo);
        $this->routesCount = count($this->routes);
    }

    /**
     * Finds Article model by alias property in related ArticleTranslation model
     *
     * @param $alias            string
     * @param $categoryId       integer
     * @param $options          array
     * @return array|null|\yii\db\ActiveRecord
     */
    private function findArticleByAlias(string $alias, $categoryId, array $options) {

        $article = Article::find()->joinWith('translations')
            ->where(array_merge(['category_id' => $categoryId, 'alias' => $alias], $options))
            ->one();

        return $article;
    }

    /**
     * Finds ArticleCategory model by alias property in related ArticleCategoryTranslation model
     *
     * @param $alias            string
     * @param $parentId         integer|NULL
     * @param $options          array
     * @return array|null|\yii\db\ActiveRecord
     */
    private function findCategoryByAlias(string $alias, $parentId, array $options) {

        $category = ArticleCategory::find()->joinWith('translations')
            ->where(array_merge(['alias' => $alias, 'parent_id' => $parentId], $options))
            ->one();

        return $category;

    }

    /**
     * Creates a URL according to the given route and parameters.
     * @param UrlManager $manager the URL manager
     * @param string $route the route. It should not have slashes at the beginning or the end.
     * @param array $params the parameters
     * @return string|boolean the created URL, or false if this rule cannot be used for creating this URL.
     */
    public function createUrl($manager, $route, $params)
    {

        if(($route == $this->articleRoute || $route == $this->categoryRoute) && !empty($params['id'])) {


            $id = $params['id'];
            $pathInfo = '';
            $parentId = null;
            $language = Language::findOne(['lang_id' => $manager->language]);

            if($route == $this->articleRoute) {
                $article = Article::findOne($id);

                if (!empty($article)) {
                    if($article->getTranslation($language->id) && $article->getTranslation($language->id)->alias) {
                        $pathInfo = $article->getTranslation($language->id)->alias;
                        $parentId = $article->category_id;
                    }

                }
                else return false;

            }

            else if($route == $this->categoryRoute) {
                $category = ArticleCategory::findOne($id);

                if($category->getTranslation($language->id) && $category->getTranslation($language->id)->alias) {
                    $pathInfo = $category->getTranslation($language->id)->alias;
                    $parentId = $category->parent_id;
                }
                else return false;
            }

            while($parentId != null) {
                $category = ArticleCategory::findOne($parentId);
                if($category->getTranslation($language->id) && $category->getTranslation($language->id)->alias) {
                    $pathInfo = $category->getTranslation($language->id)->alias . '/' . $pathInfo;
                    $parentId = $category->parent_id;
                }
                else return false;
            }

            if(!empty($this->prefix)) {
                $pathInfo = $this->prefix . $pathInfo;
            }

            return $pathInfo;
        }
        return false;
    }
}