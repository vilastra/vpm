<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class CatalogosController extends ControllerBase
{

    function Listar_Artistas()
    {
        $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
        $sql = "SELECT taxonomy_term_data.tid,taxonomy_term_data.name,file_managed.filename FROM taxonomy_term_data
    JOIN taxonomy_vocabulary ON taxonomy_vocabulary.vid=taxonomy_term_data.vid
    JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = taxonomy_term_data.tid
    JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
    WHERE taxonomy_vocabulary.name='Artistas' LIMIT 3;";
        $resultado = $mysqli->query($sql);
        $artistas = [];
        $x = 0;
        $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/styles/200x200/public/';
        while ($fila = mysqli_fetch_array($resultado)) {
            $infoArtista = [];
            $infoArtista['idArtista'] = $fila["tid"];
            $infoArtista['nombreArtista'] = $fila["name"];
            $infoArtista['rutaFoto'] = $fila["filename"];
            $infoArtista['rutaFoto'] = $rutaQuinsac . $fila["filename"];
            $artistas[$x] = $infoArtista;
            $x++;
        }

        mysqli_close($mysqli);
        return $artistas;
    }
    function Cantidad_Obras()
    {
        $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
        $sql = "SELECT count(nid) as cantObras
        FROM node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
        JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
        WHERE node.type='obra' AND node.status=1";
        $resultado = $mysqli->query($sql);
        $obras = [];
        $x = 0;
        $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/styles/200x200/public/';
        while ($fila = mysqli_fetch_array($resultado)) {
            $cantObras = 0;
            $cantObras = $fila["cantObras"];
        }
        mysqli_close($mysqli);
        return $cantObras;
    }
    public function catalogosRazonados()
    {
        $catalogos = [];
        $catalogos[0]["nombreCatalogo"] = "Monvoisin";
        $catalogos[0]["rutaFoto"] = "http://quinsac.patrimoniocultural.gob.cl/sites/default/files/styles/200x200/public/Captura%20de%20Pantalla%202020-03-11%20a%20la%28s%29%2011.08.42.png?itok=9WoQFsEf";

        $catalogos[0]["autores"] = $this->Listar_Artistas();
        $catalogos[0]["cantObras"] = $this->Cantidad_Obras();

        return [
            '#theme' => 'vpm-vista-catalogos-razonados',
            '#catalogos' => $catalogos,
        ];
    }
    public function sobreinvestigacion()
    {
        $sobreinvestigacion[0]["idCatalogo"] = 1;
        return [
            '#theme' => 'vpm-vista-sobreinvestigacion',
            '#sobreinvestigacion' => $sobreinvestigacion,
        ];
    }
    public function equipoinvestigacion()
    {
        $equipoinvestigacion[0]["idCatalogo"] = 1;
        return [
            '#theme' => 'vpm-vista-equipoinvestigacion',
            '#equipoinvestigacion' => $equipoinvestigacion,
        ];
    }
    public function preguntasfrecuentes()
    {
        $preguntasfrecuentes[0]["idCatalogo"] = 1;
        return [
            '#theme' => 'vpm-vista-preguntasfrecuentes',
            '#preguntasfrecuentes' => $preguntasfrecuentes,
        ];
    }
    public function catalogoIndividual()
    {
        $catalogos = [];
        $catalogos[0]["nombreCatalogo"] = "Monvoisin en América: Catalogación razonada de Raymond Quinsac Monvoisin y sus discípulos";
        $catalogos[0]["idCatalogo"] = 1;
        $catalogos[0]["rutaFoto"] = "http://quinsac.patrimoniocultural.gob.cl/sites/default/files/styles/200x200/public/Captura%20de%20Pantalla%202020-03-11%20a%20la%28s%29%2011.08.42.png?itok=9WoQFsEf";

        $catalogos[0]["autores"] = $this->Listar_Artistas();
        $catalogos[0]["cantObras"] = $this->Cantidad_Obras();

        return [
            '#theme' => 'vpm-vista-catalogo',
            '#catalogo' => $catalogos,
        ];
    }
}
