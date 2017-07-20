<?php

namespace xalberteinsteinx\library\backend;
use Yii;

/**
 * Backend module definition class
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'xalberteinsteinx\library\backend\controllers';

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
                'basePath'       => '@vendor/xalberteinsteinx/yii2-library/backend/messages',
            ];
    }
}