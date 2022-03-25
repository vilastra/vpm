<?php 

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use mysqli;

class ObrasController extends ControllerBase
{   
    /*if (!empty($_GET['SelColeccion'])) {
        array_push($condiciones, 'COLECCION.Asig_Corologica = ?');
        array_push($where, $_GET['SelColeccion']);
        array_push($whereTipo, 's');
        $buscar = 1;
    }*/



    function Listar_Query(){

    }

    function Listar_Obras()
    {
        $limit = 6;

        if(!empty($_GET["pag"])){
            $pag = $_GET["pag"];
        }else{
            $pag=0;
        }
        if($pag < 1){
            $pag =1;
        }
     
        $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
        $offset = ($pag - 1) * $limit;        

        $sql = "SELECT nid,title as titulo,terminoTaxAutoria.name as autor, terminoTaxAutoria.tid as autorId, 
        file_managed.filename, terminoTaxTematica.name as Tematica,
        terminoTaxTematica.tid as idTematica
        FROM node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
        LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
        LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
        LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid WHERE  node.type = 'obra' AND node.status=1";
        $sql = $sql . " LIMIT $offset, $limit";  

        $resultado = $mysqli->query($sql);       

        $obras = [];
        $x = 0;
        $rutaQuinsac='http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';
        while ($fila = mysqli_fetch_array($resultado)) {
            $infoObra = [];
            $infoObra['idObra'] = $fila["nid"];
            $infoObra['tituloObra'] = $fila["titulo"];            
            $infoObra['nombreArtista'] = $fila["autor"];                      
            $infoObra['autorId'] = $fila["autorId"];
            /* IMAGEN */
            $infoObra['rutaFoto']=$fila["filename"];
            $infoObra['rutaFoto']=$rutaQuinsac.$fila["filename"];
            /* TEMATICA */
            $infoObra['idTematica']=$fila["idTematica"];
            $infoObra['nombreTematica']=$fila["Tematica"];

            $obras[$x] = $infoObra;
            $x++;
        }
        mysqli_close($mysqli);
        return $obras;
    }
 
    function Lista_Paginador()
    {
        $limit = 6;
        $limitPage = 7;
        $pag =(int) (!empty($_GET["pag"]));
        if($pag < 1){
            $pag = 1;
        }
        

        $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
        $offset = ($pag - 1) * $limit;  

      
        $sqlTotal = "SELECT COUNT(*) total
        FROM node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
        LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
        LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
        LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid WHERE  node.type = 'obra'";
     
        $result = mysqli_query($mysqli, $sqlTotal);
        $fila = mysqli_fetch_assoc($result);   
        $total = $fila['total'];

        $page = false;

        //examino la pagina a mostrar y el inicio del registro a mostrar
        if (!isset($_GET['pag'])) {
            $page = 1;
          } else {
            $page = $_GET['pag'];
          }
        
          if (!$page) {
            $start = 0;
            $page = 1;
          } else {
            $start = ($page - 1) *  $limit;
          }
          $total_pages = ceil($total / $limit);
        
          $variables = '?';
          foreach ($_GET as $key => $value) {
            if ($key != "pag") {
              $variables = $variables . $key . "=" . $value . "&";
            }
          }
          $arrayBoton = [];
          $z =0;
          if ($total_pages >= 1 && $page <= $total_pages) {
            $counter = 1;
            $arrayBotones = [];

            if ($page != 1) {
                $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . ($page - 1) . '">
                  <button class="pager__item"><span aria-hidden="true"><i class="fa fa-chevron-left" aria-hidden="true"></i>
                  </span></button></a>';
                  $z++;
            }
            if ($page > ($limitPage / 2)) {
                $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . 1 . '">
                  <button class="pager__item"><span aria-hidden="true">1...</span></button></a>';
                  $z++;
            }
            for ($x = $page - 2; $x <= $total_pages; $x++) {
              if ($x >= 1) {
                if ($counter < $limitPage) {
                  if ($page == $x) {
                    $arrayBotones[$z] ='<a class="pagelink" href="#"><button class="pager__item is-active">' . $page . '</button></a>';
                    $z++;
                  } else {
                    $arrayBotones[$z] ='<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . $x . '">
                    <button class="pager__item">' . $x . '</button></a>';
                    $z++;
                  }
                  $counter++;
                }
              }
            }
            if ($page < $total_pages - ($limitPage / 2)) {
                $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . $total_pages . '">
                <button class="pager__item"><span aria-hidden="true">...' . $total_pages . '</span></button></a>';
                $z++;
            }
            if ($page != $total_pages) {
              $arrayBotones[$z] = '<a class="pagelink"  href="' . base_path() . 'obras/' . $variables . 'pag=' . ($page + 1) . '">
              <button class="pager__item"><span aria-hidden="true"><i class="fa fa-chevron-right" aria-hidden="true"></i></span></button></a>';
             $z++;
            }
            $arrayBoton = $arrayBotones;
            
          }
          return $arrayBoton;

    }

