<?php


/**
 * Class for DNS based ENUM lookups according to RFC3761.
 *
 * PHP version 5.
 * 
 * Usage:
 * 	1.) RFC3761::setZones()    (optional)
 *  2.) RFC3761::query()	   (required)
 *	3.) RFC3761::getResult() or RFC3761::getHTML() or RFC3761::getJSON() (required)
 *
 *	Public available helper functions:
 *		RFC3761::isNum(), RFC3761::num2RFC3966(),
 *		RFC3761::num2E164(), RFC3761::E164toENUM()
 *
 *	@author   Rene Bartsch <rene@bartschnet.de>
 *	@license  http://www.gnu.org/licenses/translations.html, GNU/GPL Version 3
 **/
class RFC3761 {


	/**
	 * Array with ENUM zones to query.
	 * 
	 *  @var	array of strings
	 *  @access	private
	 */
	private $zones = array('e164.arpa', 'e164.org');
	
	
	/**
	 * Two-dimensional array of NAPTR records.
	 * 
	 * First  dimension: indexed     array, Key:	index		type: integer	value: array with one NAPTR record.
	 * Second dimension: associative array, Key:	'order'		type: string	value: NAPTR order.
	 *												'pref'		type: string	value: NAPTR preference.
	 *												'services'	type: string	value: NAPTR services.
	 *												'regex'		type: string	value: expanded NAPTR posix regular expression extended.
	 *												'zone'		type: string	value: ENUM zone.
	 *  @var 	array
	 *  @access	private
	 */
	private $naptr = NULL;

	
	/**
	 * Sets the ENUM zones to query. 
	 *
	 *  @param	$zones	Array of strings	Array with zone TLDs.
	 *  @return			Boolean				Returns "true" on success, "false" on error.
	 *  @access	public
	 */
	function setZones($zones) {
		$this->zones = $zones;
		return ($this->zones === $zones);
	}

	
	/**
	 * Checks if string is a phone number. 
	 *
	 *  @param	$num	String	String to check.
	 *  @return			Boolean	Returns "true" if phone number, "false" if not.
	 *  @access	public
	 */
	function isNum($num) {
			
		// Must only contain decimal digits, '+', '-', '(', ')', '/', ' '
		if(0 != preg_match_all('/[^0-9\+\-\(\)\/ ]/S', $num, $null)) {
			return(false);
		}
		
		// Minimum of eight and maximum of 18 decimal digits
		$cdigits = preg_match_all('/[0-9]/S', $num, $null);
		if(($cdigits < 8) || ($cdigits > 18)) {
			return(false);
		}
		return(true);
	}

	
	/**
	 * Converts phone number to IETF RFC3966 formated URI.
	 * 
	 *  @param	$cp		String	Country prefix.
	 *  @param	$num	String	Phone number in local format.
	 *  @return			String	Returns IETF RFC 3966 formated URI.
	 *  @access	public
	 */
	function num2RFC3966($cp, $num) {
				
		// Replace '(0)' and all non-numerics by '-'
		$num = preg_replace('/(\(0\))|([^0-9])/S', '-', $num);
		
		// Replace multiple '-' by one '-'
		$num = preg_replace('/-+/S', '-', $num);
		
		// Replace leading '00' and leading '-' with '+'
		$num = preg_replace('/(^-0-0-)|(^-0-0)|(^0-0-)|(^0-0)|(^-00-)|(^-00)|(^00-)|(^00)|(^-)/S', '', $num);

		// Replace leading '0' with country prefix
		$num = preg_replace('/(^-0-)|(^-0)|(^0-)|(^0)/S', $cp.'-', $num);
		
		// Add protocol ID and international prefix
		$num = 'tel:+'.$num;
		
		// Return value
		return ($num);
	}
	
	
	/**
	 * Converts phone number from RFC3966 to ITU E.164 format.
	 * 
	 *  @param	$RFC3966	String	Phone number in RFC3966 format.
	 *  @return				String	Returns phone number in ITU E.164 format.
	 *  @access	public
	 */
	function RFC3966ToE164($RFC3966) {
				
		// Remove all non-numerics
		return(preg_replace('/[^0-9]/S', '', $RFC3966));
	} 

		
	/**
	 * Converts E.164 formatted phone number to ENUM domain.
	 * 
	 *  @param	$E164	String	Phone number in E.164 format.
	 *  @param	$zone	String	Top level domain of ENUM registry (e.g. 'e164.arpa').
	 *  @return			String	Returns ENUM domain.
	 *  @access	public
	 */
	function E164toENUM($E164, $zone) {
		$domain = '';
		
		// Invert string and add dots between each digit
		for($i=strlen($E164)-1; $i>-1; $i--) {
    		$domain .= substr($E164, $i, 1).'.';
		}
		
		// Add ENUM zone
		$domain = $domain.strtolower($zone);
		
		// Return value
		return $domain;
	}

	
	/**
	 * Queries a domain for NAPTR records of type URI.
	 * 
	 *  @param	$domain	String				Domain to query.
	 *  @return			Boolean				"true" on success, false if number not registered in ENUM zones or error.
	 *  @access	private
	 */
	private function getNAPTRs($domain) {
		
		// Check if domain is set to prevent waiting for DNS timeout
		if(empty($domain)) return false;
		
		// Query DNS and Loop through NAPTR records
		$result = false;
		foreach(dns_get_record($domain, DNS_NAPTR) as $NAPTR) {
			
			// Check Flags field
			switch (strtoupper($NAPTR['flags'])) {
				
				// A-record
				case 'A':
					foreach(dns_get_record($NAPTR['replacement'], DNS_A) as $A) {
						$res['result'] = $A['A'];
						$this->naptr[] = array_merge($NAPTR, $res);
						$result = true;
					}
					foreach(dns_get_record($NAPTR['replacement'], DNS_AAAA) as $A) {
						$res['result'] = $A['A'];
						$this->naptr[] = array_merge($NAPTR, $res);
												$result = true;
					}
					foreach(dns_get_record($NAPTR['replacement'], DNS_A6) as $A) {
						$res['result'] = $A['A'];
						$this->naptr[] = array_merge($NAPTR, $res);
						$result = true;
					}
					break;
				
				// SRV-record
				case 'S':
					foreach(dns_get_record($NAPTR['replacement'], DNS_SRV) as $SRV) {
						$res['result'] = $SRV['target'].':'.$SRV['port'];
						$this->naptr[] = array_merge($NAPTR, $res);
						$result = true;
					}
					break;
				
				// Protocol depended algorithm
				case 'P':
				// URI
				case 'U':
					// Parse NAPTR regular expression
					$regex = explode(substr($NAPTR['regex'], 0, 1), $NAPTR['regex']);
					$regex[0] = $NAPTR['host'];
					
					// Use case-insensitive regular expression if 'i'-flag is set
					if(preg_match('/i/S', $regex[3])) {
						$res['result'] = eregi_replace($regex[1], $regex[2], $regex[0]);
					} else {
						$res['result'] =  ereg_replace($regex[1], $regex[2], $regex[0]);
					}
					$this->naptr[] = array_merge($NAPTR, $res);
					$result = true;
					break;

				// Unknown flags
				default:
					break;
			}
		}

		// Return true
		return($result);
	}
	
	
	/**
	 * Queries domain/phone number for NAPTR records.
	 * 
	 *  @param	$URI	String	Domain with NAPTR records (e.g. .tel) or phone number in RFC3966 format.
	 *  @return			Boolean	"true" on success, false if domain/number not registered or error.
	 *  @access	public
	 */
	function query($URI) {
		
		// Clear array for NAPTR records
		unset($this->naptr);
		
		// Prepare return value
		$ret = false;
		
		// Check if RFC3966 formatted phone number or domain
		if(!preg_match('/tel:\+[0-9|\-]*/S', $URI)) {
			
			// Query domain for NAPTR records
			if($this->getNAPTRs($URI)) {
				$ret = true;
			}
		} else {
			
			// Query ENUM zones for NAPTR records
			foreach($this->zones as $zone) {
				if($this->getNAPTRs($this->E164toENUM($this->RFC3966ToE164($URI), $zone))) {
					$ret = true;
				}
			}
		}

		// Add dummy record if query was not successful
		if(empty($this->naptr)) {
			$this->naptr[0] = array('host'	=> 'None',
									'type' => NAPTR,
									'order' => 0,
									'pref' => 0,
									'flags' => 'U',
									'services' => 'E2U+voice:tel',
									'regex' => "!^.*$!$URI!",
									'replacement' => '',
									'class' => 'IN',
									'ttl' => 0,
									'result' => $URI
							  );
		} else {
						
			// Sort array
			array_multisort($this->naptr);
		}

		// Return value
		return $ret;
	}

	
	/**
	 * Returns the 2-dimensional sorted NAPTR record array. 
	 *
	 *  @return	Array	2-dimensional sorted NAPTR record array of query.
	 *  @see			Variable $this->naptr.		
	 *  @access	public
	 */
	function getResult() {
		return $this->naptr;
	}

	
	/**
	 * Returns the 2-dimensional sorted NAPTR record array JSON formatted. 
	 *
	 *  @return	String	2-dimensional sorted NAPTR record array of query in JSON format.
	 *  @see			Variable $this->naptr.		
	 *  @access	public
	 */
	function getJSON() {
		return json_encode($this->naptr);
	}
	

