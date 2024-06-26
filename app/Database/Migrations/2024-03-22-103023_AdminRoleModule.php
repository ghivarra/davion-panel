<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class AdminRoleModule extends Migration
{
    protected $tableName = 'admin_role_module';

    //=====================================================================================================

    public function up()
    {
        $database = new Database();
        $timeType = ($database->default['DBDriver'] === 'MySQLi') ? 'DATETIME' : 'TIMESTAMP';
        
        // set fields
        $fields = [
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'Full'
            ],
            'parameter' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'admin_role_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true
            ],
            'admin_module_id' => [
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
        $this->forge->addKey('admin_module_id');

        // add foreign key
        $this->forge->addForeignKey('admin_role_id', 'admin_role', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('admin_module_id', 'admin_module', 'id', 'CASCADE', 'CASCADE');

        // create table
        $this->forge->createTable($this->tableName, true);
    }

    //=====================================================================================================

    public function down()
    {
        // drop foreign key
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_role_id_foreign");
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_module_id_foreign");

        // drop table
        $this->forge->dropTable($this->tableName, true);
    }

    //=====================================================================================================
}
