<?php

namespace Drupal\vpm_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use mysqli;

/**
 * Provides a 'Bloque de artistas en Index VPM' Block.
 *
 * @Block(
 *   id = "vpm_index_artistas",
 *   admin_label = @Translation("Bloque de artistas en Index VPM"),
 *   category = @Translation("Modulo VPM"),
 * )
 */
class VpmIndexArtistas extends BlockBase
{

  function Listar_Artistas()
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT taxonomy_term_data.tid,taxonomy_term_data.name,file_managed.filename FROM taxonomy_term_data
    LEFT JOIN taxonomy_vocabulary ON taxonomy_vocabulary.vid=taxonomy_term_data.vid
    LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = taxonomy_term_data.tid
    LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
    WHERE taxonomy_vocabulary.name='Artistas' LIMIT 3;";
    $resultado = $mysqli->query($sql);
    $artistas=[];
    $x=0;
    $rutaQuinsac='http://quinsac.patrimoniocultural.gob.cl/sites/default/files/styles/200x200/public/';
    while ($fila = mysqli_fetch_array($resultado)) {
      $infoArtista=[];
      $infoArtista['idArtista']=$fila["tid"];
      $infoArtista['nombreArtista']=$fila["name"];
      $infoArtista['rutaFoto']=$fila["filename"];
      $infoArtista['rutaFoto']=$rutaQuinsac.$fila["filename"];
      $artistas[$x]=$infoArtista;
      $x++;
    }
    mysqli_close($mysqli);
    return $artistas;
  }
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $artistas = $this->Listar_Artistas();
    return [
      '#theme' => 'vpm-index-artistas',
      '#artistas' => $artistas,
    ];
  }
}
