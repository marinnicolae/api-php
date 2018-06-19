<?php

namespace Sturents\Api;

use DateTimeInterface;
use JsonSerializable;

abstract class Model implements JsonSerializable {
	const DATE_FORMAT = 'Y-m-d H:i:s';

	/**
	 * @return array
	 */
	public function toArray(){
		$this->preOutput();

		$data = [];
		foreach (get_object_vars($this) as $key => $val){
			if ($key[0]==='_'){
				continue;
			}

			$data[$key] = $val;
		}

		$data = $this->toArrayData($data);

		return $data;
	}

	/**
	 * Allows changing the data format before serializing
	 */
	protected function preOutput(){
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(){
		return $this->toArray();
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	private function toArrayData(array $data){
		foreach ($data as $key => &$val){

			if (is_object($val)){
				if ($val instanceof Model){
					$val = $val->toArray();
				}
				elseif ($val instanceof DateTimeInterface) {
					$val = $val->format(self::DATE_FORMAT);
				}
				else {
					$val = (array) $val;
				}
			}

			if (is_array($val)){
				$val = $this->toArrayData($val);
			}
		}

		return $data;
	}
}
