<?php
namespace cupphp;
define("_CUP_PHP_API_COOKIE_BASE_NAME",".cup-php.api.cookie.");


class Names{
	public $name,$class,$number,$username;
	public function __construct($na,$c,$nu,$us){$this->set($na,$c,$nu,$us);}
	public function set($na,$c,$nu,$us){
		$this->name=$na;
		$this->class=$c;
		$this->number=$nu;
		$this->username=$us;
	}
}

class Session{
	public $sessionId,$names,$eventvalidation;
	public function __construct($sid,$nam,$ev){$this->set($sid,$nam,$ev);}
	public function __destruct(){
		if(file_exists(_CUP_PHP_API_COOKIE_BASE_NAME.$this->sessionId))
			unlink(_CUP_PHP_API_COOKIE_BASE_NAME.$this->sessionId);
	}
	public function set($sid,$nam,$ev){
		$this->sessionId=$sid;
		$this->names=$nam;
		$this->eventvalidation=$ev;
	}
}

class Lesson{
	public $date,$day,$hour,$teacher,$subject,$classroom;
	public function __construct($dat,$da,$h,$t,$s,$classr){$this->set($dat,$da,$h,$t,$s,$classr);}
	public function set($dat,$da,$h,$t,$s,$classr){
		$this->date=$dat;
		$this->day=$da;
		$this->hour=$h;
		$this->teacher=$t;
		$this->subject=$s;
		$this->classroom=$classr;
	}
}

class Cupphp{
	//Passing a non-empty $postdata implies a POST reques; otherwise, a GET request is issued.
	//Passing $cookie_id implies using cookies; the id allows the use of multiple sessions at the same time.
	//private function curlget($url,$cookie_id="",$postdata=""){
	private function curlget($url,$cookie_id,$postdata){
		$referer=parse_url($url);
		if($referer){
			$referer=$referer["scheme"]."://".$referer["host"];
		} else {
			throw new \Exception("CUPphp:curlget:invalid_url, an invalid url was passed");
		}
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt($ch,CURLOPT_TIMEOUT,60);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		if($cookie_id&&$cookie_id!=""){
			curl_setopt($ch,CURLOPT_COOKIEJAR,_CUP_PHP_API_COOKIE_BASE_NAME.$cookie_id);
			curl_setopt($ch,CURLOPT_COOKIEFILE,_CUP_PHP_API_COOKIE_BASE_NAME.$cookie_id);
		}
		curl_setopt($ch,CURLOPT_REFERER,$referer);
		if($postdata!=""){
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
			//curl_setopt($ch,CURLOPT_POST,1);
		}
		$result=curl_exec($ch);
		curl_close($ch);
		//unlink(_CUP_PHP_API_COOKIE_BASE_NAME.$cookie_id);
		return $result;
	}
	private function encodeURIComponent($str){
		$revert=array('%21'=>'!','%2A'=>'*','%27'=>"'",'%28'=>'(','%29'=>')');
		return strtr(rawurlencode($str),$revert);
	}
	public static function getNames($filter,$schoolURL){
		if(ctype_space($filter)||strlen($filter)<3)return array();
		$sessionId=uniqid();
		$postdata = array();
		$postdata['__EVENTTARGET'] = "";
		$postdata['__EVENTARGUMENT'] = "";
		$postdata['__VIEWSTATE'] = ('/wEPDwULLTE3NDM5MzMwMzRkZA==');
		$postdata['__EVENTVALIDATION'] = ('/wEWBAL++4CfBgK52+LYCQK1gpH7BAL0w/PHAQ==');
		$postdata['_nameTextBox'] = urlencode($filter);
		$postdata['_zoekButton'] = urlencode('Zoek');
		$postdata['numberOfLettersField'] = urlencode('3');
		$result=self::curlget("http://".self::encodeURIComponent($schoolURL)."/Default.aspx",$sessionId,$postdata);
		if($result[0]=="<")throw new \Exception("CUPphp:getNames:invalid_server_response, server returned invalid response");
		preg_match_all( '/<option value="([^"]+)">/', $result, $match, PREG_SET_ORDER);
		preg_match('~<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" />~', $result, $eventValidation);
		$nameList = array();
		foreach ($match as $item) {
			preg_match( '/[^"]+ (?! \((.*?)\))/', $item[1], $name);
			preg_match( '/\((.*?)\)/', $item[1], $class);
			preg_match( '/\[(.*?)\]/', $item[1], $number);
			$nameList[]=new Names(substr_replace($name[0],"",-1),$class[1],$number[1],$item[1]);
		}
		$list=new Session($sessionId,$nameList,$eventValidation[1]);
		return $list;
	}
	public static function getTimeTable($user,$pass,$schoolURL,$session,$eventvalidation){
		$postdata = array();
		$postdata['__EVENTTARGET'] = "";
		$postdata['__EVENTARGUMENT'] = "";
		$postdata['__VIEWSTATE'] = ('/wEPDwUKLTQwMjM2NTU0NGRk');
		$postdata['__EVENTVALIDATION'] = $eventvalidation;
		$postdata['_nameDropDownList'] = $user;
		$postdata['_pincodeTextBox'] = $pass;
		$postdata['_roosterbutton'] = ('Rooster');
		$result=self::curlget("http://".self::encodeURIComponent($schoolURL)."/LogInWebForm.aspx",$session,$postdata);
		if($result[0]=="<")throw new \Exception("CUPphp:getNames:invalid_server_response, server returned invalid response");
		$list=array();
		//preg_match_all( '/<td align="center" colspan="9">week : (.*)<\/td>/', $result, $match, PREG_SET_ORDER);
		preg_match_all( '/<tr>\s*<td>(.*)......<\/td>\s*<td>(.*)......<\/td>\s*<td>(.*)<\/td><td>(.*)<\/td><td>(.*).<\/td><td>(.*)<\/td>\s*<\/tr>/', $result, $lesmatch, PREG_SET_ORDER);
		foreach ($lesmatch as $item){
			if (preg_match("/</", $item[4])){$teacher=substr($item[4], 27, -8);}else{$teacher=substr($item[4], 0, -1);}
			$list[]=new Lesson($item[1],$item[2],$item[3],$teacher,$item[5],$item[6]);
		}
		return $list;
	}
}

function getNames($filter,$schoolUrl){return Cupphp::getNames($filter,$schoolUrl);}
function getTimeTable($user,$pass,$schoolUrl,$session,$eventvalidation){return Cupphp::getTimeTable($user,$pass,$schoolUrl,$session,$eventvalidation);}
?>
