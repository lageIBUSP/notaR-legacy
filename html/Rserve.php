<?php
/**
 * Rserve client for PHP
 * Supports Rserve protocol 0103 only (used by Rserve 0.5 and higher)
 * $Revision$
 * @author Clément TURBELIN
 * Developped using code from Simple Rserve client for PHP by Simon Urbanek Licensed under GPL v2 or at your option v3
 * $Id: Connection.php 11 2011-07-06 10:13:00Z clement.turbelin@gmail.com $
 */

/**
 * Read byte from a binary packed format @see Rserve protocol
 * @param string $buf buffer
 * @param int $o offset
 */
function int8($buf, $o = 0) {
	return ord($buf[$o]);
}

/**
 * Read an integer from a 24 bits binary packed format @see Rserve protocol
 * @param string $buf buffer
 * @param int $o offset
 */
function int24($buf, $o = 0) {
	return (ord($buf[$o]) | (ord($buf[$o + 1]) << 8) | (ord($buf[$o + 2]) << 16));
}

/**
 * Read an integer from a 32 bits binary packed format @see Rserve protocol
 * @param string $buf buffer
 * @param int $o offset
 */
function int32($buf, $o=0) {
	return (ord($buf[$o]) | (ord($buf[$o + 1]) << 8) | (ord($buf[$o + 2]) << 16) | (ord($buf[$o + 3]) << 24));
}

/**
 * One Byte
 * @param $i
 */
function mkint8($i) {
	return chr($i & 255);
}

/**
 * Make a binary representation of integer using 32 bits
 * @param int $i
 * @return string
 */
function mkint32($i) {
	$r = chr($i & 255); 
	$i >>= 8; 
	$r .= chr($i & 255); 
	$i >>=8; 
	$r .= chr($i & 255); 
	$i >>=8; 
	$r .= chr($i & 255);
	return $r;
}

/*
 * Create a 24 bit integer
 * @return string binary representation of the int using 24 bits 
 */
function mkint24($i) {
	$r = chr($i & 255); 
	$i >>= 8; 
	$r .= chr($i & 255); 
	$i >>=8; 
	$r .= chr($i & 255);
	return $r;
}

/**
 * Create a binary representation of float to 64bits
 * TODO: works only for intel endianess, should be adapted for no big endian proc
 * @param double $v 
 */
function mkfloat64($v) {
	return pack('d', $v);
}

/**
 * 64bit integer to Float
 * @param $buf
 * @param $o
 */
function flt64($buf, $o = 0) {
	$ss = substr($buf, $o, 8);
	if (Rserve_Connection::$machine_is_bigendian) {
		for ($k = 0; $k < 7; $k++) { 
			$ss[7 - $k] = $buf[$o + $k];
		}	
	} 
	$r = unpack('d', substr($buf, $o, 8)); 
	return $r[1]; 
}

/**
 * Create a packet for QAP1 message
 * @param int $cmd command identifier
 * @param string $string contents of the message
 */
function _rserve_make_packet($cmd, $string) {
	$n = strlen($string) + 1; 
	$string .= chr(0);
	while (($n & 3) != 0) { 
		$string .= chr(1); 
		$n++; 
	}
	// [0]  (int) command
  	// [4]  (int) length of the message (bits 0-31)
  	// [8]  (int) offset of the data part
  	// [12] (int) length of the message (bits 32-63)
	return mkint32($cmd) . mkint32($n + 4) . mkint32(0) . mkint32(0) . chr(4) . mkint24($n) . $string;
}

/**
 * Make a data packet
 * @param unknown_type $type
 * @param unknown_type $string NULL terminated string
 */
function _rserve_make_data($type, $string) {
	$s = '';
	$len = strlen($string); // Length of the binary string
	$is_large = $len > 0xfffff0;
	$pad = 0; // Number of padding needed
	while( ($len & 3) != 0) { 
		// ensure the data packet size is divisible by 4
		++$len;
		++$pad;
	} 
	$s .= chr($type & 255) | ($is_large ? Rserve_Connection::DT_LARGE : 0);
	$s .= chr($len & 255);
	$s .= chr( ($len & 0xff00) >> 8);
	$s .= chr( ($len & 0xff0000) >> 16); 	
	if($is_large) {
		$s .= chr(($len & 0xff000000) >> 24).chr(0).chr(0).chr(0);
	}
	$s .= $string;
	if($pad) {
		$s .= str_repeat(chr(0), $pad);
	}
}

/**
 * Parse a Rserve packet from socket connection
 * @param unknown_type $socket
 */
