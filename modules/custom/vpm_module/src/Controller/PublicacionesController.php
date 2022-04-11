<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class PublicacionesController extends ControllerBase
{
  function Listar_Query($paginador)
  {
    $limit = 9;
    if (!empty($_GET["pag"])) {
      $pag = $_GET["pag"];
    } else {
      $pag = 0;
    }

    if ($pag < 1) {
      $pag = 1;
    }
    $offset = ($pag - 1) * $limit;

    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');

    $sql = "SELECT archivo.entity_id as codArchivo, managed.filename as name, managed.uri as uri, node.title as title, managed.timestamp as fecha
        FROM node
        LEFT JOIN field_data_field_archivo archivo ON archivo.entity_id = node.nid        
        LEFT JOIN field_data_field_autor_ensayo autorensayo ON autorensayo.entity_id = archivo.entity_id
        LEFT JOIN field_data_field_tags_ensayos ensayo ON ensayo.entity_id = autorensayo.entity_id
        LEFT JOIN file_managed managed ON managed.fid = archivo.field_archivo_fid
        LEFT JOIN taxonomy_term_data taxensayo ON taxensayo.tid = ensayo.entity_id
        WHERE managed.filename IS NOT null";

    if (!$paginador) {
      $sql = $sql . " LIMIT $offset, $limit";
    }

    $resultado = $mysqli->query($sql);
    mysqli_close($mysqli);
    return $resultado;
  }


  function Listar_Publicaciones($paginador)
  {
    $limit = 9;

    if (!empty($_GET["pag"])) {
      $pag = $_GET["pag"];
    } else {
      $pag = 0;
    }
    if ($pag < 1) {
      $pag = 1;
    }

    $resultado = $this->Listar_Query($paginador);
    $publicaciones = [];
    $x = 0;
    $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';
    while ($fila = mysqli_fetch_array($resultado)) {
      $infoPublica = [];

      $infoPublica['idPublica'] = $fila["codArchivo"];

      if (isset($_GET["idCat"])) {
        $infoPublica['urlPublica'] =  base_path() . "publicacion?idCat=1&Sec=Publ&idPublicacion=" . $fila["codArchivo"];
      } else {
        $infoPublica['urlPublica'] = base_path() . "publicacion?idPublicacion=" . $fila["codArchivo"];
      }


      $infoPublica['name'] = $fila["name"];
      $infoPublica['fecha'] = $fila["fecha"];
      $infoPublica['fecha'] = date("d/m/Y", strtotime($infoPublica['fecha']));
      $infoPublica['uri'] = $fila["uri"];
      $infoPublica['title'] = $fila["title"];

      /* RUTA ARCHIVO */
      $infoPublica['rutaArchivo'] = $fila["title"];
      $infoPublica['rutaArchivo'] = $rutaQuinsac . $fila["name"];


      $publicaciones[$x] = $infoPublica;
      $x++;
    }
    return $publicaciones;
  }

  function Lista_Paginador($paginador)
  {
    $limit = 9;
    $limitPage = 7;
    $pag = (int) (!empty($_GET["pag"]));
    if ($pag < 1) {
      $pag = 1;
    }


    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $offset = ($pag - 1) * $limit;
    $result = $this->Listar_Query($paginador);
    $total = $result->num_rows;

    $page = false;

    //examino la pagina a mostrar y el inicio del registro a mostrar
    if (!isset($_GET['pag'])) {
      $page = 1;
    } else {
      $page = $_GET['pag'];
    }

    if (!$page) {
      $start = 0;
      $page = 1;
    } else {
      $start = ($page - 1) *  $limit;
    }
    $total_pages = ceil($total / $limit);

    $variables = '?';
    foreach ($_GET as $key => $value) {
      if ($key != "pag") {
        $variables = $variables . $key . "=" . $value . "&";
      }
    }
    $arrayBoton = [];
    $z = 0;
    if ($total_pages >= 1 && $page <= $total_pages) {
      $counter = 1;
      $arrayBotones = [];

      if ($page != 1) {
        $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'publicaciones/' . $variables . 'pag=' . ($page - 1) . '">
                    <button class="pager__item"><span aria-hidden="true"><i class="fa fa-chevron-left" aria-hidden="true"></i>
                    </span></button></a>';
        $z++;
      }
      if ($page > ($limitPage / 2)) {
        $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'publicaciones/' . $variables . 'pag=' . 1 . '">
                    <button class="pager__item"><span aria-hidden="true">1...</span></button></a>';
        $z++;
      }
      for ($x = $page - 2; $x <= $total_pages; $x++) {
        if ($x >= 1) {
          if ($counter < $limitPage) {
            if ($page == $x) {
              $arrayBotones[$z] = '<a class="pagelink" href="#"><button class="pager__item is-active">' . $page . '</button></a>';
              $z++;
            } else {
              $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'publicaciones/' . $variables . 'pag=' . $x . '">
                      <button class="pager__item">' . $x . '</button></a>';
              $z++;
            }
            $counter++;
          }
        }
      }
      if ($page < $total_pages - ($limitPage / 2)) {
        $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'publicaciones/' . $variables . 'pag=' . $total_pages . '">
                  <button class="pager__item"><span aria-hidden="true">...' . $total_pages . '</span></button></a>';
        $z++;
      }
      if ($page != $total_pages) {
        $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'publicaciones/' . $variables . 'pag=' . ($page + 1) . '">
                <button class="pager__item"><span aria-hidden="true"><i class="fa fa-chevron-right" aria-hidden="true"></i></span></button></a>';
        $z++;
      }
      $arrayBoton = $arrayBotones;
    }
    return $arrayBoton;
  }

  function publicaciones()
  {
    $publicaciones = $this->Listar_Publicaciones(false);
    $arrayBoton = $this->Lista_Paginador(true);
    return [
      '#theme' => 'vpm-vista-publicaciones',
      '#publicaciones' => $publicaciones,
      '#arrayBoton' => $arrayBoton

    ];
  }

  function getInfPublica($idPublica)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');

    $sql = "SELECT archivo.entity_id as codArchivo,
    managed.filename as name, 
     managed.uri as uri, 
     managed.filesize as filesize, 
     node.title as title, 
     managed.timestamp as fecha,
     taxAutorEnsayo.name as nombreAutor,
     taxensayo.name as etiquetaPublicacion
        FROM node
        LEFT JOIN field_data_field_archivo archivo ON archivo.entity_id = node.nid        
        LEFT JOIN field_data_field_autor_ensayo autorensayo ON autorensayo.entity_id = node.nid
        LEFT JOIN taxonomy_term_data taxAutorEnsayo ON taxAutorEnsayo.tid = autorensayo.field_autor_ensayo_tid

        
        LEFT JOIN file_managed managed ON managed.fid = archivo.field_archivo_fid

        LEFT JOIN field_data_field_tags_ensayos tagsPublicacionEnsayo ON tagsPublicacionEnsayo.entity_id = node.nid
        LEFT JOIN taxonomy_term_data taxensayo ON taxensayo.tid = tagsPublicacionEnsayo.field_tags_ensayos_tid

        
        WHERE archivo.entity_id IS NOT NULL AND archivo.entity_id=?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idPublica);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $publica = [];

    $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';
    while ($fila = $result->fetch_assoc()) {
      $publica['name'] = $fila["name"];
      $publica['fecha'] = $fila["fecha"];
      $publica['fecha'] = date("d/m/Y", strtotime($publica['fecha']));
      $publica['uri'] = $fila["uri"];
      $publica['title'] = $fila["title"];
      $publica['autor'] = $fila["nombreAutor"];
      $publica['etiquetaPublicacion'] = $fila["etiquetaPublicacion"];
      $publica['filesize'] = $this->formatSizeUnits($fila["filesize"]);

      /* RUTA ARCHIVO */
      $publica['rutaArchivo'] = $fila["title"];
      $publica['rutaArchivo'] = $rutaQuinsac . $fila["name"];
    }
    return $publica;
  }
  function formatSizeUnits($bytes)
  {
    if ($bytes >= 1073741824) {
      $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
      $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
      $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
      $bytes = $bytes . ' byte';
    } else {
      $bytes = '0 bytes';
    }

    return $bytes;
  }
  function publicacion()
  {
    $idPublicacion = 0;
    if (isset($_GET["idPublicacion"])) {
      $idPublicacion = $_GET["idPublicacion"];
    }
    $publicacion = $this->getInfPublica($idPublicacion);
    return [
      '#theme' => 'vpm-vista-publicacion',
      '#publicacion' => $publicacion
    ];
  }
}
