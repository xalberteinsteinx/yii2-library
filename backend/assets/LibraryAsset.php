<?php
namespace xalberteinsteinx\library\backend\assets;

use yii\web\AssetBundle;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class LibraryAsset extends AssetBundle
{
    public $sourcePath = '@vendor/xalberteinsteinx/yii2-library/backend/web';
    public $js = [
        'js/alias-generator.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}