function _rserve_get_response($socket) {
	$n = socket_recv($socket, $buf, 16, 0);
	if ($n != 16) {
		return FALSE;		
	}
	$len = int32($buf, 4);
	$ltg = $len;
	while ($ltg > 0) {
		$n = socket_recv($socket, $b2, $ltg, 0);
		if ($n > 0) {
			$buf .= $b2; 
			unset($b2); 
			$ltg -= $n; 
		} else {
			 break;	
		}
	}
	return $buf;
}

class Rserve_Parser {

	/** xpression type: NULL */
	const XT_NULL =  0;

	/** xpression type: integer */
	const XT_INT = 1;

	/** xpression type: double */
	const XT_DOUBLE = 2;

	/** xpression type: String */
	const XT_STR = 3;

	/** xpression type: language construct (currently content is same as list) */
	const XT_LANG = 4;

	/** xpression type: symbol (content is symbol name: String) */
	const XT_SYM = 5;

	/** xpression type: RBool */
	const XT_BOOL = 6;

	/** xpression type: S4 object
	@since Rserve 0.5 */
	const XT_S4 = 7;

	/** xpression type: generic vector (RList) */
	const XT_VECTOR = 16;

	/** xpression type: dotted-pair list (RList) */
	const XT_LIST = 17;

	/** xpression type: closure (there is no java class for that type (yet?). currently the body of the closure is stored in the content part of the REXP. Please note that this may change in the future!) */
	const XT_CLOS = 18;

	/** +xpression type: symbol name @since Rserve 0.5 */
	const XT_SYMNAME = 19;

	/** xpression type: dotted-pair list (w/o tags)	@since Rserve 0.5 */

	const XT_LIST_NOTAG = 20;

	/** xpression type: dotted-pair list (w tags) @since Rserve 0.5 */
	const XT_LIST_TAG = 21;

	/** xpression type: language list (w/o tags)
	@since Rserve 0.5 */
	const XT_LANG_NOTAG = 22;

	/** xpression type: language list (w tags)
	@since Rserve 0.5 */
	const XT_LANG_TAG = 23;

	/** xpression type: expression vector */
	const XT_VECTOR_EXP = 26;

	/** xpression type: string vector */
	const XT_VECTOR_STR = 27;

	/** xpression type: int[] */
	const XT_ARRAY_INT = 32;

	/** xpression type: double[] */
	const XT_ARRAY_DOUBLE = 33;

	/** xpression type: String[] (currently not used, Vector is used instead) */
	const XT_ARRAY_STR = 34;

	/** internal use only! this constant should never appear in a REXP */
	const XT_ARRAY_BOOL_UA = 35;

	/** xpression type: RBool[] */
	const XT_ARRAY_BOOL = 36;

	/** xpression type: raw (byte[])
	@since Rserve 0.4-? */
	const XT_RAW = 37;

	/** xpression type: Complex[]
	@since Rserve 0.5 */
	const XT_ARRAY_CPLX = 38;

	/** xpression type: unknown; no assumptions can be made about the content */
	const XT_UNKNOWN = 48;

	/** xpression type: RFactor; this XT is internally generated (ergo is does not come from Rsrv.h) to support RFactor class which is built from XT_ARRAY_INT */
	const XT_FACTOR = 127;

	/** used for transport only - has attribute */
	const XT_HAS_ATTR = 128;

    
	/**
	* Global parameters to parse() function
	* If true, use Rserve_RNative wrapper instead of native array to handle attributes
	*/
	public static $use_array_object = FALSE;
    
    /**
    * Transform factor to native strings, only for parse() method
    * If false, factors are parsed as integers
    */
    public static $factor_as_string = TRUE;
    
