<?php
namespace xalberteinsteinx\library\backend\widgets\assets;

use yii\web\AssetBundle;
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class InputTreeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/xalberteinsteinx/yii2-library/backend/widgets/assets/src';
    public $css = [
        'css/input-tree.css'
    ];
    public $js = [
        'js/input-tree.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}