    /*public function Cb_Coleccion(){

    }*/

    public function Cb_Tematica(){

      $res = false;
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
      try {
        $query = "SELECT DISTINCT
        terminoTaxTematica.name as Tematica,
        terminoTaxTematica.tid as idTematica
        FROM node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
        LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
        LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
        LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid
        WHERE  node.type = 'obra' and terminoTaxTematica.name is not null";   
    
    
        $resultado = $mysqli->query($query);
        $tematica=[];
        $x=0;
        while ($fila = mysqli_fetch_array($resultado)) {

          $infoTematica = [];
          $infoTematica['idTematica'] = $fila["idTematica"];
          $infoTematica['tematica'] = $fila["Tematica"];

          $tematica[$x] = $infoTematica;
          $x++;
        }
        mysqli_close($mysqli);
        return $tematica;
    }

    public function Cb_Artista(){
      $res = false;
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
      try {
        $query = "SELECT DISTINCT terminoTaxAutoria.name as autor, 
        terminoTaxAutoria.tid as autorId
        FROM node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
        LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
        LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
        LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid   
        WHERE  node.type = 'obra' and terminoTaxAutoria.name is not null";   
    
        $resultado = $mysqli->query($query);
        $artista=[];
        $x=0;
        while ($fila = mysqli_fetch_array($resultado)) {

          $infoArtista = [];
          $infoArtista['autorId'] = $fila["autorId"];
          $infoArtista['autor'] = $fila["autor"];

          $artista[$x] = $infoArtista;
          $x++;
        }
        mysqli_close($mysqli);
        return $artista;
      }

    public function Cb_Annio(){
      $res = false;
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
      try {
        $query = "select node.nid,
        fecEjecucion.field_fecha_ejecucion_timestamp as idfecEjec, 
        fecEjecucion.field_fecha_ejecucion_timestamp as fecEjec
        from node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_fecha_ejecucion fecEjecucion ON fecEjecucion.entity_id = iden.field_identificacion_value
        WHERE fecEjecucion.field_fecha_ejecucion_timestamp IS NOT NULL
        GROUP BY fecEjecucion.field_fecha_ejecucion_timestamp";  
    
        $resultado = $mysqli->query($query);
        $annio=[];
        $x=0;
        while ($fila = mysqli_fetch_array($resultado)) {

          $infoAnnio = [];
          $infoAnnio['idfecEjec'] = $fila["idfecEjec"];
          $infoAnnio['fecEjec'] = data_format($fila["fecEjec"], 'U = Y');

          $annio[$x] = $infoAnnio;
          $x++;
        }
        mysqli_close($mysqli);
        return $annio;

    }    

    public function Cb_Tecnica(){
      $res = false;
      $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
      try {
        $query = "SELECT 
        terminoTaxTematica.name as Tecnica,
        terminoTaxTematica.tid as idTecnica
        FROM node
        LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
        LEFT JOIN field_data_field_tecnica tecnicaObra ON tecnicaObra.entity_id = iden.field_identificacion_value
        LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tecnicaObra.field_tecnica_tid
        WHERE terminoTaxTematica.name IS NOT NULL
        GROUP BY Tecnica";   
    
        $resultado = $mysqli->query($query);
        $tecnica=[];
        $x=0;
        while ($fila = mysqli_fetch_array($resultado)) {

          $infoTecnica = [];
          $infoTecnica['idTecnica'] = $fila["idTecnica"];
          $infoTecnica['tecnica'] = $fila["Tecnica"];

          $tecnica[$x] = $infoTecnica;
          $x++;
        }
        mysqli_close($mysqli);
        return $tecnica;

    }

    public function obras(){
        $obras = $this->Listar_Obras();
        $arrayBoton = $this->Lista_Paginador();
        /*$coleccion = $this->Cb_Coleccion();*/
        $tematica = $this->Cb_Tematica();
        $artista = $this->Cb_Artista();
        $annio = $this->Cd_Annio(); 
        $tecnica = $this->Cb_Tecnica();

        return[
            '#theme' => 'vpm-vista-obras',
            '#obras' => $obras,
            '#arrayBoton' => $arrayBoton,
            '#tematica' => $tematica,
            '#annio' => $annio,
            '#artista' => $artista,
            '#tecnica' => $tecnica, 

        ];
    }
}