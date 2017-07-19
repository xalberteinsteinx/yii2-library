<?php
namespace xalberteinsteinx\shop\frontend;
use Yii;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Module extends \yii\base\Module
{

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
