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
        $sql = "COUNT(distinct IFNULL(title, 'Desconocido'))  as EjeY";
      } elseif ($valorCorY == 2) { // SI SELECCIONÓ GÉNERO PICTORICO
        $sql = "IFNULL(terminoTaxTematica.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 3) { // SI SELECCIONÓ TÉCNICA
        $sql = "IFNULL(terminoTaxTecnica.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 4) { // SI SELECCIONÓ SOPORTE
        $sql = "IFNULL(terminoTaxSoporte.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 5) { // SI SELECCIONÓ AUTOR
        $sql = "IFNULL(terminoTaxAutoria.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 7) { // SI SELECCIONÓ PAÍS
        $sql = "IFNULL(terminoTaxPais.name, 'Desconocido') as EjeY";  
      } elseif ($valorCorY == 8) { // SI SELECCIONÓ GÉNERO
        $sql = "IFNULL(fdfg.field_genero_value, 'Desconocido') as EjeY";
      }elseif ($valorCorY == 9) { // SI SELECCIONÓ ACTIVIDAD O PROFESIÓN
        $sql = "IFNULL(terminoTaxEActiProf.name, 'Desconocido') as EjeY";
      }elseif ($valorCorY == 10) { // SI SELECCIONÓ ETNIA O RAZA
        $sql = "IFNULL(terminoTaxEtnia.name, 'Desconocido') as EjeY";
      }

      $query = "SELECT
      IFNULL(DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y'), 'Desconocido') as fecEjec,
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

      LEFT JOIN field_data_field_iconografia_retrato fdfir on fdfir.entity_id = node.nid 
      LEFT JOIN field_data_field_persona fdfp on fdfir.field_iconografia_retrato_value = fdfp.entity_id 
      LEFT JOIN field_data_field_genero fdfg on fdfp.field_persona_value = fdfg.entity_id 

      LEFT JOIN field_data_field_persona actividad on fdfir.field_iconografia_retrato_value = actividad.entity_id 
      LEFT JOIN field_data_field_actividad_o_profesion fdfaop on actividad.field_persona_value = fdfaop.entity_id 
      LEFT JOIN taxonomy_term_data terminoTaxEActiProf ON terminoTaxEActiProf.tid = fdfaop.field_actividad_o_profesion_tid 

      LEFT JOIN field_data_field_persona etnia on fdfir.field_iconografia_retrato_value = etnia.entity_id
      LEFT JOIN field_data_field_etnico_racial fdfer on etnia.field_persona_value = fdfer.entity_id  
      LEFT JOIN taxonomy_term_data terminoTaxEtnia ON terminoTaxEtnia.tid = fdfer.field_etnico_racial_tid 

      LEFT JOIN field_data_field_pais_ejecucion fdfpe on iden.field_identificacion_value = fdfpe.entity_id
      LEFT JOIN taxonomy_term_data terminoTaxPais on terminoTaxPais.tid = fdfpe.field_pais_ejecucion_tid    


      WHERE node.type = 'obra' AND node.status=1";
      if ($valorCorY == 1) { // SI SELECCIONÓ OBRAS
        $query .= " GROUP BY IFNULL(DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y'), 'Desconocido')";
      }else{
        $query .= " GROUP BY node.nid";
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

    }else if ($valorCorX ==2){
      $sql='';
      $nombre ='';
      if ($valorCorY == 1) { // SI SELECCIONÓ OBRAS
        $sql = "IFNULL(terminoTaxAutoria.name, 'Desconocido')";
      } elseif ($valorCorY == 2) { // SI SELECCIONÓ GÉNERO PICTORICO
        $sql = "IFNULL(terminoTaxTematica.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 3) { // SI SELECCIONÓ TÉCNICA
        $sql = "IFNULL(terminoTaxTecnica.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 4) { // SI SELECCIONÓ SOPORTE
        $sql = "IFNULL(terminoTaxSoporte.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 5) { // SI SELECCIONÓ AUTOR
       // $sql = "IFNULL(terminoTaxAutoria.name, 'Desconocido') as EjeY";
      } elseif ($valorCorY == 6) { // SI SELECCIONÓ AÑO
        $sql = "IFNULL(DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y'), 'Desconocido') as EjeY"; 
      } elseif ($valorCorY == 7) { // SI SELECCIONÓ PAÍS
        $sql = "IFNULL(terminoTaxPais.name, 'Desconocido') as EjeY";  
      } elseif ($valorCorY == 8) { // SI SELECCIONÓ GÉNERO
        $sql = "IFNULL(fdfg.field_genero_value, 'Desconocido') as EjeY";
      }elseif ($valorCorY == 9) { // SI SELECCIONÓ ACTIVIDAD O PROFESIÓN
        $sql = "IFNULL(terminoTaxEActiProf.name, 'Desconocido') as EjeY";
      }elseif ($valorCorY == 10) { // SI SELECCIONÓ ETNIA O RAZA
        $sql = "IFNULL(terminoTaxEtnia.name, 'Desconocido') as EjeY";
      }

      if($valorCorY == 1){
        $query = "SELECT COUNT(distinct nid)  as Obra,
        ".$sql." as EjeY     
        FROM node
        JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
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
  
        LEFT JOIN field_data_field_iconografia_retrato fdfir on fdfir.entity_id = node.nid 
        LEFT JOIN field_data_field_persona fdfp on fdfir.field_iconografia_retrato_value = fdfp.entity_id 
        LEFT JOIN field_data_field_genero fdfg on fdfp.field_persona_value = fdfg.entity_id 
  
        LEFT JOIN field_data_field_persona actividad on fdfir.field_iconografia_retrato_value = actividad.entity_id 
        LEFT JOIN field_data_field_actividad_o_profesion fdfaop on actividad.field_persona_value = fdfaop.entity_id 
        LEFT JOIN taxonomy_term_data terminoTaxEActiProf ON terminoTaxEActiProf.tid = fdfaop.field_actividad_o_profesion_tid 
  
        LEFT JOIN field_data_field_persona etnia on fdfir.field_iconografia_retrato_value = etnia.entity_id
        LEFT JOIN field_data_field_etnico_racial fdfer on etnia.field_persona_value = fdfer.entity_id  
        LEFT JOIN taxonomy_term_data terminoTaxEtnia ON terminoTaxEtnia.tid = fdfer.field_etnico_racial_tid 
        
        LEFT JOIN field_data_field_pais_ejecucion fdfpe on iden.field_identificacion_value = fdfpe.entity_id
        LEFT JOIN taxonomy_term_data terminoTaxPais on terminoTaxPais.tid = fdfpe.field_pais_ejecucion_tid    
        
        WHERE node.type = 'obra' AND node.status=1 
        GROUP BY ".$sql."";

        $resultado = $mysqli->query($query);

        $xValues = "";
        $yValues = "";
        $stringColor = "";
        $cantObras = 1;
        $prov = '';

      while ($fila = mysqli_fetch_array($resultado)) {
        $yValues .= $fila['Obra'] . ",";
        $xValues .= "'Autor: " . substr($fila['EjeY'], 0, 100) . " - Cantidad de Obras : " . $fila['Obra'] . "',";
        $stringColor .=  "'" . $this->colorRGB() . "',";
      }
      mysqli_close($mysqli);
      $grafico['yValues'] = $yValues;
      $grafico['xValues'] = $xValues;
      $grafico['stringColor'] = $stringColor;
      return $grafico;
      }else{
        $query = "SELECT
        IFNULL(terminoTaxAutoria.name, 'Desconocido') AS fecEjec, 
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
  
        LEFT JOIN field_data_field_iconografia_retrato fdfir on fdfir.entity_id = node.nid 
        LEFT JOIN field_data_field_persona fdfp on fdfir.field_iconografia_retrato_value = fdfp.entity_id 
        LEFT JOIN field_data_field_genero fdfg on fdfp.field_persona_value = fdfg.entity_id 
  
        LEFT JOIN field_data_field_persona actividad on fdfir.field_iconografia_retrato_value = actividad.entity_id 
        LEFT JOIN field_data_field_actividad_o_profesion fdfaop on actividad.field_persona_value = fdfaop.entity_id 
        LEFT JOIN taxonomy_term_data terminoTaxEActiProf ON terminoTaxEActiProf.tid = fdfaop.field_actividad_o_profesion_tid 
  
        LEFT JOIN field_data_field_persona etnia on fdfir.field_iconografia_retrato_value = etnia.entity_id
        LEFT JOIN field_data_field_etnico_racial fdfer on etnia.field_persona_value = fdfer.entity_id  
        LEFT JOIN taxonomy_term_data terminoTaxEtnia ON terminoTaxEtnia.tid = fdfer.field_etnico_racial_tid 
  
        LEFT JOIN field_data_field_pais_ejecucion fdfpe on iden.field_identificacion_value = fdfpe.entity_id
        LEFT JOIN taxonomy_term_data terminoTaxPais on terminoTaxPais.tid = fdfpe.field_pais_ejecucion_tid

        WHERE node.type = 'obra' AND node.status=1";
  
        if ($valorCorY == 1) { // SI SELECCIONÓ OBRAS
          //$query .= " GROUP BY node.nid" ;
        }else{
          $query .= " GROUP BY node.nid";
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
            $xValues .= "'Cantidad de Obras: " . substr($fila['EjeY'], 0, 100) . " - Artista: " . $fila['fecEjec'] . "',";
            $stringColor .=  "'" . $this->colorRGB() . "',";
          } else {
            if ($prov != $fila['EjeY'] . "-" . $fila['fecEjec']) {
              if ($cont != 1) {
                $xValues .= "'" . substr($provY, 0, 100) . " - Artista: " . $provX . "',";
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
          $xValues .= "'" . substr($provY, 0, 100) . " - Artista: " . $provX . "',";
          $yValues .= $cantObras . ",";
        }
  
        mysqli_close($mysqli);
        $grafico['yValues'] = $yValues;
        $grafico['xValues'] = $xValues;
        $grafico['stringColor'] = $stringColor;
        return $grafico;
      }

  

    }else if ($valorCorX == 3) {
      $sql='';
      $nombre ='';
      if ($valorCorY == 1) { // SI SELECCIONÓ OBRAS
        //$sql = "COUNT(IFNULL(title, 'Desconocido'))  as EjeY";
      } elseif ($valorCorY == 2) { // SI SELECCIONÓ GÉNERO PICTORICO
        $sql = "IFNULL(terminoTaxTematica.name, 'Desconocido')";
        $nombre = "Género Pictórico";
      } elseif ($valorCorY == 3) { // SI SELECCIONÓ TÉCNICA
        $sql = "IFNULL(terminoTaxTecnica.name, 'Desconocido')";
        $nombre = "Técnica";
      } elseif ($valorCorY == 4) { // SI SELECCIONÓ SOPORTE
        $sql = "IFNULL(terminoTaxSoporte.name, 'Desconocido')";
        $nombre = "Soporte";
      } elseif ($valorCorY == 5) { // SI SELECCIONÓ AUTOR
        $sql = "IFNULL(terminoTaxAutoria.name, 'Desconocido')";
        $nombre = "Artista";
      }elseif ($valorCorY == 6) { // SI SELECCIONÓ AÑO
        $sql = "IFNULL(DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y'), 'Desconocido')";
        $nombre = "Año";
      }elseif ($valorCorY == 7) { // SI SELECCIONÓ PAÍS
        $sql = "IFNULL(terminoTaxPais.name, 'Desconocido')"; 
        $nombre = "País";  
      } elseif ($valorCorY == 8) { // SI SELECCIONÓ GÉNERO
        $sql = "IFNULL(fdfg.field_genero_value, 'Desconocido')";
        $nombre = "Género";
      }elseif ($valorCorY == 9) { // SI SELECCIONÓ ACTIVIDAD O PROFESIÓN
        $sql = "IFNULL(terminoTaxEActiProf.name, 'Desconocido')";
        $nombre = "Actividad o Profesión";
      }elseif ($valorCorY == 10) { // SI SELECCIONÓ ETNIA O RAZA
        $sql = "IFNULL(terminoTaxEtnia.name, 'Desconocido')";
        $nombre = "Etnia o Raza";
      }

      $dataExcel ='["Cantidad de obras", "'.$nombre.'"],';


      $query = "SELECT COUNT(distinct nid)  as Obra,
      ".$sql." as EjeY     
      FROM node
      JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
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

      LEFT JOIN field_data_field_iconografia_retrato fdfir on fdfir.entity_id = node.nid 
      LEFT JOIN field_data_field_persona fdfp on fdfir.field_iconografia_retrato_value = fdfp.entity_id 
      LEFT JOIN field_data_field_genero fdfg on fdfp.field_persona_value = fdfg.entity_id 

      LEFT JOIN field_data_field_persona actividad on fdfir.field_iconografia_retrato_value = actividad.entity_id 
      LEFT JOIN field_data_field_actividad_o_profesion fdfaop on actividad.field_persona_value = fdfaop.entity_id 
      LEFT JOIN taxonomy_term_data terminoTaxEActiProf ON terminoTaxEActiProf.tid = fdfaop.field_actividad_o_profesion_tid 

      LEFT JOIN field_data_field_persona etnia on fdfir.field_iconografia_retrato_value = etnia.entity_id
      LEFT JOIN field_data_field_etnico_racial fdfer on etnia.field_persona_value = fdfer.entity_id  
      LEFT JOIN taxonomy_term_data terminoTaxEtnia ON terminoTaxEtnia.tid = fdfer.field_etnico_racial_tid 
      
      LEFT JOIN field_data_field_pais_ejecucion fdfpe on iden.field_identificacion_value = fdfpe.entity_id
      LEFT JOIN taxonomy_term_data terminoTaxPais on terminoTaxPais.tid = fdfpe.field_pais_ejecucion_tid    
      
      WHERE node.type = 'obra' AND node.status=1 
      GROUP BY ".$sql."";
      $resultado = $mysqli->query($query);

      $xValues = "";
      $yValues = "";
      $stringColor = "";
      $cantObras = 1;
      $prov = '';
      

      while ($fila = mysqli_fetch_array($resultado)) {
        $yValues .= $fila['Obra'] . ",";
        $xValues .= "'Cantidad de Obras: " . substr($fila['Obra'], 0, 100) . " - ".$nombre.": " . $fila['EjeY'] . "',";
        $stringColor .=  "'" . $this->colorRGB() . "',"; 

        
        $dataExcel .= '["';
        $dataExcel .= $fila["Obra"].'","';
        $dataExcel .= $fila["EjeY"];
        $dataExcel .= '"],';
   
        /*'["ValorY", "ValorX"],
        ["1", "A1"],*/
      }
      mysqli_close($mysqli);
      $grafico['yValues'] = $yValues;
      $grafico['xValues'] = $xValues;
      $grafico['stringColor'] = $stringColor;

      $grafico['dataExcel'] = $dataExcel;


      return $grafico;
    } else {
      return null;
    }
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
    
    $grafico["dataExcel"] =  $grafico['dataExcel'];
    /*'["ValorY", "ValorX"],
    ["1", "A1"],
    ["2", "B1"],
    ["3", "A2"],
    ["4", "B2"]'; */

    return [
      '#theme' => 'vpm-vista-grafico',
      '#grafico' => $grafico
    ];
  }
}
