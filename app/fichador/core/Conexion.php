<?php

namespace app\fichador\core;

use \Dotenv\{Dotenv};

class Conexion extends BaseClass
{
	private $conexion, $sql;
	public $result, $error = false, $mQCount, $mQResult, $status = false;
	protected
		$table,
		$server,
		$user,
		$pass,
		$type,
		$db;

	function __construct(string $db_name, ?string $table = null, ?bool $log = false)
	{
		parent::__construct($log);
		$this->connect($db_name);
		$this->table($table);
	}
	private function load_data_conection(): self
	{
		$arr = explode('/', $_SERVER['DOCUMENT_ROOT']);
		$folder_root = str_replace(array_pop($arr), '', $_SERVER['DOCUMENT_ROOT']);
		// Carga de la libreria para las variables de entorno
		$this->credentials = Dotenv::createImmutable($folder_root)->load();
		return $this;
	}
	private function connect(string $db_name = null): self
	{
		$this->load_data_conection();
		$this->log('CONECTING AT DATABASE...');
		if ($this->conexion = mysqli_connect(
			$this->credentials['DATABASE_HOST'],
			$this->credentials['DATABASE_USER'],
			$this->credentials['DATABASE_PASS'],
			$db_name,
			$this->credentials['DATABASE_PORT']
		)) {
			$this->log('CONECTED!');
			$this->conexion->query("SET NAMES 'utf8'");
			$this->conexion->query("SET time_zone = '+2:00'");
			$this->status = true;
		} else {
			throw new \Exception('Error de conexion');
		}
		return $this;
	}
	public function show_tables(): self
	{
		$this->log('SHOW TABLES...');
		$this->query("SHOW TABLES");
		$result = $this->return;
		$this->return = [];
		foreach ($result as $key => $value) {
			$this->return[] = $value;
		}
		return $this;
	}
	/**
	 *  Si la consulta SQL contiene un SELECT o SHOW, devolverá un arreglo conteniendo todas las filas del resultado
	 *	Si la consulta SQL es un DELETE, INSERT o UPDATE, retornará el número de filas afectadas
	 *
	 *  @param  string $sql
	 *	@param  array  $params
	 *	@param  int    $fetchmode
	 *	@return mixed
	 */

	public function query(string $sql, $params = null) : self
	{

		$this->sql = trim(str_replace("\r", " ", $sql));
		/* var_dump($this->sql, $params); 
        die(); */
		// Prepara la sentencia con sus parametros y la inicia
		$respond = $this->init($params);

		$rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $this->sql));
		# Determina el tipo de SQL 
		$statement = strtolower($rawStatement[0]);
		if ($statement === 'select' || $statement === 'show') {
			$this->return = $this->sqlPrepare->fetchAll(\PDO::FETCH_ASSOC);
		} elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
			$this->return = $this->sqlPrepare->rowCount();
		} else {
			$this->return = $respond;
		}

		return $this;
	}
	public function table(string $table = null): string
	{
		if ($table) $this->table = $table;
		return $this->table;
	}
	public function select(string $sql = "*"): self
	{
		$this->log('SELECT VALUES : ', $sql);
		$this->sql = str_replace('*', $sql, $this->sql);
		return $this;
	}
	public function filter(string $sql = ""): self
	{
		$this->log('FILTER BY : ', $sql);
		$this->sql .= ' WHERE ' . $sql . ";";
		return $this;
	}
	public function get(): mixed
	{
		return $this->return;
	}
	/**
	 * Devuelve datos de una peticion por algun campo del registro
	 */
	public function getBy($field, $value, string $order = 'id'): self
	{
		$this->return = $this->query("SELECT * FROM {$this->table} WHERE $field LIKE '$value'  ORDER BY $order;");
		var_dump($this->return);
		exit;
		return $this;
	}
	/********* */
	//Funcion original multi query 
	//La que hay que usar por defecto
	public function multi_query($sql)
	{
		$return = mysqli_multi_query($this->conexion, $sql)
			or throw new \Exception(mysqli_error($this->conexion));
		while (mysqli_more_results($this->conexion) && mysqli_next_result($this->conexion)) {
			$result = mysqli_store_result($this->conexion);
			if (is_object($result)) {
				$result->free();
			}
			unset($result);
		}
		return $return;
	}

	public function scape($str)
	{

		if (!$this->error) {

			$replace = ['=', "'", '"', '/', '#', '*', "<", ">", ":", "{", "}", "?", "|", "&"];
			$str = str_replace($replace, '', $str);
			$str = trim($str);
			return mysqli_real_escape_string($this->conexion, $str);
		} else {

			return false;
		}
	}
	public function row($sql)
	{
		$this->query($sql);
		return  mysqli_fetch_row($this->return);
	}
	public function assoc($sql)
	{
		$this->query($sql);
		return  mysqli_fetch_assoc($this->return);
	}
	public function all($sql, $type = MYSQLI_NUM)
	{
		$this->query($sql);
		return mysqli_fetch_all($this->return, $type);
	}
	public function array($sql)
	{
		$this->query($sql);
		return  mysqli_fetch_array($this->return);
	}
	public function id()
	{
		return mysqli_insert_id($this->conexion);
	}
	public function num($sql)
	{
		$this->query($sql);
		return mysqli_num_rows($this->return);
	}
	public function error()
	{
		return mysqli_error($this->conexion);
	}
	public function errno()
	{
		return mysqli_errno($this->conexion);
	}
	function __destruct()
	{
		if ($this->error != false) {
			echo $this->error;
		}
		if ($this->conexion) $this->conexion->close();
	}
}
