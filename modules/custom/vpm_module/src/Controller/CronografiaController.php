<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class CronografiaController extends ControllerBase
{
    function cronografia()
    {

        $cronografia = [];
        return [
            '#theme' => 'vpm-vista-cronografia',
            '#cronografia' => $cronografia
        ];
    }
}
