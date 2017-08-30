<?php
namespace xalberteinsteinx\library\frontend\behaviors;

use yii\base\Behavior;

/**
 * This class allows you to manage a title and meta-tags of page.
 * A page object must have properties such as "seo_title", "meta_keywords",
 * "meta_description", "meta_robots", "meta_author", "meta_copyright".
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class SeoBehavior extends Behavior
{

    /**
     * If you want to translate characters or replace substrings in title tag or some meta-tags,
     * use this property.
     * Example: array("," => " ", "hello" => "hi", "hi" => "hello")
     *
     * @var array
     */
    public $replace_pairs_in_title = [];

    /**
     * Description like for $replace_pairs_in_title
     * @var array
     */
    public $replace_pairs_in_keywords = [];

    /**
     * Description like for $replace_pairs_in_title
     * @var array
     */
    public $replace_pairs_in_description = [];

    /**
     * Description like for $replace_pairs_in_title
     * @var array
     */
    public $replace_pairs_in_author = [];

    /**
     * Description like for $replace_pairs_in_title
     * @var array
     */
    public $replace_pairs_in_copyright = [];


    /**
     * Sets meta-tags
     *
     * @param $translation
     */
    public function setMetaTags($translation)
    {

        /*Sets title tag*/
        $this->owner->view->title = strtr(($translation->seo_title ?? $translation->title), $this->replace_pairs_in_title);

        /*Sets meta-keywords*/
        if (!empty($translation->meta_keywords)) {
            $this->owner->view->registerMetaTag([
                'name' => 'keywords',
                'content' => html_entity_decode(strtr(($translation->meta_keywords), $this->replace_pairs_in_keywords))
            ]);
        }

        /*Sets meta-description*/
        if (!empty($translation->meta_description)) {
            $this->owner->view->registerMetaTag([
                'name' => 'description',
                'content' => html_entity_decode(strtr($translation->meta_description, $this->replace_pairs_in_description))
            ]);
        }

        /*Sets meta-robots*/
        if (!empty($translation->meta_robots)) {
            $this->owner->view->registerMetaTag([
                'name' => 'robots',
                'content' => html_entity_decode($translation->meta_robots)
            ]);
        }

        /*Sets meta-description*/
        if (!empty($translation->meta_author)) {
            $this->owner->view->registerMetaTag([
                'name' => 'author',
                'content' => html_entity_decode(strtr($translation->meta_author, $this->replace_pairs_in_author))
            ]);
        }

        /*Sets meta-description*/
        if (!empty($translation->meta_copyright)) {
            $this->owner->view->registerMetaTag([
                'name' => 'description',
                'content' => html_entity_decode(strtr($translation->meta_copyright, $this->replace_pairs_in_copyright))
            ]);
        }

    }
}