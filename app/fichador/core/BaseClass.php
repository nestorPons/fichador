<?php
class BaseClass
{
    protected $return;
	protected bool $logon;

	function __construct(bool $log = false)
	{
		$this->logon = $log;
	}
	public function print(): self
	{
		if (is_array($this->return)) {
			foreach ($this->return as $key => $value) {
				print('<pre>');
				print('KEY: ');
				print_r($key);
				print('</pre>');
				print('<pre>');
				print('VALUE: ');
				print_r($value);
				print('</pre>');
			}
		}else{
            print($this->return);
        }
		return $this;
	}
    public function get() : mixed
    {
        return $this->return;
    }
	protected function log(...$menssages): void
    {
        if ($this->logon) {
            foreach ($menssages as $mens) {
                print('<pre>');
                var_dump($mens);
                print('</pre>');
            }
        }
    }
	public function save_file(string $file = 'src/tmp.log'): void {
		$result = file_put_contents($file, print_r($this->return, true));
		if ($result === false) 
			throw new \Exception("Error guardado el archivo!!");
		
	}
	public function return(){
		return $this->return; 
	}
}
