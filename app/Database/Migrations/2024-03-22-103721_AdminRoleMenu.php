<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class AdminRoleMenu extends Migration
{
    protected $tableName = 'admin_role_menu';

    //=====================================================================================================

    public function up()
    {
        $database = new Database();
        $timeType = ($database->default['DBDriver'] === 'MySQLi') ? 'DATETIME' : 'TIMESTAMP';
        $charType = ($database->default['DBDriver'] === 'MySQLi') ? 'CHAR' : 'VARCHAR';
        
        // set fields
        $fields = [
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'admin_role_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true
            ],
            'admin_menu_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true
            ],
            'created_at' => [
                'type'    => $timeType,
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP')
            ],
            'updated_at' => [
                'type'    => $timeType,
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP')
            ]
        ];

        // add fields
        $this->forge->addField($fields);

        // add primary key
        $this->forge->addKey('id', true);

        // add indexes
        $this->forge->addKey('admin_role_id');
        $this->forge->addKey('admin_menu_id');

        // add foreign key
        $this->forge->addForeignKey('admin_role_id', 'admin_role', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('admin_menu_id', 'admin_menu', 'id', 'CASCADE', 'CASCADE');

        // option
        $option = [];

        if ($database->default['DBDriver'] === 'MySQLi')
        {
            $option['ROW_FORMAT'] = 'COMPACT';
        }

        // create table
        $this->forge->createTable($this->tableName, true, $option);
    }

    //=====================================================================================================

    public function down()
    {
        // drop foreign key
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_role_id_foreign");
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_menu_id_foreign");

        // drop table
        $this->forge->dropTable($this->tableName, true);
    }

    //=====================================================================================================
}
