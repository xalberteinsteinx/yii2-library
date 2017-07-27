<?php
namespace xalberteinsteinx\library\frontend\widgets

use yii\helpers\Html;
use xalberteinsteinx\library\common\entities\Article;
use newerton\fancybox\FancyBox;
use evgeniyrru\yii2slick\Slick;

/**
 * Widget renders product images.
 * @see https://github.com/EvgeniyRRU/yii2-slick
 * @see http://kenwheeler.github.io/slick/
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 *
 * Example:
 * ```php
 * <?= \bl\cms\shop\frontend\widgets\ImageSlider::widget([
 *      'product' => $article,
 *
 *      // @see http://kenwheeler.github.io/slick/#settings
 *      'clientOptions' => [
 *          'autoplay' => true,
 *      ]
 * ]); ?>
 */


/**
 * @inheritdoc
 *
 * @author Vyacheslav Nozhenko <vv.nojenko@gmail.com>
 */
class ImageSlider extends Slick
{
    /**
     * @var Article
     */
    public $article;
    /**
     * @var string
     */
    public $imagesSize = 'hd';
    /**
     * @inheritdoc
     */
    public $containerOptions = ['class' => 'article-image-slider'];
    /**
     * @inheritdoc
     */
    public $itemOptions = ['class' => 'article-image-slide'];
    /**
     * @var bool
     */
    public $fancyBox = false;
    /**
     * @var array
     */
    public $fancyBoxWidgetConfig = [];
    /**
     * @var string
     */
    public $defaultImage = '';


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->normalizeOptions();
        if(empty($this->items) && empty($this->article)) {
            throw new \Exception('Not allowed without items');
        }
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        $slider = Html::beginTag($this->containerTag, $this->containerOptions);
        if (!empty($this->article)) {
            $this->items = $this->renderItems();
        }
        foreach($this->items as $item) {
            $slider .= Html::tag($this->itemContainer, $item, $this->itemOptions);
        }


        if($this->fancyBox) {
            $this->fancyBoxWidgetConfig['target'] = "a[rel=article-image-fancybox]";
            echo FancyBox::widget($this->fancyBoxWidgetConfig);
        }
        $slider .= Html::endTag($this->containerTag);
        echo $slider;
        $this->registerClientScript();
    }
    /**
     * @inheritdoc
     */
    protected function renderItems()
    {
        $items = [];
        if (empty($this->article->images)) {
            $items[] = ($this->fancyBox) ? Html::a(Html::img($this->defaultImage)) : Html::img($this->defaultImage);
        }
        foreach ($this->article->images as $articleImage) {
            $items[] = $this->renderItem($articleImage);
        }
        return $items;
    }
    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $img = (!empty($item->getImage($this->imagesSize))) ? $item->getImage($this->imagesSize) : '';
        $alt = (!empty($item->translation->alt)) ? $item->translation->alt : '';
        if($this->fancyBox) {
            return Html::a(Html::img($img), $img, ['rel' => "article-image-fancybox"]);
        }
        return Html::img($img, ['alt' => $alt]);
    }
}