	/**
	 * Returns a string with a HMTL tree of the NAPTR records of a ENUM number.
	 * 
	 *  @param	$CSStr			String				CSS identifier of HTML tree.
	 *  @param	$CSSul			String				CSS identifier of HTML list.
	 *  @param	$CSSli			String				CSS identifier of HTML list elements.
	 *  @param  $showMultiple	Boolean				Show records which are in multiple zones.
	 *  @param	$showZones		Boolean 			Show ENUM  zones if "true".	
	 *  @param	$showServices	Boolean 			Show NAPTR services if "true".
	 *  @param	$showXservices	Boolean 			Show NAPTR experimental services if "true".
	 *  @param	$selectServices	Array of strings	Selects which service-types to show. Array with IANA service types IN UPPER CASE as keys and custom replacements as string values.
	 *  @access	public
	 */
	function getHTML($CSStr='', $CSSul='', $CSSli='', $showMultiple=true, $showZones=true, $showServices=true, $showXservices=true, $selectServices=array()) {

		// Get copy of NAPTR record array, remove unwanted service types
		// and convert to five-dimensional array
		$arr1 = array();
		$tmpmulti = array();
		foreach($this->naptr as $record) {
			
			// Do not add experimental records if $showXservices is false
			if(!$showXservices && preg_match('/^E2U\+X-/iS', $record['services'])) {
				continue;
			}
			
			// Do not add records in multiple zones if $showMultiple is false
			if(!$showMultiple && in_array($record['result'], $tmpmulti)) {
				continue;
			} else {
				$tmpmulti[] = $record['result'];
			}
			
			// Do not add unwanted services if $selectServices is set/replace service types with custom names 					
			if(!empty($selectServices)) {
				$pattern = strtoupper($record['services']);
				if(in_array($pattern, array_keys($selectServices))) {
					if(!empty($selectServices[$pattern])) {
						$record['services'] = $selectServices[$pattern];
					}
				} else {
					continue;
				}
			}

			// Add record with changed sort order to $temp and remove 'E2U+'
			$arr1[$record['order']][$record['pref']]
			[preg_replace('/^(E2U\+)|(:.*)/iS', '', $record['services'])]
			[strtolower($record['host'])][] = $record['result']; 
		}

		
		// Create HTML tree by using HTML lists
		$tro = "<div class=\"".$CSStr."\">";
		$trc = "</div>";
		$ulo = "<ul  class=\"".$CSSul  ."\">";
		$ulc = "</ul>";
		$lio = "<li  class=\"".$CSSli  ."\">";
		$lic = "</li>";

		$html = $ulo;
		$firstorder = false;
		foreach(array_keys($arr1) as $order){
			if($firstorder) {
				break;
			} else {
				$firstorder = true;
			}
			foreach(array_keys($arr1[$order]) as $preference){
				foreach(array_keys($arr1[$order][$preference]) as $service){
					
					// Show service types if $showServices is set
					if($showServices) {
						$html .= $lio.ucwords($service).$ulo;
					}
					
					foreach(array_keys($arr1[$order][$preference][$service]) as $zone){

						// Show zones if $showZones is set
						if($showZones) {
							$html .= $lio.$zone.$ulo;
						}
						foreach($arr1[$order][$preference][$service][$zone] as $value){
							
							// Create link
							$html .= $lio."<a href=\"".$value."\" target=\"_blank\">".preg_replace('/^.*[:|:,|\/\/]/S', '', $value)."</a>".$lic;
						}

						// Show zones if $showZones is set
						if($showZones) {
							$html .= $ulc.$lic;
						}
					}
					
					// Show service types if $showServices is set
					if($showServices) {
						$html .= $ulc.$lic;
					}
				}
			}
		}
		$html .= $ulc;
		return $tro.$html.$trc;
	}
}

?>
