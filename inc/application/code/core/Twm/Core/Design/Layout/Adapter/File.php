<?php

class Twm_Core_Design_Layout_Adapter_File extends Twm_Core_Design_Layout_Adapter_Abstract {

	protected $_layoutdirectory = "layouts";

	function __construct($options) {
		foreach ($options as $key => $value) {
			switch (strtolower($key)) {
				case 'layoutdirectoryname':
					$this->_layoutdirectory = $value;
					break;
				default:
					break;
			}
		}
	}

	public function getLayoutConfig($section="") {
		$Package = Twm::getDesign()->getPackage();
		$paths = $Package->getBasePaths();

		$config = array();
		$loaded = array();
		$ds = DIRECTORY_SEPARATOR;

		/*
		 * read all layout xml files to arrays
		 */
		foreach ($paths as $path) {
			$path = $path . $ds . $this->_layoutdirectory ;
			if (!file_exists($path)) continue;
			
			$Dir = new DirectoryIterator($path);
			foreach ($Dir as $fileInfo) {
				if ($fileInfo->isFile()) {
					$filename = $fileInfo->getFilename();
					$module = explode('.', $filename);
					array_pop($module);
					$module = implode('.', $module);
					if (!isset($loaded[$module])) {
						$config[$module] = $this->_loadLayoutConfig($path . $ds . $filename);
					}
				}
			}
		}

		/*
		 * merge all arrays to one array
		 */
		$mergedConfig = array();
		foreach ($config as $module => $config) {
			$mergedConfig = array_merge_recursive($mergedConfig, $config);
		}

		/*
		 * merge default section with the requested section
		 */
		$sectionConfig = null;
		if (isset($mergedConfig[$section])) {
			if (empty($mergedConfig[$section])) {
				$mergedConfig[$section] = array();
			}
			$mergedConfig = array_merge_recursive($mergedConfig['default'], $mergedConfig[$section]);
		}

		return $mergedConfig;
	}

	protected function _loadLayoutConfig($file) {

		$config = new Twm_Core_Design_Layout_Reader_Xml($file);
		return $config->toArray();
	}

}