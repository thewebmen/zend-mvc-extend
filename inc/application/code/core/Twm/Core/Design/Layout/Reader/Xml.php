<?php

class Twm_Core_Design_Layout_Reader_Xml extends Zend_Config_Xml {

	protected function _toArray(SimpleXMLElement $xmlObject) {
		$config = array();
		$nsAttributes = $xmlObject->attributes(self::XML_NAMESPACE);

		// Search for parent node values
		if (count($xmlObject->attributes()) > 0) {
			foreach ($xmlObject->attributes() as $key => $value) {
				if ($key === 'extends') {
					continue;
				}

				$value = (string) $value;

				if (array_key_exists($key, $config)) {
					if (!is_array($config[$key])) {
						$config[$key] = array($config[$key]);
					}

					$config[$key][] = $value;
				} else {
					$config[$key] = $value;
				}
			}
		}

		// Search for local 'const' nodes and replace them
		if (count($xmlObject->children(self::XML_NAMESPACE)) > 0) {
			if (count($xmlObject->children()) > 0) {
				require_once 'Zend/Config/Exception.php';
				throw new Zend_Config_Exception("A node with a 'const' childnode may not have any other children");
			}

			$dom = dom_import_simplexml($xmlObject);
			$namespaceChildNodes = array();

			// We have to store them in an array, as replacing nodes will
			// confuse the DOMNodeList later
			foreach ($dom->childNodes as $node) {
				if ($node instanceof DOMElement && $node->namespaceURI === self::XML_NAMESPACE) {
					$namespaceChildNodes[] = $node;
				}
			}

			foreach ($namespaceChildNodes as $node) {
				switch ($node->localName) {
					case 'const':
						if (!$node->hasAttributeNS(self::XML_NAMESPACE, 'name')) {
							require_once 'Zend/Config/Exception.php';
							throw new Zend_Config_Exception("Misssing 'name' attribute in 'const' node");
						}

						$constantName = $node->getAttributeNS(self::XML_NAMESPACE, 'name');

						if (!defined($constantName)) {
							require_once 'Zend/Config/Exception.php';
							throw new Zend_Config_Exception("Constant with name '$constantName' was not defined");
						}

						$constantValue = constant($constantName);

						$dom->replaceChild($dom->ownerDocument->createTextNode($constantValue), $node);
						break;

					default:
						require_once 'Zend/Config/Exception.php';
						throw new Zend_Config_Exception("Unknown node with name '$node->localName' found");
				}
			}

			return (string) simplexml_import_dom($dom);
		}

		// Search for children
		if (count($xmlObject->children()) > 0) {
			foreach ($xmlObject->children() as $key => $value) {
				if (count($value->children()) > 0 || count($value->children(self::XML_NAMESPACE)) > 0) {
					$value = $this->_toArray($value);
				} else if (count($value->attributes()) > 0) {
					$attributes = $value->attributes();
					if (isset($attributes['value'])) {
						$value = (string) $attributes['value'];
					} else {
						$value = $this->_toArray($value);
					}
				} else {
					$value = (string) $value;
				}

				if (array_key_exists($key, $config)) {
					if (!is_array($config[$key]) || !array_key_exists(0, $config[$key])) {
						$config[$key] = array($config[$key]);
					}

					$config[$key][] = $value;
				} else {
					// twm fix. changed 
					// $config[$key] = $value;
					$config[$key][] = $value;
				}
			}
		} else if (!isset($xmlObject['extends']) && !isset($nsAttributes['extends']) && (count($config) === 0)) {
			// Object has no children nor attributes and doesn't use the extends
			// attribute: it's a string
			$config = (string) $xmlObject;
		}

		return $config;
	}

}