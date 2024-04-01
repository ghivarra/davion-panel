<?php

namespace App\Libraries\Ghivarra;

use Config\Services;
use App\Models\AdminModel;
use App\Models\AdminModuleListModel;
use App\Models\AdminSessionModel;

class DavionShield
{
    protected $session;

    //================================================================================================

    public function __construct()
    {
        $this->session = Services::session();
    }

    //================================================================================================

    protected function parseUserAgent($request): array
    {
        $useragent = $request->getUserAgent();

        return [
            'browser'  => $useragent->getBrowser() . ' ' . $useragent->getVersion(),
            'os'       => $useragent->getPlatform(),
            'mobile'   => $useragent->isMobile(),
            'platform' => $useragent->isMobile() ? $useragent->getMobile() : 'Non-Mobile Platform'
        ];
    }

    //================================================================================================

    public function check(): bool
    {
        $sessionToken = $this->session->get($_ENV['SESSION_LOGIN_PARAM']);

        // session is not valid
        if (empty($sessionToken) OR !hash_equals(md5($_ENV['SESSION_LOGIN_TOKEN']), $sessionToken))
        {
            return false;
        }

        // update account data
        $adminModel        = new AdminModel();
        $adminSessionModel = new AdminSessionModel();
        $accountData       = $this->session->get('accountData');
        $accountId         = $accountData['id'];

        // get from db
        $accountData = $adminModel->select(['admin.id', 'username', 'admin.name', 'email', 'email_verified_at', 'admin.status', 'admin_role_id', 'admin_role.name as admin_role_name', 'is_superadmin', 'photo'])
                                  ->join('admin_role', 'admin_role_id = admin_role.id', 'inner')
                                  ->where('admin.id', $accountId)
                                  ->where('admin.status', 'Aktif')
                                  ->where('email_verified_at IS NOT', NULL)
                                  ->first();

        // set new admin data 
        $this->session->set('accountData', $accountData);

        // check if session ttl needed to be regenerate
        $TTL = time() - $_ENV['SESSION_LOGIN_TTL'];

        if ($TTL > $this->session->get('lastRegenerate'))
        {
            // store old id
            $oldId = $this->session->session_id;
            
            // regenerate
            $this->session->regenerate();

            // load services and models
            $request = Services::request();

            // update old id to new id
            $adminSessionModel->where('name', $oldId)
                              ->set([
                                'name'       => $this->session->session_id,
                                'admin_id'   => $accountId,
                                'useragent'  => json_encode($this->parseUserAgent($request)),
                                'ip_address' => $request->getIPAddress()
                              ])
                              ->update();
        }
        
        // return
        return true;
    }

    //================================================================================================

    public function attempt(): bool
    {
        $request    = Services::request();
        $adminModel = new AdminModel();

        // credentials
        $account  = $request->getPost('account');
        $password = $request->getPost('password');

        if (empty($account) OR empty($password))
        {
            return false;
        }

        // get account data
        $accountData = $adminModel->select(['admin.id', 'username', 'password', 'admin.name', 'email', 'email_verified_at', 'admin.status', 'admin_role_id', 'admin_role.name as admin_role_name', 'is_superadmin', 'photo'])
                                  ->join('admin_role', 'admin_role_id = admin_role.id', 'inner')
                                  ->where('username', $account)
                                  ->where('admin.status', 'Aktif')
                                  ->where('email_verified_at IS NOT', NULL)
                                  ->orWhere('email', $account)
                                  ->where('admin.status', 'Aktif')
                                  ->where('email_verified_at IS NOT', NULL)
                                  ->first();

        // account not found
        if (empty($accountData))
        {
            return false;
        }

        // auth failed, wrong password
        if (!password_verify($password, $accountData['password']))
        {
            return false;
        }

        // auth success, regenerate session id
        $this->session->regenerate();

        // clear private data and put admin data + login token in sessions
        unset($accountData['password']);
        $this->session->set([
            'lastRegenerate'             => time(),
            'accountData'                => $accountData,
            $_ENV['SESSION_LOGIN_PARAM'] => md5($_ENV['SESSION_LOGIN_TOKEN'])
        ]);
        
        // save login data in admin_session
        $adminSessionModel = new AdminSessionModel();
        $adminSessionModel->insert([
            'name'       => $this->session->session_id,
            'admin_id'   => $accountData['id'],
            'useragent'  => json_encode($this->parseUserAgent($request)),
            'ip_address' => $request->getIPAddress()
        ]);

        // auth success
        return true;
    }

    //================================================================================================

    public function logout(): bool
    {
        $sessionId = $this->session->session_id;

        // remove active session
        $adminSessionModel = new AdminSessionModel();
        $adminSessionModel->where('name', $sessionId)->delete();

        // invalidate session and return
        $this->session->destroy();
        return true;
    }

    //================================================================================================

    public function getAccountData(): array
    {
        return $this->session->get('accountData');
    }

    //================================================================================================

    public function hasAccess($moduleAlias, $roleId, $superadmin): bool | array
    {
        if ($superadmin)
        {
            return [
                'access_type' => 'Full',
                'parameter'   => null
            ];
        }

        // get
        $adminModuleListModel = new AdminModuleListModel();
        $moduleList           = $adminModuleListModel->select(['type', 'parameter'])
                                                     ->where('admin_module_alias', $moduleAlias)
                                                     ->where('admin_role_id', $roleId)
                                                     ->first();
        
        if (empty($moduleList))
        {
            return false;
        }

        return [
            'access_type' => $moduleList['type'],
            'parameter'   => empty($moduleList['parameter']) ? null : json_decode($moduleList['parameter'], true)
        ];
    }

    //================================================================================================
}