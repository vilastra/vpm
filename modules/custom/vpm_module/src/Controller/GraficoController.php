<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class GraficoController extends ControllerBase
{
  function Listar_Query($buscar, $condiciones, $where, $whereTipo,  $valorCorY)
  {
    $sql = '';
    $nombre = '';


    if ($valorCorY == 1) { // SI SELECCIONÓ OBRAS
      $sql = "COUNT(IFNULL(title, 'Desconocido'))";
    } elseif ($valorCorY == 2) { // SI SELECCIONÓ GÉNERO PICTORICO
      $query = "IFNULL(terminoTaxTematica.name, 'Desconocido')";
      $nombre = "Género Pictórico";
    } elseif ($valorCorY == 3) { // SI SELECCIONÓ TÉCNICA
      $query = "IFNULL(terminoTaxTecnica.name, 'Desconocido')";
      $nombre = "Técnica";
    } elseif ($valorCorY == 4) { // SI SELECCIONÓ SOPORTE
      $query = "IFNULL(terminoTaxSoporte.name, 'Desconocido')";
      $nombre = "Soporte";
    } elseif ($valorCorY == 5) { // SI SELECCIONÓ AUTOR
      $query = "IFNULL(terminoTaxAutoria.name, 'Desconocido')";
      $nombre = "Artista";
    } elseif ($valorCorY == 0) { // SI SELECCIONÓ AÑO
      $query = "IFNULL(DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y'), 'Desconocido')";
      $nombre = "Año";
    } elseif ($valorCorY == 6) { // SI SELECCIONÓ AÑO
      $query = "IFNULL(DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y'), 'Desconocido')";
      $nombre = "Año";
    } elseif ($valorCorY == 7) { // SI SELECCIONÓ PAÍS
      $query = "IFNULL(terminoTaxPais.name, 'Desconocido')";
      $nombre = "País";
    } elseif ($valorCorY == 8) { // SI SELECCIONÓ GÉNERO
      $query = "IFNULL(fdfg.field_genero_value, 'Desconocido')";
      $nombre = "Género";
    } elseif ($valorCorY == 9) { // SI SELECCIONÓ ACTIVIDAD O PROFESIÓN
      $query = "IFNULL(terminoTaxEActiProf.name, 'Desconocido')";
      $nombre = "Actividad o Profesión";
    } elseif ($valorCorY == 10) { // SI SELECCIONÓ ETNIA O RAZA
      $query = "IFNULL(terminoTaxEtnia.name, 'Desconocido')";
      $nombre = "Etnia o Raza";
    }

    //$dataExcel ='["Cantidad de obras", "'.$nombre.'"],';
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');

    $sql = "SELECT COUNT(distinct nid)  as Obra,
       ".$query."  AS EjeY   
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
        
        WHERE node.type = 'obra' AND node.status=1 ";


    if ($buscar == 1) {
      $sql .= ' AND ' . implode(' AND ', $condiciones);
    }
     $sql =  $sql . " GROUP BY " . $query . "";
   
    
    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
    $a_params = array();
    $param_type = '';
    $n = count($whereTipo);
    for ($i = 0; $i < $n; $i++) {
      $param_type .= $whereTipo[$i];
    }

    /* with call_user_func_array, array params must be passed by reference */
    $a_params[] = &$param_type;

    for ($i = 0; $i < $n; $i++) {
      /* with call_user_func_array, array params must be passed by reference */
      $a_params[] = &$where[$i];
    }

    /* Prepare statement */
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
      trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->errno . ' ' . $mysqli->error, E_USER_ERROR);
    }

    /* use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array */
    if ($buscar != 0) {
      call_user_func_array(array($stmt, 'bind_param'), $a_params);
    }
    /* Execute statement */
    $stmt->execute();
    $resultado = $stmt->get_result();

    return $resultado;
  }

  function Listar_Excel($buscar, $condiciones, $where, $whereTipo,  $valorCorY)
  {
    $resultado = $this->Listar_Query($buscar, $condiciones, $where, $whereTipo,  $valorCorY);
    
    $xValues = "";
    $yValues = "";
    $stringColor = "";
    $cantObras = 1;
    $prov = '';
    $nombre = '';
    $dataExcel='';

    $variableX =[];
    $variableY = [];
    $x = 0;
    $y = 0;

    if ($valorCorY == 1) { // SI SELECCIONÓ OBRAS
      //$sql = "COUNT(IFNULL(title, 'Desconocido'))";
    } elseif ($valorCorY == 2) { // SI SELECCIONÓ GÉNERO PICTORICO
      $nombre = "Género Pictórico";
    } elseif ($valorCorY == 3) { // SI SELECCIONÓ TÉCNICA
      $nombre = "Técnica";
    } elseif ($valorCorY == 4) { // SI SELECCIONÓ SOPORTE
      $nombre = "Soporte";
    } elseif ($valorCorY == 5) { // SI SELECCIONÓ AUTOR
      $nombre = "Artista";
    } elseif ($valorCorY == 0) { // SI SELECCIONÓ AÑO
      $nombre = "Año";
    } elseif ($valorCorY == 6) { // SI SELECCIONÓ AÑO
      $nombre = "Año";
    } elseif ($valorCorY == 7) { // SI SELECCIONÓ PAÍS
      $nombre = "País";
    } elseif ($valorCorY == 8) { // SI SELECCIONÓ GÉNERO
      $nombre = "Género";
    } elseif ($valorCorY == 9) { // SI SELECCIONÓ ACTIVIDAD O PROFESIÓN
      $nombre = "Actividad o Profesión";
    } elseif ($valorCorY == 10) { // SI SELECCIONÓ ETNIA O RAZA
      $nombre = "Etnia o Raza";
    }

    $dataExcel ='["Obras", "'.$nombre.'"],';
 
    while ($fila = mysqli_fetch_array($resultado)) {
      $yValues .= $fila['Obra'] . ",";
      $xValues .= "'Cantidad de Obras: " . substr($fila['Obra'], 0, 100) . " - " . $nombre . ": " . $fila['EjeY'] . "',";
      $stringColor .=  "'" . $this->colorRGB() . "',";

      $variableX[$x]  = $fila["Obra"];
      $x++;
      $variableY[$y]  = $fila["EjeY"];
      $y++;

      $dataExcel .= '["';
      $dataExcel .= $fila["Obra"] . '","';
      $dataExcel .= $fila["EjeY"];
      $dataExcel .= '"],';



    }
    $grafico['yValues'] = $yValues;
    $grafico['xValues'] = $xValues;
    $grafico['stringColor'] = $stringColor;
    $grafico['dataExcel'] = $dataExcel;


    $grafico['variableX'] = $variableX;
    $grafico['variableY'] = $variableY;
  

    return $grafico;
  }


  function Cb_CoordenadaEjeY(){
     
    $cY['variable'] = [
      ['idcY' => 2, 'cY' => 'Género pictórico'],
      ['idcY' => 3, 'cY' => 'Técnica'],
      ['idcY' => 4, 'cY' => 'Soporte'],
      ['idcY' => 5, 'cY' => 'Artista'],
      ['idcY' => 6, 'cY' => 'Año'],      
      ['idcY' => 7, 'cY' => 'País'],      
      ['idcY' => 8, 'cY' => 'Género'],      
      ['idcY' => 9, 'cY' => 'Actividad o profesión'],
      ['idcY' => 10, 'cY' => 'Etnia o raza'],
    ];    
    if (isset($_GET["cY"])) {
      $cY['selected'] = $_GET["cY"];
    }

    return $cY;
  }

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
  }

  function Cb_Artista(){
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
  }
  function Cb_Annio(){
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
  }

  function Cb_Tecnica(){
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
  }

  function Cb_Etnia(){
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $query = "SELECT DISTINCT
      terminoTaxEtnia.tid as idEtnia,
      terminoTaxEtnia.name as Etnia
      FROM node
      LEFT JOIN field_data_field_iconografia_retrato fdfir on fdfir.entity_id = node.nid 
      LEFT JOIN field_data_field_persona etnia on fdfir.field_iconografia_retrato_value = etnia.entity_id
      LEFT JOIN field_data_field_etnico_racial fdfer on etnia.field_persona_value = fdfer.entity_id  
      LEFT JOIN taxonomy_term_data terminoTaxEtnia ON terminoTaxEtnia.tid = fdfer.field_etnico_racial_tid 
      WHERE  node.type = 'obra'  and terminoTaxEtnia.name is not null";

     $resultado = $mysqli->query($query);
   
    $etnia = [];
    $x = 0;
    while ($fila = mysqli_fetch_array($resultado)) {

      $infoEtnia = [];
      $infoEtnia['idEtnia'] = $fila["idEtnia"];
      $infoEtnia['Etnia'] = $fila["Etnia"];
      $infoEtnia['selected'] = false;
      if (isset($_GET["etnia"]) && $_GET["etnia"] != 0 && $_GET["etnia"] == $fila["idEtnia"]) {
        $infoEtnia['selected'] = true;
      }

      $etnia[$x] = $infoEtnia;
      $x++;
    }
    mysqli_close($mysqli);
    return $etnia;
  }

  function Cb_Pais(){
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $query = "SELECT DISTINCT
      terminoTaxPais.tid as idPais,
      terminoTaxPais.name as Pais
      FROM node
      LEFT  JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
      LEFT JOIN field_data_field_pais_ejecucion fdfpe on iden.field_identificacion_value = fdfpe.entity_id
      LEFT JOIN taxonomy_term_data terminoTaxPais on terminoTaxPais.tid = fdfpe.field_pais_ejecucion_tid    
      WHERE  node.type = 'obra' AND terminoTaxPais.name is not null";

    $resultado = $mysqli->query($query);
   
    $pais = [];
    $x = 0;
    while ($fila = mysqli_fetch_array($resultado)) {

      $infoPais = [];
      $infoPais['idPais'] = $fila["idPais"];
      $infoPais['Pais'] = $fila["Pais"];
      $infoPais['selected'] = false;
      if (isset($_GET["pais"]) && $_GET["pais"] != 0 && $_GET["pais"] == $fila["idPais"]) {
        $infoPais['selected'] = true;
      }

      $pais[$x] = $infoPais;
      $x++;
    }
    mysqli_close($mysqli);
    return $pais;
  }

  function Cb_ActProf(){
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $query = "SELECT DISTINCT
      terminoTaxEActiProf.tid as idactProf,
      terminoTaxEActiProf.name as ActProf
      FROM node
      LEFT JOIN field_data_field_iconografia_retrato fdfir on fdfir.entity_id = node.nid 
      LEFT JOIN field_data_field_persona actividad on fdfir.field_iconografia_retrato_value = actividad.entity_id 
      LEFT JOIN field_data_field_actividad_o_profesion fdfaop on actividad.field_persona_value = fdfaop.entity_id 
      LEFT JOIN taxonomy_term_data terminoTaxEActiProf ON terminoTaxEActiProf.tid = fdfaop.field_actividad_o_profesion_tid
    
      WHERE  node.type = 'obra' AND terminoTaxEActiProf.name is not null";

    $resultado = $mysqli->query($query);
   
    $actProf = [];
    $x = 0;
    while ($fila = mysqli_fetch_array($resultado)) {

      $infoActProf = [];
      $infoActProf['idactProf'] = $fila["idactProf"];
      $infoActProf['ActProf'] = $fila["ActProf"];
      $infoActProf['selected'] = false;
      if (isset($_GET["actProf"]) && $_GET["actProf"] != 0 && $_GET["actProf"] == $fila["idactProf"]) {
        $infoActProf['selected'] = true;
      }

      $actProf[$x] = $infoActProf;
      $x++;
    }
    mysqli_close($mysqli);
    return $actProf;
  }

  function Cb_Genero(){
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $query = " SELECT distinct
      fdfg.field_genero_value as idGenero,	
      fdfg.field_genero_value as Genero 
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

      WHERE  node.type = 'obra' and  fdfg.field_genero_value  is not null";

    $resultado = $mysqli->query($query);
   
    $genero = [];
    $x = 0;
    while ($fila = mysqli_fetch_array($resultado)) {

      $infoGenero = [];
      $infoGenero['idGenero'] = $fila["idGenero"];
      $infoGenero['Genero'] = $fila["Genero"];
      $infoGenero['selected'] = false;
      if (isset($_GET["genero"]) && $_GET["genero"] != 0 && $_GET["genero"] == $fila["idGenero"]) {
        $infoGenero['selected'] = true;
      }

      $genero[$x] = $infoGenero;
      $x++;
    }
    mysqli_close($mysqli);
    return $genero;
  }

  function colorRGB()
  {
    $color = ['#22555D', '#2D717C', '#578D96', '#ABC6CB', '#D5E3E5'];
    return $color[rand(0, 4)];
  }

  function grafico()
  {
    $buscar = 0;
    $condiciones = [];
    $where = [];
    $whereTipo = [];

    if (!empty($_GET['tematica'])) {
      array_push($condiciones, 'terminoTaxTematica.tid = ?');
      array_push($where, $_GET['tematica']);
      array_push($whereTipo, 's');
      $buscar = 1;
    }
    if (!empty($_GET['artista'])) {
      array_push($condiciones, 'terminoTaxAutoria.tid = ?');
      array_push($where, $_GET['artista']);
      array_push($whereTipo, 's');
      $buscar = 1;
    }

    if (!empty($_GET['ano'])) {
      array_push($condiciones, 'fecEjecucion.field_fecha_ejecucion_timestamp = ?');
      array_push($where, $_GET['ano']);
      array_push($whereTipo, 's');
      $buscar = 1;
    }


    if (!empty($_GET['tecnica'])) {
      array_push($condiciones, 'terminoTaxTecnica.tid = ?');
      array_push($where, $_GET['tecnica']);
      array_push($whereTipo, 's');
      $buscar = 1;
    }

    if (!empty($_GET['etnia'])) {
      array_push($condiciones, 'terminoTaxEtnia.tid = ?');
      array_push($where, $_GET['etnia']);
      array_push($whereTipo, 's');
      $buscar = 1;
    }

    if (!empty($_GET['pais'])) {
      array_push($condiciones, 'terminoTaxPais.tid = ?');
      array_push($where, $_GET['pais']);
      array_push($whereTipo, 's');
      $buscar = 1;
    }

    if (!empty($_GET['actProf'])) {
      array_push($condiciones, 'terminoTaxEActiProf.tid = ?');
      array_push($where, $_GET['actProf']);
      array_push($whereTipo, 's');
      $buscar = 1;
    }

    if (!empty($_GET['genero'])) {
      array_push($condiciones, 'fdfg.field_genero_value = ?');
      array_push($where, $_GET['genero']);
      array_push($whereTipo, 's');
      $buscar = 1;
    }

    $valorCorY = 0;
    if (isset($_GET["cY"])) {
      $valorCorY = $_GET["cY"];
    }

    $cY = $this->Cb_CoordenadaEjeY();
    $tematica = $this->Cb_Tematica();
    $artista = $this->Cb_Artista();
    $annio = $this->Cb_Annio();
    $tecnica = $this->Cb_Tecnica();
    $etnia = $this->Cb_Etnia();
    $pais = $this->Cb_Pais();
    $actProf = $this->Cb_ActProf();
    $genero = $this->Cb_Genero();

    $grafico = $this->Listar_Excel($buscar, $condiciones, $where, $whereTipo, $valorCorY);


    $grafico["dataArrayExcel"] = null;
    if (isset($grafico['dataExcel'])) {
      $grafico["dataArrayExcel"] =  $grafico['dataExcel'];
    }



    return [
      '#theme' => 'vpm-vista-grafico',
      /*'#ordenaX' => $ordenaX,
      '#ordenaY' => $ordenaY,*/
      '#cY' => $cY,
      '#tematica' => $tematica,
      '#annio' => $annio,
      '#artista' => $artista,
      '#tecnica' => $tecnica,
      '#etnia' => $etnia,
      '#pais' => $pais,
      '#actProf' => $actProf,
      '#genero' => $genero,
      '#grafico' => $grafico
    ];
  }
}
