<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class AdminMenu extends Migration
{
    protected $tableName = 'admin_menu';

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
            $db->query("CREATE TYPE {$this->tableName}_type as ENUM('Primary', 'Parent', 'Child')");
        }

        // set fields
        $fields = [
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 200
            ],
            'router_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'sort_order' => [
                'type'    => 'INT',
                'default' => 1
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Nonaktif'],
                'default'    => 'Aktif'
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['Primary', 'Parent', 'Child'],
                'default'    => 'Primary'
            ],
            'admin_menu_group_id' => [
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

            $fields['type'] = [
                'type'    => "{$this->tableName}_type",
                'default' => 'Primary'
            ];
        }

        // add fields
        $this->forge->addField($fields);

        // add primary key
        $this->forge->addKey('id', true);

        // add indexes
        $this->forge->addKey('admin_menu_group_id');
        $this->forge->addKey('type');
        $this->forge->addKey('status');
        $this->forge->addKey('deleted_at');

        // add foreign key
        $this->forge->addForeignKey('admin_menu_group_id', 'admin_menu_group', 'id', 'CASCADE', 'SET NULL');

        // create table
        $this->forge->createTable($this->tableName, true);
    }

    //=====================================================================================================

    public function down()
    {
        // drop foreign key
        $this->forge->dropForeignKey($this->tableName, "{$this->tableName}_admin_menu_group_id_foreign");

        // drop table
        $this->forge->dropTable($this->tableName, true);

        // connect db and check db driver for compatibility between MariaDB and Postgresql
        $db     = Database::connect();
        $driver = $db->DBDriver;

        // drop enum type if using Postgresql
        if ($driver === 'Postgre')
        {
            $db->query("DROP TYPE IF EXISTS {$this->tableName}_status");
            $db->query("DROP TYPE IF EXISTS {$this->tableName}_type");
        }
    }

    //=====================================================================================================
}
