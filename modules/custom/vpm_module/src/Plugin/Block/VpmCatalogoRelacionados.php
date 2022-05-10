<?php

namespace Drupal\vpm_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use mysqli;

/**
 * Provides a 'Bloque de artistas en Index VPM' Block.
 *
 * @Block(
 *   id = "vpm_catalogo_relacionados",
 *   admin_label = @Translation("Bloque para contenido relacionado en catálogo VPM"),
 *   category = @Translation("Modulo VPM"),
 * )
 */
class VpmCatalogoRelacionados extends BlockBase
{

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
    $menuLink['SobreInvestigacion']=$host.'/sobreinvestigacion?idCat='.$idCatalogo."&Sec=Sobr";
    return $menuLink;
  }
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $links = $this->Crea_Menu();
    return [
      '#theme' => 'vpm-catalogo-relacionados',
      '#links' => $links,
    ];
  }
}