	/**
	 * parse SEXP results -- limited implementation for now (large packets and some data types are not supported)
	 * @param string $buf
	 * @param int $offset
	 * @param unknown_type $attr
	 */
	public static function parse($buf, $offset, $attr = NULL) {
		$r = $buf;
		$i = $offset;

		// some simple parsing - just skip attributes and assume short responses
		$ra = int8($r, $i);
		$rl = int24($r, $i + 1);
		$i += 4;

		$offset = $eoa = $i + $rl;
		//echo '[ '.self::xtName($ra & 63).', length '.$rl.' ['.$i.' - '.$eoa.']<br/>';
		if (($ra & 64) == 64) {
			throw new Exception('long packets are not supported (yet).');
		}
		if ($ra > self::XT_HAS_ATTR) {
			//echo '(ATTR*[';
			$ra &= ~self::XT_HAS_ATTR;
			$al = int24($r, $i + 1);
			$attr = self::parse($buf, $i);
			//echo '])';
			$i += $al + 4;
		}
		
        switch($ra) {
            case self::XT_NULL:
                $a = NULL;
                break;
            case self::XT_VECTOR: // generic vector
                $a = array();
                while ($i < $eoa) {
                    $a[] = self::parse($buf, &$i);
                }
                // if the 'names' attribute is set, convert the plain array into a map
                if ( isset($attr['names']) ) {
                    $names = $attr['names'];
                    $na = array();
                    $n = count($a);
                    for ($k = 0; $k < $n; $k++) {
                        $na[$names[$k]] = $a[$k];
                    }
                    $a = $na;
                }
            break;
            
            case self::XT_INT:
                $a = int32($r, $i);
                $i += 4;
            break;
            
            case self::XT_DOUBLE:
                $a = flt64($r, $i);
                $i += 8;
            break;
            
            case self::XT_BOOL:
                $v = int8($r, $i++);
                $a = ($v == 1) ? TRUE : (($v == 0) ? FALSE : NULL);
            break;
            
            case self::XT_SYMNAME: // symbol
                $oi = $i;
                while ($i < $eoa && ord($r[$i]) != 0) {
                    $i++;
                }
                $a = substr($buf, $oi, $i - $oi);
            break;
            
            case self::XT_LANG_NOTAG:
            case self::XT_LIST_NOTAG : // pairlist w/o tags
                $a = array();
                while ($i < $eoa) $a[] = self::parse($buf, &$i);
            break;
            
            case self::XT_LIST_TAG:
            case self::XT_LANG_TAG:
                // pairlist with tags
                $a = array();
                while ($i < $eoa) {
                    $val = self::parse($buf, &$i);
                    $tag = self::parse($buf, &$i);
                    $a[$tag] = $val;
                }
            break;
            
            case self::XT_ARRAY_INT: // integer array
                $a = array();
                while ($i < $eoa) {
                    $a[] = int32($r, $i);
                    $i += 4;
                }
                if (count($a) == 1) {
                    $a = $a[0];
                }
                // If factor, then transform to characters
                if( self::$factor_as_string  and isset($attr['class']) ) {
                    $c = $attr['class'];
                    $is_factor = is_string($c) && ($c == 'factor');
                    if($is_factor) {
                        $n = count($a);
                        $levels = $attr['levels'];
                        for($k = 0; $k < $n; ++$k) {
                            $i = $a[$k];
                            if($i < 0) {
                                $a[$k] = NULL;
                            } else {
                                $a[$k] = $levels[ $i -1];       
                            }
                        }
                    }
                }
            break;
            
            case self::XT_ARRAY_DOUBLE:// double array
                $a = array();
                while ($i < $eoa) {
                    $a[] = flt64($r, $i);
                    $i += 8;
                }
                if (count($a) == 1) {
                    $a = $a[0];
                }
            break;
            
            case self::XT_ARRAY_STR: // string array
                $a = array();
                $oi = $i;
                while ($i < $eoa) {
                    if (ord($r[$i]) == 0) {
                        $a[] = substr($r, $oi, $i - $oi);
                        $oi = $i + 1;
                    }
                    $i++;
                }
                if (count($a) == 1) {
                    $a = $a[0];
                }
            break;
            
            case self::XT_ARRAY_BOOL:  // boolean vector
                $n = int32($r, $i);
                $i += 4;
                $k = 0;
                $a = array();
                while ($k < $n) {
                    $v = int8($r, $i++);
                    $a[$k++] = ($v == 1) ? TRUE : (($v == 0) ? FALSE : NULL);
                }
                if ($n == 1) {
                    $a =  $a[0];
                }
            break;
            
            case self::XT_RAW: // raw vector
                $len = int32($r, $i);
                $i += 4;
                $a =  substr($r, $i, $len);
            break;
            
            case self::XT_CLOS: // do nothing
				$a = NULL;
            break;

            /*
            case self::XT_ARRAY_CPLX:

            break;
            */
            case 48: // unimplemented type in Rserve
                $uit = int32($r, $i);
                // echo "Note: result contains type #$uit unsupported by Rserve.<br/>";
                $a = NULL;
            break;
            
            default:
                echo 'Warning: type '.$ra.' is currently not implemented in the PHP client.';
                $a = NULL;
        } // end switch
        
        if(self::$use_array_object) {
            if( is_array($a) & $attr) {
                return new Rserve_RNative($a, $attr);
            } else {
                return $a;
            }
        }
        return $a;
	}

	
	/**
	 * parse SEXP to Debug array(type, length,offset, contents, n)
	 * @param string $buf
	 * @param int $offset
	 * @param unknown_type $attr
	 */
	public static function parseDebug($buf, $offset, $attr = NULL) {
		$r = $buf;
		$i = $offset;

		// some simple parsing - just skip attributes and assume short responses
		$ra = int8($r, $i);
		$rl = int24($r, $i + 1);
		$i += 4;

		$offset = $eoa = $i + $rl;
		
		$result = array();
		
		$result['type'] = self::xtName($ra & 63);
		$result['length'] =  $rl;
		$result['offset'] = $i;
		$result['eoa'] = $eoa;
		if (($ra & 64) == 64) {
			$result['long'] = TRUE;
			return $result;
		}
		if ($ra > self::XT_HAS_ATTR) {
		
			$ra &= ~self::XT_HAS_ATTR;
			$al = int24($r, $i + 1);
			$attr = self::parseDebug($buf, $i);
			$result['attr'] = $attr;
			$i += $al + 4;
		}
		if ($ra == self::XT_NULL) {
			return $result;
		}
		if ($ra == self::XT_VECTOR) { // generic vector
			$a = array();
			while ($i < $eoa) {
				$a[] = self::parseDebug($buf, &$i);
			}
			$result['contents'] = $a;			
		}
		if ($ra == self::XT_SYMNAME) { // symbol
			$oi = $i;
			while ($i < $eoa && ord($r[$i]) != 0) {
				$i++;
			}
			$result['contents'] = substr($buf, $oi, $i - $oi);
		}
		if ($ra == self::XT_LIST_NOTAG || $ra == self::XT_LANG_NOTAG) { // pairlist w/o tags
			$a = array();
			while ($i < $eoa) $a[] = self::parseDebug($buf, &$i);
			$result['contents'] = $a;
		}
		if ($ra == self::XT_LIST_TAG || $ra == self::XT_LANG_TAG) { // pairlist with tags
			$a = array();
			while ($i < $eoa) {
				$val = self::parseDebug($buf, &$i);
				$tag = self::parse($buf, &$i);
				$a[$tag] = $val;
			}
			$result['contents'] = $a;
		}
		if ($ra == self::XT_ARRAY_INT) { // integer array
			$a = array();
			while ($i < $eoa) {
				$a[] = int32($r, $i);
				$i += 4;
			}
			if (count($a) == 1) {
				$result['contents'] = $a[0];
			}
			$result['contents'] = $a;
		}
		if ($ra == self::XT_ARRAY_DOUBLE) { // double array
			$a = array();
			while ($i < $eoa) {
				$a[] = flt64($r, $i);
				$i += 8;
			}
			if (count($a) == 1) {
				$result['contents'] = $a[0];
			}
			$result['contents'] = $a;
		}
		if ($ra == self::XT_ARRAY_STR) { // string array
			$a = array();
			$oi = $i;
			while ($i < $eoa) {
				if (ord($r[$i]) == 0) {
					$a[] = substr($r, $oi, $i - $oi);
					$oi = $i + 1;
				}
				$i++;
			}
			if (count($a) == 1) {
				$result['contents'] = $a[0];
			}
			$result['contents'] = $a;
		}
		if ($ra == self::XT_ARRAY_BOOL) {  // boolean vector
			$n = int32($r, $i);
			$result['size'] = $n;
			$i += 4;
			$k = 0;
			$a = array();
			while ($k < $n) {
				$v = int8($r, $i++);
				$a[$k] = ($v === 1) ? TRUE : (($v === 0) ? FALSE : NULL);
				++$k;
			}
			if (count($a) == 1) {
				$result['contents'] = $a[0];
			}
			$result['contents'] = $a;
		}
		if ($ra == self::XT_RAW) { // raw vector
			$len = int32($r, $i);
			$i += 4;
			$result['size'] = $len;
			$result['contents'] = substr($r, $i, $len);
		}
		if($ra == self::XT_ARRAY_CPLX) {
			$result['not_implemented'] = true;
            // TODO: complex
		}
		if ($ra == 48) { // unimplemented type in Rserve
			$uit = int32($r, $i);
			$result['unknownType'] = $uit;
		}
		return $result;
	}
	
	
	public static function parseREXP($buf, $offset, $attr = NULL) {
		$r = $buf;
		$i = $offset;

		// some simple parsing - just skip attributes and assume short responses
		$ra = int8($r, $i);
		$rl = int24($r, $i + 1);
		$i += 4;

		$offset = $eoa = $i + $rl;
		if (($ra & 64) == 64) {
			throw new Exception('Long packets are not supported (yet).');
		}

		if ($ra > self::XT_HAS_ATTR) {
			$ra &= ~self::XT_HAS_ATTR;
			$al = int24($r, $i + 1);
			$attr = self::parseREXP($buf, $i);
			$i += $al + 4;
		}
		switch($ra) {
			case self::XT_NULL:
				$a =  new Rserve_REXP_Null();
				break;
			case self::XT_VECTOR: // generic vector
				$v = array();
				while ($i < $eoa) {
					$v[] = self::parseREXP($buf, &$i);
				}
				$a =  new Rserve_REXP_GenericVector();
				$a->setValues($v);
				break;

			case self::XT_SYMNAME: // symbol
				$oi = $i;
				while ($i < $eoa && ord($r[$i]) != 0) {
					$i++;
				}
				$v =  substr($buf, $oi, $i - $oi);
				$a = new Rserve_REXP_Symbol();
				$a->setValue($v);
				break;
			case self::XT_LIST_NOTAG:
			case self::XT_LANG_NOTAG: // pairlist w/o tags
				$v = array();
				while ($i < $eoa) {
					$v[] = self::parseREXP($buf, &$i);
				}
				$clasz = ($ra == self::XT_LIST_NOTAG) ? 'Rserve_REXP_List' : 'Rserve_REXP_Language';
				$a = new $clasz();
				$a->setValues($a);
				break;
			case self::XT_LIST_TAG :
			case self::XT_LANG_TAG: // pairlist with tags
				$clasz = ($ra == self::XT_LIST_TAG) ? 'Rserve_REXP_List' : 'Rserve_REXP_Language';
				$v = array();
				$names = array();
				while ($i < $eoa) {
					$v[] = self::parseREXP($buf, &$i);
					$names[] = self::parseREXP($buf, &$i);
				}
				$a = new $clasz();
				$a->setValues($v);
				$a->setNames($names);
				break;

			case self::XT_ARRAY_INT: // integer array
				$v = array();
				while ($i < $eoa) {
					$v[] = int32($r, $i);
					$i += 4;
				}
				$a = new Rserve_REXP_Integer();
				$a->setValues($v);
				break;

			case self::XT_ARRAY_DOUBLE: // double array
				$v = array();
				while ($i < $eoa) {
					$v[] = flt64($r, $i);
					$i += 8;
				}
				$a = new Rserve_REXP_Double();
				$a->setValues($v);
				break;

			case self::XT_ARRAY_STR: // string array
				$v = array();
				$oi = $i;
				while ($i < $eoa) {
					if (ord($r[$i]) == 0) {
						$v[] = substr($r, $oi, $i - $oi);
						$oi = $i + 1;
					}
					$i++;
				}
				$a = new Rserve_REXP_String();
				$a->setValues($v);
				break;

			case self::XT_ARRAY_BOOL:  // boolean vector
				$n = int32($r, $i);
				$i += 4;
				$k = 0;
				$vv = array();
				while ($k < $n) {
					$v = int8($r, $i++);
					$vv[$k] = ($v == 1) ? TRUE : (($v == 0) ? FALSE : NULL);
					$k++;
				}
				$a = new Rserve_REXP_Logical();
				$a->setValues($vv);
				break;

			case self::XT_RAW: // raw vector
				$len = int32($r, $i);
				$i += 4;
				$v = substr($r, $i, $len);
				$a = new Rserve_REXP_Raw();
				$a->setValue($v);
				break;

			case self::XT_ARRAY_CPLX:
				$a = FALSE;
				break;
					
			case 48: // unimplemented type in Rserve
				$uit = int32($r, $i);
				// echo "Note: result contains type #$uit unsupported by Rserve.<br/>";
				$a = NULL;
				break;

			default:
				echo 'Warning: type '.$ra.' is currently not implemented in the PHP client.';
				$a = FALSE;
		}
		if( $attr && is_object($a) ) {
			$a->setAttributes($attr);
		}
			
		return $a;
	}

