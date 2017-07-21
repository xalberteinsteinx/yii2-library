<?php

use yii\db\Migration;

class m170719_211926_init extends Migration
{
    public function safeUp()
    {
        $seoTitleLength = 80;
        $metaKeyWordsFieldLength = 200;
        $metaDescriptionFieldLength = 200;
        $metaRobotsLength = 20;

        /*Categories tables*/
        $this->createTable('{{%article_category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'user_id' => $this->integer(),
            'key' => $this->string(),
            'view_name' => $this->string(),
            'article_view_name' => $this->string(),
            'position' => $this->integer()->notNull(),
            'hits' => $this->integer(),
            'show' => $this->boolean()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'publish_at' => $this->timestamp()->notNull()
        ]);
        $this->createTable('{{%article_category_image}}', [
            'id' => $this->primaryKey(),
            'article_category_id' => $this->integer(),
            'key' => $this->string(),
            'position' => $this->integer()->notNull(),
        ]);
        $this->createTable('{{%article_category_translation}}', [
            'id' => $this->primaryKey(),
            'article_category_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'intro_text' => $this->text(),
            'full_text' => $this->text(),
            'seo_title' => $this->string($seoTitleLength),
            'alias' => $this->string()->notNull(),
            'meta_keywords' => $this->string($metaKeyWordsFieldLength),
            'meta_description' => $this->string($metaDescriptionFieldLength),
            'meta_robots' => $this->string($metaRobotsLength),
            'meta_author' => $this->string(),
            'meta_copyright' => $this->string(),
        ]);

