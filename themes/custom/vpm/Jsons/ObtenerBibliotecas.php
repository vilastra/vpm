<?php
class DetalleBiblioteca
{
    public $idBiblioteca = null;
    public $nombreBiblioteca = null;
    public $idComuna = null;
    public $nombreComuna = null;
    public $idRegion = null;
    public $nombreRegion = null;
}
class DetalleComuna
{
    public $idComuna = null;
    public $nombreComuna = null;
    public $idRegion = null;
    public $nombreRegion = null;
    public $latitud = null;
    public $longitud = null;
}
class ObtenerBibliotecasComuna
{
    private $conexion;
    private $validacion;

    public function __construct()
    {
        require_once('Conexion.php');
        $this->conexion = new conexion();
        $this->conexion->conectar();
    }
    function Obtener_Region($idRegion)
    {
        $consulta = "SELECT TOP 1 * FROM mu.vRegiones 
        WHERE CodigoRegion=?
        ORDER BY Orden ASC";
        $pstmt = sqlsrv_prepare($this->conexion->conexion_bd, $consulta, array($idRegion));
        $res = sqlsrv_execute($pstmt);
        $obj = sqlsrv_fetch_object($pstmt);
        return $obj;
    }
    function ObtenerDetalleBiblioteca($idBiblioteca)
    {
        $consulta = "SELECT TOP 1 recinto.CodigoSistema,recinto.NombreRecinto,comunas.CodigoComuna,comunas.NombreComuna,region.CodigoRegion,region.NombreRegion 
        FROM pcl.vRecintos AS recinto
        INNER JOIN mu.vComunas AS comunas ON comunas.CodigoComuna = recinto.CodigoComuna
        INNER JOIN mu.vProvincias AS provincia ON provincia.CodigoProvincia = comunas.CodigoProvincia
        INNER JOIN mu.vRegiones AS region ON region.CodigoRegion = provincia.CodigoRegion
        WHERE provincia.Activo = 1 AND recinto.CodigoSistema=? 
        ORDER BY recinto.CodigoSistema";
        $pstmt = sqlsrv_prepare($this->conexion->conexion_bd, $consulta, array($idBiblioteca));
        $res = sqlsrv_execute($pstmt);
        $obj = sqlsrv_fetch_object($pstmt);

        //$ejecutar = sqLsrv_query($this->conexion->conexion_bd, utf8_decode($consulta));
        $DetalleBiblioteca = new DetalleBiblioteca();
        //while ($obj = sqlsrv_fetch_object($ejecutar)) {
        $DetalleBiblioteca->idBiblioteca = $obj->CodigoSistema;
        $DetalleBiblioteca->nombreBiblioteca = $obj->NombreRecinto;
        $DetalleBiblioteca->idComuna = $obj->CodigoComuna;
        $DetalleBiblioteca->nombreComuna =  $obj->NombreComuna;
        $DetalleBiblioteca->idRegion = $obj->CodigoRegion;
        $DetalleBiblioteca->nombreRegion =  $obj->NombreRegion;
        //}
        return $DetalleBiblioteca;
        $this->conexion->cerrar();
    }
    function ObtenerDetalleComuna($idComuna)
    {
        $consulta = "SELECT TOP 1 comunas.CodigoComuna,comunas.NombreComuna,region.CodigoRegion,region.NombreRegion,ubicacion.Latitud,ubicacion.Longitud
        FROM dbo.Comunas AS comunas
        INNER JOIN mu.vProvincias ON mu.vProvincias.CodigoProvincia = comunas.CodigoProvincia
        INNER JOIN mu.vRegiones AS region ON region.CodigoRegion = mu.vProvincias.CodigoRegion
        INNER JOIN pcl.UbicacionTerritorio AS ubicacion ON ubicacion.CodigoTerritorio = comunas.CodigoComuna
        WHERE comunas.CodigoComuna=?";

        $pstmt = sqlsrv_prepare($this->conexion->conexion_bd, $consulta, array($idComuna));
        $res = sqlsrv_execute($pstmt);
        $obj = sqlsrv_fetch_object($pstmt);

        //$ejecutar = sqLsrv_query($this->conexion->conexion_bd, utf8_decode($consulta));
        //while ($obj = sqlsrv_fetch_object($ejecutar)) {
        $DetalleComuna = new DetalleComuna();
        $DetalleComuna->idComuna = $obj->CodigoComuna;
        $DetalleComuna->nombreComuna = $obj->NombreComuna;
        $DetalleComuna->idRegion = $obj->CodigoRegion;
        $DetalleComuna->nombreRegion = $obj->NombreRegion;
        $DetalleComuna->latitud = $obj->Latitud;
        $DetalleComuna->longitud = $obj->Longitud;
        //}
        return $DetalleComuna;
        $this->conexion->cerrar();
    }
    function Lista_BibliotecasComuna($comuna)
    {
        $consulta = "SELECT * FROM pcl.vRecintos where CodigoComuna = ? ORDER BY CodigoSistema";
        $pstmt = sqlsrv_prepare($this->conexion->conexion_bd, $consulta, array($comuna));
        $res = sqlsrv_execute($pstmt);      
        //$ejecutar = odbc_exec($this->conexion->conexion_bd, utf8_decode($consulta));
        return $pstmt;
        $this->conexion->cerrar();
    }
    function Lista_BibliotecasRegion($region)
    {
        $consulta = "SELECT * FROM pcl.vRecintos where CodigoRegion = ? ORDER BY CodigoSistema";
        $pstmt = sqlsrv_prepare($this->conexion->conexion_bd, $consulta, array($region));
        $res = sqlsrv_execute($pstmt);      
        //$ejecutar = odbc_exec($this->conexion->conexion_bd, utf8_decode($consulta));
        return $pstmt;
        $this->conexion->cerrar();
    }