	public static function  xtName($xt) {
		switch($xt) {
			case self::XT_NULL:  return 'null';
			case self::XT_INT:  return 'int';
			case self::XT_STR:  return 'string';
			case self::XT_DOUBLE:  return 'real';
			case self::XT_BOOL:  return 'logical';
			case self::XT_ARRAY_INT:  return 'int*';
			case self::XT_ARRAY_STR:  return 'string*';
			case self::XT_ARRAY_DOUBLE:  return 'real*';
			case self::XT_ARRAY_BOOL:  return 'logical*';
			case self::XT_ARRAY_CPLX:  return 'complex*';
			case self::XT_SYM:  return 'symbol';
			case self::XT_SYMNAME:  return 'symname';
			case self::XT_LANG:  return 'lang';
			case self::XT_LIST:  return 'list';
			case self::XT_LIST_TAG:  return 'list+T';
			case self::XT_LIST_NOTAG:  return 'list/T';
			case self::XT_LANG_TAG:  return 'lang+T';
			case self::XT_LANG_NOTAG:  return 'lang/T';
			case self::XT_CLOS:  return 'clos';
			case self::XT_RAW:  return 'raw';
			case self::XT_S4:  return 'S4';
			case self::XT_VECTOR:  return 'vector';
			case self::XT_VECTOR_STR:  return 'string[]';
			case self::XT_VECTOR_EXP:  return 'expr[]';
			case self::XT_FACTOR:  return 'factor';
			case self::XT_UNKNOWN:  return 'unknown';
		}
		return '<? '.$xt.'>';
	}

