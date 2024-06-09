<?php

namespace App\Libraries\Ghivarra;

use Config\Services;
use App\Models\AdminModel;
use App\Models\AdminRoleModuleModel;
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
        $useragent  = $request->getUserAgent();
        $mobileType = $useragent->getMobile();

        return [
            'browser'  => $useragent->getBrowser(),
            'os'       => $useragent->getPlatform(),
            'mobile'   => $useragent->isMobile(),
            'platform' => $useragent->isMobile() ? 'Mobile Platform' : 'Non-Mobile Platform'
        ];
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
            $_ENV['SESSION_LOGIN_PARAM'] => $_ENV['SESSION_LOGIN_TOKEN']
        ]);
        
        // save login data in admin_session
        $adminSessionModel = new AdminSessionModel();
        $adminSessionModel->save([
            'name'       => $this->session->session_id,
            'admin_id'   => $accountData['id'],
            'useragent'  => json_encode($this->parseUserAgent($request)),
            'ip_address' => $request->getIPAddress()
        ]);

        // auth success
        return true;
    }

    //================================================================================================

    public function check(): bool
    {
        $sessionToken = $this->session->get($_ENV['SESSION_LOGIN_PARAM']);

        // session is not valid
        if (empty($sessionToken) OR $_ENV['SESSION_LOGIN_TOKEN'] !== $sessionToken)
        {
            return false;
        }

        // update account data
        $adminModel        = new AdminModel();
        $adminSessionModel = new AdminSessionModel();
        $accountData       = $this->session->get('accountData');
        $accountId         = $accountData['id'];

        // check if session exist
        $session = $adminSessionModel->select(['id', 'useragent', 'ip_address'])
                                     ->where('name', $this->session->session_id)
                                     ->first();

        if (empty($session))
        {
            return false;
        }

        // check if account exist
        $accountData = $adminModel->select(['admin.id', 'username', 'admin.name', 'email', 'email_verified_at', 'admin.status', 'admin_role_id', 'admin_role.name as admin_role_name', 'is_superadmin', 'photo'])
                                  ->join('admin_role', 'admin_role_id = admin_role.id', 'inner')
                                  ->where('admin.id', $accountId)
                                  ->where('admin.status', 'Aktif')
                                  ->where('email_verified_at IS NOT', NULL)
                                  ->first();

        if (empty($accountData))
        {
            return false;
        }

        // set new admin data 
        $this->session->set('accountData', $accountData);

        // check request type
        $request = Services::request();

        // update user agent
        $userAgent = json_encode($this->parseUserAgent($request));
        $ipAddress = $request->getIPAddress();

        // update if different
        if ($session['useragent'] !== $userAgent OR $session['ip_address'] !== $ipAddress)
        {
            $adminSessionModel->save([
                'id'         => $session['id'],
                'useragent'  => $userAgent,
                'ip_address' => $ipAddress
            ]);
        }

        // only regenerate on non ajax request to prevent session racing
        if (!$request->isAJAX())
        {
            // check if session ttl needed to be regenerate
            $TTL = time() - $_ENV['SESSION_LOGIN_TTL'];
    
            if ($TTL > $this->session->get('lastRegenerate'))
            {
                // store old id
                $oldId = $session['id'];
                
                // regenerate
                $this->session->regenerate();
    
                // load services and models
                $request = Services::request();
    
                // update old id to new id
                $adminSessionModel->where('id', $oldId)
                                ->set([
                                    'name'       => $this->session->session_id,
                                    'admin_id'   => $accountId,
                                    'useragent'  => json_encode($this->parseUserAgent($request)),
                                    'ip_address' => $request->getIPAddress()
                                ])
                                ->update();
            }
        }
        
        // return
        return true;
    }

    //================================================================================================

    public function deleteSession($sessionId): bool
    {
        $db  = \Config\Database::connect();
        $orm = new AdminSessionModel();
        
        // transaction start
        $db->transBegin();
        $orm->delete($sessionId);

        if ($db->transStatus() === false) {

            $db->transRollback();
            return false;

        } else {

            $db->transCommit();
            return true;
        }
    }

    //================================================================================================

    public function getAccountData(): array
    {
        return $this->session->get('accountData');
    }

    //================================================================================================

    public function getSession(): array
    {
        $accountData = $this->getAccountData();

        $orm = new AdminSessionModel();
        $get = $orm->select(['id', 'name', 'useragent', 'ip_address', 'updated_at as last_update'])
                   ->where('admin_id', $accountData['id'])
                   ->orderBy('last_update', 'DESC')
                   ->find();

        // return
        return empty($get) ? [] : $get;
    }

    //================================================================================================

    public function getSessionFromUser($adminId): array
    {
        $orm = new AdminSessionModel();
        $get = $orm->select(['id', 'name', 'useragent', 'ip_address', 'updated_at as last_update'])
                   ->where('admin_id', $adminId)
                   ->orderBy('last_update', 'DESC')
                   ->find();

        // return
        return empty($get) ? [] : $get;
    }

    //================================================================================================

    public function getSessionName(): string
    {
        return $this->session->session_id;
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
        $orm        = new AdminRoleModuleModel();
        $moduleList = $orm->select(['type', 'parameter'])
                          ->join('admin_module', 'admin_module_id = admin_module.id', 'left')
                          ->where('alias', $moduleAlias)
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
}