<?php
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

	function __construct(array $credentials, $log = false)
	{
		parent::__construct($log);
		$this->credentials = $credentials;
		$this->connect();
	}
	public function connect(): self
	{
		$this->log('CONECTING AT DATABASE...');
		if ($this->conexion = mysqli_connect(
			$this->credentials['DATABASE_HOST'],
			$this->credentials['DATABASE_USER'],
			$this->credentials['DATABASE_PASS'],
			$this->credentials['DATABASE_NAME'],
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
		while ($table = mysqli_fetch_array($result)) {
			$this->return[] = $table[0];
		}
		return $this;
	}
	public function query($sql): self
	{
		$this->log('SQL REQUEST...');
		$this->return = [];
		$result = mysqli_query($this->conexion, $sql)
			or throw new \Exception(mysqli_error($this->conexion));
		
		if(is_bool($result)){
			$this->return = mysqli_affected_rows($this->conexion);
		}else{
			while ($row = mysqli_fetch_assoc($result)) {
				$this->return[] = $row;
			}
		}
		return $this;
	}
	public function table(string $table): self
	{
		$this->log('GET TABLE');
		$this->table = $table;
		$this->query("SELECT * FROM $table");		
		return $this;
	}
	public function select(string $sql = "*") : self
	{
		$this->log('SELECT VALUES : ', $sql);
		$this->sql = str_replace('*', $sql, $this->sql);
		return $this;
	}
	public function filter( string $sql = "") : self 
	{
		$this->log('FILTER BY : ', $sql);
		$this->sql .= ' WHERE ' . $sql . ";";
		return $this;
	}
	public function get() : mixed
    {
        return $this->return;
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
