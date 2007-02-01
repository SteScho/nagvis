<?php
/**
 * This Class handles the NagVis configuration file
 *
 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
 */
class GlobalMapCfg {
	var $MAINCFG;
	
	var $name;
	var $image;
	var $mapConfig;
	
	// Array for config validation
	var $validConfig;
	
	/**
	 * Class Constructor
	 *
	 * @param	GlobalMainCfg	$MAINCFG	
	 * @param	String			$name		Name of the map
	 * @author	Lars Michelsen <larsi@nagios-wiki.de>
	 */
	function GlobalMapCfg(&$MAINCFG,$name='') {
		$this->MAINCFG = &$MAINCFG;
		$this->name	= $name;
		
		$this->validConfig = Array(
			'global' => Array('type' => Array('must' => 0),
							'allowed_for_config' => Array('must' => 1),
							'allowed_user' => Array('must' => 1),
							'map_image' => Array('must' => 1),
							'backend_id' => Array('must' => 0,
												'default' => $this->MAINCFG->getValue('defaults', 'backend')),
							'recognize_services' => Array('must' => 0,
												'default' => $this->MAINCFG->getValue('defaults', 'recognizeservices')),
							'only_hard_states' => Array('must' => 0,
												'default' => $this->MAINCFG->getValue('defaults', 'onlyhardstates')),
							'iconset' => Array('must' => 0,
												'default' => $this->MAINCFG->getValue('defaults', 'icons')),
							'background_color' => Array('must' => 0,
												'default' => $this->MAINCFG->getValue('defaults', 'backgroundcolor'))),
			'host' => Array('type' => Array('must' => 0),
							'backend_id' => Array('must' => 0,
												'default' => ''),
							'host_name' => Array('must' => 1),
							'x' => Array('must' => 1),
							'y' => Array('must' => 1),
							'z' => Array('must' => 0,
												'default' => 1),
							'recognize_services' => Array('must' => 0,
												'default' => ''),
							'only_hard_states' => Array('must' => 0,
												'default' => ''),
							
							'iconset' => Array('must' => 0,
												'default' => ''),
							'hover_url' => Array('must' => 0),
							'line_type' => Array('must' => 0),
							'url' => Array('must' => 0)),
			'hostgroup' => Array('type' => Array('must' => 0),
							'backend_id' => Array('must' => 0,
												'default' => ''),
							'hostgroup_name' => Array('must' => 1),
							'x' => Array('must' => 1),
							'y' => Array('must' => 1),
							'z' => Array('must' => 0,
												'default' => 1),
							'recognize_services' => Array('must' => 0,
												'default' => ''),
							'only_hard_states' => Array('must' => 0,
												'default' => ''),
							'iconset' => Array('must' => 0,
												'default' => ''),
							'hover_url' => Array('must' => 0),
							'line_type' => Array('must' => 0),
							'url' => Array('must' => 0)),
			'service' => Array('type' => Array('must' => 0),
							'backend_id' => Array('must' => 0,
												'default' => ''),
							'host_name' => Array('must' => 1),
							'service_description' => Array('must' => 1),
							'x' => Array('must' => 1),
							'y' => Array('must' => 1),
							'z' => Array('must' => 0,
												'default' => 1),
							'only_hard_states' => Array('must' => 0,
												'default' => ''),
							'iconset' => Array('must' => 0,
												'default' => ''),
							'hover_url' => Array('must' => 0),
							'line_type' => Array('must' => 0),
							'url' => Array('must' => 0)),
			'servicegroup' => Array('type' => Array('must' => 0),
							'backend_id' => Array('must' => 0,
												'default' => ''),
							'servicegroup_name' => Array('must' => 1),
							'x' => Array('must' => 1),
							'y' => Array('must' => 1),
							'z' => Array('must' => 0,
												'default' => 1),
							'only_hard_states' => Array('must' => 0,
												'default' => ''),
							'iconset' => Array('must' => 0,
												'default' => ''),
							'hover_url' => Array('must' => 0),
							'line_type' => Array('must' => 0),
							'url' => Array('must' => 0)),
			'map' => Array('type' => Array('must' => 0),
							'map_name' => Array('must' => 1),
							'x' => Array('must' => 1),
							'y' => Array('must' => 1),
							'z' => Array('must' => 0,
												'default' => 1),
							'only_hard_states' => Array('must' => 0,
												'default' => ''),
							'iconset' => Array('must' => 0,
												'default' => ''),
							'url' => Array('must' => 0),
							'hover_url' => Array('must' => 0)),
			'textbox' => Array('type' => Array('must' => 0),
							'text' => Array('must' => 1),
							'x' => Array('must' => 1),
							'y' => Array('must' => 1),
							'z' => Array('must' => 0,
												'default' => 1),
							'w' => Array('must' => 1),
							'background_color' => Array('must' => 0),
							'host_name' => Array('must' => 0)),
			'shape' => Array('type' => Array('must' => 0),
							'icon' => Array('must' => 1),
							'x' => Array('must' => 1),
							'y' => Array('must' => 1),
							'z' => Array('must' => 0,
												'default' => 1),
							'url' => Array('must' => 0),
							'hover_url' => Array('must' => 0)));
		
		
		$this->getMap();
	}
	