	/**
	 *
	 * @param Rserve_REXP $value
     * This function is not functionnal. Please use it only for testing
	 */
	public static function createBinary(Rserve_REXP $value) {
		// Current offset
		$o = 0; // Init with header size
		$contents = '';
		$type = $value->getType();
		switch($type) {
			case self::XT_S4:
			case self::XT_NULL:
				break;
			case self::XT_INT:
				$v = (int)$value->at(0);
				$contents .= mkint32($v);
				$o += 4;
				break;
			case self::XT_DOUBLE:
				$v = (float)$value->at(0);
				$contents .= mkfloat64($v);
				$o += 8;
				break;
			case self::XT_ARRAY_INT:
				$vv = $value->getValues();
				$n = count($vv);
				for($i = 0; $i < $n; ++$i) {
					$v = $vv[$i];
					$contents .= mkint32($v);
					$o += 4;
				}
				break;
			case self::XT_ARRAY_BOOL:
				$vv = $value->getValues();
				$n = count($vv);
				$contents .= mkint32($n);
				$o += 4;
				if( $n ) {
					for($i = 0; $i < $n; ++$i) {
						$v = $vv[$i];
						if(is_null($v)) {
							$v = 2;
						} else {
							$v = (int)$v;
						}
						if($v != 0 AND $v != 1) {
							$v = 2;
						}
						$contents .= chr($v);
						++$o;
					}
					while( ($o & 3) != 0 ) {
						$contents .= chr(3);
						++$o;
					}
				}
				break;
			case self::XT_ARRAY_DOUBLE:
				$vv = $value->getValues();
				$n = count($vv);
				for($i = 0; $i < $n; ++$i) {
					$v = (float)$vv[$i];
					$contents .= mkfloat64($v);
					$o += 8;
				}
				break;
			case self::XT_RAW :
				$v = $value->getValue();
				$n = $value->length();
				$contents .= mkint32($n);
				$o += 4;
				$contents .= $v;
				break;
					
			case self::XT_ARRAY_STR:
				$vv = $value->getValues();
				$n = count($vv);
				for($i = 0; $i < $n; ++$i) {
					$v = $vv[$i];
					if( is_null($v) ) {
						if( ord($v[0]) == 255 ) {
							$contents .= chr(255);
							++$o;
						}
						$contents .= $v;
						$o += strlen($v);
					} else {
						$contents .= chr(255).chr(0);
						$o += 2;
					}
				}
				while( ($o & 3) != 0) {
					$contents .= chr(1);
					++$o;
				}
				break;
			case self::XT_LIST_TAG:
			case self::XT_LIST_NOTAG:
			case self::XT_LANG_TAG:
			case self::XT_LANG_NOTAG:
			case self::XT_LIST:
			case self::XT_VECTOR:
			case self::XT_VECTOR_EXP:
				$l = $value->getValues();
				if($type == XT_LIST_TAG || $type == XT_LANG_TAG) {
					$names = $value->getNames();
				}
				$i = 0; 
				$n = count($l);
				while($i < $n) {
					$x = $l[$i];
					if( is_null($x) ) {
						$x = new Rserve_REXP_Null();
					}
					$iof = strlen($contents);
					$contents .= self::createBinary($x);
					if($type == XT_LIST_TAG || $type == XT_LANG_TAG) {
						$sym = new Rserve_REXP_Symbol();
						$sym->setValue($names[$i]);
						$contents .= self::createBinary($sym);
					}
					++$i;
				}
				break;

			case self::XT_SYMNAME:
			case self::XT_STR:
				$s = (string)$value->getValue();
				$contents .= $s;
				$o += strlen($s);
				$contents .= chr(0);
				++$o;
				//padding if necessary
				while( ($o & 3) != 0) {
					$contents .= chr(0);
					++$o;
				}
				break;
		}
		/*
		TODO: handling attr
		$attr = $value->attr();
		$attr_bin = '';
		if( is_null($attr) ) {
			$attr_off = self::createBinary($attr, $attr_bin, 0);
			$attr_flag = self::XT_HAS_ATTR; 
		} else {
			$attr_off = 0;
			$attr_flag = 0;
		}
		  // [0]   (4) header SEXP: len=4+m+n, XT_HAS_ATTR is set
		  // [4]   (4) header attribute SEXP: len=n
  		  // [8]   (n) data attribute SEXP
  		  // [8+n] (m) data SEXP
		*/		
		$attr_flag = 0;
		$length = $o;
		$isLarge = ($length > 0xfffff0);
		$code = $type | $attr_flag;
		
		// SEXP Header (without ATTR)
		// [0]  (byte) eXpression Type
		// [1]  (24-bit int) length
		$r  = chr( $code & 255);
		$r .= mkint24($length);
		$r .= $contents;
		return $r;
	}
}

