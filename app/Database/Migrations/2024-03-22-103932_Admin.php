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
        // connect db and check db driver for compatibility between MariaDB and Postgresql
        $db     = Database::connect();
        $driver = $db->DBDriver;

        // create enum type if using Postgresql
        if ($driver === 'Postgre')
        {
            $db->query("CREATE TYPE {$this->tableName}_status as ENUM('Aktif', 'Nonaktif', 'Diblokir')");
        }

        // set fields
        $fields = [
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 1000
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'unique'     => true
            ],
            'email_verified_at' => [
                'type'       => 'TIMESTAMP',
                'null'       => true
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Nonaktif', 'Diblokir'],
                'default'    => 'Aktif'
            ],
            'admin_role_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true
            ],
            'token_expired_at' => [
                'type'       => 'TIMESTAMP',
                'null'       => true
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

        // alter fields data if using Postgresql
        if ($driver === 'Postgre')
        {
            $fields['status'] = [
                'type'    => "{$this->tableName}_status",
                'default' => 'Aktif'
            ];
        }

        // add fields
        $this->forge->addField($fields);

        // add primary key
        $this->forge->addKey('id', true);

        // add indexes
        $this->forge->addKey('admin_role_id');
        $this->forge->addKey('status');
        $this->forge->addKey('email_verified_at');
        $this->forge->addKey('token_expired_at');
        $this->forge->addKey('deleted_at');

        // add foreign key
        $this->forge->addForeignKey('admin_role_id', 'admin_role', 'id');

        // create table
        $this->forge->createTable($this->tableName, true);
    }

    //=====================================================================================================

    public function down()
    {
        // drop foreign key
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_role_id_foreign");

        // drop table
        $this->forge->dropTable($this->tableName, true);

        // connect db and check db driver for compatibility between MariaDB and Postgresql
        $db     = Database::connect();
        $driver = $db->DBDriver;

        // drop enum type if using Postgresql
        if ($driver === 'Postgre')
        {
            $db->query("DROP TYPE IF EXISTS {$this->tableName}_status");
        }
    }

    //=====================================================================================================
}
