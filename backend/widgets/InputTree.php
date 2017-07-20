<?php
namespace xalberteinsteinx\library\backend\widgets;

use xalberteinsteinx\library\backend\widgets\assets\InputTreeAsset;
use bl\multilang\entities\Language;
use yii\base\Widget;
use yii\db\ActiveRecord;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class InputTree extends Widget
{
    /**
     * @var ActiveRecord
     */
    public $className;
    public $form;
    public $model;
    public $attribute;
    public $languageId;
    public function init()
    {
        $this->languageId = Language::getCurrent()->id;
        InputTreeAsset::register($this->getView());
    }
    public function run()
    {
        parent::run();
        $parents = self::findChildren($this->className, null);
        return $this->render('input-tree/index',
            [
                'parents' => $parents,
                'form' => $this->form,
                'model' => $this->model,
                'attribute' => $this->attribute,
                'languageId' => $this->languageId
            ]);
    }
    /**
     * @param $parentId
     * @param $model ActiveRecord
     *
     * @return array
     */
    public static function findChildren($model, $parentId) {
        return $children = $model::find()->where(['parent_id' => $parentId])->orderBy('position')->all();
    }
}