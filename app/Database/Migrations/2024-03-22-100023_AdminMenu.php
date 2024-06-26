<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class AdminMenu extends Migration
{
    protected $tableName = 'admin_menu';

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
            'title' => [
                'type'       => $charType,
                'constraint' => 50
            ],
            'router_name' => [
                'type'       => $charType,
                'constraint' => 100,
                'null'       => true
            ],
            'icon' => [
                'type'       => $charType,
                'constraint' => 100,
                'null'       => true
            ],
            'sort_order' => [
                'type'    => 'INT',
                'default' => 1
            ],
            'status' => [
                'type'       => $charType,
                'constraint' => 10,
                'default'    => 'Aktif'
            ],
            'type' => [
                'type'       => $charType,
                'constraint' => 20,
                'default'    => 'Primary'
            ],
            'admin_menu_parent_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true
            ],
            'admin_menu_group_id' => [
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
            ],
            'deleted_at' => [
                'type'    => $timeType,
                'null'    => true
            ]
        ];

        // add fields
        $this->forge->addField($fields);

        // add primary key
        $this->forge->addKey('id', true);

        // add indexes
        $this->forge->addKey('admin_menu_group_id');
        $this->forge->addKey('type');
        $this->forge->addKey('deleted_at');

        // add foreign key
        $this->forge->addForeignKey('admin_menu_parent_id', 'admin_menu', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('admin_menu_group_id', 'admin_menu_group', 'id', 'CASCADE', 'SET NULL');

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
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_menu_parent_id_foreign");
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_menu_group_id_foreign");

        // drop table
        $this->forge->dropTable($this->tableName, true);
    }

    //=====================================================================================================
}
