<?php

namespace xalberteinsteinx\library\common\entities;

use bl\imagable\helpers\FileHelper;
use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "article_image".
 * @author Albert Gainutdinov
 *
 * @property integer                        $id
 * @property integer                        $article_id
 * @property string                         $image_name
 * @property integer                        $position
 * @property integer                        $is_cover
 *
 * @property Article                        $article
 *
 * @property ArticleImageTranslation[]      $translations
 * @property ArticleImageTranslation        $translation
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
            [['article_id', 'image_name'], 'required'],
            [['article_id', 'position', 'is_cover'], 'integer'],
            [['image_name'], 'string', 'max' => 255],
            [['is_cover'], 'default', 'value' => 0],
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
    public function getTranslations()
    {
        return $this->hasMany(ArticleImageTranslation::className(), ['article_image_id' => 'id']);
    }

    /**
     * @param string $size image size.
     * @return string path to product image.
     */
    public function getBySize($size = 'hd') {
        $image = \Yii::$app->library_imagable->get('article', $size, $this->image_name);
        return '/images/library/article/' . FileHelper::getFullName($image);
    }
}
