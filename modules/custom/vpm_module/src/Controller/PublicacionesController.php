<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class PublicacionesController extends ControllerBase
{
    function publicaciones()
    {
        $publicaciones = "Esto es una prueba";
        return [
            '#theme' => 'vpm-vista-publicaciones',
            '#publicaciones' => $publicaciones
    
        ];
    }
}