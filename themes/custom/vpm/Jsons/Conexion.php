<?php
class conexion
{
	private $servidor;
	private $datos;
	public  $conexion_bd;

	// public function __construct()
	// {
	// 	$this->servidor   = '10.0.1.225';
	// 	$this->datos	  = array("database" => "Biblioredesv2", "uid" => "dba", "pwd" => "P0o9i8u7y6");
	// }

	// function conectar()
	// {
	// 	$dsn = "10.0.1.225";
	// 	$database="Biblioredesv2";
	// 	//debe ser de sistema no de usuario
	// 	$usuario = "dba";
	// 	$clave = "P0o9i8u7y6";
	// 	$this->conexion_bd = odbc_connect("Driver={SQL Server};Server=$dsn;Database=$database;", $usuario, $clave);
	// 	// $this->conexion_bd= sqlsrv_connect($this->servidor,$this->datos);
	// }

	// function cerrar()
	// {
	// 	$this->conexion_bd->close();
	// }

	public function __construct(){
		$this->servidor   = '10.0.1.225'; 
		$this->datos	  = array("database" => "Biblioredes", "uid" => "dba", "pwd" => "P0o9i8u7y6");
	}
	// public function __construct(){
	// 	$this->servidor   = '10.0.2.175'; 
	// 	$this->datos	  = array("database" => "Biblioredes", "uid" => "sqlPCL", "pwd" => "P0o9i8u7");
	// }

	function conectar(){
		$this->conexion_bd= sqlsrv_connect($this->servidor,$this->datos);
	}

	function cerrar(){
		$this->conexion_bd->close();
	}
}
