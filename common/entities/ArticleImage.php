<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "article_image".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $article_id
 * @property string $image_name
 * @property integer $position
 * @property integer $is_cover
 *
 * @property Article $article
 * @property ArticleImageTranslation[] $articleImageTranslations
 */
class ArticleImage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_image';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ArticleImageTranslation::className(),
                'relationColumn' => 'article_image_id'
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
            [['article_id', 'image_name', 'position', 'is_cover'], 'required'],
            [['article_id', 'position', 'is_cover'], 'integer'],
            [['image_name'], 'string', 'max' => 255],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::className(), 'targetAttribute' => ['article_id' => 'id']],
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
            'image_name' => Yii::t('library', 'Image Name'),
            'position' => Yii::t('library', 'Position'),
            'is_cover' => Yii::t('library', 'Is cover'),
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
    public function getArticleImageTranslations()
    {
        return $this->hasMany(ArticleImageTranslation::className(), ['article_image_id' => 'id']);
    }
}
