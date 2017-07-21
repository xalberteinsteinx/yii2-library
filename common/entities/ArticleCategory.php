<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use dektrium\user\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "article_category".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $user_id
 * @property string $key
 * @property string $view_name
 * @property string $article_view_name
 * @property integer $position
 * @property integer $hits
 * @property integer $show
 * @property string $created_at
 * @property string $updated_at
 * @property string $publish_at
 *
 * @property Article[] $articles
 * @property ArticleCategory $parent
 * @property ArticleCategory[] $articleCategories
 * @property User $user
 * @property ArticleCategoryImage[] $articleCategoryImages
 * @property ArticleCategoryTranslation[] $articleCategoryTranslations
 */
class ArticleCategory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_category';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ArticleCategoryTranslation::className(),
                'relationColumn' => 'article_category_id'
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => [
                    'parent_id'
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
            [['parent_id', 'user_id', 'position', 'hits', 'show'], 'integer'],
            [['show', 'publish_at'], 'required'],
            [['created_at', 'updated_at', 'publish_at'], 'safe'],
            [['key', 'view_name', 'article_view_name'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => ArticleCategory::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('library', 'ID'),
            'parent_id' => Yii::t('library', 'Parent'),
            'user_id' => Yii::t('library', 'User'),
            'key' => Yii::t('library', 'Key'),
            'view_name' => Yii::t('library', 'View Name'),
            'article_view_name' => Yii::t('library', 'Article View Name'),
            'position' => Yii::t('library', 'Position'),
            'hits' => Yii::t('library', 'Hits'),
            'show' => Yii::t('library', 'Show'),
            'created_at' => Yii::t('library', 'Created At'),
            'updated_at' => Yii::t('library', 'Updated At'),
            'publish_at' => Yii::t('library', 'Publish At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(ArticleCategory::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleCategories()
    {
        return $this->hasMany(ArticleCategory::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleCategoryImages()
    {
        return $this->hasMany(ArticleCategoryImage::className(), ['article_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ArticleCategoryTranslation::className(), ['article_category_id' => 'id']);
    }
}
