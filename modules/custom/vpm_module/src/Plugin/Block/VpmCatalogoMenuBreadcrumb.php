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



        $menuLink['CatalogosRazonados']=$host.'/catalogos-razonados';
        $menuLink['EquipoInvestigacion']=$host.'/equipoinvestigacion';
        $menuLink['QuinsacEnCifras']=$host.'/quinsac-en-cifras';
        $menuLink['PreguntasFrecuentes']=$host.'/preguntasfrecuentes';
        //$menuLink['QuinsacEnCifras']=$host.'/


        return $menuLink;
      }

     /*function Listar_Artista($idCatalogo){
        $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
        $sql = "SELECT taxonomy_term_data.tid,taxonomy_term_data.name,file_managed.filename ,taxonomy_term_data.description 
        FROM taxonomy_term_data
        JOIN taxonomy_vocabulary ON taxonomy_vocabulary.vid=taxonomy_term_data.vid
        JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = taxonomy_term_data.tid
        JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
        WHERE taxonomy_vocabulary.name='Artistas' AND taxonomy_term_data.tid=?";
        
        $resultado = $mysqli->query($sql);
        $artistas = [];
        $x = 0;
        while ($fila = mysqli_fetch_array($resultado)) {
            $infoArtista = [];          
            $infoArtista['nombreArtista'] = $fila["name"];
            $artistas[$x] = $infoArtista;
            $x++;
        }
        mysqli_close($mysqli);
        return $artistas;
      }*/

      /**
       * {@inheritdoc}
       */
      public function build()
      {
       /* if(isset($_GET["idCat"])){
          $idCatalogo=$_GET["idCat"];*/

        $links = $this->Crea_Menu();
        //$artistas = $this->Listar_Artista($idCatalogo);
        return [
          '#theme' => 'vpm-catalogo-menu-breadcrumb',
          '#links' => $links,
          //'#artista' => $artistas
        ];
      }
}