<?php
namespace xalberteinsteinx\library\common\components\rbac;

use xalberteinsteinx\library\common\entities\Article;
use yii\rbac\Rule;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ArticleOwnerRule extends Rule
{
    public $name = 'isArticleOwner';

    /**
     * @param string|integer $userId the user ID.
     * @param Article $articleOwner Id of article's owner
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean
     */
    public function execute($userId, $articleOwner, $params)
    {
        if (\Yii::$app->user->isGuest) return false;
        else return array_key_exists('articleOwner', $params) ? $params['articleOwner'] == $userId : false;
    }
}