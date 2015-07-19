<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        //Create users table
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL',

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        
        $this->createTable('{{%categories}}', [
            'id' => Schema::TYPE_PK,
            'category' => Schema::TYPE_STRING . '(1024) NOT NULL',
        ], $tableOptions);
        
        //Create posts table
        $this->createTable('{{%posts}}', [
            'id' => Schema::TYPE_PK,
            'author' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'date' => Schema::TYPE_STRING . '(512) NOT NULL',
            'title' => Schema::TYPE_TEXT . ' NOT NULL',
            'content' => Schema::TYPE_TEXT . ' NOT NULL',
            'excerpt' => Schema::TYPE_TEXT . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . '(1) NOT NULL DEFAULT 1',
            'category' => Schema::TYPE_INTEGER . ' NOT NULL',
            'KEY `author` (`author`)',
            'KEY `date` (`date`)',
        ], $tableOptions);
        $this->addForeignKey("fk_category_posts", "posts", "category", "categories", "id");
        
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%posts}}');
        $this->dropTable('{{%categories}}');
    }
}
