<?php

namespace xalberteinsteinx\library\common\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_category_image".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $article_category_id
 * @property string $image_name
 * @property string $key
 * @property integer $position
 * @property integer $is_cover
 *
 * @property ArticleCategory $articleCategory
 * @property ArticleCategoryImageTranslation[] $articleCategoryImageTranslations
 */
class ArticleCategoryImage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_category_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_category_id', 'position', 'is_cover'], 'integer'],
            [['position'], 'required'],
            [['image_name', 'key'], 'string', 'max' => 255],
            [['article_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ArticleCategory::className(), 'targetAttribute' => ['article_category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('library', 'ID'),
            'article_category_id' => Yii::t('library', 'Article Category'),
            'image_name' => Yii::t('library', 'Image Name'),
            'key' => Yii::t('library', 'Key'),
            'position' => Yii::t('library', 'Position'),
            'is_cover' => Yii::t('library', 'Is cover'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleCategory()
    {
        return $this->hasOne(ArticleCategory::className(), ['id' => 'article_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleCategoryImageTranslations()
    {
        return $this->hasMany(ArticleCategoryImageTranslation::className(), ['image_id' => 'id']);
    }
}
