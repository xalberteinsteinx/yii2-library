<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\entities\Language;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_attachment_translation".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $article_attachment_id
 * @property integer $language_id
 * @property string $title
 * @property string $description
 *
 * @property ArticleAttachment $articleAttachment
 * @property Language $language
 */
class ArticleAttachmentTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_attachment_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_attachment_id', 'language_id'], 'required'],
            [['article_attachment_id', 'language_id'], 'integer'],
            [['title', 'description'], 'string', 'max' => 255],
            [['article_attachment_id'], 'exist', 'skipOnError' => true, 'targetClass' => ArticleAttachment::className(), 'targetAttribute' => ['article_attachment_id' => 'id']],
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
            'article_attachment_id' => Yii::t('library', 'Article Attachment'),
            'language_id' => Yii::t('library', 'Language'),
            'title' => Yii::t('library', 'Title'),
            'description' => Yii::t('library', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleAttachment()
    {
        return $this->hasOne(ArticleAttachment::className(), ['id' => 'article_attachment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }
}
