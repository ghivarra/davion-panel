<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class AdminRole extends Migration
{
    protected $tableName = 'admin_role';

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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200
            ],
            'is_superadmin' => [
                'type'    => 'SMALLINT',
                'default' => 0
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'Aktif'
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
        $this->forge->addKey('is_superadmin');
        $this->forge->addKey('status');
        $this->forge->addKey('deleted_at');

        // create table
        $this->forge->createTable($this->tableName, true);
    }

    //=====================================================================================================

    public function down()
    {
        // drop table
        $this->forge->dropTable($this->tableName, true);
    }

    //=====================================================================================================
}
