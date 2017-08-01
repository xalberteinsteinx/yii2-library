<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "article_attachment".
 * @author Albert Gainutdinov
 *
 * @property integer                            $id
 * @property integer                            $article_id
 * @property string                             $file_name
 * @property integer                            $hits
 *
 * @property Article                            $article
 * @property ArticleAttachmentTranslation[]     $translations
 * @property ArticleAttachmentTranslation       $translation
 */
class ArticleAttachment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_attachment';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ArticleAttachmentTranslation::className(),
                'relationColumn' => 'article_attachment_id'
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
            [['article_id', 'file_name'], 'required'],
            [['article_id', 'hits'], 'integer'],
            [['file_name'], 'string', 'max' => 255],
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
            'file_name' => Yii::t('library', 'File Name'),
            'hits' => Yii::t('library', 'Hits'),
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
        return $this->hasMany(ArticleAttachmentTranslation::className(), ['article_attachment_id' => 'id']);
    }
}
