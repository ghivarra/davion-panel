<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class SeedTable extends Migration
{
    public function up()
    {
        $this->seedWebsite();
        $this->seedAdmin();
    }

    //=====================================================================================================

    public function down()
    {
        //
    }

    //=====================================================================================================

    protected function seedWebsite()
    {
        $db  = Database::connect();
        $now = date('Y-m-d H:i:s');

        $db->table('website')->insertBatch([
            ['name' => 'name', 'value' => 'Davion Panel', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'tagline', 'value' => 'Aplikasi Panel Serbaguna', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'description', 'value' => 'Aplikasi Panel Serbaguna Davion dibuat menggunakan CodeIgniter 4 + VueIgniter JS untuk mengakomodasi Vue JS 3 dan PHP 8', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'app_version', 'value' => '0.0.1-alpha', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'icon', 'value' => 'icon.png', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'icon_version', 'value' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'logo', 'value' => 'logo.png', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'logo_version', 'value' => '1.0.0', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    //=====================================================================================================

    protected function seedAdmin()
    {
        $db  = Database::connect();
        $now = date('Y-m-d H:i:s');

        // seed admin role
        $db->table('admin_role')->insert([
            'name'          => 'Super Administrator',
            'is_superadmin' => 1,
            'status'        => 'Aktif',
            'created_at'    => $now,
            'updated_at'    => $now
        ]);
        $roleId = $db->insertId();

        // seed admin
        $db->table('admin')->insert([
            'username'          => 'admin',
            'password'          => password_hash($_ENV['DEFAULT_ADMIN_PASSWORD'], PASSWORD_DEFAULT),
            'name'              => 'Admin Panel',
            'email'             => 'gsenandika@gmail.com',
            'email_verified_at' => $now,
            'status'            => 'Aktif',
            'admin_role_id'     => $roleId,
            'created_at'        => $now,
            'updated_at'        => $now
        ]);
    }

    //=====================================================================================================
}