	/**
	 * Gets the default values for the objects 
	 *
	 * @author	Lars Michelsen <larsi@nagios-wiki.de>
	 */
	function getObjectDefaults() {
		$this->validConfig['host']['recognize_services']['default'] = $this->getValue('global', 0, 'recognize_services');
		$this->validConfig['host']['only_hard_states']['default'] = $this->getValue('global', 0, 'only_hard_states');
		$this->validConfig['host']['backend_id']['default'] = $this->getValue('global', 0, 'backend_id');
		$this->validConfig['host']['iconset']['default'] = $this->getValue('global', 0, 'iconset');
		$this->validConfig['hostgroup']['recognize_services']['default'] = $this->getValue('global', 0, 'recognize_services');
		$this->validConfig['hostgroup']['only_hard_states']['default'] = $this->getValue('global', 0, 'only_hard_states');
		$this->validConfig['hostgroup']['backend_id']['default'] = $this->getValue('global', 0, 'backend_id');
		$this->validConfig['hostgroup']['iconset']['default'] = $this->getValue('global', 0, 'iconset');
		$this->validConfig['service']['only_hard_states']['default'] = $this->getValue('global', 0, 'only_hard_states');
		$this->validConfig['service']['backend_id']['default'] = $this->getValue('global', 0, 'backend_id');
		$this->validConfig['service']['iconset']['default'] = $this->getValue('global', 0, 'iconset');
		$this->validConfig['servicegroup']['only_hard_states']['default'] = $this->getValue('global', 0, 'only_hard_states');
		$this->validConfig['servicegroup']['backend_id']['default'] = $this->getValue('global', 0, 'backend_id');
		$this->validConfig['servicegroup']['iconset']['default'] = $this->getValue('global', 0, 'iconset');
		$this->validConfig['map']['only_hard_states']['default'] = $this->getValue('global', 0, 'only_hard_states');
		$this->validConfig['map']['iconset']['default'] = $this->getValue('global', 0, 'iconset');
	}
	
	/**
	 * Reads which map should be displayed, primary use
	 * the map defined in the url, if there is no map
	 * in url, use first entry of "maps" defined in 
	 * the NagVis main config
	 *
	 * @author	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function getMap() {
		// if no map was given with parameter, search for a map
		if($this->name == '') {
			// only try to get a map, if we are not in wui
			if($this->MAINCFG->getRuntimeValue('wui') == 1) {
				$this->name = '';
			} else {
				$arr = explode(',',$this->MAINCFG->getValue('global', 'maps'));
				$this->name = $arr[0];
			}
		} else {
			// check the $this->name string for security reasons (its the ONLY value we get directly from external...)
			// Allow ONLY Characters, Numbers, - and _ inside the Name of a Map
			$this->name = preg_replace("/[^a-zA-Z0-9_-]/",'',$this->name);
		}
	}
	
	/**
	 * Reads which map image should be used
	 *
	 * @return	String	MapImage
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function getImage() {
		return $this->image = $this->getValue('global', 0, 'map_image');
	}
	
	/**
	 * Deletes the map image
	 *
	 * @param	Boolean	$printErr
	 * @return	Boolean	Is Check Successful?
	 * @author	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function deleteImage($printErr) {
		if($this->checkMapImageWriteable($printErr)) {
			if(unlink($this->MAINCFG->getValue('paths', 'map').$this->image)) {
				return TRUE;
			} else {
				// FIXME: Need an error message: "Image could not be deleted"
				return FALSE;
			}
		}
	}
	
	/**
	 * Creates a new Configfile
	 *
	 * @return	Boolean	Is Successful?
	 * @author Lars Michelsen <larsi@nagios-wiki.de>
     */
	function createMapConfig() {
		// does file exists?
		if(!$this->checkMapConfigReadable(0)) {
			if($this->MAINCFG->checkMapCfgFolderWriteable(1)) {
				// create empty file
				$fp = fopen($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.".cfg", "w");
				fclose($fp); 
				// set permissions
	  			chmod($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.".cfg",0666);
	  			
  				return TRUE;
  			} else {
  				return FALSE;
  			}
		} else {
			// file exists & is readable
			return FALSE;
		}
	}
	
