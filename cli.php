<?php

if (php_sapi_name() != 'cli') {
    die('CLI only');
}

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Minimal extends CLI
{
    function __construct()
    {
        $this->bin = __FILE__;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function main(Options $options)
    {
        switch ($options->getCmd()) {
            case 'cache:clear':
                array_map(function (string $cache) {
                    unlink($cache);
                }, glob(buildpath(COREPATH, 'cache' . DIRECTORY_SEPARATOR . '*.php')));
                Plugins::plugin_list_recache();
                file_put_contents(buildpath(COREPATH, 'version'), time());
                $this->success('Cache cleared');
                break;
            default:
                echo $options->help();
                break;
        }
    }


    /**
     * @return void
     */
    protected function setup(Options $options)
    {
        $options->setHelp('SphereCube CLI');
        $options->registerCommand('cache:clear', 'Clear cache');
    }
}
// execute it
$cli = new Minimal();
$cli->run();
