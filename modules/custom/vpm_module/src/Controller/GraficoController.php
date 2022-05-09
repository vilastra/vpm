<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class GraficoController extends ControllerBase
{
  function Listar_Query($valorCorX, $valorCorY)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    if ($valorCorX == 1) {
      if ($valorCorY == 1) { // SI SELECCIONÓ OBRAS
        $sql = "COUNT(IFNULL(title, 'Desconocido'))  as EjeY";
      } elseif ($valorCorY == 2) { // SI SELECCIONÓ GÉNERO PICTORICO
        $sql = "IFNULL(terminoTaxTematica.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 3) { // SI SELECCIONÓ TÉCNICA
        $sql = "IFNULL(terminoTaxTecnica.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 4) { // SI SELECCIONÓ SOPORTE
        $sql = "IFNULL(terminoTaxSoporte.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 5) { // SI SELECCIONÓ AUTOR
        $sql = "IFNULL(terminoTaxAutoria.name, 'Desconocido') as EjeY";
      }
    } else {
      return null;
    }
    //IFNULL($sql, 'Desconocido') as nameY,

    $query = "SELECT
      DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') as fecEjec,
      " . $sql . "
      FROM node
      LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
      LEFT JOIN field_data_field_fecha_ejecucion fecEjecucion ON fecEjecucion.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
      LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
      LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
      LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid 
      LEFT JOIN field_data_field_tecnica tecnicaObra ON tecnicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTecnica ON terminoTaxTecnica.tid = tecnicaObra.field_tecnica_tid
      LEFT JOIN field_data_field_soporte soporte ON soporte.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxSoporte ON terminoTaxSoporte.tid = soporte.field_soporte_tid
      WHERE node.type = 'obra' AND node.status=1 AND DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') IS NOT NUll";
    if ($valorCorY == 1) { // SI SELECCIONÓ OBRAS
      $query .= " GROUP BY DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y')";
    }
    $resultado = $mysqli->query($query);

    $xValues = "";
    $yValues = "";
    $stringColor = "";
    $cantObras = 1;
    $prov = '';
    $provY = '';
    $provX = '';
    $cont = 1;


    while ($fila = mysqli_fetch_array($resultado)) {
      if ($valorCorY == 1) {
        $yValues .= $fila['EjeY'] . ",";
        $xValues .= "'Cantidad de Obras: " . substr($fila['EjeY'], 0, 100) . " - Año: " . $fila['fecEjec'] . "',";
        $stringColor .=  "'" . $this->colorRGB() . "',";
      } else {
        if ($prov != $fila['EjeY'] . "-" . $fila['fecEjec']) {
          if ($cont != 1) {
            $xValues .= "'" . substr($provY, 0, 100) . " - Año: " . $provX . "',";
            $yValues .= $cantObras . ",";
            $cantObras = 1;
          }
          $cont++;
          
          $prov = $fila['EjeY'] . "-" . $fila['fecEjec'];
          $provY = $fila['EjeY'];
          $provX = $fila['fecEjec'];
          $stringColor .=  "'" . $this->colorRGB() . "',";
        } else {
          $cantObras++;
          $provY = $fila['EjeY'];
          $provX = $fila['fecEjec'];
        }
      }
    }
    if ($valorCorY != 1) {
      $xValues .= "'" . substr($provY, 0, 100) . " - Año: " . $provX . "',";         
      $yValues .= $cantObras . ",";
    }
    mysqli_close($mysqli);
    $grafico['yValues'] = $yValues;
    $grafico['xValues'] = $xValues;
    $grafico['stringColor'] = $stringColor;
    return $grafico;
  }
  function colorRGB()
  {
    $color = ['#22555D', '#2D717C', '#578D96', '#ABC6CB', '#D5E3E5'];
    return $color[rand(0, 4)];
  }

  function grafico()
  {
    $valorCorX = 0;
    if (isset($_GET["cX"])) {
      $valorCorX = $_GET["cX"];
    }
    $valorCorY = 0;
    if (isset($_GET["cY"])) {
      $valorCorY = $_GET["cY"];
    }


    $grafico = $this->Listar_Query($valorCorX, $valorCorY);

    return [
      '#theme' => 'vpm-vista-grafico',
      '#grafico' => $grafico
    ];
  }
}


