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
        $this->seedMenu();
        $this->seedModule();
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

    protected function seedMenu()
    {
        $db  = Database::connect();
        $now = date('Y-m-d H:i:s');

        // create menu group
        $db->table("admin_menu_group")->insert([
            'name'       => 'Default',
            'sort_order' => 1,
            'status'     => 'Aktif',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        $defaultGroupId = $db->insertId();

        $db->table("admin_menu_group")->insert([
            'name'       => 'Pengaturan',
            'sort_order' => 2,
            'status'     => 'Aktif',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        $pengaturanGroupId = $db->insertId();

        // add primary and parent menu
        $db->table('admin_menu')->insertBatch([
            [
                'title'               => 'Dasbor', 
                'router_name'         => 'panel.dashboard', 
                'icon'                => 'fa-solid fa-table-cells-large',
                'type'                => 'Primary',
                'sort_order'          => 1,
                'created_at'          => $now, 
                'updated_at'          => $now,
                'admin_menu_group_id' => $defaultGroupId
            ],[
                'title'               => 'Profil', 
                'router_name'         => 'panel.profile', 
                'icon'                => 'fa-solid fa-user',
                'type'                => 'Primary',
                'sort_order'          => 2,
                'created_at'          => $now, 
                'updated_at'          => $now,
                'admin_menu_group_id' => $defaultGroupId
            ], [
                'title'               => 'Admin', 
                'router_name'         => NULL,
                'icon'                => 'fa-solid fa-user-tie',
                'type'                => 'Parent',
                'sort_order'          => 1,
                'created_at'          => $now, 
                'updated_at'          => $now,
                'admin_menu_group_id' => $pengaturanGroupId,
            ], [
                'title'               => 'Halaman', 
                'router_name'         => NULL,
                'icon'                => 'fa-solid fa-table-columns',
                'type'                => 'Parent',
                'sort_order'          => 2,
                'created_at'          => $now, 
                'updated_at'          => $now,
                'admin_menu_group_id' => $pengaturanGroupId,
            ], [
                'title'               => 'Website', 
                'router_name'         => 'panel.website',
                'icon'                => 'fa-solid fa-globe',
                'type'                => 'Primary',
                'sort_order'          => 3,
                'created_at'          => $now, 
                'updated_at'          => $now,
                'admin_menu_group_id' => $pengaturanGroupId,
            ]
        ]);

        // search and get parents id
        $adminParent   = $db->table('admin_menu')->select('id')->where('title', 'Admin')->get()->getRowArray();
        $halamanParent = $db->table('admin_menu')->select('id')->where('title', 'Halaman')->get()->getRowArray();

        // add child menu
        $db->table('admin_menu')->insertBatch([
            [
                'title'                => 'Akun', 
                'router_name'          => 'panel.admin',
                'icon'                 => NULL,
                'type'                 => 'Child',
                'sort_order'           => 1,
                'created_at'           => $now, 
                'updated_at'           => $now,
                'admin_menu_group_id'  => $pengaturanGroupId,
                'admin_menu_parent_id' => $adminParent['id'],
            ], [
                'title'                => 'Role', 
                'router_name'          => 'panel.role',
                'icon'                 => NULL,
                'type'                 => 'Child',
                'sort_order'           => 2,
                'created_at'           => $now, 
                'updated_at'           => $now,
                'admin_menu_group_id'  => $pengaturanGroupId,
                'admin_menu_parent_id' => $adminParent['id'],
            ], [
                'title'                => 'Modul', 
                'router_name'          => 'panel.module',
                'icon'                 => NULL,
                'type'                 => 'Child',
                'sort_order'           => 1,
                'created_at'           => $now, 
                'updated_at'           => $now,
                'admin_menu_group_id'  => $pengaturanGroupId,
                'admin_menu_parent_id' => $halamanParent['id'],
            ], [
                'title'                => 'Menu', 
                'router_name'          => 'panel.menu',
                'icon'                 => NULL,
                'type'                 => 'Child',
                'sort_order'           => 2,
                'created_at'           => $now, 
                'updated_at'           => $now,
                'admin_menu_group_id'  => $pengaturanGroupId,
                'admin_menu_parent_id' => $halamanParent['id'],
            ]
        ]);
    }

    //=====================================================================================================

    protected function seedModule()
    {
        $db  = Database::connect();
        $now = date('Y-m-d H:i:s');

        $db->table('admin_module')->insertBatch([
            [
                'alias'      => 'websiteView',
                'name'       => 'Pengaturan Website - View',
                'group'      => 'Website',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'websiteUpdate',
                'name'       => 'Pengaturan Website - Update',
                'group'      => 'Website',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'moduleView',
                'name'       => 'Pengaturan Modul - View',
                'group'      => 'Module',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'moduleCreate',
                'name'       => 'Pengaturan Modul - Create',
                'group'      => 'Module',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'moduleUpdate',
                'name'       => 'Pengaturan Modul - Update',
                'group'      => 'Module',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'moduleDelete',
                'name'       => 'Pengaturan Modul - Delete',
                'group'      => 'Module',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'menuView',
                'name'       => 'Pengaturan Menu - View',
                'group'      => 'Menu',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'menuCreate',
                'name'       => 'Pengaturan Menu - Create',
                'group'      => 'Menu',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'menuUpdate',
                'name'       => 'Pengaturan Menu - Update',
                'group'      => 'Menu',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'menuDelete',
                'name'       => 'Pengaturan Menu - Delete',
                'group'      => 'Menu',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'roleView',
                'name'       => 'Pengaturan Role - View',
                'group'      => 'Role',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'roleCreate',
                'name'       => 'Pengaturan Role - Create',
                'group'      => 'Role',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'roleUpdate',
                'name'       => 'Pengaturan Role - Update',
                'group'      => 'Role',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'roleDelete',
                'name'       => 'Pengaturan Role - Delete',
                'group'      => 'Role',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'adminView',
                'name'       => 'Pengaturan Admin - View',
                'group'      => 'Admin',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'adminCreate',
                'name'       => 'Pengaturan Admin - Create',
                'group'      => 'Admin',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'adminUpdate',
                'name'       => 'Pengaturan Admin - Update',
                'group'      => 'Admin',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'adminDelete',
                'name'       => 'Pengaturan Admin - Delete',
                'group'      => 'Admin',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'alias'      => 'adminSession',
                'name'       => 'Pengaturan Admin - Session',
                'group'      => 'Admin',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }

    //=====================================================================================================
}