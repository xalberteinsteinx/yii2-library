<?php

namespace xalberteinsteinx\library\backend\widgets;
use bl\multilang\entities\Language;
use yii\base\Widget;

/**
 * This widget adds language switcher button.
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class LanguageSwitcher extends Widget
{
    public $languages;
    public $selectedLanguage;
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (empty($this->languages)) {
            $this->languages = Language::find()->where(['active' => true])->all();
        }
        return $this->render('language-switcher/index', [
            'languages' => $this->languages,
            'selectedLanguage' => $this->selectedLanguage,
        ]);
    }
}