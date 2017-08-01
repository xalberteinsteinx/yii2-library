<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "article_tag".
 * @author Albert Gainutdinov
 *
 * @property integer                    $id
 *
 * @property ArticleTagArticle[]        $articleTagArticles
 * @property ArticleTagTranslation      $translation
 * @property ArticleTagTranslation[]    $translations
 */
class ArticleTag extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_tag';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ArticleTagTranslation::className(),
                'relationColumn' => 'article_tag_id'
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => [
                    'article_id'
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('library', 'ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleTagArticles()
    {
        return $this->hasMany(ArticleTagArticle::className(), ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ArticleTagTranslation::className(), ['article_tag_id' => 'id']);
    }
}