/**
 * Handle Connection and communicating with Rserve instance
 * @author Clément Turbelin
 *
 */
class Rserve_Connection {

    const PARSER_NATIVE = 0;
    const PARSER_REXP = 1;
    const PARSER_DEBUG = 2;
    const PARSER_NATIVE_WRAPPED = 3;
    
	const DT_INT = 1;
	const DT_CHAR = 2;
	const DT_DOUBLE = 3;
	const DT_STRING = 4;
	const DT_BYTESTREAM = 5;
	const DT_SEXP = 10;
	const DT_ARRAY = 11;

	/** this is a flag saying that the contents is large (>0xfffff0) and hence uses 56-bit length field */
	const DT_LARGE = 64;

	const CMD_login			= 0x001;
	const CMD_voidEval		= 0x002;
	const CMD_eval			= 0x003;
	const CMD_shutdown		= 0x004;
	const CMD_openFile		= 0x010;
	const CMD_createFile	= 0x011;
	const CMD_closeFile		= 0x012;
	const CMD_readFile		= 0x013;
	const CMD_writeFile		= 0x014;
	const CMD_removeFile	= 0x015;
	const CMD_setSEXP		= 0x020;
	const CMD_assignSEXP	= 0x021;

	const CMD_setBufferSize	= 0x081;
	const CMD_setEncoding	= 0x082;

