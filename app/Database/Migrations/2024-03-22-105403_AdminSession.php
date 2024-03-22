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
        // set fields
        $fields = [
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true
            ],
            'admin_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'useragent' => [
                'type' => 'text',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 100
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
            ]
        ];

        // add fields
        $this->forge->addField($fields);

        // add primary key
        $this->forge->addKey('name', true);

        // add indexes
        $this->forge->addKey('admin_id');
        
        // add foreign key
        $this->forge->addForeignKey('admin_id', 'admin', 'id', 'CASCADE', 'CASCADE');

        // create table
        $this->forge->createTable($this->tableName, true);
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
