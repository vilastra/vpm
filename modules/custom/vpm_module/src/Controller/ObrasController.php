<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class ObrasController extends ControllerBase
{
  function Listar_Query($buscar, $condiciones, $where, $whereTipo, $paginador)
  {
    $limit = 6;
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

    $sql = "SELECT nid,title as titulo,terminoTaxAutoria.name as autor, terminoTaxAutoria.tid as autorId, 
      file_managed.filename, terminoTaxTematica.name as Tematica,
      terminoTaxTematica.tid as idTematica,
      fecEjecucion.field_fecha_ejecucion_timestamp as idfecEjec,
      DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') as fecEjec,
      terminoTaxTecnica.name as Tecnica,
      terminoTaxTecnica.tid as idTecnica
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
      WHERE node.type = 'obra' AND node.status=1 ";

    //WHERE  node.type = 'obra' AND node.status=1 
    if ($buscar['select'] == 1) {
      $sql .= ' AND ' . implode(' AND ', $condiciones['busqueda1']);
    }
    if ($buscar['texto'] == 1) {
      $sql .= ' AND ' . implode(' OR ', $condiciones['busqueda2']);
    }

    if (!$paginador) {
      $sql = $sql . " LIMIT $offset, $limit";
    }

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
    if($buscar['select'] != 0 || $buscar['texto'] !=0){
      call_user_func_array(array($stmt, 'bind_param'), $a_params);
    }

    /* Execute statement */
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado;
  }

  function Listar_Obras($buscar, $condiciones, $where, $whereTipo, $paginador)
  {
    $limit = 6;

    if (!empty($_GET["pag"])) {
      $pag = $_GET["pag"];
    } else {
      $pag = 0;
    }
    if ($pag < 1) {
      $pag = 1;
    }
    $resultado = $this->Listar_Query($buscar, $condiciones, $where, $whereTipo, $paginador);
    $obras = [];
    $x = 0;
    $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';
    while ($fila = mysqli_fetch_array($resultado)) {
      $infoObra = [];
      $infoObra['idObra'] = $fila["nid"];
      $infoObra['tituloObra'] = $fila["titulo"];
      $infoObra['nombreArtista'] = $fila["autor"];
      $infoObra['autorId'] = $fila["autorId"];
      if (isset($_GET["idCat"])) {
        $infoObra['urlArtista'] = "idCat=1&Sec=Art&id=" . $fila["autorId"];
      } else {
        $infoObra['urlArtista'] = "id=" . $fila["autorId"];
      }
      /* IMAGEN */
      $infoObra['rutaFoto'] = $fila["filename"];
      $infoObra['rutaFoto'] = $rutaQuinsac . $fila["filename"];
      /* TEMATICA */
      $infoObra['idTematica'] = $fila["idTematica"];
      $infoObra['nombreTematica'] = $fila["Tematica"];

      $obras[$x] = $infoObra;
      $x++;
    }
    return $obras;
  }

  function Lista_Paginador($buscar, $condiciones, $where, $whereTipo, $paginador)
  {
    $limit = 6;
    $limitPage = 7;
    $pag = (int) (!empty($_GET["pag"]));
    if ($pag < 1) {
      $pag = 1;
    }


    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $offset = ($pag - 1) * $limit;

    $sqlTotal = "SELECT COUNT(*) total
        FROM node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
        LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
        LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
        LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid WHERE  node.type = 'obra'";

    $result = $this->Listar_Query($buscar, $condiciones, $where, $whereTipo, $paginador);
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

  function Cb_Tematica()
  {
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

  function obras()
  {
    $buscar['select'] = 0;
    $buscar['texto'] = 0;
    $condiciones['busqueda1'] = [];
    $condiciones['busqueda2'] = [];
    $where = [];
    $whereTipo = [];


    $palabrasOmitir = ['pieza', 'piezas', 'en', 'primer', 'lugar', 'segundo', 'tercero', 'ante', 'todo', 'fundamentalmente', 'lo', 'más', 'importante', 'después', 'por', 'fin', 'es', 'decir', 'agrega', 'considerar', 'retirar', 'acotar', 'primero', 'para', 'empezar', 'finalmente', 'mientras', 'ultimo', 'sobre', 'podemos', 'incluir', 'agregar', 'sustentar', 'adicionar', 'comprender', 'de', 'modo', 'accesorio', 'y', 'todos', 'modos', 'cualquier', 'forma', 'manera', 'cabe', 'destacar', 'idéntico', 'nuevo', 'al', 'mismo', 'tiempo', 'así', 'se', 'puede', 'señalar', 'inclusive', 'además', 'la', 'misma', 'también', 'algo', 'semejante', 'ocurre', 'con…', 'otra', 'vez', 'pero', 'aunque', 'otro', 'sentido', 'no', 'obstante', 'parte', 'como', 'contrapartida', 'sin', 'embargo', 'a', 'pesar', 'diferencia', 'camino', 'un', 'lado', 'el', 'orden', 'ideas', 'extremo', 'ahora', 'bien', 'contrario', 'que', 'antagónicamente', 'contraposición', 'revés', 'ejemplo', 'tal', 'caso', 'si', 'apelamos', 'usamos', 'una', 'imagen', 'símil', 'similarmente', 'identificante', 'permítanme', 'explicarle', 'decir', 'principio', 'otras', 'palabras', 'hecho', 'conforme', 'circunstancia', 'sea', 'inicio', 'esto', 'manera', 'eso', 'quiere', 'expresar', 'aludir', 'significa', 'razón', 'objeto', 'puesto', 'causa', 'de', 'solicitando', 'debido', 'porque', 'dado', 'ya', 'consecuencia', 'consiguiente', 'esta', 'ello', 'allí', 'ende', 'motivo', 'concordancia', 'resultado', 'cual', 'hay', 'inferir', 'siempre', 'condición', 'cuando', 'con', 'menos', 'acuerdo', 'propósito', 'cono', 'similar', 'igual', 'manera', 'situación', 'comparamos', 'idéntica', 'situación', 'circunstancia', 'paralelamente', 'definitiva', 'resumiendo', 'planteado', 'terminar', 'concretizando', 'resumen', 'englobando', 'conclusión', 'palabra', 'síntesis', 'finalizando', 'habitualmente', 'duda', 'alguna', 'supuesto', 'probablemente', 'notablemente', 'evidentemente', 'efectivamente', 'sencillamente', 'resulta', 'lógico', 'razonable', 'naturalmente', 'debe', 'suponerse', 'generalmente', 'cierto', 'posiblemente', 'efecto', 'mejor', 'desde', 'luego', 'específicamente'];

      if (!empty($_GET["busquedaIndex"])) {
      $buscar['texto'] = 1;
      $arrayBusqueda = explode(" ", $_GET["busquedaIndex"]);
      foreach ($arrayBusqueda as &$valor) {
        if (!in_array($valor, $palabrasOmitir)) {
          array_push($condiciones['busqueda2'], 'node.title LIKE ?');
          array_push($where, "%{$_GET['busquedaIndex']}%");
          array_push($whereTipo, 's');
        }
        if (!in_array($valor, $palabrasOmitir)) {
          array_push($condiciones['busqueda2'], 'terminoTaxTematica.tid LIKE ?');
          array_push($where, "%{$_GET['busquedaIndex']}%");
          array_push($whereTipo, 's');
        }
        if (!in_array($valor, $palabrasOmitir)) {
          array_push($condiciones['busqueda2'], 'terminoTaxAutoria.tid LIKE ?');
          array_push($where, "%{$_GET['busquedaIndex']}%");
          array_push($whereTipo, 's');
        }
        if (!in_array($valor, $palabrasOmitir)) {
          array_push($condiciones['busqueda2'], 'fecEjecucion.field_fecha_ejecucion_timestamp LIKE ?');
          array_push($where, "%{$_GET['busquedaIndex']}%");
          array_push($whereTipo, 's');
        }

        if (!in_array($valor, $palabrasOmitir)) {
          array_push($condiciones['busqueda2'], 'terminoTaxTecnica.tid LIKE ?');
          array_push($where, "%{$_GET['busquedaIndex']}%");
          array_push($whereTipo, 's');
        }
      }
    }


    if (!empty($_GET['tematica'])) {
      array_push($condiciones['busqueda1'], 'terminoTaxTematica.tid = ?');
      array_push($where, $_GET['tematica']);
      array_push($whereTipo, 's');
      $buscar['select'] = 1;
    }

    if (!empty($_GET['artista'])) {
      array_push($condiciones['busqueda1'], 'terminoTaxAutoria.tid = ?');
      array_push($where, $_GET['artista']);
      array_push($whereTipo, 's');
      $buscar['select'] = 1;
    }

    if (!empty($_GET['ano'])) {
      array_push($condiciones['busqueda1'], 'fecEjecucion.field_fecha_ejecucion_timestamp = ?');
      array_push($where, $_GET['ano']);
      array_push($whereTipo, 's');
      $buscar['select'] = 1;
    }


    if (!empty($_GET['tecnica'])) {
      array_push($condiciones['busqueda1'], 'terminoTaxTecnica.tid = ?');
      array_push($where, $_GET['tecnica']);
      array_push($whereTipo, 's');
      $buscar['select'] = 1;
    }
    $pag = 0;
    if (!empty($_GET["pag"])) {
      $pag = $_GET["pag"];
    } else {
      $pag = 0;
    }

    if ($pag < 1) {
      $pag = 1;
    }


    $obras = $this->Listar_Obras($buscar, $condiciones, $where, $whereTipo, false);
    $arrayBoton = $this->Lista_Paginador($buscar, $condiciones, $where, $whereTipo, true);


    $tematica = $this->Cb_Tematica();
    $artista = $this->Cb_Artista();
    $annio = $this->Cb_Annio();
    $tecnica = $this->Cb_Tecnica();
    $idCatalogo = 'null';
    $menuLink = 'null';
    if (isset($_GET["Sec"])) {
      $menuLink = $_GET["Sec"];
    } else {
      $menuLink = 'null';
    }
    if (isset($_GET["idCat"])) {
      $idCatalogo = $_GET["idCat"];
    } else {
      $idCatalogo = 'null';
    }
    $busquedaIndex = '';
    if(isset($_GET["busquedaIndex"])){
      $busquedaIndex = $_GET["busquedaIndex"];
    }

    return [
      '#theme' => 'vpm-vista-obras',
      '#obras' => $obras,
      '#arrayBoton' => $arrayBoton,
      '#tematica' => $tematica,
      '#annio' => $annio,
      '#artista' => $artista,
      '#idCat' => $idCatalogo,
      '#Sec' => $menuLink,
      '#busquedaIndex' => $busquedaIndex,
      '#tecnica' => $tecnica

    ];
  }


  function getInfoObra($idObra)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT nid,title as Titulo,
    fecEjecucion.field_fecha_ejecucion_year as fecha_ejecucion,
    terminoTaxAutoria.name as Autoria,
    terminoTaxtipoAutoriaFinal.name as Tipo_Autoria_Final,
    terminoTaxtipoAutoriaPrinFinal.name as Tipo_Autoria_Prin_Final,
    terminoTaxtipoInsc.name as Tipo_Inscripcion,
    ubicacionIns.field_ubicacion_en_la_obra_value as Ubicacion_Inscripcion,
    file_managed.filename as urlImagen,
    transcripcionIns.field_transcripcion_value as Transcripcion_Inscripcion,
    textoRazonado.field_texto_razonado_value as Texto_Razonado,
    terminoTaxTecnica.name as Tecnica,
    terminoTaxSoporte.name as Soporte,
    altoImagen.field_alto_value as Alto,
    anchoImagen.field_ancho_value as Ancho
    FROM node
    LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
    LEFT JOIN field_data_field_numero numero ON numero.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_titulo_actual titActual ON titActual.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_fecha_ejecucion fecEjecucion ON fecEjecucion.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_inscripciones inscrip ON inscrip.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_tipo_de_inscripcion tipoins ON tipoins.entity_id = inscrip.field_inscripciones_value
    LEFT JOIN taxonomy_term_data terminoTaxtipoInsc ON terminoTaxtipoInsc.tid = tipoins.field_tipo_de_inscripcion_tid
    LEFT JOIN field_data_field_ubicacion_en_la_obra ubicacionIns ON ubicacionIns.entity_id = inscrip.field_inscripciones_value
    LEFT JOIN field_data_field_transcripcion transcripcionIns ON transcripcionIns.entity_id = inscrip.field_inscripciones_value
    LEFT JOIN field_data_field_tipo_de_autoria tipoAutoria ON tipoAutoria.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_alto altoImagen ON altoImagen.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_ancho anchoImagen ON anchoImagen.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_soporte soporte ON soporte.entity_id = iden.field_identificacion_value
    JOIN taxonomy_term_data terminoTaxSoporte ON terminoTaxSoporte.tid = soporte.field_soporte_tid
    LEFT JOIN field_data_field_tecnica tecnica ON tecnica.entity_id = iden.field_identificacion_value
    JOIN taxonomy_term_data terminoTaxTecnica ON terminoTaxTecnica.tid = tecnica.field_tecnica_tid
    JOIN taxonomy_term_data terminoTaxtipoAutoria ON terminoTaxtipoAutoria.tid = tipoAutoria.field_tipo_de_autoria_tid
    LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
    JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
    LEFT JOIN field_data_field_tipo_de_autoria_final tipoAutoriaFinal ON tipoAutoriaFinal.entity_id = iden.field_identificacion_value
    JOIN taxonomy_term_data terminoTaxtipoAutoriaFinal ON terminoTaxtipoAutoriaFinal.tid = tipoAutoriaFinal.field_tipo_de_autoria_final_tid
    LEFT JOIN field_data_field_autoria_principal_final tipoAutoriaPrinFinal ON tipoAutoriaPrinFinal.entity_id = iden.field_identificacion_value
    JOIN taxonomy_term_data terminoTaxtipoAutoriaPrinFinal ON terminoTaxtipoAutoriaPrinFinal.tid = tipoAutoriaPrinFinal.field_autoria_principal_final_tid
    LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
    LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
    LEFT JOIN field_data_field_texto_razonado textoRazonado ON textoRazonado.entity_id = iden.field_identificacion_value
    WHERE nid=? ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idObra);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $obra = [];
    while ($row = $result->fetch_assoc()) {
      $obra['idObra'] = $row["nid"];
      $obra['titulo'] = $row["Titulo"];
      $obra['urlImagen'] = $row["urlImagen"];
      $obra['textoRazonado'] = $row["Texto_Razonado"];
      $obra['fechaEjecucion'] = $row["fecha_ejecucion"];
      $obra['autoria'] = $row["Autoria"];

      $obra['tecnica'] = $row["Tecnica"];
      $obra['soporte'] = $row["Soporte"];
      $obra['alto'] = $row["Alto"];
      $obra['ancho'] = $row["Ancho"];

      $obra['tipoAutoria'] = $row["Tipo_Autoria_Final"];
      $obra['tipoInscripcion'] = $row["Tipo_Inscripcion"];
      $obra['ubicacionInscripcion'] = $row["Ubicacion_Inscripcion"];
      $obra['transInscripcion'] = $row["Transcripcion_Inscripcion"];
    }
    return $obra;
  }

  function getPropiedadObra($idObra)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT nombrePropietario.field_nombre_propietario_value as nombrePropiedad,
    ingrAdquisicion.field_ano_ingreso_adquisicion_value as fechaAdqPropiedad,
    ciudadTermDataAdquisicion.name as ciudadAdq FROM node 
    LEFT JOIN field_data_field_historial_de_propiedad propiedad ON propiedad.entity_id = node.nid
    LEFT JOIN field_data_field_nombre_propietario nombrePropietario ON nombrePropietario.entity_id = propiedad.field_historial_de_propiedad_value
    LEFT JOIN field_data_field_ano_ingreso_adquisicion ingrAdquisicion ON ingrAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
    LEFT JOIN field_data_field_ciudad ciudadAdquisicion ON ciudadAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
    LEFT JOIN taxonomy_term_data ciudadTermDataAdquisicion ON ciudadTermDataAdquisicion.tid = ciudadAdquisicion.field_ciudad_tid
    WHERE nid=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idObra);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $propiedades = [];
    $x = 0;
    while ($row = $result->fetch_assoc()) {
      $propiedad['nombrePropietarioObra'] = $row["nombrePropiedad"];
      $propiedad['fechaAquisicionObra'] = $row["fechaAdqPropiedad"];
      $propiedad['ciudadAquisicionObra'] = $row["ciudadAdq"];
      $propiedades[$x] = $propiedad;
      $x++;
    }
    return $propiedades;
  }

  function getExhibicionesObra($idObra)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT tituloExhibicion.field_t_tulo_de_la_exhibici_n_value as Titulo_Exhibicion,
    nombreResponsableExhibicion.field_nombre_curador_responsable_value as Responsable_Exhibicion,
    nombreInstitucionExhibicion.field_institucion_value as Institucion_Exhibicion,
    ciudadTermDataExhibicion.name as Ciudad_Exhibicion,
    fechaExhibicion.field_fecha_inicio_year as Ano_Exhibicion FROM node 
    LEFT JOIN field_data_field_circulacion_de_la_imagen circulacion ON circulacion.entity_id = node.nid
    LEFT JOIN field_data_field_exhibiciones exhibiciones ON exhibiciones.entity_id = circulacion.field_circulacion_de_la_imagen_value
    LEFT JOIN field_data_field_t_tulo_de_la_exhibici_n tituloExhibicion ON tituloExhibicion.entity_id = exhibiciones.field_exhibiciones_value
    LEFT JOIN field_data_field_nombre_curador_responsable nombreResponsableExhibicion ON nombreResponsableExhibicion.entity_id = exhibiciones.field_exhibiciones_value
    LEFT JOIN field_data_field_institucion nombreInstitucionExhibicion ON nombreInstitucionExhibicion.entity_id = exhibiciones.field_exhibiciones_value
    LEFT JOIN field_data_field_ciudad ciudadExhibicion ON ciudadExhibicion.entity_id = exhibiciones.field_exhibiciones_value
    LEFT JOIN taxonomy_term_data ciudadTermDataExhibicion ON ciudadTermDataExhibicion.tid = ciudadExhibicion.field_ciudad_tid
    LEFT JOIN field_data_field_fecha_inicio fechaExhibicion ON fechaExhibicion.entity_id = exhibiciones.field_exhibiciones_value
    WHERE nid=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idObra);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $exhibiciones = [];
    $x = 0;
    while ($row = $result->fetch_assoc()) {
      $exhibicion['tituloExhibicion'] = $row["Titulo_Exhibicion"];
      $exhibicion['responsableExhibicion'] = $row["Responsable_Exhibicion"];
      $exhibicion['institucionExhibicion'] = $row["Institucion_Exhibicion"];
      $exhibicion['ciudadExhibicion'] = $row["Ciudad_Exhibicion"];
      $exhibicion['anoExhibicion'] = $row["Ano_Exhibicion"];
      $exhibiciones[$x] = $exhibicion;
      $x++;
    }
    return $exhibiciones;
  }




  function obra()
  {
    $obra = [];
    $obra['idObra'] = null;
    if (isset($_GET["idObra"])) {
      $obra['idObra'] = $_GET["idObra"];
    }
    $obra["infoObra"] = $this->getInfoObra($obra['idObra']);
    $obra["propiedadesObra"] = $this->getPropiedadObra($obra['idObra']);
    $obra["exhibicionesObra"] = $this->getExhibicionesObra($obra['idObra']);

    return [
      '#theme' => 'vpm-vista-obra',
      '#obra' => $obra
    ];
  }
}
