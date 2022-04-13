<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class CronografiaController extends ControllerBase
{
    function Listar_Obras($where, $whereTipo)
    {

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
        $where = [];
        $whereTipo = [];
        $resultado = $this->Listar_Obras($where, $whereTipo);
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
                  "headline": "Cronologia catálogo Monvoisin",
                  "text": "Follow the presidential caucuses and primaries as members of the Republican Party race to become the GO"
                }
              },
            "events": [';
        while ($fila = mysqli_fetch_array($resultado)) {
            if ($fila["fecEjec"] != 0) {
                $infoObra = [];
                $infoObra['idObra'] = $fila["nid"];
                if (isset($_GET["idCat"])) {
                    $infoObra['urlObra'] =  base_path() . "obra?idCat=1&Sec=Obras&idObra=" . $fila["nid"];
                } else {
                    $infoObra['urlObra'] = base_path() . "obra?idObra=" . $fila["nid"];
                }

                $infoObra['tituloObra'] = $fila["titulo"];
                $infoObra['nombreArtista'] = $fila["autor"];
                $infoObra['autorId'] = $fila["autorId"];
                if (isset($_GET["idCat"])) {
                    $infoObra['urlArtista'] = base_path() . "artista?idCat=1&Sec=Art&id=" . $fila["autorId"];
                } else {
                    $infoObra['urlArtista'] = base_path() . "artista?id=" . $fila["autorId"];
                }
                /* IMAGEN */
                $infoObra['rutaFoto'] = $fila["filename"];
                $infoObra['rutaFoto'] = $rutaQuinsac . $fila["filename"];
                /* TEMATICA */
                $infoObra['idTematica'] = $fila["idTematica"];
                $infoObra['nombreTematica'] = $fila["Tematica"];

                $infoObra['fecEjec'] = $fila["fecEjec"];


                $jsonObj .= '{
                    "media": {
                      "url": "' . $infoObra['rutaFoto'] . '",
                      "thumbnail": ""
                    },
                    "text": {
                      "headline": "' . $infoObra['tituloObra'] . '",
                      "text": "' . $infoObra['nombreArtista'] . '"
                    },
                    "start_date": {
                      "year": "' . $infoObra['fecEjec'] . '"
                    }
                  },';

                $obras[$x] = $infoObra;
                $x++;
            }
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
