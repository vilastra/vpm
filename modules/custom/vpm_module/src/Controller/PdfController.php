<?php

namespace Drupal\vpm_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Renderer;
use mysqli;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfController extends ControllerBase
{
  protected $renderer;

  public function __construct(Renderer $renderer)
  {
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('renderer')
    );
  }

  function getInfoObra($idObra)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT nid,title as Titulo,
      fecEjecucion.field_fecha_ejecucion_year as fecha_ejecucion,
      terminoTaxAutoria.name as Autoria,
      terminoTaxAutoria.tid as autorId,
      terminoTaxtipoAutoriaFinal.name as Tipo_Autoria_Final,
      terminoTaxtipoAutoriaPrinFinal.name as Tipo_Autoria_Prin_Final,
      terminoTaxtipoInsc.name as Tipo_Inscripcion,
      ubicacionIns.field_ubicacion_en_la_obra_value as Ubicacion_Inscripcion,
      file_managed.uri as urlImagen,
      transcripcionIns.field_transcripcion_value as Transcripcion_Inscripcion,
      textoRazonado.field_texto_razonado_cuerpo_value as Texto_Razonado,
      terminoTaxTecnica.name as Tecnica,
      terminoTaxSoporte.name as Soporte,
      altoImagen.field_alto_value as Alto,
      anchoImagen.field_ancho_value as Ancho,
      linkImgOrig.field_enlace_imagen_original_url as linkImgOriginal,
      autorTexto.field_autor_texto_razonado_value as AutorTexto
      FROM node
      LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = node.nid
      LEFT JOIN field_data_field_numero numero ON numero.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_titulo_actual titActual ON titActual.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_fecha_ejecucion fecEjecucion ON fecEjecucion.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_inscripciones inscrip ON inscrip.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_tipo_de_inscripcion tipoins ON tipoins.entity_id = inscrip.field_inscripciones_value
      LEFT JOIN taxonomy_term_data terminoTaxtipoInsc ON terminoTaxtipoInsc.tid = tipoins.field_tipo_de_inscripcion_tid
      LEFT JOIN field_data_field_ubicacion_en_la_obra ubicacionIns ON ubicacionIns.entity_id = inscrip.field_inscripciones_value
      LEFT JOIN field_data_field_transcripcion transcripcionIns ON transcripcionIns.entity_id = inscrip.field_inscripciones_value
      LEFT JOIN field_data_field_tipo_de_autoria tipoAutoria ON tipoAutoria.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_alto altoImagen ON altoImagen.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_ancho anchoImagen ON anchoImagen.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_autor_texto_razonado autorTexto ON autorTexto.entity_id = iden.field_identificacion_value
      LEFT JOIN field_data_field_enlace_imagen_original linkImgOrig ON linkImgOrig.entity_id = iden.field_identificacion_value
  
      LEFT JOIN field_data_field_soporte soporte ON soporte.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxSoporte ON terminoTaxSoporte.tid = soporte.field_soporte_tid
      LEFT JOIN field_data_field_tecnica tecnica ON tecnica.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTecnica ON terminoTaxTecnica.tid = tecnica.field_tecnica_tid
      LEFT JOIN taxonomy_term_data terminoTaxtipoAutoria ON terminoTaxtipoAutoria.tid = tipoAutoria.field_tipo_de_autoria_tid
      LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
      LEFT JOIN field_data_field_tipo_de_autoria_final tipoAutoriaFinal ON tipoAutoriaFinal.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxtipoAutoriaFinal ON terminoTaxtipoAutoriaFinal.tid = tipoAutoriaFinal.field_tipo_de_autoria_final_tid
      LEFT JOIN field_data_field_autoria_principal_final tipoAutoriaPrinFinal ON tipoAutoriaPrinFinal.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxtipoAutoriaPrinFinal ON terminoTaxtipoAutoriaPrinFinal.tid = tipoAutoriaPrinFinal.field_autoria_principal_final_tid
      LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
      LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
      LEFT JOIN field_data_field_cotenido_razonado contenidoRazonado ON contenidoRazonado.entity_id = iden.field_identificacion_value
    LEFT JOIN field_data_field_texto_razonado_cuerpo textoRazonado ON textoRazonado.entity_id = contenidoRazonado.field_cotenido_razonado_value
      WHERE node.type = 'obra' AND node.status=1 AND nid=? ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idObra);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $obra = [];
    $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';

    while ($row = $result->fetch_assoc()) {
      $obra['idObra'] = $row["nid"];
      $obra['titulo'] = $row["Titulo"];
      //$obra['urlImagen'] = $row["urlImagen"];
      $obra['linkImgOriginal'] = $row["linkImgOriginal"];

     
      /* IMAGEN */
      if($row["urlImagen"] !=null){

        $obra['urlImagen'] = str_replace("public://","",$row["urlImagen"]);
        $obra['urlImagen'] = $rutaQuinsac . $obra['urlImagen'];
      }else{
        $obra['urlImagen'] = '';
      }




      $obra['textoRazonado'] = $row["Texto_Razonado"];
      $obra['fechaEjecucion'] = $row["fecha_ejecucion"];


      $obra['autoria'] = $row["Autoria"];
      $obra['autorId'] = $row["autorId"];
      if (isset($_GET["idCat"])) {
        $obra['autorUrl'] = base_path() . "artista?idCat=1&Sec=Art&id=" . $row["autorId"];
      } else {
        $obra['autorUrl'] = base_path() . "artista?id=" . $row["autorId"];
      }

      $obra['autorTexto'] = $row["AutorTexto"];

      $obra['tecnica'] = $row["Tecnica"];
      $obra['soporte'] = $row["Soporte"];
      $obra['alto'] = $row["Alto"];
      $obra['ancho'] = $row["Ancho"];

      $obra['tipoAutoria'] = $row["Tipo_Autoria_Final"];
      $obra['tipoInscripcion'] = $row["Tipo_Inscripcion"];
      $obra['ubicacionInscripcion'] = $row["Ubicacion_Inscripcion"];
      $obra['transInscripcion'] = $row["Transcripcion_Inscripcion"];
    }
    return $obra;
  }

  function getPropiedadObra($idObra, $reqLatLon)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT nombrePropietario.field_nombre_propietario_value as nombrePropiedad,
      ingrAdquisicion.field_ano_ingreso_adquisicion_value as fechaAdqPropiedad,
      latitudAdquisicion.field_latitud_value as latitudAdqPropiedad,
      longitudAdquisicion.field_longitud_value as longitudAdqPropiedad,
      ciudadTermDataAdquisicion.name as ciudadAdq FROM node 
      LEFT JOIN field_data_field_historial_de_propiedad propiedad ON propiedad.entity_id = node.nid
      LEFT JOIN field_data_field_nombre_propietario nombrePropietario ON nombrePropietario.entity_id = propiedad.field_historial_de_propiedad_value
      LEFT JOIN field_data_field_ano_ingreso_adquisicion ingrAdquisicion ON ingrAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
      LEFT JOIN field_data_field_latitud latitudAdquisicion ON latitudAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
      LEFT JOIN field_data_field_longitud longitudAdquisicion ON longitudAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
      LEFT JOIN field_data_field_ciudad ciudadAdquisicion ON ciudadAdquisicion.entity_id = propiedad.field_historial_de_propiedad_value
      LEFT JOIN taxonomy_term_data ciudadTermDataAdquisicion ON ciudadTermDataAdquisicion.tid = ciudadAdquisicion.field_ciudad_tid
      WHERE nid=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idObra);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $propiedades = [];
    $x = 0;
    if ($reqLatLon) {
      while ($row = $result->fetch_assoc()) {
        $propiedades['latitudAdqObra'] = str_replace(',', '.', $row["latitudAdqPropiedad"]);
        $propiedades['longitudAdqObra'] = str_replace(',', '.', $row["longitudAdqPropiedad"]);
        break;
      }
    } else {
      while ($row = $result->fetch_assoc()) {
        if ($row["nombrePropiedad"] != null) {
          $propiedad['nombrePropietarioObra'] = $row["nombrePropiedad"];
          $propiedad['fechaAquisicionObra'] = $row["fechaAdqPropiedad"];
          $propiedad['ciudadAquisicionObra'] = $row["ciudadAdq"];
          $propiedad['latitudAdqObra'] = $row["latitudAdqPropiedad"];
          $propiedad['longitudAdqObra'] = $row["longitudAdqPropiedad"];
          $propiedades[$x] = $propiedad;
          $x++;
        }
      }
    }

    return $propiedades;
  }

  function getExhibicionesObra($idObra)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT tituloExhibicion.field_t_tulo_de_la_exhibici_n_value as Titulo_Exhibicion,
      nombreResponsableExhibicion.field_nombre_curador_responsable_value as Responsable_Exhibicion,
      nombreInstitucionExhibicion.field_institucion_value as Institucion_Exhibicion,
      ciudadTermDataExhibicion.name as Ciudad_Exhibicion,
      fechaExhibicion.field_fecha_inicio_year as Ano_Exhibicion FROM node 
      LEFT JOIN field_data_field_circulacion_de_la_imagen circulacion ON circulacion.entity_id = node.nid
      LEFT JOIN field_data_field_exhibiciones exhibiciones ON exhibiciones.entity_id = circulacion.field_circulacion_de_la_imagen_value
      LEFT JOIN field_data_field_t_tulo_de_la_exhibici_n tituloExhibicion ON tituloExhibicion.entity_id = exhibiciones.field_exhibiciones_value
      LEFT JOIN field_data_field_nombre_curador_responsable nombreResponsableExhibicion ON nombreResponsableExhibicion.entity_id = exhibiciones.field_exhibiciones_value
      LEFT JOIN field_data_field_institucion nombreInstitucionExhibicion ON nombreInstitucionExhibicion.entity_id = exhibiciones.field_exhibiciones_value
      LEFT JOIN field_data_field_ciudad ciudadExhibicion ON ciudadExhibicion.entity_id = exhibiciones.field_exhibiciones_value
      LEFT JOIN taxonomy_term_data ciudadTermDataExhibicion ON ciudadTermDataExhibicion.tid = ciudadExhibicion.field_ciudad_tid
      LEFT JOIN field_data_field_fecha_inicio fechaExhibicion ON fechaExhibicion.entity_id = exhibiciones.field_exhibiciones_value
      WHERE nid=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idObra);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $exhibiciones = [];
    $x = 0;
    while ($row = $result->fetch_assoc()) {
      if ($row["Titulo_Exhibicion"] != null) {
        $exhibicion['tituloExhibicion'] = $row["Titulo_Exhibicion"];
        $exhibicion['responsableExhibicion'] = $row["Responsable_Exhibicion"];
        $exhibicion['institucionExhibicion'] = $row["Institucion_Exhibicion"];
        $exhibicion['ciudadExhibicion'] = $row["Ciudad_Exhibicion"];
        $exhibicion['anoExhibicion'] = $row["Ano_Exhibicion"];
        $exhibiciones[$x] = $exhibicion;
        $x++;
      }
    }
    return $exhibiciones;
  }

  function getReferenciasBiblioObra($idObra)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "SELECT node.title, 
      biblio.biblio_sort_title as Titulo,
      biblio.biblio_secondary_title as Revista,
      biblio.biblio_year as Anio,
      biblio.biblio_volume as Volumen,
      biblio.biblio_pages as Paginacion,
      biblioConData.name as nombreAutor,
      node.nid FROM node
      JOIN field_data_field_referencias_bibliograficas refBiblio ON refBiblio.entity_id= node.nid
      JOIN field_data_field_referencia ref ON ref.entity_id = refBiblio.field_referencias_bibliograficas_value
      JOIN biblio ON biblio.nid = ref.field_referencia_target_id
      JOIN biblio_contributor biblioCon ON biblioCon.nid=biblio.nid
      JOIN biblio_contributor_data biblioConData ON biblioConData.cid = biblioCon.cid
      WHERE node.nid=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idObra);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $bibliografias = [];
    $x = 0;
    while ($row = $result->fetch_assoc()) {
      $bibliografia['tituloBiblio'] = $row["Titulo"];
      $bibliografia['revistaBiblio'] = $row["Revista"];
      $bibliografia['anioBiblio'] = $row["Anio"];
      $bibliografia['volumenBiblio'] = $row["Volumen"];
      $bibliografia['paginacionBiblio'] = $row["Paginacion"];
      $bibliografia['nombreAutorBiblio'] = $row["nombreAutor"];
      $bibliografias[$x] = $bibliografia;
      $x++;
    }
    return $bibliografias;
  }

  function getObrasRelacionadas($idObra)
  {
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'quinsac');
    $sql = "select node.title as ObraPrincipal,
      obrasRelacionadas.nid as idObraRelacionada,
      obrasRelacionadas.title as titulo,
      terminoTaxAutoria.name as autor,
      terminoTaxAutoria.tid as autorId,
      terminoTaxTematica.name as Tematica,
      terminoTaxTematica.tid as idTematica,
      file_managed.filename
      FROM node
      LEFT JOIN field_data_field_iconografia_retrato idenRetrato ON idenRetrato.entity_id = node.nid
      LEFT JOIN field_data_field_persona idenPersona ON idenPersona.entity_id = idenRetrato.field_iconografia_retrato_value
      LEFT JOIN field_data_field_num_obra_relacionada targetObra ON targetObra.entity_id = idenPersona.field_persona_value
      LEFT JOIN node obrasRelacionadas ON obrasRelacionadas.nid = targetObra.field_num_obra_relacionada_target_id
      LEFT JOIN field_data_field_identificacion iden ON iden.entity_id = obrasRelacionadas.nid
      LEFT JOIN field_data_field_autoria_principal autoria ON autoria.entity_id = iden.field_identificacion_value
      JOIN taxonomy_term_data terminoTaxAutoria ON terminoTaxAutoria.tid = autoria.field_autoria_principal_tid
      LEFT JOIN field_data_field_tematica_de_la_obra tematicaObra ON tematicaObra.entity_id = iden.field_identificacion_value
      LEFT JOIN taxonomy_term_data terminoTaxTematica ON terminoTaxTematica.tid = tematicaObra.field_tematica_de_la_obra_tid 
      LEFT JOIN field_data_field_imagen ON field_data_field_imagen.entity_id = iden.field_identificacion_value
      LEFT JOIN file_managed ON file_managed.fid = field_data_field_imagen.field_imagen_fid
      WHERE node.nid = ? AND targetObra.field_num_obra_relacionada_target_id IS NOT NULL AND obrasRelacionadas.type = 'obra' AND obrasRelacionadas.status=1 LIMIT 3";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $idObra);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($mysqli);
    $obras = [];
    $x = 0;
    $rutaQuinsac = 'http://quinsac.patrimoniocultural.gob.cl/sites/default/files/';
    while ($fila = mysqli_fetch_array($result)) {
      $infoObra = [];
      $infoObra['idObra'] = $fila["idObraRelacionada"];
      if (isset($_GET["idCat"])) {
        $infoObra['urlObra'] =  base_path() . "obra?idCat=1&Sec=Obras&idObra=" . $fila["idObraRelacionada"];
      } else {
        $infoObra['urlObra'] = base_path() . "obra?idObra=" . $fila["idObraRelacionada"];
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
      if($fila["filename"] !=null){
        $infoObra['rutaFoto'] = $fila["filename"];
        $infoObra['rutaFoto'] = $rutaQuinsac . $fila["filename"];
      }else{
        $infoObra['rutaFoto'] = 	"http://quinsac.patrimoniocultural.gob.cl/sites/default/files/Portada_recorte.jpg";
      }
    
      /* TEMATICA */
      $infoObra['idTematica'] = $fila["idTematica"];
      $infoObra['nombreTematica'] = $fila["Tematica"];

      $obras[$x] = $infoObra;
      $x++;
    }
    return $obras;
  }

  public function getViewPdf()
  {
    
    $obra['idObra'] = null;
    if (isset($_GET["idObra"])) {
      $idObra = $_GET["idObra"];
    }
    //$idObra = 1;
   
    $obra["infoObra"] = $this->getInfoObra($idObra);
    $obra["propiedadesObra"] = $this->getPropiedadObra($idObra, false);
    $obra["latYLong"] = $this->getPropiedadObra($idObra, true);
    $obra["exhibicionesObra"] = $this->getExhibicionesObra($idObra);
    $obra["bibliografiaObra"] = $this->getReferenciasBiblioObra($idObra);
    $obra["obrasRelacionadas"] = $this->getObrasRelacionadas($idObra);
    $renderable = [
      '#theme' => 'vpm-vista-pdf',
      '#obra' => $obra,
    ];
    $rendered = \Drupal::service('renderer')->renderPlain($renderable);
    // Cast to string since twig_render_template returns a Markup object.
    $body = (string) $renderable;

    return $rendered;
  }

  function pdf()
  {
    $dompdf = new Dompdf();
    $html = '';
    $options = $dompdf->getOptions();
    $options->set(array('isRemoteEnabled' => true));
    $dompdf->setOptions($options);
   
    $html .= $this->getViewPdf();

    $dompdf->loadhtml($html);
    $dompdf->setPaper('letter');
    $tituloVar = "Ficha";
    $tituloVar = str_replace('/', '-', $tituloVar);
    $dompdf->render();
    $dompdf->stream($tituloVar, array("Attachment" => false));
  }



  function obra()
  {
    $obra = $this->pdf();

    return [
      '#theme' => 'vpm-vista-pdf',
      '#obra' => $obra
    ];
  }
}
