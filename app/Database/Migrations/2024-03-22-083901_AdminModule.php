<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class AdminModule extends Migration
{
    protected $tableName = 'admin_module';

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
            'alias' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200
            ],
            'group' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true
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
        $this->forge->addKey('group');
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
