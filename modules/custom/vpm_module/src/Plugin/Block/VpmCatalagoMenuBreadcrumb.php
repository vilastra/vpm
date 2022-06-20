<?php

namespace Drupal\vpm_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use mysqli;


/**
 * Provides a 'Bloque de breadcrumb en VPM' Block.
 *
 * @Block(
 *   id = "vpm_catalogo_menu_breadcrumb",
 *   admin_label = @Translation("Bloque menú en breadcrumb VPM"),
 *   category = @Translation("Modulo VPM"),
 * )
 */

class VpmCatalogoMenuBreadcrumb extends BlockBase
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
          '#theme' => 'vpm-catalogo-menu-breadcrumb',
          '#links' => $links,
        ];
      }
}