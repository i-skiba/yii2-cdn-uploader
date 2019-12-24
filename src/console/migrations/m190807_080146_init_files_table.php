<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m190807_080146_init_locales_table
 */
class m190807_080146_init_files_table extends Migration
{
    function getTableName()
    {
        return 'files';
    }

    public function up()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'path' => $this->string(512)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime(),
        ]);
        $this->addIndex(['type']);
    }
}