    function Lista_Comunas()
    {
        $consulta = "SELECT * FROM mu.vComunas AS comunas 
        INNER JOIN mu.vProvincias AS provincia ON provincia.CodigoProvincia = comunas.CodigoProvincia
        WHERE provincia.Activo = 1 
        ORDER BY NombreComuna ASC";
        $ejecutar = sqLsrv_query($this->conexion->conexion_bd, utf8_decode($consulta));
        return $ejecutar;
        $this->conexion->cerrar();
    }
    function Lista_ComunasRegion($region)
    {
        $consulta = "SELECT t0.* FROM mu.vComunas t0 INNER JOIN mu.vProvincias t1 ON t0.CodigoProvincia = t1.CodigoProvincia
        WHERE CodigoRegion = ? and Activo = '1' ORDER BY NombreComuna ASC";
        $pstmt = sqlsrv_prepare($this->conexion->conexion_bd, $consulta, array($region));
        $res = sqlsrv_execute($pstmt);
        //$ejecutar = odbc_exec($this->conexion->conexion_bd, utf8_decode($consulta));
        return $pstmt;
        $this->conexion->cerrar();
    }
    function Lista_Region()
    {
        $consulta = "SELECT * FROM mu.vRegiones ORDER BY Orden ASC";
        $ejecutar = sqLsrv_query($this->conexion->conexion_bd, $consulta);
        return $ejecutar;
        $this->conexion->cerrar();
    }
    function Lista_Recintos()
    {
        $consulta = "SELECT * FROM pcl.vRecintos ORDER BY CodigoSistema";
        $ejecutar = sqLsrv_query($this->conexion->conexion_bd, utf8_decode($consulta));
        return $ejecutar;
        $this->conexion->cerrar();
    }
}
if ($_POST["opcion"] == 1) {
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $_POST["id"];
        $recintos = $instancia_lfiltro->Lista_BibliotecasComuna($id);
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            //$filaRecinto = mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            $codigoRecinto = $filaRecinto['CodigoSistema'];
            $nombreRecinto = $filaRecinto['NombreRecinto'];
            echo "<option value='" . $codigoRecinto . "'>" . $nombreRecinto . "</option>";
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else if ($_POST["opcion"] == 2) {
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $_POST["id"];
        $recintos = $instancia_lfiltro->Lista_BibliotecasRegion($id);
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            // $filaRecinto = //mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            $codigoRecinto = $filaRecinto['CodigoSistema'];
            $nombreRecinto = $filaRecinto['NombreRecinto'];
            echo "<option value='" . $filaRecinto["CodigoSistema"] . "'>" . $filaRecinto["NombreRecinto"] . "</option>";
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }

} else if ($_POST["opcion"] == 3) {
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $instancia_lfiltro->ObtenerDetalleBiblioteca($_POST["id"]);
        $recintos = $instancia_lfiltro->Lista_Region();
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            // $filaRecinto = //mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            if($filaRecinto["CodigoRegion"]==$id->idRegion){
                echo "<option selected value='" . $filaRecinto["CodigoRegion"] . "'>" . $filaRecinto["NombreRegion"] . "</option>";
            }else{
                echo "<option value='" . $filaRecinto["CodigoRegion"] . "'>" . $filaRecinto["NombreRegion"] . "</option>";
            }
            
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else if ($_POST["opcion"] == 4) {
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $instancia_lfiltro->ObtenerDetalleComuna($_POST["id"]);
        $recintos = $instancia_lfiltro->Lista_Region();
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            // $filaRecinto = //mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            if($filaRecinto["CodigoRegion"]==$id->idRegion){
                echo "<option selected value='" . $filaRecinto["CodigoRegion"] . "'>" . $filaRecinto["NombreRegion"] . "</option>";
            }else{
                echo "<option value='" . $filaRecinto["CodigoRegion"] . "'>" . $filaRecinto["NombreRegion"] . "</option>";
            }
            
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else if ($_POST["opcion"] == 5) {
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $_POST["id"];
        $recintos = $instancia_lfiltro->Lista_Region();
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            //$filaRecinto = mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            $codigoRecinto = $filaRecinto['CodigoRegion'];
            $nombreRecinto = $filaRecinto['NombreRegion'];
            echo "<option value='" . $codigoRecinto . "'>" . $nombreRecinto . "</option>";
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else if ($_POST["opcion"] == 6) {
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $_POST["id"];
        $recintos = $instancia_lfiltro->Lista_Comunas();
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            //$filaRecinto = mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            $codigoRecinto = $filaRecinto['CodigoComuna'];
            $nombreRecinto = $filaRecinto['NombreComuna'];
            echo "<option value='" . $codigoRecinto . "'>" . $nombreRecinto . "</option>";
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else if ($_POST["opcion"] == 7) {
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $_POST["id"];
        $recintos = $instancia_lfiltro->Lista_Recintos();
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            //$filaRecinto = mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            $codigoRecinto = $filaRecinto['CodigoSistema'];
            $nombreRecinto = $filaRecinto['NombreRecinto'];
            echo "<option value='" . $codigoRecinto . "'>" . $nombreRecinto . "</option>";
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}else if ($_POST["opcion"] == 8) { // Lista bibliotecas por comuna y selecciona una 
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $_POST["id"];
        $idBiblio = $_POST["idBiblio"];
        $recintos = $instancia_lfiltro->Lista_BibliotecasComuna($id);
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            //$filaRecinto = mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            $codigoRecinto = $filaRecinto['CodigoSistema'];
            $nombreRecinto = $filaRecinto['NombreRecinto'];

            if($codigoRecinto==$idBiblio){
                echo "<option selected value='" . $codigoRecinto . "'>" . $nombreRecinto . "</option>";
            }else{
                echo "<option value='" . $codigoRecinto . "'>" . $nombreRecinto . "</option>";
            }           
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}else if ($_POST["opcion"] == 9) {
    try {
        $data = array();
        $instancia_lfiltro = new ObtenerBibliotecasComuna();
        $id = $_POST["id"];
        $idComuna = $_POST["comuna"];
        $recintos = $instancia_lfiltro->Lista_ComunasRegion($id);
        echo "<option value='0'>Elija una opción</option>";

        while ($filaRecinto = sqLsrv_fetch_array($recintos)) {
            //$filaRecinto = mb_convert_encoding($filaRecinto, "UTF-8", "iso-8859-1");
            $codigoRecinto = $filaRecinto['CodigoComuna'];
            $nombreRecinto = $filaRecinto['NombreComuna'];
            
            if($codigoRecinto==$idComuna){
                echo "<option selected value='" . $codigoRecinto . "'>" . $nombreRecinto . "</option>";
            }else{
                echo "<option value='" . $codigoRecinto . "'>" . $nombreRecinto . "</option>";
            } 
            
        }
        echo "</optgroup>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
