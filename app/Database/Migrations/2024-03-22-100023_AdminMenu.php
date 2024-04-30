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
        // set fields
        $fields = [
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 200
            ],
            'router_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'sort_order' => [
                'type'    => 'INT',
                'default' => 1
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'Aktif'
            ],
            'type' => [
                'type'       => 'VARCHAR',
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
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP')
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP')
            ],
            'deleted_at' => [
                'type'    => 'TIMESTAMP',
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
        $this->forge->addKey('status');
        $this->forge->addKey('deleted_at');

        // add foreign key
        $this->forge->addForeignKey('admin_menu_parent_id', 'admin_menu', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('admin_menu_group_id', 'admin_menu_group', 'id', 'CASCADE', 'SET NULL');

        // create table
        $this->forge->createTable($this->tableName, true);
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
