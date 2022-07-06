<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class ArtistaController extends ControllerBase
{
    function Listar_Artista()
    {
        $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
        $sql = "SELECT taxonomy_term_data.tid,taxonomy_term_data.name,
        file_managed.uri ,taxonomy_term_data.description 
        FROM taxonomy_term_data
        JOIN taxonomy_vocabulary ON taxonomy_vocabulary.vid=taxonomy_term_data.vid
        JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = taxonomy_term_data.tid
        JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
        WHERE taxonomy_vocabulary.name='Artistas' AND taxonomy_term_data.tid=" . $_GET["id"] . " LIMIT 3;";
        $resultado = $mysqli->query($sql);
        $artistas = [];
        $x = 0;
        $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';
        while ($fila = mysqli_fetch_array($resultado)) {
            $infoArtista = [];

            $infoArtista['idArtista'] = $fila["tid"];
            if (isset($_GET["idCat"])) {
                $infoArtista['urlArtista'] =  base_path() . "obras?idCat=1&Sec=Obras&artista=" . $fila["tid"];
            } else {
                $infoArtista['urlArtista'] =  base_path() . "obras?artista=" . $fila["tid"];
            }
            $infoArtista['nombreArtista'] = $fila["name"];
            // $infoArtista['descripcionArtista'] = $fila["description"];

            $infoArtista['rutaFoto'] = str_replace("public://","",$fila["uri"]);
            $infoArtista['rutaFoto'] = $rutaQuinsac . $infoArtista['rutaFoto'];
            $artistas[$x] = $infoArtista;
            $x++;
        }
        mysqli_close($mysqli);
        return $artistas;
    }
    function Listar_Artistas()
    {
        $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
        $sql = "SELECT taxonomy_term_data.tid,taxonomy_term_data.name,file_managed.uri FROM taxonomy_term_data
    LEFT JOIN taxonomy_vocabulary ON taxonomy_vocabulary.vid=taxonomy_term_data.vid
    LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = taxonomy_term_data.tid
    LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
    WHERE taxonomy_vocabulary.name='Artistas';";
        $resultado = $mysqli->query($sql);
        $artistas = [];
        $x = 0;
        $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/styles/200x200/public/';
        while ($fila = mysqli_fetch_array($resultado)) {
            $infoArtista = [];
            $infoArtista['idArtista'] = $fila["tid"];
            if (isset($_GET["idCat"])) {
                $infoArtista['urlArtista'] = "idCat=1&Sec=Art&id=" . $fila["tid"];
            } else {
                $infoArtista['urlArtista'] = "id=" . $fila["tid"];
            }
            $host = $GLOBALS["base_url"];
            $infoArtista['nombreArtista'] = $fila["name"];
            if ($fila["uri"] != null) {
                $infoArtista['rutaFoto'] = str_replace("public://","",$fila["uri"]);
            $infoArtista['rutaFoto'] = $rutaQuinsac . $infoArtista['rutaFoto'];
            } else {
                $infoArtista['rutaFoto'] = "http://quinsac.patrimoniocultural.gob.cl/sites/default/files/default_images/user-img.png";
            }

            $artistas[$x] = $infoArtista;
            $x++;
        }
        mysqli_close($mysqli);
        return $artistas;
    }
    function Listar_Obras_Artista($idArtista)
    {
        $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
        $sql = "SELECT nid,title as titulo,
        autoria.field_autoria_principal_tid as idAutoria,
        file_managed.uri ,
        terminoTaxTematica.name as Tematica,
        terminoTaxTematica.tid as idTematica,
        terminoTaxAutoria.tid as idArtista,
        terminoTaxAutoria.name as nombreArtista
        FROM node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
        LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
        LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
        LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid 
        
        where autoria.field_autoria_principal_tid = " . $idArtista . "";
        $resultado = $mysqli->query($sql);
        $resultadoFinal[0] = $resultado->num_rows;
        $obras = [];
        $x = 0;
        $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';
        while ($fila = mysqli_fetch_array($resultado)) {
            $infoObra = [];
            $infoObra['idObra'] = $fila["nid"];
            if (isset($_GET["idCat"])) {
                $infoObra['urlObra'] =  base_path() . "obra?idCat=1&Sec=Obras&idObra=" . $fila["nid"];
            } else {
                $infoObra['urlObra'] = base_path() . "obra?idObra=" . $fila["nid"];
            }
            $infoObra['idArtista'] = $fila["idArtista"];
            $infoObra['nombreArtista'] = $fila["nombreArtista"];
            $infoObra['tituloObra'] = $fila["titulo"];
            $infoObra['rutaFoto'] = str_replace("public://","",$fila["uri"]);
            $infoObra['rutaFoto'] = $rutaQuinsac . $infoObra['rutaFoto'];
            $infoObra['idTematica'] = $fila["idTematica"];
            $infoObra['nombreTematica'] = $fila["Tematica"];
            $obras[$x] = $infoObra;
            $x++;
            if ($x >= 6) {
                break;
            }
        }
        mysqli_close($mysqli);
        $resultadoFinal[1] = $obras;
        return $resultadoFinal;
    }

    public function artista()
    {
        $artistas = $this->Listar_Artista();
        $obras = $this->Listar_Obras_Artista($_GET["id"]);        
        return [
            '#theme' => 'vpm-vista-artista',
            '#artista' => $artistas,
            '#obrasDestacadas' => $obras,

        ];
    }
    public function artistasCatalogo()
    {
        $artistas = $this->Listar_Artistas();
        return [
            '#theme' => 'artistas',
            '#artistas' => $artistas
        ];
    }
}
