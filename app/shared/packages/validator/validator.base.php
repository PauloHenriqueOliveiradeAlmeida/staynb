<?php

require_once "validator.base.php";

abstract class Validator {

	protected mixed $data;

	public function get() {
		return $this->data;
	}


	public function isRequired(string $message = null)
	{
		if (isset($this->data)) {
			return $this;
		}
		else {
			throw new Exception($message ? $message : "{$this->data} não foi informado, mas é obrigatório", 400);
		}
	}

	public function length(?int $min = 0, ?int $max = 10, ?string $message = null) {
		$data_length = strlen(strval($this->data));

		if ($data_length >= $min && $data_length <= $max) {
			return new $this;
		}
		else {
			throw new LengthException($message ? $message : "{$this->data} deve ter entre $min e $max caracteres", 400);
		}
	}

}
