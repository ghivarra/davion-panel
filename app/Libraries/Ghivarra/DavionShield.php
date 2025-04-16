<?php

namespace App\Libraries\Ghivarra;

use Config\Services;
use App\Models\AdminModel;
use App\Models\AdminRoleModuleModel;
use App\Models\AdminSessionModel;

class DavionShield
{
    protected $session;
    protected $accountData;
    protected $clientID;

    //================================================================================================

    public function __construct()
    {
        $this->session     = Services::session();
        $this->accountData = $this->session->get('__accountData');
        $this->clientID    = $this->session->get('__clientID');
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

        // create client ID
        $clientID = createRandomID();

        // clear private data and put admin data + login token in sessions
        unset($accountData['password']);
        $this->session->set([
            '__clientID'                 => $clientID,
            '__accountData'              => $accountData,
            $_ENV['SESSION_LOGIN_PARAM'] => $_ENV['SESSION_LOGIN_TOKEN']
        ]);

        // update account data
        $this->accountData = $accountData;
        
        // save login data in admin_session
        $adminSessionModel = new AdminSessionModel();
        $adminSessionModel->save([
            'name'       => $this->session->session_id,
            'admin_id'   => $accountData['id'],
            'client_id'  => $clientID,
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
        $accountData       = $this->accountData;
        $accountId         = $accountData['id'];
        $sessionId         = $this->session->session_id;

        // check if session exist
        $session = $adminSessionModel->select(['id', 'name', 'useragent', 'ip_address'])
                                     ->where('name', $sessionId)
                                     ->orWhere('client_id', $this->clientID)
                                     ->first();

        // if session is not found
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
        $this->session->set('__accountData', $accountData);

        // update account data
        $this->accountData = $accountData;

        // check request type
        $request = Services::request();

        // update user agent
        $userAgent = json_encode($this->parseUserAgent($request));
        $ipAddress = $request->getIPAddress();

        // update if different
        if ($session['useragent'] !== $userAgent OR $session['ip_address'] !== $ipAddress OR $session['name'] !== $sessionId)
        {
            $adminSessionModel->save([
                'id'         => $session['id'],
                'name'       => $sessionId,
                'useragent'  => $userAgent,
                'ip_address' => $ipAddress,
            ]);
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
        return $this->accountData;
    }

    //================================================================================================

    public function getClientID(): string
    {
        return $this->clientID;
    }

    //================================================================================================

    public function getSession(): array
    {
        $accountData = $this->getAccountData();

        $orm = new AdminSessionModel();
        $get = $orm->select(['id', 'name', 'useragent', 'client_id', 'ip_address', 'updated_at as last_update'])
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
        $get = $orm->select(['id', 'name', 'useragent', 'client_id', 'ip_address', 'updated_at as last_update'])
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
        $clientId  = $this->clientID;

        // remove active session
        $adminSessionModel = new AdminSessionModel();
        $adminSessionModel->where('name', $sessionId)
                          ->orWhere('client_id', $clientId)
                          ->delete();

        // invalidate session and return
        $this->session->destroy();

        // always return true
        return true;
    }

    //================================================================================================
}