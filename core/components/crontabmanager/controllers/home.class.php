<?php

/**
 * The home manager controller for CronTabManager.
 *
 */
class CronTabManagerHomeManagerController extends modExtraManagerController
{
    /** @var CronTabManager $CronTabManager */
    public $CronTabManager;


    /**
     *
     */
    public function initialize()
    {
        $this->CronTabManager = $this->modx->getService('CronTabManager', 'CronTabManager', MODX_CORE_PATH . 'components/crontabmanager/model/');

        $this->tokenIssuance();
        parent::initialize();
    }


    /**
     *  Выдача api_key после подтверждения прав
     */
    public function tokenIssuance()
    {
        if (!empty($_GET['oauth']) && $_GET['oauth'] === 'application') {
            if (!$this->modx->hasPermission('crontabmanager_view')) {
                $this->failure($this->modx->lexicon('access_denied'));
            } else {
                $Auth = new \Webnitros\CronTabManager\Auth($this->CronTabManager);
                $Auth->createApiKey($this->modx->user);
            }
        }
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['crontabmanager:manager', 'crontabmanager:default'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return $this->modx->hasPermission('crontabmanager_view');
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('crontabmanager');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->CronTabManager->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/crontabmanager.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/misc/processorx.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/tasks/grid.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/tasks/logs/grid.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/tasks/autopauses/grid.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/tasks/autopauses/windows.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/tasks/windows.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/categories/grid.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/categories/windows.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/notifications/grid.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/notifications/windows.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/sections/home.js');

        $time_server = date('H:i:s', time());

        $this->CronTabManager->config['help_buttons'] = ($buttons = $this->getButtons()) ? $buttons : '';

        $this->addHtml('<script type="text/javascript">
        CronTabManager.config = ' . json_encode($this->CronTabManager->config) . ';
        CronTabManager.config.connector_url = "' . $this->CronTabManager->config['connectorUrl'] . '";
        CronTabManager.config.connector_cron_url = "' . $this->CronTabManager->config['connectorCronUrl'] . '";
        CronTabManager.config.time_server = "' . $time_server . '";
        Ext.onReady(function() {MODx.load({ xtype: "crontabmanager-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="crontabmanager-panel-home-div"></div>';
        return '';
    }


    /**
     * @return string
     */
    public function getButtons()
    {
        $buttons = null;
        $name = 'CronTabManager';
        $path = "Extras/{$name}/_build/build.php";
        if (file_exists(MODX_BASE_PATH . $path)) {
            $site_url = $this->modx->getOption('site_url') . $path;
            $buttons[] = [
                'url' => $site_url,
                'text' => $this->modx->lexicon('crontabmanager_button_install'),
            ];
            $buttons[] = [
                'url' => $site_url . '?download=1&encryption_disabled=1',
                'text' => $this->modx->lexicon('crontabmanager_button_download'),
            ];
            $buttons[] = [
                'url' => $site_url . '?download=1',
                'text' => $this->modx->lexicon('crontabmanager_button_download_encryption'),
            ];
        }
        return $buttons;
    }
}