	const CMD_detachSession	= 0x030;
	const CMD_detachedVoidEval = 0x031;
	const CMD_attachSession = 0x032;

	// control commands since 0.6-0
	const CMD_ctrlEval		= 0x42;
	const CMD_ctrlSource	= 0x45;
	const CMD_ctrlShutdown	= 0x44;

	// errors as returned by Rserve
	const ERR_auth_failed	= 0x41;
	const ERR_conn_broken	= 0x42;
	const ERR_inv_cmd		= 0x43;
	const ERR_inv_par		= 0x44;
	const ERR_Rerror		= 0x45;
	const ERR_IOerror		= 0x46;
	const ERR_not_open		= 0x47;
	const ERR_access_denied = 0x48;
	const ERR_unsupported_cmd=0x49;
	const ERR_unknown_cmd	= 0x4a;
	const ERR_data_overflow	= 0x4b;
	const ERR_object_too_big = 0x4c;
	const ERR_out_of_mem	= 0x4d;
	const ERR_ctrl_closed	= 0x4e;
	const ERR_session_busy	= 0x50;
	const ERR_detach_failed	= 0x51;

	public static $machine_is_bigendian = NULL;

	private static $init = FALSE;

	private $socket;
	private $auth_request;
	private $auth_method;

	/**
	 * initialization of the library
	 */
	public static function init() {
		if( self::$init ) {
            return;
        }
        $m = pack('s', 1);
		self::$machine_is_bigendian = ($m[0] == 0);
		spl_autoload_register('Rserve_Connection::autoload');
		self::$init = TRUE;
	}

	public static function autoload($name) {
		$s = strtolower(substr($name, 0, 6));
		if($s != 'rserve') {
			return FALSE;
		}
		$s = substr($name, 7);
		$s = str_replace('_', '/', $s);
		$s .= '.php';
		require $s;
		return TRUE; 
	}

