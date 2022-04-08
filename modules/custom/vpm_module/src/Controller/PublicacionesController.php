<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class PublicacionesController extends ControllerBase
{
    function Listar_Query($paginador){
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

        $sql = "SELECT managed.filename as name 
        FROM node
        LEFT JOIN field_data_field_archivo archivo ON archivo.entity_id = node.nid        
        LEFT JOIN field_data_field_autor_ensayo autorensayo ON autorensayo.entity_id = archivo.entity_id
        LEFT JOIN field_data_field_tags_ensayos ensayo ON ensayo.entity_id = autorensayo.entity_id
        LEFT JOIN file_managed managed ON managed.fid = archivo.field_archivo_fid
        LEFT JOIN taxonomy_term_data taxensayo ON taxensayo.tid = ensayo.entity_id
        WHERE managed.filename IS NOT NULL";

        if (!$paginador) {
            $sql = $sql . " LIMIT $offset, $limit";
        }      

        $resultado = $mysqli->query($sql);
        mysqli_close($mysqli);
        return $resultado;

    }


    function Listar_Publicaciones($paginador){
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
        while ($fila = mysqli_fetch_array($resultado)) {
            $infoPublica = [];
        
            $infoPublica['name'] = $fila["name"];
            //$infoPublica['fechaPublica'] = $fila["fecha"];
            
            $publicaciones[$x] = $infoPublica;
            $x++;
        }
        return $publicaciones;
    }

    function Lista_Paginador($paginador)
    {
      $limit = 9;
      $limitPage = 9;
      $pag = (int) (!empty($_GET["pag"]));
      if ($pag < 1) {
        $pag = 1;
      }
  
  
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
      $offset = ($pag - 1) * $limit;
  
      $sqlTotal = "SELECT COUNT(*) total         
        FROM node
        LEFT JOIN field_data_field_archivo archivo ON archivo.entity_id = node.nid        
        LEFT JOIN field_data_field_autor_ensayo autorensayo ON autorensayo.entity_id = archivo.entity_id
        LEFT JOIN field_data_field_tags_ensayos ensayo ON ensayo.entity_id = autorensayo.entity_id
        LEFT JOIN file_managed managed ON managed.fid = archivo.field_archivo_fid
        LEFT JOIN taxonomy_term_data taxensayo ON taxensayo.tid = ensayo.entity_id
        WHERE managed.filename IS NOT NULL";

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
          $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . ($page - 1) . '">
                    <button class="pager__item"><span aria-hidden="true"><i class="fa fa-chevron-left" aria-hidden="true"></i>
                    </span></button></a>';
          $z++;
        }
        if ($page > ($limitPage / 2)) {
          $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . 1 . '">
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
                $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . $x . '">
                      <button class="pager__item">' . $x . '</button></a>';
                $z++;
              }
              $counter++;
            }
          }
        }
        if ($page < $total_pages - ($limitPage / 2)) {
          $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . $total_pages . '">
                  <button class="pager__item"><span aria-hidden="true">...' . $total_pages . '</span></button></a>';
          $z++;
        }
        if ($page != $total_pages) {
          $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . ($page + 1) . '">
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
}