	/**
	 * Deletes the map configfile
	 *
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function deleteMapConfig() {
		// is file writeable?
		if($this->checkMapConfigWriteable(0)) {
			if(unlink($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.'.cfg')) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Reads the map config file (copied from readFile->readNagVisCfg())
	 *
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function readMapConfig($onlyGlobal=0) {
		if($this->name != '') {
			if($this->checkMapConfigReadable(1)) {
				$this->mapConfig = Array();
				
				// read file in array
				$file = file($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.".cfg");
				$createArray = array("allowed_user","allowed_for_config");
				$l = 0;
				$a = 0;
						
				while (isset($file[$l]) && $file[$l] != "") {
					if(!ereg("^#",$file[$l]) && !ereg("^;",$file[$l])) {
						$defineCln = explode("{", $file[$l]);
						$define = explode(" ",$defineCln[0]);
						if (isset($define[1]) && array_key_exists(trim($define[1]),$this->validConfig)) {
							$l++;
							if(isset($this->mapConfig[$define[1]])) {
								$nrOfType = count($this->mapConfig[$define[1]]);
							} else {
								$nrOfType = 0;
							}
							$this->mapConfig[$define[1]][$nrOfType]['type'] = $define[1];
							while (isset($file[$l]) && trim($file[$l]) != "}") {
								$entry = explode("=",$file[$l], 2);
								
								if(isset($entry[1])) {
									if(in_array(trim($entry[0]),$createArray)) {
										$this->mapConfig[$define[1]][$nrOfType][trim($entry[0])] = explode(",",str_replace(' ','',trim($entry[1])));
									} else {
										$this->mapConfig[$define[1]][$nrOfType][trim($entry[0])] = trim($entry[1]);
									}
								}
								$l++;	
							}
						}
					}
					$l++;
				}
				
				/**
				 * The default values refer to global settings in the validConfig array - so they have to be 
				 * defined here and mustn't be defined in the array at creation.
				 * Cause of the default values should refer to the truely defined settings in global area they have to be read here.
				 */
				$this->getObjectDefaults();
				
				if($onlyGlobal == 1) {
					$this->filterGlobal();	
				}
				
