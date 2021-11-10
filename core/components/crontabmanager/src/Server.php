<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 09.11.2021
 * Time: 01:51
 */

namespace Webnitros\CronTabManager;


use CronTabManager;
use LogicException;
use modRestServiceResponse;
use Webnitros\CronTabManager\Exceptions\AuthException;
use Webnitros\CronTabManager\Exceptions\RestException;

class Server
{
    /* @var CronTabManager $CronTabManager */
    protected $CronTabManager;

    public function __construct(CronTabManager $cronTabManager)
    {
        $this->CronTabManager = $cronTabManager;
        $cronTabManager->modx->lexicon->load('crontabmanager:rest');

        $config = [
            'format' => 'json',
            'realm' => 'CronTabManager'
        ];
        $this->server = $this->CronTabManager->modx->getService('rest.server', 'rest.modRestServer', null, $config);
    }


    public function process()
    {

        $status = 200;
        $enable = $this->CronTabManager->modx->getOption('crontabmanager_rest_enable', NULL, false);
        if ($enable) {
            try {
                $Rest = new Rest($this->CronTabManager);
                $Rest->process();
                $response = $Rest->getResponse();
                $status = $Rest->getStatusCode();
            } catch (AuthException $e) {
                $response = $this->CronTabManager->error($e->getMessage());
            } catch (LogicException $e) {
                $response = $this->CronTabManager->error($e->getMessage());
            } catch (RestException $e) {
                $status = $e->getCode();
                $response = $this->CronTabManager->error($e->getMessage());
            }
        } else {
            $response = $this->CronTabManager->error('rest для приложения отключен');
        }

        $this->response($response, $status);
    }

    public function response($response, $status)
    {
        $headers = $_SERVER['SERVER_PROTOCOL'] . ' ' . $status . ' ' . $this->getResponseCodeMessage($status);
        header($headers);
        header('Content-Type: application/json');

        @session_write_close();
        exit($response);
    }

    /**
     * Get the proper response code message for the passed status code
     *
     * @param int $status
     * @return string
     */
    protected function getResponseCodeMessage($status)
    {
        return (isset(self::$responseCodes[$status])) ? self::$responseCodes[$status] : self::$responseCodes[500];
    }


    /**
     * Map of formats to their parallel content types
     * @var array
     */
    protected static $contentTypes = array(
        'xml' => 'application/xml',
        'json' => 'application/json',
        'qs' => 'text/plain'
    );
    /**
     * Dictionary of response codes and their text descriptions
     * @var array
     */
    protected static $responseCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );


}