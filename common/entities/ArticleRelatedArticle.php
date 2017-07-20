<?php

namespace xalberteinsteinx\library\common\entities;

use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "article_related_article".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $related_article_id
 * @property integer $position
 *
 * @property Article $article
 * @property Article $relatedArticle
 */
class ArticleRelatedArticle extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_related_article';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
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
            [['article_id', 'related_article_id', 'position'], 'required'],
            [['article_id', 'related_article_id', 'position'], 'integer'],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::className(), 'targetAttribute' => ['article_id' => 'id']],
            [['related_article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::className(), 'targetAttribute' => ['related_article_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('library', 'ID'),
            'article_id' => Yii::t('library', 'Article'),
            'related_article_id' => Yii::t('library', 'Related Article'),
            'position' => Yii::t('library', 'Position'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedArticle()
    {
        return $this->hasOne(Article::className(), ['id' => 'related_article_id']);
    }
}
