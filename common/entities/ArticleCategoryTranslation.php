<?php

namespace xalberteinsteinx\library\common\entities;

use bl\multilang\entities\Language;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * This is the model class for table "article_category_translation".
 * @author Albert Gainutdinov
 *
 * @property integer $id
 * @property integer $article_category_id
 * @property integer $language_id
 * @property string $title
 * @property string $intro_text
 * @property string $full_text
 * @property string $seo_title
 * @property integer $alias
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $meta_robots
 * @property string $meta_author
 * @property string $meta_copyright
 *
 * @property ArticleCategory $articleCategory
 * @property Language $language
 */
class ArticleCategoryTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_category_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_category_id', 'language_id', 'title'], 'required'],
            [['article_category_id', 'language_id'], 'integer'],
            [['intro_text', 'full_text'], 'string'],
            [['title', 'meta_author', 'meta_copyright', 'alias'], 'string', 'max' => 255],
            [['seo_title'], 'string', 'max' => 80],
            [['meta_keywords', 'meta_description'], 'string', 'max' => 200],
            [['meta_robots'], 'string', 'max' => 20],
            [['article_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ArticleCategory::className(), 'targetAttribute' => ['article_category_id' => 'id']],
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
            'article_category_id' => Yii::t('library', 'Article Category'),
            'language_id' => Yii::t('library', 'Language'),
            'title' => Yii::t('library', 'Title'),
            'intro_text' => Yii::t('library', 'Intro Text'),
            'full_text' => Yii::t('library', 'Full Text'),
            'seo_title' => Yii::t('library', 'Seo Title'),
            'alias' => Yii::t('library', 'Alias'),
            'meta_keywords' => Yii::t('library', 'Meta Keywords'),
            'meta_description' => Yii::t('library', 'Meta Description'),
            'meta_robots' => Yii::t('library', 'Meta Robots'),
            'meta_author' => Yii::t('library', 'Meta Author'),
            'meta_copyright' => Yii::t('library', 'Meta Copyright'),
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
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * Generates alias from title
     * @param $title
     * @return string
     */
    public static function generateAlias($title)
    {
        $newAlias = Inflector::slug($title);
        $existingAlias = ArticleCategoryTranslation::find()
            ->where(['alias' => $newAlias])
            ->one();
        if (!empty($existingAlias)) $newAlias = $newAlias . '-' . date("d-m-y-H-i-s");
        return $newAlias;
    }
}
