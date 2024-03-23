<?php

namespace App\Libraries\Ghivarra;

use Config\Services;
use App\Models\AdminModel;
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

    public function check(): bool
    {
        $sessionToken = $this->session->get($_ENV['SESSION_LOGIN_PARAM']);

        if (empty($sessionToken))
        {
            return false;
        }

        // check if hash is equals
        return hash_equals(md5($_ENV['SESSION_LOGIN_TOKEN']), $sessionToken) ? true : false;
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

        // clear private data and put admin data + login token in sessions
        unset($accountData['password']);
        $this->session->set([
            'accountData'                => $accountData,
            $_ENV['SESSION_LOGIN_PARAM'] => md5($_ENV['SESSION_LOGIN_TOKEN'])
        ]);

        // get user agent data
        $useragent = $request->getUserAgent();
        $agentData = [
            'browser'  => $useragent->getBrowser() . ' ' . $useragent->getVersion(),
            'os'       => $useragent->getPlatform(),
            'mobile'   => $useragent->isMobile(),
            'platform' => $useragent->isMobile() ? $useragent->getMobile() : 'Non-Mobile Platform'
        ];

        // save login data in admin_session
        $adminSessionModel = new AdminSessionModel();
        $adminSessionModel->insert([
            'name'       => $this->session->session_id,
            'admin_id'   => $accountData['id'],
            'useragent'  => json_encode($agentData),
            'ip_address' => $request->getIPAddress()
        ]);

        // auth success
        return true;
    }

    //================================================================================================

    public function logout()
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

    public function getAccountData()
    {
        return $this->session->get('accountData');
    }

    //================================================================================================
}