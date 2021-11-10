<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 09.11.2021
 * Time: 01:13
 */

namespace Webnitros\CronTabManager;

use CronTabManager;
use GetPhoto\Oauth2\Client\Provider\OauthProvider;
use modRestServer;
use modUser;

class Auth
{
    /* @var CronTabManager $CronTabManager */
    protected $CronTabManager;

    /* @var modRestServer $server */
    protected $server;

    public function __construct(CronTabManager $cronTabManager)
    {
        $this->CronTabManager = $cronTabManager;
    }

    public function process()
    {

    }


    public function createApiKey(modUser $User)
    {
        $username = $User->get('username');
        $user_id = $User->get('id');
        $criteria = [
            'active:=' => true,
            'user_id:=' => $user_id,
            'valid_until:>' => time(),
        ];
        /* @var CronTabManagerToken $Token */
        if ($Token = $this->CronTabManager->modx->getObject('CronTabManagerToken', $criteria)) {
            $token = $Token->get('token');
        } else {
            $token = $this->_token($User);
            $valid_until = strtotime(date('Y-m-d', strtotime('+30 days', time())));
            $Token = $this->CronTabManager->modx->newObject('CronTabManagerToken');
            $Token->set('user_id', $user_id);
            $Token->set('active', true);
            $Token->set('valid_until', $valid_until);
            $Token->set('token', $token);
            $Token->set('createdon', time());
            $Token->save();
        }


        $url = $this->CronTabManager->modx->getOption('crontabmanager_rest_controller', null,'');
        $redirect_uri = $_REQUEST['redirect_uri'];
        $redirect_uri .= '?token=' . $token . '&username=' . $username . '&rest_url=' . $url;
        $this->CronTabManager->modx->sendRedirect($redirect_uri, false, '', 302);

    }


    public function _token(modUser $User)
    {
        return md5(
            $User->get('username') .
            rand(5, 10) .
            $User->get('createdon') .
            time() .
            __CLASS__
        );
    }


}