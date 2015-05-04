<?php
/*
 * Copyright (c) 2015, Josef Kufner  <josef@kufner.cz>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Duf;

/**
 * Simple buffer with helpers to generate PHP code.
 */
class PhpBuffer
{
	protected $code = '';
	protected $indent = '';
	private $next_var = 1;


	/**
	 * Get result.
	 */
	public function getCode()
	{
		return $this->code;
	}


	/**
	 * Get result.
	 */
	public function __toString()
	{
		return $this->code;
	}


	/**
	 * Append arguments as lines to buffer.
	 */
	public function write()
	{
		foreach (func_get_args() as $line) {
			if ($line != '') {
				$this->code .= $this->indent;
			}
			$this->code .= $line;
			$this->code .= "\n";
		}
	}


	/**
	 * Append arguments as one line to buffer.
	 */
	public function writeLn()
	{
		$this->code .= $this->indent;
		foreach (func_get_args() as $line) {
			$this->code .= $line;
		}
		$this->code .= "\n";
	}


	/**
	 * Convert value to PHP representation.
	 */
	public function quoteValue($value)
	{
		return var_export($value, true);
	}


	/**
	 * Get unique variable name.
	 */
	public function getVar($prefix = 'x')
	{
		return '$' . $prefix . '_' . ($this->next_var++);
	}


	/**
	 * Append arguments as lines of comments to buffer.
	 */
	public function writeComment()
	{
		foreach (func_get_args() as $line) {
			$this->code .= $this->indent;
			$this->code .= '// ';
			$this->code .= $line;
			$this->code .= "\n";
		}
	}


	/**
	 * Write echos with raw output without additional line wraps in output.
	 */
	public function writeEcho()
	{
		$args = func_get_args();
		if (empty($args)) {
			return;
		}

		$this->code .= $this->indent;
		$this->code .= "echo ";
		$sep = null;
		foreach ($args as $line) {
			$this->code .= $sep;
			$this->code .= $this->quoteValue((string) $line);
			if ($sep === null) {
				$sep = ",\n\t".$this->indent;
			}
		}
		$this->code .= ";\n";
	}


	/**
	 * Write echos with raw output with additional "\n" at the end.
	 */
	public function writeEchoLn()
	{
		$args = func_get_args();
		$this->code .= $this->indent;
		$this->code .= "echo ";
		if (empty($args)) {
			$this->code .= "\"\\n\";\n";
		} else {
			$sep = null;
			foreach ($args as $line) {
				$this->code .= $sep;
				$this->code .= $this->quoteValue((string) $line);
				if ($sep === null) {
					$sep = ",\n\t".$this->indent;
				}
			}
			$this->code .= ", \"\\n\";\n";
		}
	}


	/**
	 * Write echos with HTML escaping (without additional line wraps in HTML).
	 */
	public function writeHtml()
	{
		$args = func_get_args();
		if (empty($args)) {
			return;
		}

		$this->code .= $this->indent;
		$this->code .= "echo ";
		$sep = null;
		foreach ($args as $line) {
			$this->code .= $sep;
			$this->code .= 'htmlspecialchars(';
			$this->code .= $line;
			$this->code .= ')';
			if ($sep === null) {
				$sep = ",\n\t".$this->indent;
			}
		}
		$this->code .= ";\n";
	}


	/**
	 * Begin a block (like if, for, while, ...)
	 */
	public function beginBlock($start_line = '')
	{
		if ($start_line !== '') {
			$this->code .= $this->indent;
			$this->code .= $start_line;
			$this->code .= " {\n";
		} else {
			$this->code .= $this->indent;
			$this->code .= "{\n";
		}

		$this->indent .= "\t";
	}

	/**
	 * End a block.
	 */
	public function endBlock()
	{
		$this->indent = substr($this->indent, 1);
		$this->code .= $this->indent;
		$this->code .= "}\n";

	}


	/**
	 * Begin a foreach cycle.
	 */
	public function beginForeach($collection, & $key_var, & $value_var)
	{
		$key_var = $this->getVar('k');
		$value_var = $this->getVar('v');
		$this->beginBlock("foreach($collection as $key_var => $value_var)");
	}


	/**
	 * Assign a value, use default value if not set.
	 */
	public function writeIfSet($dst, $src, $default = 'null')
	{
		$this->write("$dst = isset($src) ? $src : $default");
	}


	/**
	 * Wrte HTML/XML attribute using PHP expression as value, adding leading space
	 *
	 * Usage:
	 *
	 *     $form->writeEcho('<div');
	 *     $form->writeEchoAttr('id', '$id');
	 *     $form->writeEchoLn('>');
	 *     // Result: echo '<div id=', htmlspecialchars($id), ">\n";
	 *
	 */
	public function writeEchoAttr($attr, $expr)
	{
		$this->writeLn('echo \' ', $attr, '="\', ', 'htmlspecialchars(', $expr, '), \'"\';');
	}

}

