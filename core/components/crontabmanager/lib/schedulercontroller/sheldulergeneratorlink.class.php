<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 27.07.2019
 * Time: 16:14
 */

class SheldulerGeneratorLink
{
    /* @var modX $modx */
    public $modx;

    /* @var string $basePath */
    public $basePath;
    /* @var string $basePathControllersLink */
    public $basePathControllersLink;

    public function __construct(modX &$modx)
    {
        $this->modx = $modx;
    }

    public function process($basePath,$basePathControllersLink)
    {
        $this->basePath = $basePath;
        $this->basePathControllersLink = $basePathControllersLink;
        $this->get_dir($basePath);
    }


    /**
     * Создание ссылка на файл для задания крон
     */
    static private function createTaskCron($task, $basePath,$basePathControllersLink)
    {
        if (!empty($task)) {

            $dirLink = $basePathControllersLink;
            $link = $dirLink . '/' . $task . '.php';
            $dir = $dirLink . '' . dirname('/' . $task);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $output = '';
            $output .= "<?php\n";
            $output .= "define('MODX_CRONTAB_MODE', true); \n";
            $output .= "require_once '" . $basePath . "/index.php'; \n";
            $output .= '$scheduler->php("' . $task . '");
';
            $output .= '$scheduler->process();';
            $fp = fopen($link, 'w+');
            fwrite($fp, $output);
            fclose($fp);

        }

    }

    /**
     * Вспомогательнная функция
     * @param $dir
     * @return array
     */
    static private function myscandir($dir)
    {
        $list = scandir($dir);
        unset($list[0], $list[1]);
        return array_values($list);
    }

    /**
     * Сканирует в директории контроллеры для создания карты
     * @param $dir
     * @return bool
     */
    private function get_dir($dir)
    {
        if (empty($dir)) return false;

        $list = $this->myscandir($dir);
        foreach ($list as $file) {
            if (is_dir($dir . $file)) {
                $this->get_dir($dir . $file . '/');
            } else {
                // Пропуск отключенных контроллеров и других файлов, для избежания создания ссылок на них

                if (strripos($file, '_') === false) {
                    $task = $dir . $file;
                    $task = str_ireplace($this->basePath, '', $task);
                    $task = str_ireplace('.php', '', $task);
                    $this->createTaskCron($task, dirname($this->basePath),$this->basePathControllersLink);
                    #unlink($dir.$file);
                }
            }
        }
        return true;
    }

}