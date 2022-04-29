<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class GraficoController extends ControllerBase
{
   // ARTISTA //

  /*function Cb_Artista()
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
    $artista = [];
    $x = 0;
    while ($fila = mysqli_fetch_array($resultado)) {

      $infoArtista = [];
      $infoArtista['autorId'] = $fila["autorId"];
      $infoArtista['autor'] = $fila["autor"];
      $infoArtista['selected'] = false;
      if (isset($_GET["artista"]) && $_GET["artista"] != 0 && $_GET["artista"] == $fila["autorId"]) {
        $infoArtista['selected'] = true;
      }

      $artista[$x] = $infoArtista;
      $x++;
    }
    mysqli_close($mysqli);
    return $artista;

  }*/

  /* TEMATICA */
  /*function Cb_Tematica(){
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
    $tematica = [];
    $x = 0;
    while ($fila = mysqli_fetch_array($resultado)) {

      $infoTematica = [];
      $infoTematica['idTematica'] = $fila["idTematica"];
      $infoTematica['tematica'] = $fila["Tematica"];
      $infoTematica['selected'] = false;
      if (isset($_GET["tematica"]) && $_GET["tematica"] != 0 && $_GET["tematica"] == $fila["idTematica"]) {
        $infoTematica['selected'] = true;
      }

      $tematica[$x] = $infoTematica;
      $x++;
    }
    mysqli_close($mysqli);
    return $tematica;
  }*/

  /* ANNIO */ 
  /*function Cb_Annio()
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
    $annio = [];
    $x = 0;
    while ($fila = mysqli_fetch_array($resultado)) {

      $infoAnnio = [];
      $infoAnnio['idfecEjec'] = $fila["idfecEjec"];
      $infoAnnio['fecEjec'] = $fila["fecEjec"];
      $infoAnnio['selected'] = false;
      if (isset($_GET["ano"]) && $_GET["ano"] != 0 && $_GET["ano"] == $fila["idfecEjec"]) {
        $infoAnnio['selected'] = true;
      }

      $annio[$x] = $infoAnnio;
      $x++;
    }
    mysqli_close($mysqli);
    return $annio;
  }*/

  /* TECNICA */
  /*function Cb_Tecnica()
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
    $tecnica = [];
    $x = 0;
    while ($fila = mysqli_fetch_array($resultado)) {

      $infoTecnica = [];
      $infoTecnica['idTecnica'] = $fila["idTecnica"];
      $infoTecnica['tecnica'] = $fila["Tecnica"];
      $infoTecnica['selected'] = false;
      if (isset($_GET["tecnica"]) && $_GET["tecnica"] != 0 && $_GET["tecnica"] == $fila["idTecnica"]) {
        $infoTecnica['selected'] = true;
      }

      $tecnica[$x] = $infoTecnica;
      $x++;
    }
    mysqli_close($mysqli);
    return $tecnica;
  }*/



  function Listar_Query()
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');

    /* QUERY POR TEMATICA * FECHA */ 

    $valorCorX = $_GET["cX"];
    if($valorCorX == 1){
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
      $query ="SELECT 
      IFNULL(terminoTaxTematica.name, 'Desconocido')  as Tematica,
       DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') as fecEjec,
       COUNT(*) as Cantidad
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
       WHERE node.type = 'obra' AND node.status=1 AND DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') IS NOT NUll
       GROUP BY DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y')";

        $resultado = $mysqli->query($query);
                  
        $xValues = "";
        $yValues = "";
        $stringColor ="";

        while ($fila = mysqli_fetch_array($resultado)) { 
          
          $yValues.=$fila["Cantidad"].",";  
          $xValues.="'Temática: ".substr($fila['Tematica'],0,100)." - Año: ".$fila['fecEjec']."',"; 
          $stringColor .=  "'".$this->colorRGB()."',"; 
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


    $grafico = $this->Listar_Query();
    
    return [
        '#theme' => 'vpm-vista-grafico',
        '#grafico' => $grafico
      ];
  }
}