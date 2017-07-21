<?php
namespace xalberteinsteinx\library\backend\components\forms;

use xalberteinsteinx\library\common\entities\Article;
use Yii;
use yii\base\Model;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ArticleVideoForm extends Model
{
    /**
     * @var string
     */
    public $file_name;

    /**
     * @var string
     */
    public $resource;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_name'], 'file', 'skipOnEmpty' => true, 'extensions' => 'avi, mp4']
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $dir = Yii::getAlias('@frontend/web/video');
            if (!file_exists($dir)) {
                mkdir($dir);
            }
            if (!empty($this->file_name)) {
                $baseName = Article::generateImageName($this->file_name->name) . '.' . $this->file_name->extension;
                $this->file_name->saveAs($dir . '/' . $baseName);
                return $baseName;
            }
        }

        return false;
    }

}