        /*Articles tables*/
        $this->createTable('{{%article}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(),
            'user_id' => $this->integer(),
            'key' => $this->string(),
            'view_name' => $this->string(),
            'position' => $this->integer()->notNull(),
            'hits' => $this->integer(),
            'show' => $this->boolean(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'publish_at' => $this->timestamp()->notNull()
        ]);
        $this->createTable('{{%article_translation}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'intro_text' => $this->text(),
            'full_text' => $this->text(),
            'seo_title' => $this->string($seoTitleLength),
            'alias' => $this->string()->notNull(),
            'meta_keywords' => $this->string($metaKeyWordsFieldLength),
            'meta_description' => $this->string($metaDescriptionFieldLength),
            'meta_robots' => $this->string($metaRobotsLength),
            'meta_author' => $this->string(),
            'meta_copyright' => $this->string()
        ]);

        /*Articles images tables*/
        $this->createTable('{{%article_image}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'image_name' => $this->string()->notNull(),
            'position' => $this->integer()->notNull(),
            'is_cover' => $this->boolean()->notNull()
        ]);
        $this->createTable('{{%article_image_translation}}', [
            'id' => $this->primaryKey(),
            'article_image_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'alt_text' => $this->string()
        ]);

        /*Articles video tables*/
        $this->createTable('{{%article_video}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'video_name' => $this->string()->notNull(),
            'resource' => $this->string(),
            'position' => $this->integer()->notNull(),
            'auto_play' => $this->boolean()->notNull(),
            'loop' => $this->boolean()->notNull(),
            'poster' => $this->string()
        ]);
        $this->createTable('{{%article_video_translation}}', [
            'id' => $this->primaryKey(),
            'article_video_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'description' => $this->text()
        ]);

        /*Articles tags tables*/
        $this->createTable('{{%article_tag}}', [
            'id' => $this->primaryKey()
        ]);
        $this->createTable('{{%article_tag_article}}', [
            'id' => $this->primaryKey(),
            'tag_id' => $this->integer()->notNull(),
            'article_id' => $this->integer()->notNull()
        ]);
        $this->createTable('{{%article_tag_translation}}', [
            'id' => $this->primaryKey(),
            'article_tag_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'title' => $this->string(),
            'intro_text' => $this->text(),
            'full_text' => $this->text(),
            'seo_title' => $this->string($seoTitleLength),
            'alias' => $this->string()->notNull(),
            'meta_keywords' => $this->string($metaKeyWordsFieldLength),
            'meta_description' => $this->string($metaDescriptionFieldLength),
            'meta_robots' => $this->string($metaRobotsLength),
            'meta_author' => $this->string(),
            'meta_copyright' => $this->string()
        ]);

        /*Articles attachments tables*/
        $this->createTable('{{%article_attachment}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'file_name' => $this->string()->notNull(),
            'hits' => $this->integer()
        ]);
        $this->createTable('{{%article_attachment_translation}}', [
            'id' => $this->primaryKey(),
            'article_attachment_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'title' => $this->string(),
            'description' => $this->string()
        ]);

        /*Related articles table*/
        $this->createTable('{{%article_related_article}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'related_article_id' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull()
        ]);

        /*Foreign keys*/
        $this->addForeignKey('{{%fk_articleCategory_article}}',
            '{{%article_category}}', 'parent_id',
            '{{%article_category}}', 'id', 'SET NULL');
        $this->addForeignKey('{{%fk_articleCategoryImage_articleCategory}}',
            '{{%article_category_image}}', 'article_category_id',
            '{{%article_category}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleCategory_user}}',
            '{{%article_category}}', 'user_id',
            'user', 'id', 'SET NULL');

        $this->addForeignKey('{{%fk_articleCategoryTranslation_articleCategory}}',
            '{{%article_category_translation}}', 'article_category_id',
            '{{%article_category}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleCategoryTranslation_language}}',
            '{{%article_category_translation}}', 'language_id',
            'language', 'id', 'CASCADE');

        $this->addForeignKey('{{%fk_article_category}}',
            '{{%article}}', 'category_id',
            '{{%article_category}}', 'id', 'SET NULL');
        $this->addForeignKey('{{%fk_article_user}}',
            '{{%article}}', 'user_id',
            'user', 'id', 'SET NULL');

        $this->addForeignKey('{{%fk_articleTranslation_article}}',
            '{{%article_translation}}', 'article_id',
            '{{%article}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleTranslation_language}}',
            '{{%article_translation}}', 'language_id',
            'language', 'id', 'CASCADE');

        $this->addForeignKey('{{%fk_articleImage_article}}',
            '{{%article_image}}', 'article_id',
            '{{%article}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleImageTranslation_language}}',
            '{{%article_image_translation}}', 'language_id',
            'language', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleImageTranslation_articleImage}}',
            '{{%article_image_translation}}', 'article_image_id',
            '{{%article_image}}', 'id', 'CASCADE');

        $this->addForeignKey('{{%fk_articleVideo_article}}',
            '{{%article_video}}', 'article_id',
            '{{%article}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleVideoTranslation_language}}',
            '{{%article_video_translation}}', 'language_id',
            'language', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleVideoTranslation_articleVideo}}',
            '{{%article_video_translation}}', 'article_video_id',
            '{{%article_video}}', 'id', 'CASCADE');

        $this->addForeignKey('{{%fk_articleTagArticle_article}}',
            '{{%article_tag_article}}', 'article_id',
            '{{%article}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleTagArticle_tag}}',
            '{{%article_tag_article}}', 'tag_id',
            '{{%article_tag}}', 'id', 'CASCADE');

        $this->addForeignKey('{{%fk_articleTagTranslation_articleTag}}',
            '{{%article_tag_translation}}', 'article_tag_id',
            '{{%article_tag}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleTagTranslation_language}}',
            '{{%article_tag_translation}}', 'language_id',
            'language', 'id', 'CASCADE');

        $this->addForeignKey('{{%fk_articleAttachment_article}}',
            '{{%article_attachment}}', 'article_id',
            '{{%article}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleAttachmentTranslation_articleAttachment}}',
            '{{%article_attachment_translation}}', 'article_attachment_id',
            '{{%article_attachment}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleAttachmentTranslation_language}}',
            '{{%article_attachment_translation}}', 'language_id',
            'language', 'id', 'CASCADE');

        $this->addForeignKey('{{%fk_articleRelatedArticle_article}}',
            '{{%article_related_article}}', 'article_id',
            '{{%article}}', 'id', 'CASCADE');
        $this->addForeignKey('{{%fk_articleRelatedArticle_relatedArticle}}',
            '{{%article_related_article}}', 'related_article_id',
            '{{%article}}', 'id', 'CASCADE');

    }

    public function safeDown()
    {
        $this->dropTable('{{%article_related_article}}');
        $this->dropTable('{{%article_attachment_translation}}');
        $this->dropTable('{{%article_attachment}}');
        $this->dropTable('{{%article_tag_translation}}');
        $this->dropTable('{{%article_tag_article}}');
        $this->dropTable('{{%article_tag}}');
        $this->dropTable('{{%article_image_translation}}');
        $this->dropTable('{{%article_image}}');
        $this->dropTable('{{%article_video_translation}}');
        $this->dropTable('{{%article_video}}');
        $this->dropTable('{{%article_translation}}');
        $this->dropTable('{{%article}}');
        $this->dropTable('{{%article_category_translation}}');
        $this->dropTable('{{%article_category}}');
    }

}
