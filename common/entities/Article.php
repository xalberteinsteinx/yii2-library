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
 * This is the model class for table "article".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $user_id
 * @property string $key
 * @property string $view_name
 * @property integer $position
 * @property integer $hits
 * @property integer $show
 * @property string $created_at
 * @property string $updated_at
 * @property string $publish_at
 *
 * @property ArticleCategory $category
 * @property User $user
 * @property ArticleAttachment[] $articleAttachments
 * @property ArticleImage[] $images
 * @property ArticleRelatedArticle[] $articleRelatedArticles
 * @property ArticleRelatedArticle[] $articleRelatedArticles0
 * @property ArticleTagArticle[] $articleTagArticles
 * @property ArticleTranslation[] $articleTranslations
 * @property ArticleVideo[] $videos
 */
class Article extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
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
                'translationClass' => ArticleTranslation::className(),
                'relationColumn' => 'article_id'
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => [
                    'category_id'
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
            [['category_id', 'user_id', 'position', 'hits', 'show'], 'integer'],
            [['category_id'], 'default', 'value' => NULL],
            [['position', 'publish_at'], 'required'],
            [['created_at', 'updated_at', 'publish_at'], 'safe'],
            [['key', 'view_name'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ArticleCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
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
            'category_id' => Yii::t('library', 'Category'),
            'user_id' => Yii::t('library', 'User'),
            'key' => Yii::t('library', 'Key'),
            'view_name' => Yii::t('library', 'View Name'),
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
    public function getCategory()
    {
        return $this->hasOne(ArticleCategory::className(), ['id' => 'category_id']);
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
    public function getArticleAttachments()
    {
        return $this->hasMany(ArticleAttachment::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(ArticleImage::className(), ['article_id' => 'id'])->orderBy('position');
    }

    /**
     * @return ArticleImage|ActiveRecord
     */
    public function getFirstImage()
    {
        $firstImage = ArticleImage::find()->where(['article_id' => $this->id])->one();
        return $firstImage;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleRelatedArticles()
    {
        return $this->hasMany(ArticleRelatedArticle::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleRelatedArticles0()
    {
        return $this->hasMany(ArticleRelatedArticle::className(), ['related_article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleTagArticles()
    {
        return $this->hasMany(ArticleTagArticle::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ArticleTranslation::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVideos()
    {
        return $this->hasMany(ArticleVideo::className(), ['article_id' => 'id'])->orderBy('position');
    }


    /**
     * Generates unique name by string.
     * @param $baseName
     * @return string
     */
    public static function generateImageName($baseName)
    {
        $fileName = hash('crc32', $baseName . time());
        if (file_exists(Yii::getAlias('@frontend/web/images/library/' . $fileName . '-original.jpg'))) {
            return static::generateImageName($baseName);
        }
        return $fileName;
    }
}
