<?php

namespace App\Filters;

use App\Models\AdminSessionModel;
use Config\Session as SessionConfig;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SessionGarbageCollector implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (random_int(1, 100) <= $_ENV['SESSION_GC_PROBABILITY'])
        {
            // session
            $sessionConfig = new SessionConfig();

            // glob session folders
            $sessions       = glob($sessionConfig->savePath . DIRECTORY_SEPARATOR .  $sessionConfig->cookieName . "*");
            $expirationTime = time() - $sessionConfig->expiration;
            $expiredSession = [];

            foreach ($sessions as $session):
                
                // delete if session already expired
                if (filemtime($session) <= $expirationTime)
                {
                    unlink($session);
                    $sessionName = substr(strrchr($session, DIRECTORY_SEPARATOR), 1);
                    array_push($expiredSession, str_ireplace($sessionConfig->cookieName, '', $sessionName));
                }

            endforeach;

            // search and delete sessions from db
            if (!empty($expiredSession))
            {
                $adminSessionModel = new AdminSessionModel();
                $adminSessionModel->whereIn('name', $expiredSession)->delete();
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
