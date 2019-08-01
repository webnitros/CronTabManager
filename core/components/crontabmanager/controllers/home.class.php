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
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['crontabmanager:manager','crontabmanager:default'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
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
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/tasks/windows.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/categories/grid.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/categories/windows.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->CronTabManager->config['jsUrl'] . 'mgr/sections/home.js');

        $time_server = date('H:i:s', time());

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
}