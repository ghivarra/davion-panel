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
        // connect db and check db driver for compatibility between MariaDB and Postgresql
        $db     = Database::connect();
        $driver = $db->DBDriver;

        // create enum type if using Postgresql
        if ($driver === 'Postgre')
        {
            $db->query("CREATE TYPE {$this->tableName}_status as ENUM('Aktif', 'Nonaktif')");
        }

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
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Nonaktif'],
                'default'    => 'Aktif'
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
