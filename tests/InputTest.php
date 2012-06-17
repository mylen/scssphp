<?php

require_once __DIR__ . "/../scss.inc.php";

// Runs all the tests in inputs/ and compares their output to ouputs/

function _dump($value) {
	fwrite(STDOUT, print_r($value, true));
}

function _quote($str) {
	return preg_quote($str, "/");
}

class InputTest extends PHPUnit_Framework_TestCase {
	protected static $inputDir = "inputs";
	protected static $outputDir = "outputs";

	public function setUp() {
		$this->scss = new scssc();
	}

	/**
	 * @dataProvider fileNameProvider
	 */
	public function testInputFile($inFname) {
		if (getenv("BUILD")) {
			return $this->buildInput($inFname);
		}

		$outFname = self::outputNameFor($inFname);

		if (!is_readable($outFname)) {
			$this->fail("$outFname is missing, ".
				"consider building tests with BUILD=true");
		}

		$input = file_get_contents($inFname);
		$output = file_get_contents($outFname);

		$this->assertEquals($output, $this->scss->compile($input));
	}

	public function fileNameProvider() {
		return array_map(function($a) { return array($a); },
			self::findInputNames());
	}

	// only run when env is set
	public function buildInput($inFname) {
		$css = $this->scss->compile(file_get_contents($inFname));
		file_put_contents(self::outputNameFor($inFname), $css);
	}

	static public function findInputNames($pattern="*") {
		$files = glob(__DIR__ . "/" . self::$inputDir . "/" . $pattern);
		return $files;
	}

	static public function outputNameFor($input) {
		$front = _quote(__DIR__ . "/");
		$out = preg_replace("/^$front/", "", $input);

		$in = _quote(self::$inputDir . "/");
		$out = preg_replace("/$in/", self::$outputDir . "/", $out);
		$out = preg_replace("/.scss$/", ".css", $out);

		return __DIR__ . "/" . $out;
	}

	static public function buildTests($pattern) {
		$files = self::findInputNames($pattern);
		foreach ($files as $file) {
		}
	}
}

