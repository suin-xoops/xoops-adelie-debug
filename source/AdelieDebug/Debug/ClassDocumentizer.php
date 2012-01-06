<?php
/**
 * AdelieDebug\Debug\Documentizer class.
 * @package    AdelieDebug
 * @author     Suin <suinyeze@gmail.com>
 * @copyright  2012 Suin
 * @license    MIT License
 */

class AdelieDebug_Debug_ClassDocumentizer extends ReflectionClass
{
	public function documentize()
	{
		$output = array();
		$output['filename'] = $this->getFileName();
		$output['class'] = $this->_getClassPhrase();
		$output['parent'] = $this->_getParentPhrase();
		$output['interface'] = $this->_getInterfacePhrase();
		$output['constants']  = $this->_getConstants();
		$output['properties'] = $this->_getPropertyPhrases();
		$output['methods'] = $this->_getMethodPhrases();
		return $this->_renderDocument($output);
	}

	protected function _getClassPhrase()
	{
		$tokens = array();

		if ( $this->isFinal() === true )
		{
			$tokens[] = 'final';
		}

		if ( $this->isAbstract() === true )
		{
			$tokens[] = 'abstract';
		}

		if ( $this->isInterface() === true )
		{
			$tokens[] = 'interface';
		}
		else
		{
			$tokens[] = 'class';
		}

		$tokens[] = $this->getName();

		return implode(' ', $tokens);
	}

	protected function _getParentPhrase()
	{
		$parent = $this->getParentClass();

		if ( $parent === false )
		{
			return '';
		}

		if ( method_exists($parent, 'getNamespaceName') === true )
		{
			$namespace = $parent->getNamespaceName();
		}
		else
		{
			$namespace = '';
		}

		if ( $namespace === '' )
		{
			$className = $parent->getName();
		}
		else
		{
			$className = $namespace.'\\'.$parent->getName();
		}


		return 'extends '.$className;
	}
	
	protected function _getInterfacePhrase()
	{
		$interfaces = $this->getInterfaceNames();

		$interfacePhrase = '';

		if ( count($interfaces) === 0 )
		{
			return $interfacePhrase;
		}

		if ( $this->isInterface() === true )
		{
			$interfacePhrase .= 'extends ';
		}
		else
		{
			$interfacePhrase .= 'implements ';
		}

		$interfacePhrase .= implode(', ', $interfaces);
		
		return $interfacePhrase;
	}
	
	protected function _getConstants()
	{
		$constantPhrases = array();
		$constants = $this->getConstants();

		foreach ( $constants as $name => $value )
		{
			$value = $this->_renderValue($value);
			$constantPhrases[] = sprintf('const %s = %s;', $name, $value);
		}

		return $constantPhrases;
	}

	protected function _getPropertyPhrases()
	{
		$properties = $this->getProperties();
		$defaultValues = $this->getDefaultProperties();
		$categories = array();

		foreach ( $properties as $property )
		{
			$tokens = array();

			if ( $property->isPublic() === true )
			{
				$tokens[] = 'public';
				$category = '1';
			}
			elseif ( $property->isProtected() === true )
			{
				$tokens[] = 'protected';
				$category = '2';
			}
			elseif ( $property->isPrivate() === true )
			{
				$tokens[] = 'private';
				$category = '3';
			}
		
			if ( $property->isStatic() === true )
			{
				$tokens[] = 'static';
				$category .= '1';
			}
			else
			{
				$category .= '2';
			}
			
			$name = $property->getName();
			$value = $this->_renderValue($defaultValues[$name]);
			
			$tokens[] = '$'.$name;
			$tokens[] = '=';
			$tokens[] = $value.';';

			if ( isset($categories[$category]) === false )
			{
				$categories[$category] = array();
			}

			$categories[$category][] = implode(' ', $tokens);
		}

		ksort($categories);

		$propertyPhrases = array();

		foreach ( $categories as $category )
		{
			$propertyPhrases = array_merge($propertyPhrases, $category);
		}

		return $propertyPhrases;
	}

