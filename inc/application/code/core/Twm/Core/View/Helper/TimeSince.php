<?php
/**
 * View helper for date captions like "1 month ago"
 * @author Michiel
 */
class Twm_Core_View_Helper_TimeSince extends Zend_View_Helper_Abstract {

	/**
	 * The list of translated units
	 * @var array
	 */
	protected $units;
	
	/**
	 * The time fragments
	 * @var array
	 */
	protected $fragments = array(
		31536000 => 'year', /* 60 * 60 * 24 * 365 */
		2592000 => 'month', /* 60 * 60 * 24 * 30 */
		604800 => 'week', /* 60 * 60 * 24 * 7 */
		86400 => 'day', /* 60 * 60 * 24 */
		3600 => 'hour', /* 60 * 60 */
		60 => 'minute',
		1 => 'second'
	);
	
	public function __construct() {
		$this->units = Zend_Locale::getTranslationList('Unit');
	}
	
	/**
	 * Get the localized time caption since $time
	 * @param int $time
	 * @param int $from
	 * @return string 
	 */
	public function timeSince($time, $from = null) {
		if ($from == null) {
			$from = time();
		} $time = $from - $time;

		foreach($this->fragments as $seconds => $name){
			if (($count = floor($time / $seconds)) != 0) {
				break;
			}
		}
		
		if($count == 1){
			return str_replace('{0}', 1, $this->units[$name]['one']);
		}else{
			return str_replace('{0}', $count, $this->units[$name]['other']);
		}
	}

}