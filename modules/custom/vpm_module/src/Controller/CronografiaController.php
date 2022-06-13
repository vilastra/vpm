<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class CronografiaController extends ControllerBase
{
  function getPropiedadObra($idObra, $reqLatLon)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT nombrePropietario.field_nombre_propietario_value as nombrePropiedad,
    ingrAdquisicion.field_ano_ingreso_adquisicion_value as fechaAdqPropiedad,
    latitudAdquisicion.field_latitud_value as latitudAdqPropiedad,
    longitudAdquisicion.field_longitud_value as longitudAdqPropiedad,
    ciudadTermDataAdquisicion.name as ciudadAdq FROM node 
    LEFT JOIN field_data_field_historial_de_propiedad propiedad ON propiedad.entity_id = node.nid
    LEFT JOIN field_data_field_nombre_propietario nombrePropietario ON nombrePropietario.entity_id = propiedad.field_historial_de_propiedad_value
    LEFT JOIN field_data_field_ano_ingreso_adquisicion ingrAdquisicion ON ingrAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
    LEFT JOIN field_data_field_latitud latitudAdquisicion ON latitudAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
    LEFT JOIN field_data_field_longitud longitudAdquisicion ON longitudAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
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
    if ($reqLatLon) {
      while ($row = $result->fetch_assoc()) {
        $propiedades['latitudAdqObra'] = str_replace(',', '.', $row["latitudAdqPropiedad"]);
        $propiedades['longitudAdqObra'] = str_replace(',', '.', $row["longitudAdqPropiedad"]);
        break;
      }
    } else {
      while ($row = $result->fetch_assoc()) {
        if ($row["nombrePropiedad"] != null) {
          $propiedad['nombrePropietarioObra'] = $row["nombrePropiedad"];
          $propiedad['fechaAquisicionObra'] = $row["fechaAdqPropiedad"];
          $propiedad['ciudadAquisicionObra'] = $row["ciudadAdq"];
          $propiedad['latitudAdqObra'] = $row["latitudAdqPropiedad"];
          $propiedad['longitudAdqObra'] = $row["longitudAdqPropiedad"];
          $propiedades[$x] = $propiedad;
          $x++;
        }
      }
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
      if ($row["Titulo_Exhibicion"] != null) {
        $exhibicion['tituloExhibicion'] = $row["Titulo_Exhibicion"];
        $exhibicion['responsableExhibicion'] = $row["Responsable_Exhibicion"];
        $exhibicion['institucionExhibicion'] = $row["Institucion_Exhibicion"];
        $exhibicion['ciudadExhibicion'] = $row["Ciudad_Exhibicion"];
        $exhibicion['anoExhibicion'] = $row["Ano_Exhibicion"];
        $exhibiciones[$x] = $exhibicion;
        $x++;
      }
    }
    return $exhibiciones;
  }
  function getExhibiciones($where, $whereTipo)
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
    WHERE tituloExhibicion.field_t_tulo_de_la_exhibici_n_value IS NOT NULL
    GROUP BY tituloExhibicion.field_t_tulo_de_la_exhibici_n_value";
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


    /* Execute statement */
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado;
  }
  function Listar_Obras($where, $whereTipo)
  {

    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT nid,title as Titulo,
    DATE_FORMAT(fecEjecucion.field_fecha_ejecucion_timestamp, '%Y') as fecha_ejecucion,
    terminoTaxAutoria.name as Autoria,
    terminoTaxAutoria.tid as autorId,
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
    anchoImagen.field_ancho_value as Ancho,
    linkImgOrig.field_enlace_imagen_original_url as linkImgOriginal,
    autorTexto.field_autor_texto_razonado_value as AutorTexto
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
    LEFT JOIN field_data_field_autor_texto_razonado autorTexto ON autorTexto.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_enlace_imagen_original linkImgOrig ON linkImgOrig.entity_id = iden.field_identificacion_value

    LEFT JOIN field_data_field_soporte soporte ON soporte.entity_id = iden.field_identificacion_value
    LEFT JOIN taxonomy_term_data terminoTaxSoporte ON terminoTaxSoporte.tid = soporte.field_soporte_tid
    LEFT JOIN field_data_field_tecnica tecnica ON tecnica.entity_id = iden.field_identificacion_value
    LEFT JOIN taxonomy_term_data terminoTaxTecnica ON terminoTaxTecnica.tid = tecnica.field_tecnica_tid
    LEFT JOIN taxonomy_term_data terminoTaxtipoAutoria ON terminoTaxtipoAutoria.tid = tipoAutoria.field_tipo_de_autoria_tid
    LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
    LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
    LEFT JOIN field_data_field_tipo_de_autoria_final tipoAutoriaFinal ON tipoAutoriaFinal.entity_id = iden.field_identificacion_value
    LEFT JOIN taxonomy_term_data terminoTaxtipoAutoriaFinal ON terminoTaxtipoAutoriaFinal.tid = tipoAutoriaFinal.field_tipo_de_autoria_final_tid
    LEFT JOIN field_data_field_autoria_principal_final tipoAutoriaPrinFinal ON tipoAutoriaPrinFinal.entity_id = iden.field_identificacion_value
    LEFT JOIN taxonomy_term_data terminoTaxtipoAutoriaPrinFinal ON terminoTaxtipoAutoriaPrinFinal.tid = tipoAutoriaPrinFinal.field_autoria_principal_final_tid
    LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
    LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
    LEFT JOIN field_data_field_texto_razonado textoRazonado ON textoRazonado.entity_id = iden.field_identificacion_value
    WHERE node.type = 'obra' AND node.status=1";



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


    /* Execute statement */
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado;
  }

  function armarJson()
  {

    $obras = [];
    $x = 0;
    $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';

    $jsonObj = '{
            "scale": "human",
            "title": {
                "media": {
                  "caption": "",
                  "credit": "",
                  "url": "",
                  "thumbnail": ""
                },
                "text": {
                  "headline": "Cronografía Catálogo Monvoisin",
                  "text": ""
                }
              },
            "events": [';
    $where = [];
    $whereTipo = [];
    $resultado = $this->Listar_Obras($where, $whereTipo);
    while ($fila = mysqli_fetch_array($resultado)) {
      if ($fila["fecha_ejecucion"] != 0) {
        $infoObra = [];
        $infoObra['idObra'] = $fila["nid"];
        if (isset($_GET["idCat"])) {
          $infoObra['urlObra'] =  base_path() . "obra?idCat=1&Sec=Obras&idObra=" . $fila["nid"];
        } else {
          $infoObra['urlObra'] = base_path() . "obra?idObra=" . $fila["nid"];
        }

        $infoObra['tituloObra'] = $fila["Titulo"];
        $infoObra['nombreArtista'] = $fila["Autoria"];
        $infoObra['autorId'] = $fila["autorId"];
        if (isset($_GET["idCat"])) {
          $infoObra['urlArtista'] = base_path() . "artista?idCat=1&Sec=Art&id=" . $fila["autorId"];
        } else {
          $infoObra['urlArtista'] = base_path() . "artista?id=" . $fila["autorId"];
        }
        /* IMAGEN */
        $infoObra['rutaFoto'] = $fila["urlImagen"];
        $infoObra['rutaFoto'] = $rutaQuinsac . $fila["urlImagen"];

        $infoObra['fecEjec'] = $fila["fecha_ejecucion"];
        $infoObra['tipoInscripcion'] = $fila["Tipo_Inscripcion"];
        $infoObra['ubicacionInscripcion'] = $fila["Ubicacion_Inscripcion"];
        $infoObra['transInscripcion'] = $fila["Transcripcion_Inscripcion"];
        $infoObra['transInscripcion'] = str_replace($infoObra['transInscripcion'], '"', '');
        $infoObra["propiedadesObra"] = $this->getPropiedadObra($infoObra['idObra'], false);
        $infoObra["exhibicionesObra"] = $this->getExhibicionesObra($infoObra['idObra']);
        $jsonObj .= '{
                    "media": {
                      "url": "' . $infoObra['rutaFoto'] . '",
                      "thumbnail": ""
                    },
                    "text": {
                      "headline": "' . $infoObra['tituloObra'] . '",
                      "text": "' . $infoObra['nombreArtista'];

        if (!empty($infoObra["propiedadesObra"])) {
          $jsonObj .= "<div class='divSepFicha'><div class='subTextoObraFicha'>Circulación</div>";
          foreach ($infoObra["propiedadesObra"] as $propiedad) {
            $jsonObj .= "<div class='descObraFicha'>" . $propiedad["nombrePropietarioObra"] . " " . $propiedad["ciudadAquisicionObra"] . " " . $propiedad["fechaAquisicionObra"] . "</div>";
          }
          $jsonObj .= "</div>";
        }
        if (!empty($infoObra["exhibicionesObra"])) {
          $jsonObj .= "<div class='divSepFicha'><div class='subTextoObraFicha'>Exhibiciones</div>";
          foreach ($infoObra["exhibicionesObra"] as $exhibicion) {
            $jsonObj .= "<div class='descObraFicha'>" . $exhibicion["tituloExhibicion"] . " " . $exhibicion["institucionExhibicion"] . " " . $exhibicion["ciudadExhibicion"] . " " . $exhibicion["anoExhibicion"] . "</div>";
          }
          $jsonObj .= "</div>";
        }
        $jsonObj .= "<div class='descObraFicha'><a target='_self' href='" . $infoObra['urlObra'] . "' class='btn button btnBuscar more-link keychainify-checked'>Ve la ficha de obra completa</a></div>";
        $jsonObj .= '"
                    },
                    "start_date": {
                      "year": "' . $infoObra['fecEjec'] . '"
                    },
                    "unique_id": "obraCrono"
                  },';

        $obras[$x] = $infoObra;
        $x++;
      }
    }
    $resultado = $this->getExhibiciones($where, $whereTipo);
    while ($fila = mysqli_fetch_array($resultado)) {
      $jsonObj .= '{
        "media": {
          "caption": "",
          "url": "",
          "thumbnail": "http://localhost/vpm/sites/default/files/2022-04/bookmark.png"
        },
        "text": {
          "headline": "' . $fila["Titulo_Exhibicion"] . '",
          "text": "';
      if ($fila["Responsable_Exhibicion"] != null) {
        $jsonObj .= "<div class='descObraFicha'><b>Responsable:</b> " . $fila["Responsable_Exhibicion"] . "</div>";
      }
      if ($fila["Institucion_Exhibicion"] != null) {
        $jsonObj .= "<div class='descObraFicha'><b>Institucion:</b> " . $fila["Institucion_Exhibicion"] . "</div>";
      }
      $jsonObj .= "<div class='descObraFicha'>" . $fila["Ciudad_Exhibicion"] . " " . $fila["Ano_Exhibicion"] . "</div>";
      $jsonObj .= '"';
      $jsonObj .= '},
        "start_date": {
          
          "year": "' . $fila['Ano_Exhibicion'] . '"
        },
        "unique_id": "exhiCrono"
      },';
    }


    $jsonObj .= ']}';

    return $jsonObj;
  }

  function cronografia()
  {

    $cronografia = $this->armarJson();
    return [
      '#theme' => 'vpm-vista-cronografia',
      '#cronografia' => $cronografia
    ];
  }
}