				if($this->checkMapConfigIsValid(1)) {
					$this->getImage();
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				return FALSE;	
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Deletes all elements from the array, only global will be left
	 * Is needed in WUI to prevent config error warnings while loading the map credentials from
	 * global section of the map
	 *
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function filterGlobal() {
		foreach($this->mapConfig AS $key => $val) {
			if($key != 'global') {
				unset($this->mapConfig[$key]);
			}
		}
		
		if(count($this->mapConfig) == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Writes the element from array to the config file
	 *
	 * @param	String	$type	Type of the Element
	 * @param	Integer	$id		Id of the Element
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function writeElement($type,$id) {
		if($this->checkMapConfigExists(1) && $this->checkMapConfigReadable(1) && $this->checkMapConfigWriteable(1)) {
			// read file in array
			$file = file($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.".cfg");
			
			// number of lines in the file
			$l = 0;
			// number of elements of the given type
			$a = 0;
			// done?!
			$done = FALSE;
			while(isset($file[$l]) && $file[$l] != "" && $done == FALSE) {
				// ignore comments
				if(!ereg("^#",$file[$l]) && !ereg("^;",$file[$l])) {
					$defineCln = explode("{", $file[$l]);
					$define = explode(" ",$defineCln[0]);
					// select only elements of the given type
					if(isset($define[1]) && trim($define[1]) == $type) {
						// check if element exists
						if($a == $id) {
							// check if element is an array...
							if(is_array($this->mapConfig[$type][$a])) {
								// ...array: update!
								
								// choose first parameter line
								$l++;
								
								
								// loop parameters from array
								foreach($this->mapConfig[$type][$id] AS $key => $val) {
									// if key is not type
									if($key != 'type') {
										$cfgLines = 0;
										$cfgLine = '';
										$cfgLineNr = 0;
										// Parameter aus Datei durchlaufen
										while(isset($file[($l+$cfgLines)]) && trim($file[($l+$cfgLines)]) != '}') {
											$entry = explode("=",$file[$l+$cfgLines], 2);
											if($key == trim($entry[0])) {
												$cfgLineNr = $l+$cfgLines;
												if(is_array($val)) {
													$val = implode(",",$val);
												}
												$cfgLine = $key."=".$val."\n";
											}
											$cfgLines++;	
										}
										
										if($cfgLineNr != 0 && $val != '') {
											// if a parameter was found in file and value is not empty, replace line
											$file[$cfgLineNr] = $cfgLine;
										} elseif($cfgLineNr != 0 && $val == '') {
											// if a paremter is not in array or a value is empty, delete the line in the file
											unset($file[$cfgLineNr]);
										} elseif($cfgLineNr == 0 && $val != '') {
											// if a parameter is was not found in array and a value is not empty, create line
											if(is_array($val)) {
												$val = implode(",",$val);
											}
											$neu = $key."=".$val."\n";
											
											for($i = $l; $i < count($file);$i++) {
												$tmp = $file[$i];
												$file[$i] = $neu;
												$neu = $tmp;
											}
											$file[count($file)] = $neu;
										} elseif($cfgLineNr == 0 && $val == '') {
											// if a parameter is empty and a value is empty, do nothing
										}
									}
								}
								$l++;
							} else {
								// ...no array: delete!
								$cfgLines = 0;
								while(trim($file[($l+$cfgLines)]) != '}') {
									$cfgLines++;
								}
								$cfgLines++;
								
								for($i = $l; $i <= $l+$cfgLines;$i++) {
									unset($file[$i]);	
								}
							}
							
							$done = TRUE;
						}
						$a++;
					}
				}
				$l++;	
			}
			
			// reached end of file - couldn't find that element, create a new one...
			if($done == FALSE) {
				if($file[count($file)-1] != "\n") {
					$file[] = "\n";
				}
				$file[] = "define ".$type." {\n";
				foreach($this->mapConfig[$type][$id] AS $key => $val) {
					if(isset($val) && $val != '') {
						$file[] = $key."=".$val."\n";
					}
				}
				$file[] = "}\n";
				$file[] = "\n";
			}
			
			// open file for writing and replace it
		 	$fp = fopen($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.".cfg","w");
		 	fwrite($fp,implode("",$file));
		 	fclose($fp);
		 	
			return TRUE;
		} else {
			return FALSE;
		} 
	}
	
	/**
	 * Checks for existing map image file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function checkMapImageExists($printErr) {
		if($this->image != '') {
			if(file_exists($this->MAINCFG->getValue('paths', 'map').$this->image)) {
				return TRUE;
			} else {
				if($printErr == 1) {
					//Error Box
					$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
		            $FRONTEND->messageToUser('ERROR','backgroundNotExists','IMGPATH~'.$this->MAINCFG->getValue('paths', 'map').$this->image);
				}
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
    /**
	 * Checks for readable map image file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function checkMapImageReadable($printErr) {
		if($this->image != '') {
			if($this->checkMapImageExists($printErr) && is_readable($this->MAINCFG->getValue('paths', 'map').$this->image)) {
				return TRUE;
			} else {
				if($printErr == 1) {
					//Error Box
					$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
		            $FRONTEND->messageToUser('ERROR','backgroundNotReadable','IMGPATH~'.$this->MAINCFG->getValue('paths', 'map').$this->image);
				}
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
    /**
	 * Checks for writeable map image file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function checkMapImageWriteable($printErr) {
		if($this->image != '') {
			//FIXME: is_writable doesn't check write permissions
			if($this->checkMapImageExists($printErr) /*&& is_writable($this->MAINCFG->getValue('paths', 'map').$this->image)*/) {
				return TRUE;
			} else {
				if($printErr == 1) {
					//Error Box
					$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
		            $FRONTEND->messageToUser('ERROR','backgroundNotWriteable','IMGPATH~'.$this->MAINCFG->getValue('paths', 'map').$this->image);
				}
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Checks for existing config file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function checkMapConfigExists($printErr) {
		if($this->name != '') {
			if(file_exists($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.".cfg")) {
				return TRUE;
			} else {
				if($printErr == 1) {
					$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
		            $FRONTEND->messageToUser('ERROR','mapCfgNotExists','MAP~'.$this->MAINCFG->getValue('paths', 'mapcfg').$this->name.'.cfg');
				}
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Checks for readable config file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function checkMapConfigReadable($printErr) {
		if($this->name != '') {
			if($this->checkMapConfigExists($printErr) && is_readable($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.'.cfg')) {
				return TRUE;
			} else {
				if($printErr == 1) {
					$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
		            $FRONTEND->messageToUser('ERROR','mapCfgNotReadable','MAP='.$this->MAINCFG->getValue('paths', 'mapcfg').$this->name.'.cfg');
				}
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Checks for writeable map image file
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function checkMapConfigWriteable($printErr) {
		if($this->checkMapConfigExists($printErr) && is_writeable($this->MAINCFG->getValue('paths', 'mapcfg').$this->name.".cfg")) {
			return TRUE;
		} else {
			if($printErr == 1) {
				$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
	            $FRONTEND->messageToUser('ERROR','mapCfgNotWriteable','MAP~'.$this->MAINCFG->getValue('paths', 'mapcfg').$this->name.'.cfg');
			}
			return FALSE;
		}
	}
	
	/**
	 * Checks if the config file is valid
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function checkMapConfigIsValid($printErr) {
		// check given objects and attributes
		foreach($this->mapConfig AS $type => $elements) {
			if(array_key_exists($type,$this->validConfig)) {
				foreach($elements AS $id => $element) {
					// loop validConfig for checking: => missing "must" atributes
					foreach($this->validConfig[$type] AS $key => $val) {
						if((isset($val['must']) && $val['must'] == '1')) {
							// value is "must"
							if(!isset($element[$key]) || $element[$key] == '') {
								// a "must" value is missing or empty
								$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
							    $FRONTEND->messageToUser('ERROR','mustValueNotSet','MAPNAME~'.$this->name.',ATTRIBUTE~'.$key.',TYPE~'.$type.',ID~'.$id);
							}
						}
					}
					
					// loop given elements for checking: => all given atributes valid
					foreach($element AS $key => $val) {
						// check for valid atributes - TODO: check valid values
						if(!array_key_exists($key,$this->validConfig[$type])) {
							// unknown atribute
							if($printErr == 1) {
								$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
					            $FRONTEND->messageToUser('ERROR','unknownAttribute','MAPNAME~'.$this->name.',ATTRIBUTE~'.$key.',TYPE~'.$type);
							}
							return FALSE;
						}
					}
				}	
			} else {
				// unknown type
				if($printErr == 1) {
					$FRONTEND = new GlobalPage($this->MAINCFG,Array('languageRoot'=>'global:global'));
		            $FRONTEND->messageToUser('ERROR','unknownObject','TYPE~'.$type);
				}
				return FALSE;
			}
		}
		return TRUE;
	}
	
    /**
	 * Gets all definitions of type $type
	 *
	 * @param	String	$type
	 * @return	Array	All elements of this type
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function getDefinitions($type) {
		if(isset($this->mapConfig[$type]) && count($this->mapConfig[$type]) > 0) {
			return $this->mapConfig[$type];
		} else {
			return Array();
		}
	}
	
    /**
	 * Deletes an element of the specified type to the config array
	 *
	 * @param	String	$type
	 * @param	Integer	$id
	 * @return	Boolean	TRUE
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function deleteElement($type,$id) {
		$this->mapConfig[$type][$id] = '';
		
		return TRUE;
	}
	
    /**
	 * Adds an element of the specified type to the config array
	 *
	 * @param	String	$type
	 * @param	Array	$properties
	 * @return	Integer	Id of the Element
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function addElement($type,$properties) {
		//$elementId = (count($this->getDefinitions($type))+1);
		$this->mapConfig[$type][] = $properties;
		
		return count($this->mapConfig[$type])-1;
	}
	
    /**
	 * Sets a config value in the array
	 *
	 * @param	String	$type
	 * @param	Integer	$id
	 * @param	String	$key
	 * @param	String	$value
	 * @return	Boolean	TRUE
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function setValue($type, $id, $key, $value) {
       $this->mapConfig[$type][$id][$key] = $value;
       return TRUE;
	}
	
    /**
	 * Gets a config value in the array
	 *
	 * @param	String	$type
	 * @param	Integer	$id
	 * @param	String	$key
	 * @param	Boolean	$ignoreDefault
	 * @return	String	Value
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function getValue($type, $id, $key, $ignoreDefault=FALSE) {
		if(isset($this->mapConfig[$type][$id]) && is_array($this->mapConfig[$type][$id]) && array_key_exists($key,$this->mapConfig[$type][$id]) && $this->mapConfig[$type][$id][$key] != '') {
			return $this->mapConfig[$type][$id][$key];
		} elseif(!$ignoreDefault) {
			if(isset($this->validConfig[$type][$key]['default'])) {
				return $this->validConfig[$type][$key]['default'];
			}
		}
	}
	
    /**
	 * Gets the mapName
	 *
	 * @return	String	MapName
	 * @author 	Lars Michelsen <larsi@nagios-wiki.de>
     */
	function getName() {
		return $this->name;	
	}
	
}
?>