/*elseif($valorCorX == 2){ 
         $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
         $query ="SELECT
         DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') as fecEjec,
         IFNULL(terminoTaxTecnica.name, 'Desconocido') as Tecnica
         FROM node
         LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
         LEFT JOIN field_data_field_fecha_ejecucion fecEjecucion ON fecEjecucion.entity_id = iden.field_identificacion_value
         LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
         LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
         LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
         LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
         LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
         LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid 
         LEFT JOIN field_data_field_tecnica tecnicaObra ON tecnicaObra.entity_id = iden.field_identificacion_value
         LEFT JOIN taxonomy_term_data terminoTaxTecnica ON terminoTaxTecnica.tid = tecnicaObra.field_tecnica_tid
         WHERE node.type = 'obra' AND node.status=1 AND DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') IS NOT NUll";
         
         $resultado = $mysqli->query($query);
                  
         $xValues = "";
         $yValues = "";
         $stringColor ="";
         $cantObras=1;
         $prov='';
         
         while ($fila = mysqli_fetch_array($resultado)) { 
          if($prov!="'Técnica: ".substr($fila['Tecnica'],0,100)." - Año: ".$fila['fecEjec']."',"){
            $yValues.=$cantObras.",";      
            $cantObras=1;
                    
            $xValues.="'Técnica: ".substr($fila['Tecnica'],0,100)." - Año: ".$fila['fecEjec']."',"; 
            $prov="'Técnica: ".substr($fila['Tecnica'],0,100)." - Año: ".$fila['fecEjec']."',"; 
            $stringColor .=  "'".$this->colorRGB()."',"; 
          }else{
            $cantObras++;
          }      
        }
        mysqli_close($mysqli);
        $grafico['yValues'] = $yValues;
        $grafico['xValues'] = $xValues;
        $grafico['stringColor'] = $stringColor;
        return $grafico;

    }elseif($valorCorX == 3){ 
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');

      $query ="SELECT count(IFNULL(title, 'Desconocido'))  as Obra,
      DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') as fecEjec
      FROM node
      LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
      LEFT JOIN field_data_field_fecha_ejecucion fecEjecucion ON fecEjecucion.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
      LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
      LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
      LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid 
      LEFT JOIN field_data_field_tecnica tecnicaObra ON tecnicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTecnica ON terminoTaxTecnica.tid = tecnicaObra.field_tecnica_tid
      WHERE node.type = 'obra' AND node.status=1
      AND DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') IS NOT null
      group by DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y')";
      
      $resultado = $mysqli->query($query);
               
      $xValues = "";
      $yValues = "";
      $stringColor ="";
      $cantObras=1;
      $prov='';
      
      while ($fila = mysqli_fetch_array($resultado)) { 
       $yValues.=$fila['Obra'].",";
       $xValues.="'Cantidad de Obras: ".substr($fila['Obra'],0,100)." - Año: ".$fila['fecEjec']."',"; 
       $stringColor .=  "'".$this->colorRGB()."',"; 
     }
     mysqli_close($mysqli);
     $grafico['yValues'] = $yValues;
     $grafico['xValues'] = $xValues;
     $grafico['stringColor'] = $stringColor;
     return $grafico;

    }elseif($valorCorX == 4){ 
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
      $query ="SELECT 
      IFNULL(terminoTaxAutoria.name, 'Desconocido') as autor,
      DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') as fecEjec
      FROM node
      LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
      LEFT JOIN field_data_field_fecha_ejecucion fecEjecucion ON fecEjecucion.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
      LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
      LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
      LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid 
      LEFT JOIN field_data_field_tecnica tecnicaObra ON tecnicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTecnica ON terminoTaxTecnica.tid = tecnicaObra.field_tecnica_tid
      WHERE node.type = 'obra' AND node.status=1
      AND DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') IS NOT NUll";
      
      $resultado = $mysqli->query($query);
               
      $xValues = "";
      $yValues = "";
      $stringColor ="";
      $cantObras=1;
      $prov='';
      
      while ($fila = mysqli_fetch_array($resultado)) { 
       if($prov!="'Artista: ".substr($fila['autor'],0,50)." - Año: ".$fila['fecEjec']."',"){
         $yValues.=$cantObras.",";      
         $cantObras=1;
                 
         $xValues.="'Artista: ".substr($fila['autor'],0,50)." - Año: ".$fila['fecEjec']."',"; 
         $prov="'Artista: ".substr($fila['autor'],0,50)." - Año: ".$fila['fecEjec']."',"; 
         $stringColor .=  "'".$this->colorRGB()."',"; 
       }else{
         $cantObras++;
       }      
     }
     mysqli_close($mysqli);
     $grafico['yValues'] = $yValues;
     $grafico['xValues'] = $xValues;
     $grafico['stringColor'] = $stringColor;
     return $grafico;
    }
  }*/