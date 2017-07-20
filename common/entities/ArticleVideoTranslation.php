<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\entities\Language;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_video_translation".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $article_video_id
 * @property integer $language_id
 * @property string $description
 *
 * @property ArticleVideo $articleVideo
 * @property Language $language
 */
class ArticleVideoTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_video_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_video_id', 'language_id'], 'required'],
            [['article_video_id', 'language_id'], 'integer'],
            [['description'], 'string'],
            [['article_video_id'], 'exist', 'skipOnError' => true, 'targetClass' => ArticleVideo::className(), 'targetAttribute' => ['article_video_id' => 'id']],
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
            'article_video_id' => Yii::t('library', 'Article Video'),
            'language_id' => Yii::t('library', 'Language'),
            'description' => Yii::t('library', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleVideo()
    {
        return $this->hasOne(ArticleVideo::className(), ['id' => 'article_video_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }
}
