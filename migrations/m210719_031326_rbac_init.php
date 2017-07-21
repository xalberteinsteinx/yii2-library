<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

use xalberteinsteinx\library\common\components\rbac\ArticleOwnerRule;
use yii\db\Migration;

class m210719_031326_rbac_init extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        /*ARTICLES*/
        /*Add permissions*/
        $viewListOfArticles = $auth->createPermission('viewListOfArticles');
        $viewListOfArticles->description = ('View list of articles');
        $auth->add($viewListOfArticles);

        $viewCompleteListOfArticles = $auth->createPermission('viewCompleteListOfArticles');
        $viewCompleteListOfArticles->description = ('View complete list of articles');
        $auth->add($viewCompleteListOfArticles);


        $createArticle = $auth->createPermission('createArticle');
        $createArticle->description = 'Create article';
        $auth->add($createArticle);

        $updateArticle = $auth->createPermission('updateArticle');
        $updateArticle->description = 'Update article';
        $auth->add($updateArticle);

        $deleteArticle = $auth->createPermission('deleteArticle');
        $deleteArticle->description = 'Delete article';
        $auth->add($deleteArticle);

        $createArticleWithoutModeration = $auth->createPermission('createArticleWithoutModeration');
        $createArticleWithoutModeration->description = 'Create article without moderation';
        $auth->add($createArticleWithoutModeration);


        /*Add the rule*/
        $rule = new ArticleOwnerRule;
        $auth->add($rule);
        $updateOwnArticle = $auth->createPermission('updateOwnArticle');
        $updateOwnArticle->description = 'Update own article';
        $updateOwnArticle->ruleName = $rule->name;
        $auth->add($updateOwnArticle);

        $deleteOwnArticle = $auth->createPermission('deleteOwnArticle');
        $deleteOwnArticle->description = 'Delete own article';
        $deleteOwnArticle->ruleName = $rule->name;
        $auth->add($deleteOwnArticle);


        /*Add roles*/
        $articlePartner = $auth->createRole('articlePartner');
        $articlePartner->description = 'Article Partner';
        $auth->add($articlePartner);

        $articleManager = $auth->createRole('articleManager');
        $articleManager->description = 'Article Manager';
        $auth->add($articleManager);

        $auth->addChild($updateArticle, $updateOwnArticle);
        $auth->addChild($deleteArticle, $deleteOwnArticle);
        $auth->addChild($articlePartner, $createArticle);
        $auth->addChild($articlePartner, $updateOwnArticle);
        $auth->addChild($articlePartner, $deleteOwnArticle);
        $auth->addChild($articleManager, $updateArticle);
        $auth->addChild($articleManager, $deleteArticle);
        $auth->addChild($createArticleWithoutModeration, $createArticle);
        $auth->addChild($articleManager, $createArticleWithoutModeration);
        $auth->addChild($articleManager, $articlePartner);

        $auth->addChild($articlePartner, $viewListOfArticles);
        $auth->addChild($articleManager, $viewListOfArticles);
        $auth->addChild($articleManager, $viewCompleteListOfArticles);


        /*CATEGORIES*/
        /*Add permissions*/
        $createCategory = $auth->createPermission('createCategory');
        $createCategory->description = 'Create category';
        $auth->add($createCategory);
        $updateCategory = $auth->createPermission('updateCategory');
        $updateCategory->description = 'Update category';
        $auth->add($updateCategory);
        $deleteCategory = $auth->createPermission('deleteCategory');
        $deleteCategory->description = 'Delete category';
        $auth->add($deleteCategory);


        /*Add roles*/
        $articleCategoryManager = $auth->createRole('articleCategoryManager');
        $articleCategoryManager->description = 'Article category manager';
        $auth->add($articleCategoryManager);

        $auth->addChild($articleCategoryManager, $createCategory);
        $auth->addChild($articleCategoryManager, $updateCategory);
        $auth->addChild($articleCategoryManager, $deleteCategory);

        $libraryAdministrator = $auth->createRole('libraryAdministrator');
        $libraryAdministrator->description = 'Library administrator';
        $auth->add($libraryAdministrator);

        $auth->addChild($libraryAdministrator, $articleCategoryManager);
        $auth->addChild($libraryAdministrator, $articleManager);


    }
    public function down()
    {
        Yii::$app->authManager->removeAll();
    }
}