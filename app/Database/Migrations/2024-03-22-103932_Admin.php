<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class Admin extends Migration
{
    protected $tableName = 'admin';

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
            'username' => [
                'type'       => 'CHAR',
                'constraint' => 50,
                'unique'     => true
            ],
            'password' => [
                'type'       => 'CHAR',
                'constraint' => 100
            ],
            'name' => [
                'type'       => 'CHAR',
                'constraint' => 100
            ],
            'email' => [
                'type'       => 'CHAR',
                'constraint' => 100,
                'unique'     => true
            ],
            'email_verified_at' => [
                'type'       => $timeType,
                'null'       => true
            ],
            'status' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'default'    => 'Aktif'
            ],
            'admin_role_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'photo' => [
                'type'       => 'CHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'token' => [
                'type'       => 'CHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'token_expired_at' => [
                'type'       => $timeType,
                'null'       => true
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
        $this->forge->addKey('admin_role_id');
        $this->forge->addKey('email_verified_at');
        $this->forge->addKey('token_expired_at');
        $this->forge->addKey('deleted_at');

        // add foreign key
        $this->forge->addForeignKey('admin_role_id', 'admin_role', 'id', 'CASCADE', 'RESTRICT');

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

        // drop table
        $this->forge->dropTable($this->tableName, true);
    }

    //=====================================================================================================
}
