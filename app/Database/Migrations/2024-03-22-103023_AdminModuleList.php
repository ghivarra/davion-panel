<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class AdminModuleList extends Migration
{
    protected $tableName = 'admin_module_list';

    //=====================================================================================================

    public function up()
    {
        // connect db and check db driver for compatibility between MariaDB and Postgresql
        $db     = Database::connect();
        $driver = $db->DBDriver;

        // create enum type if using Postgresql
        if ($driver === 'Postgre')
        {
            $db->query("CREATE TYPE {$this->tableName}_type as ENUM('Full', 'Partial')");
        }

        // set fields
        $fields = [
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['Full', 'Partial'],
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

        // alter fields data if using Postgresql
        if ($driver === 'Postgre')
        {
            $fields['type'] = [
                'type'    => "{$this->tableName}_type",
                'default' => 'Full'
            ];
        }

        // add fields
        $this->forge->addField($fields);

        // add primary key
        $this->forge->addKey('id', true);

        // add indexes
        $this->forge->addKey('admin_role_id');
        $this->forge->addKey('admin_module_id');
        $this->forge->addKey('type');

        // add foreign key
        $this->forge->addForeignKey('admin_role_id', 'admin_role', 'id');
        $this->forge->addForeignKey('admin_module_id', 'admin_module', 'id');

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

        // connect db and check db driver for compatibility between MariaDB and Postgresql
        $db     = Database::connect();
        $driver = $db->DBDriver;

        // drop enum type if using Postgresql
        if ($driver === 'Postgre')
        {
            $db->query("DROP TYPE IF EXISTS {$this->tableName}_type");
        }
    }

    //=====================================================================================================
}
