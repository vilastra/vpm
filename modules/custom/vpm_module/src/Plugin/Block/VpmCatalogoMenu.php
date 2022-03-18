<?php

namespace Drupal\vpm_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use mysqli;

/**
 * Provides a 'Bloque de artistas en Index VPM' Block.
 *
 * @Block(
 *   id = "vpm_catalogo_menu",
 *   admin_label = @Translation("Bloque para menú en catálogo VPM"),
 *   category = @Translation("Modulo VPM"),
 * )
 */
class VpmCatalogoMenu extends BlockBase
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
  function Crea_Menu(){
    $menuLink=[];
    if(isset($_GET["idCat"])){
      $idCatalogo=$_GET["idCat"];
    }else{
      $idCatalogo='null';
    }
    $host = $GLOBALS["base_url"];
    if(isset($_GET["Sec"])){
      $menuLink['ActualPage']=$_GET["Sec"];
    }else{
      $menuLink['ActualPage']='null';
    }
    $menuLink['idCatalogo']=$idCatalogo;
    $menuLink['Portada']=$host.'/catalogo?idCat='.$idCatalogo;
    $menuLink['Artistas']=$host.'/artistas?idCat='.$idCatalogo."&Sec=Art";
    $menuLink['Obras']=$host.'/obras?idCat='.$idCatalogo."&Sec=Obras";
    $menuLink['Publicaciones']=$host.'/publicaciones?idCat='.$idCatalogo."&Sec=Publ";
    $menuLink['Cronografia']=$host.'/cronografia?idCat='.$idCatalogo."&Sec=Cron";
    $menuLink['SobreInvestigacion']=$host.'/sobre-investigacion?idCat='.$idCatalogo."&Sec=Sobr";
    return $menuLink;
  }
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $links = $this->Listar_Artistas();
    $links = $this->Crea_Menu();
    return [
      '#theme' => 'vpm-catalogo-menu',
      '#links' => $links,
    ];
  }
}
