<?php
namespace xalberteinsteinx\library\frontend;
use Yii;

/**
 * Frontend module definition class
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'xalberteinsteinx\library\frontend\controllers';

    /**
     * If enable page with all categories will be existing
     * @var bool
     */
    public $enableIndexCategoryAction = false;

    /**
     * If enable page with all article will be existing
     * @var bool
     */
    public $enableIndexArticleAction = false;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    /**
     * Registers translations
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['library'] =
            Yii::$app->i18n->translations['library'] ??
            [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath'       => '@vendor/xalberteinsteinx/yii2-library/frontend/messages',
            ];
    }
}
