<?php

namespace xalberteinsteinx\library\common\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use xalberteinsteinx\library\common\entities\ArticleCategory;

/**
 * ArticleCategorySearch represents the model behind the search form about `xalberteinsteinx\library\common\entities\ArticleCategory`.
 */
class ArticleCategorySearch extends ArticleCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'user_id', 'position', 'hits', 'show'], 'integer'],
            [['key', 'view_name', 'article_view_name', 'created_at', 'updated_at', 'publish_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ArticleCategory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'user_id' => $this->user_id,
            'position' => $this->position,
            'hits' => $this->hits,
            'show' => $this->show,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'publish_at' => $this->publish_at,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'view_name', $this->view_name])
            ->andFilterWhere(['like', 'article_view_name', $this->article_view_name]);

        return $dataProvider;
    }
}
