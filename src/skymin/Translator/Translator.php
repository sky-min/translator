<?php
declare(strict_types = 1);

namespace skymin\Translator;

use function str_replace;

final class Translator{

	private string $default;

	/**
	 * @var Language[]
	 * @phpstan-var array<string, Language>
	 */
	private array $langs = [];

	public function __construct(Language $default){
		$this->default = $default->getLocal();
		$this->addLanguage($default);
	}

	public function getDefaultLocal() : string{
		return $this->default;
	}

	public function getLocalList() : array{
		return array_keys($this->langs);
	}

	public function addLanguage(Language $lang) : void{
		$local = $lang->getLocal();
		if(isset($this->langs[$local])){
			throw new TranslatorException('This is an already registered local name');
		}
		$this->langs[$local] = $lang;
	}

	public function getLanguage(string $local) : Language{
		if(isset($this->langs[$local])){
			return $this->langs[$local];
		}
		throw new TranslatorException($local . ' is not registered.');
	}

	/** @param string[] $parameters */ 
	public function translate(string $id, array $parameters, ?string $local = null) : string{
		if($local === null || !isset($this->langs[$local])){
			$local = $this->default;
		}
		$str = $this->getLanguage($local)->getText($id);
		$str = str_replace('{br}',  "\n", $str);
		$str = str_replace('{tab}', "\t", $str);
		foreach($parameters as $key => $parameter){
			$str = str_replace('{%' . $key . '}',  $parameter, $str);
		}
		return $str;
	}

}