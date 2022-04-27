<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class GraficoController extends ControllerBase
{
   // ARTISTA //

  function Cb_Artista()
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $query = "SELECT DISTINCT terminoTaxAutoria.name as autor, 
      terminoTaxAutoria.tid as autorId
      FROM node
      LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
      LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
      LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
      LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
      LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid   
      WHERE  node.type = 'obra' and terminoTaxAutoria.name is not null";
  
    $resultado = $mysqli->query($query);
    $xValues = "";
    $yValues = "";
    $stringColor ="";

    while ($fila = mysqli_fetch_array($resultado)) { 

      $yValues.=$fila['autorId'].",";  
      $xValues.="'".substr($fila['autor'],0,100)."',"; 
      $stringColor .=  "'".$this->colorRGB()."',"; 
    }
    mysqli_close($mysqli);
    $grafico['yValues'] = $yValues;
    $grafico['xValues'] = $xValues;
    $grafico['stringColor'] = $stringColor;
    return $grafico;

  }

  /* TEMATICA */
  function Cb_Tematica(){
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $query = "SELECT DISTINCT
    terminoTaxTematica.name as Tematica,
    terminoTaxTematica.tid as idTematica
    FROM node
    LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
    LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
    LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
    LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
    LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
    LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
    LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid
    WHERE  node.type = 'obra' and terminoTaxTematica.name is not null";

    $resultado = $mysqli->query($query);
    $xValues = "";
    $yValues = "";
    $stringColor ="";

    while ($fila = mysqli_fetch_array($resultado)) { 

      $yValues.=$fila['idTematica'].",";  
      $xValues.="'".substr($fila['Tematica'],0,100)."',"; 
      $stringColor .=  "'".$this->colorRGB()."',"; 

    }
    mysqli_close($mysqli);
    $grafico['yValues'] = $yValues;
    $grafico['xValues'] = $xValues;
    $grafico['stringColor'] = $stringColor;
    return $grafico;
  }

  /* ANNIO */ 
  function Cb_Annio()
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $query = "select node.nid,
      fecEjecucion.field_fecha_ejecucion_timestamp as idfecEjec, 
      DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') as fecEjec
      from node
      LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
      LEFT JOIN field_data_field_fecha_ejecucion fecEjecucion ON fecEjecucion.entity_id = iden.field_identificacion_value
      WHERE fecEjecucion.field_fecha_ejecucion_timestamp IS NOT NULL AND DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') IS NOT NULL
      GROUP BY fecEjecucion.field_fecha_ejecucion_timestamp";

    $resultado = $mysqli->query($query);
    $xValues = "";
    $yValues = "";
    $stringColor ="";

    while ($fila = mysqli_fetch_array($resultado)) {

     $yValues.= $fila["idfecEjec"];
     $xValues.="'".substr($fila['fecEjec'],0,100)."',"; 
     $stringColor .=  "'".$this->colorRGB()."',"; 
  
    }
    mysqli_close($mysqli);
    $grafico['yValues'] = $yValues;
    $grafico['xValues'] = $xValues;
    $grafico['stringColor'] = $stringColor;
    return $grafico;
  }

  /* TECNICA */
  function Cb_Tecnica()
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $query = "SELECT 
      terminoTaxTematica.name as Tecnica,
      terminoTaxTematica.tid as idTecnica
      FROM node
      LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
      LEFT JOIN field_data_field_tecnica tecnicaObra ON tecnicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tecnicaObra.field_tecnica_tid
      WHERE terminoTaxTematica.name IS NOT NULL
      GROUP BY Tecnica";

    $resultado = $mysqli->query($query);
    $xValues = "";
    $yValues = "";
    $stringColor ="";

    while ($fila = mysqli_fetch_array($resultado)) {
      $yValues.= $fila["idTecnica"];
      $xValues.="'".substr($fila['Tecnica'],0,100)."',"; 
      $stringColor .=  "'".$this->colorRGB()."',"; 
    }
    mysqli_close($mysqli);
    $grafico['yValues'] = $yValues;
    $grafico['xValues'] = $xValues;
    $grafico['stringColor'] = $stringColor;
    return $grafico;
  }

  function colorRGB(){
    $color = ['#22555D','#2D717C', '#578D96','#ABC6CB','#D5E3E5'];
    return $color[rand(0,4)];
  }

  function grafico()
  {

    $grafico = $this->Cb_Artista();
    $grafico = $this->Cb_Tematica();
    $grafico = $this->Cb_Annio();
    $grafico = $this->Cb_Tecnica();
    return [
        '#theme' => 'vpm-vista-grafico',
        '#grafico' => $grafico,
        //'#artista' => $artista,
  
      ];
  }
}

    //var xValues = [{{ grafico['strinTecnica']|raw }}];
    //var yValues = [{{ grafico['strinIdTecnica']|raw }}];

    //$infoArtista['autorId'] = $fila["autorId"];
    //$infoArtista['autor'] = $fila["autor"];