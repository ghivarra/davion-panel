<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class AdminSession extends Migration
{
    protected $tableName = 'admin_session';

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
                'constraint' => 255,
                'unique'     => true
            ],
            'admin_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'client_id' => [
                'type'       => 'CHAR',
                'constraint' => 128,
            ],
            'useragent' => [
                'type' => 'text',
            ],
            'ip_address' => [
                'type'       => $charType,
                'constraint' => 130
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
        $this->forge->addKey('admin_id');
        $this->forge->addKey('client_id');
        
        // add foreign key
        $this->forge->addForeignKey('admin_id', 'admin', 'id', 'CASCADE', 'CASCADE');

        // option
        $option = [];

        if ($database->default['DBDriver'] === 'MySQLi')
        {
            $option['ROW_FORMAT'] = 'DYNAMIC';
        }

        // create table
        $this->forge->createTable($this->tableName, true, $option);
    }

    //=====================================================================================================

    public function down()
    {
        // drop foreign key
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_id_foreign");

        // drop table
        $this->forge->dropTable($this->tableName, true);
    }

    //=====================================================================================================
}
