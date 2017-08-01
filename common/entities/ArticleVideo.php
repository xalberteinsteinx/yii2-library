<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "article_video".
 * @author Albert Gainutdinov
 *
 * @property integer                        $id
 * @property integer                        $article_id
 * @property string                         $video_name
 * @property string                         $resource
 * @property integer                        $position
 * @property integer                        $auto_play
 * @property integer                        $loop
 * @property string                         $poster
 *
 * @property Article                        $article
 * @property ArticleVideoTranslation[]      $translations
 * @property ArticleVideoTranslation        $translation
 */
class ArticleVideo extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_video';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ArticleVideoTranslation::className(),
                'relationColumn' => 'article_video_id'
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
            [['article_id', 'video_name'], 'required'],
            [['article_id', 'position', 'auto_play', 'loop'], 'integer'],
            [['auto_play', 'loop'], 'default', 'value' => 0],
            [['video_name', 'resource', 'poster'], 'string', 'max' => 255],
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
            'video_name' => Yii::t('library', 'Video Name'),
            'resource' => Yii::t('library', 'Resource'),
            'position' => Yii::t('library', 'Position'),
            'auto_play' => Yii::t('library', 'Auto Play'),
            'loop' => Yii::t('library', 'Loop'),
            'poster' => Yii::t('library', 'Poster'),
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
        return $this->hasMany(ArticleVideoTranslation::className(), ['article_video_id' => 'id']);
    }
}
