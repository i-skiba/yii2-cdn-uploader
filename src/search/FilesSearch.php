<?php

namespace kamaelkz\yii2cdnuploader\search;

use kamaelkz\yii2cdnuploader\models\Files;
use yii\db\ActiveQuery;

/**
 * Class FilesSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class FilesSearch extends Files
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer']
        ];
    }

    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
        ]);
    }
}
