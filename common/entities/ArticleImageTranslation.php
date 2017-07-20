<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\entities\Language;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_image_translation".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $article_image_id
 * @property integer $language_id
 * @property string $alt_text
 *
 * @property ArticleImage $articleImage
 * @property Language $language
 */
class ArticleImageTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_image_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_image_id', 'language_id'], 'required'],
            [['article_image_id', 'language_id'], 'integer'],
            [['alt_text'], 'string', 'max' => 255],
            [['article_image_id'], 'exist', 'skipOnError' => true, 'targetClass' => ArticleImage::className(), 'targetAttribute' => ['article_image_id' => 'id']],
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
            'article_image_id' => Yii::t('library', 'Article Image'),
            'language_id' => Yii::t('library', 'Language'),
            'alt_text' => Yii::t('library', 'Alt Text'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleImage()
    {
        return $this->hasOne(ArticleImage::className(), ['id' => 'article_image_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }
}
