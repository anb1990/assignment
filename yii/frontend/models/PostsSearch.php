<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\Posts;
use common\classescustom\security\EncDecrypt;

/**
 * PostsSearch represents the model behind the search form about `app\models\Posts`.
 */
class PostsSearch extends Posts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author'], 'integer'],
            [['id','title', 'content'], 'safe'],
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
        $encDecrypt = new EncDecrypt();
        //print_r($params['PostsSearch']);die;
        if(!isset($params['PostsSearch'])){
            $searchTerms = array();
        }else{
            $searchTerms = array('author'=>$params['PostsSearch']['author'],'title'=>$params['PostsSearch']['title'],'content'=>$params['PostsSearch']['content']);
        }
        //print_r($searchTerms);die;
        
        
        $searchTerms = $encDecrypt->encrypt(json_encode($searchTerms));
        
        $curl = curl_init("http://api.com/fetchposts/$searchTerms");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        
        $posts = json_decode($encDecrypt->decrypt($curl_response), true);
        
        
$arr = $posts;
        $dataProvider = new ArrayDataProvider([
'allModels' => $arr
]);
                
                

        

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
   
        return $dataProvider;
    }
}
