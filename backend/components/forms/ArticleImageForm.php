<?php
namespace xalberteinsteinx\library\backend\components\forms;
use xalberteinsteinx\library\common\entities\Article;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class ArticleImageForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $image;

    /**
     * @var string
     */
    public $link;

    /**
     * @var string
     */
    public $alt1;

    /**
     * @var string
     */
    public $alt2;

    /**
     * @var string
     */
    public $extension = '.jpg';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['image'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize'=>'3000000'],
            [['link', 'alt1', 'alt2'], 'string', 'skipOnEmpty' => true]
        ];
    }

    /** Upload image from drive
     * @return bool
     * @throws Exception
     */
    public function upload()
    {
        if ($this->validate()) {
            $imagable = \Yii::$app->get('library_imagable');
            $dir = $imagable->imagesPath . '/article/';
            if (!empty($this->image)) {
                if (!file_exists($dir)) BaseFileHelper::createDirectory($dir);
                $newFile = $dir . mt_rand() . $this->image->name;
                if ($this->image->saveAs($newFile)) {
                    $image_name = $imagable->create('article', $newFile);
                    unlink($newFile);
                    return $image_name;
                }
                else throw new Exception('Image saving failed.');
            }
        }
        return false;
    }

    /**
     * Copy image from link
     * @param $link
     * @return bool
     */
    public function copy($link) {
        $imagable = \Yii::$app->get('library_imagable');
        $dir = $imagable->imagesPath . '/article/';
        if (exif_imagetype($link) == IMAGETYPE_JPEG || exif_imagetype($link) == IMAGETYPE_PNG) {
            if (!empty($link)) {
                $baseName = Article::generateImageName($link);
                if (!file_exists($dir)) mkdir($dir);
                $newFile = $dir . $baseName . $this->extension;
                if (copy($link, $newFile)) {
                    $image_name = $imagable->create('article', $newFile);
                    unlink($newFile);
                    return $image_name;
                }
            }
        }
        return false;
    }
}