	protected function _getMethodPhrases()
	{
		$methods = $this->getMethods();
		$categories = array(
			'self' => array(),
			'parent' => array(),
		);
		$thisClass = $this->getName();

		foreach ( $methods as $method )
		{
			$tokens = array();
			$category = '1';

			$declaringClass = $method->getDeclaringClass()->getName();

			if ( $thisClass === $declaringClass )
			{
				$class = 'self';
			}
			else
			{
				$class = 'parent';
			}

			if ( $method->isFinal() === true )
			{
				$tokens[] = 'final';
			}
			
			if ( $method->isAbstract() === true )
			{
				$tokens[] = 'abstract';
			}

			if ( $method->isPublic() === true )
			{
				$tokens[] = 'public';
				$category .= '1';
			}
			elseif ( $method->isProtected() === true )
			{
				$tokens[] = 'protected';
				$category .= '2';
			}
			elseif ( $method->isPrivate() === true )
			{
				$tokens[] = 'private';
				$category .= '3';
			}
		
			if ( $method->isStatic() === true )
			{
				$tokens[] = 'static';
			}

			$tokens[] = 'function';

			$name = $method->getName();

			if ( strpos($name, '__') === 0 )
			{
				$category[0] = '0'; // magic method comes first.
			}

			$parameters = $this->_getParameterPhrases($method);

			if ( $parameters )
			{
				$tokens[] = $declaringClass.'::'.$name.'( '.$parameters.' );';
			}
			else
			{
				$tokens[] = $declaringClass.'::'.$name.'( void );';
			}

			if ( isset($categories[$class][$category]) === false )
			{
				$categories[$class][$category] = array();
			}

			$categories[$class][$category][] = implode(' ', $tokens);
		}

		ksort($categories);

		$methodPhrases = array(
			'self' => array(),
			'parent' => array(),
		);

		foreach ( $categories['self'] as $category )
		{
			$methodPhrases['self'] = array_merge($methodPhrases['self'], $category);
		}

		foreach ( $categories['parent'] as $category )
		{
			$methodPhrases['parent'] = array_merge($methodPhrases['parent'], $category);
		}

		return $methodPhrases;
	}

	protected static function _getParameterPhrases(ReflectionMethod $method)
	{
		$parameterPhrases = array();
		$parameters = $method->getParameters();

		foreach ( $parameters as $parameter )
		{
			$tokens = array();

			if ( $parameter->isArray() === true )
			{
				$tokens[] = 'array';
			}

			if ( $parameter->isPassedByReference() === true )
			{
				$tokens[] = '&';
			}
			
			$tokens[] = '$'.$parameter->getName();

			if ( $parameter->isOptional() === true )
			{
				$value = $parameter->getDefaultValue();
				$value = self::_renderValue($value);
				$tokens[] = '=';
				$tokens[] = $value;
			}
			
			$parameterPhrases[] = implode(' ', $tokens);
		}

		return implode(', ', $parameterPhrases);
	}

	protected static function _renderValue($value)
	{
		if ( is_array($value) === true )
		{
			if ( count($value) > 0 )
			{
				$value = 'array(#'.count($value).')';
			}
			else
			{
				$value = 'array()';
			}
		}
		else
		{
			$value = var_export($value, true);
		}

		return $value;
	}

	protected static function _renderDocument(array $params)
	{
		extract($params);
		$lines = array();
		$indent = '    ';

		if ( $filename )
		{
			$lines[] = "// defined in file: $filename";
		}

		$firstLine = $class;

		if ( $parent )
		{
			$firstLine .= ' '.$parent;
		}

		if ( $interface )
		{
			$firstLine .= ' '.$interface;
		}

		$lines[] = $firstLine.' {';

		if ( count($constants) > 0 )
		{
			$lines[] = '';
			$lines[] = $indent.'/* 定数 */';

			foreach ( $constants as $constant )
			{
				$lines[] = $indent.$constant;
			}
		}

		if ( count($properties) > 0 )
		{
			$lines[] = '';
			$lines[] = $indent.'/* プロパティ */';

			foreach ( $properties as $property )
			{
				$lines[] = $indent.$property;
			}
		}

		if ( count($methods['self']) > 0 )
		{
			$lines[] = '';
			$lines[] = $indent.'/* メソッド */';

			foreach ( $methods['self'] as $method )
			{
				$lines[] = $indent.$method;
			}
		}

		if ( count($methods['parent']) > 0 )
		{
			$lines[] = '';
			$lines[] = $indent.'/* 継承したメソッド */';

			foreach ( $methods['parent'] as $method )
			{
				$lines[] = $indent.$method;
			}
		}

		$lines[] = '}';
	
		$lines = implode("\n", $lines);
		return $lines;
	}
}
