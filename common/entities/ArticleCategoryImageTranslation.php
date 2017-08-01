<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\entities\Language;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_category_image_translation".
 * @author Albert Gainutdinov
 *
 * @property integer                $id
 * @property integer                $image_id
 * @property integer                $language_id
 * @property string                 $alt_text
 *
 * @property ArticleCategoryImage   $image
 * @property Language               $language
 */
class ArticleCategoryImageTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_category_image_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['image_id', 'language_id'], 'integer'],
            [['alt_text'], 'string', 'max' => 255],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => ArticleCategoryImage::className(), 'targetAttribute' => ['image_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('library', 'ID'),
            'image_id' => Yii::t('library', 'Image'),
            'language_id' => Yii::t('library', 'Language'),
            'alt-text' => Yii::t('library', 'Alt text'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(ArticleCategoryImage::className(), ['id' => 'image_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }
}
