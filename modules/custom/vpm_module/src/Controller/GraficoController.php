<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class GraficoController extends ControllerBase
{
  function Listar_Query($opcion)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');

    /* QUERY POR TEMATICA * FECHA */ 
  
    $valorCorX = $opcion;
    if($valorCorX == 1){
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
      $query ="SELECT 
      IFNULL(terminoTaxTematica.name, 'Desconocido')  as Tematica,
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
       WHERE node.type = 'obra' AND node.status=1 AND DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') IS NOT NUll";

        $resultado = $mysqli->query($query);
                  
        $xValues = "";
        $yValues = "";
        $stringColor ="";
        $cantObras=1;
        $prov='';
        while ($fila = mysqli_fetch_array($resultado)) { 
          if($prov!="'Género pictórico: ".substr($fila['Tematica'],0,100)." - Año: ".$fila['fecEjec']."',"){
            $yValues.=$cantObras.",";      
            $cantObras=1;
                    
            $xValues.="'Género pictórico: ".substr($fila['Tematica'],0,100)." - Año: ".$fila['fecEjec']."',"; 
            $prov="'Género pictórico: ".substr($fila['Tematica'],0,100)." - Año: ".$fila['fecEjec']."',"; 
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

    }elseif($valorCorX == 2){ 
         /* QUERY POR TECNICA * FECHA */ 
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
      /* QUERY POR OBRA * FECHA OCUPAR ESTA QUERY PARA SEGUIR EL FILTRO AVANZADO*/ 
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
      /* QUERY POR ARTISTA*OBRA - FECHA */ 
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
  }

  function colorRGB(){
    $color = ['#22555D','#2D717C', '#578D96','#ABC6CB','#D5E3E5'];
    return $color[rand(0,4)];
  }

  function grafico()
  {
    $valorCorX = 0;
    if (isset($_GET["cX"])) {
      $valorCorX = $_GET["cX"];
    }
    
    $grafico = $this->Listar_Query($valorCorX);
    
    return [
        '#theme' => 'vpm-vista-grafico',
        '#grafico' => $grafico
      ];
  }
}