	/**
	 *  if port is 0 then host is interpreted as unix socket, otherwise host is the host to connect to (default is local) and port is the TCP port number (6311 is the default)
	 */
	public function __construct($host='127.0.0.1', $port = 6311, $debug = FALSE) {
		if( !self::$init ) {
			self::init();
		}
		if( $port == 0 ) {
			$socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
		} else {
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		}
        if( !$socket ) {
            throw new Rserve_Exception("Unable to create socket<pre>".socket_strerror(socket_last_error())."</pre>");
        }
        //socket_set_option($socket, SOL_TCP, SO_DEBUG,2);
        $ok = socket_connect($socket, $host, $port);
        if( !$ok ) {
            throw new Rserve_Exception("Unable to connect<pre>".socket_strerror(socket_last_error())."</pre>");
        }
        $buf = '';
        $n = socket_recv($socket, $buf, 32, 0);
        if( $n < 32 || strncmp($buf, 'Rsrv', 4) != 0 ) {
            throw new Rserve_Exception('Invalid response from server.');
        }
        $rv = substr($buf, 4, 4);
        if( strcmp($rv, '0103') != 0 ) {
            throw new Rserve_Exception('Unsupported protocol version.');
        }
        for($i = 12; $i < 32; $i += 4) {
            $attr = substr($buf, $i, $i + 4);
            if($attr == 'ARpt') {
                $this->auth_request = TRUE;
                $this->auth_method = 'plain';
            } elseif($attr == 'ARuc') {
                $this->auth_request = TRUE;
                $this->auth_method = 'crypt';
            }
            if($attr[0] === 'K') {
                $key = substr($attr, 1, 3);
            }
        }
		$this->socket = $socket;
	}

	/**
	 * Evaluate a string as an R code and return result
	 * @param string $string
	 * @param int $parser 
	 * @param REXP_List $attr
	 */
	public function evalString($string, $parser = self::PARSER_NATIVE, $attr=NULL) {
		$r = $this->command(self::CMD_eval, $string );
		$i = 20;
		if( !$r['is_error'] ) {
			$buf = $r['contents'];
			$r = NULL;
            switch($parser) {
                case self::PARSER_NATIVE:
                    $r = Rserve_Parser::parse($buf, $i, &$attr);
                break;
                case self::PARSER_REXP:
                    $r = Rserve_Parser::parseREXP($buf, $i, &$attr);
                break;
                case self::PARSER_DEBUG:
                    $r = Rserve_Parser::parseDebug($buf, $i, &$attr);
                    break;
                case self::PARSER_NATIVE_WRAPPED:
                        $old = Rserve_Parser::$use_array_object;
                        Rserve_Parser::$use_array_object = TRUE;
                        $r = Rserve_Parser::parse($buf, $i, &$attr);
                        Rserve_Parser::$use_array_object = $old;
                    break;
                default:
                    throw new Exception('Unknown parser');
            }
			return $r;
		}
		// TODO: contents and code in exception
		throw new Rserve_Exception('unable to evaluate');
	}

	/**
	 * Close the current connection
	 */
	public function close() {
		if($this->socket) {
			return socket_close($this->socket);
		}
		return TRUE;
	}

	/**
	 * send a command to R
	 * @param int $command command code
	 * @param string $v command contents
	 */
	private function command($command, $v) {
		$pkt = _rserve_make_packet($command, $v);
		socket_send($this->socket, $pkt, strlen($pkt), 0);

		// get response
		$n = socket_recv($this->socket, $buf, 16, 0);
		if ($n != 16) {
			return FALSE;
		}
		$len = int32($buf, 4);
		$ltg = $len;
		while ($ltg > 0) {
			$n = socket_recv($this->socket, $b2, $ltg, 0);
			if ($n > 0) {
				$buf .= $b2;
				unset($b2);
				$ltg -= $n;
			} else {
			 break;
			}
		}
		$res = int32($buf);
		return(array(
			'code'=>$res,
			'is_error'=>($res & 15) != 1,
			'error'=>($res >> 24) & 127,
			'contents'=>$buf
		));
	}

	/**
	 * Assign a value to a symbol in R
	 * @param string $symbol name of the variable to set (should be compliant with R syntax !)
	 * @param Rserve_REXP $value value to set
     Commented because not ready for this release
	public function assign($symbol, $value) {
		if(! is_object($symbol) and !$symbol instanceof Rserve_REXP_Symbol) {
			$symbol = (string)$symbol;
			$s = new Rserve_REXP_Symbol();
			$s->setValue($symbol);
		}
		if(!is_object($value) AND ! $value instanceof Rserve_REXP) {
			throw new InvalidArgumentException('value should be REXP object');
		}
		$contents .= Rserve_Parser::createBinary($s);
		$contents .= Rserve_Parser::createBinary($value);
	}
   	 */

}

class Rserve_Exception extends Exception { }

class Rserve_Parser_Exception extends Rserve_Exception {
}

Rserve_Connection::init();

