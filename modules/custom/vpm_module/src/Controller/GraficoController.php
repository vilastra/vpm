<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class GraficoController extends ControllerBase
{
  function Cb_Grafico()
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
    $grafico = [];
    $x = 0;
    $strinIdTecnica = "";
    $strinTecnica= "";
    $strinColor = "";

    while ($fila = mysqli_fetch_array($resultado)) { 

     
      $strinIdTecnica.=$fila['idTecnica'].",";  
      $strinTecnica.="'".substr($fila['Tecnica'],0,100)."',"; 
      $strinColor .=  "'".$this->colorRGB()."',"; 

    }
    mysqli_close($mysqli);
    $grafico['strinIdTecnica'] = $strinIdTecnica;
    $grafico['strinTecnica'] = $strinTecnica;
    $grafico['strinColor'] = $strinColor;
    return $grafico;
  }
  function colorRGB(){
    $color = ['#22555D','#2D717C', '#578D96','#ABC6CB','#D5E3E5'];
    return $color[rand(0,4)];
  }

  function grafico()
  {

    $grafico = $this->Cb_Grafico();

    return [
        '#theme' => 'vpm-vista-grafico',
        '#grafico' => $grafico
  
      ];
  }
}

/*$resultado = $mysqli->query($query);
$grafico = [];
$x = 0;
$strinIdTecnica = "'Italy', 'France', 'Spain', 'USA', 'Argentina'";

while ($fila = mysqli_fetch_array($resultado)) {

  $infoGrafico = [];
  $infoGrafico['idTecnica'] = $fila["idTecnica"];
  $infoGrafico['tecnica'] = $fila["Tecnica"];

  //strinIdTecnica.=[  

  $grafico[$x] = $infoGrafico;
  $x++;
}
mysqli_close($mysqli);
$grafico['strinIdTecnica'] = $strinIdTecnica;
return $grafico;*/