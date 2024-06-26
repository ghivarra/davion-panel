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
        $charType = ($database->default['DBDriver'] === 'MySQLi') ? 'CHAR' : 'VARCHAR';
        
        // set fields
        $fields = [
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'name' => [
                'type'       => $charType,
                'constraint' => 100
            ],
            'is_superadmin' => [
                'type'    => 'SMALLINT',
                'default' => 0
            ],
            'status' => [
                'type'       => $charType,
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
        $this->forge->addKey('deleted_at');

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
        // drop table
        $this->forge->dropTable($this->tableName, true);
    }

    //=====================================================================================================
}
