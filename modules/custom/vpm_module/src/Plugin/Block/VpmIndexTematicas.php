<?php

namespace Drupal\vpm_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use mysqli;

/**
 * Provides a 'Bloque de tematicas en Index VPM' Block.
 *
 * @Block(
 *   id = "vpm_index_tematicas",
 *   admin_label = @Translation("Bloque de tematicas en Index VPM"),
 *   category = @Translation("Modulo VPM"),
 * )
 */
class VpmIndexTematicas extends BlockBase
{

  function Listar_Tematicas()
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT file_managed.uri,
    terminoTaxTematica.name as Tematica,
    terminoTaxTematica.tid as idTematica
    FROM node
    LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
    LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
    LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid
    LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
    LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
    WHERE terminoTaxTematica.name IS NOT NULL
    GROUP BY Tematica";
    $resultado = $mysqli->query($sql);
    $Tematicas = [];
    $x = 0;
    $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';
    while ($fila = mysqli_fetch_array($resultado)) {
      $infoTematica = [];
      $infoTematica['idTematica'] = $fila["idTematica"];
      $infoTematica['nombreTematica'] = $fila["Tematica"];

      $infoTematica['rutaFoto'] = str_replace("public://", "", $fila["uri"]);
      $infoTematica['rutaFoto'] = $rutaQuinsac . $infoTematica['rutaFoto'];
      $Tematicas[$x] = $infoTematica;
      $x++;
    }
    mysqli_close($mysqli);
    return $Tematicas;
  }
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $Tematicas = $this->Listar_Tematicas();
    return [
      '#theme' => 'vpm-index-tematicas',
      '#tematicas' => $Tematicas,
    ];
  }
}
