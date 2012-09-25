<?php
//to init:
//http://whatever.herokuapp.com/proxy/TwitterJSONSearchProxy.php?searchTerm=nathank000&test=true&init=true


include('../includes/db.php');
//include('../includes/local.php');
//comment added to make sure that I did not eff up git with config edit

class TwitterJSONSearchProxy
{
	private $_cachedFilePath;
	private $_cacheFileTime;
	private $_cacheFolderName;
	private $_cachePath;
	private $_searchString;
	private $_queryString;
	private $_results;
	private $_cached;
	private $_ch;
	
	public function TwitterJSONSearchProxy($searchTerm, $testing, $init=FALSE)
	{
		
		$this->_searchString 	= $searchTerm;
		$this->_queryString     = 'q=' .$searchTerm;
		$this->_searchURL       = "http://search.twitter.com/search.json";
		$this->_cached          = false; 
		$this->_cachedFileName  = "twitter.cache";
		$this->_cacheFolderName = "cache"; 
		
		$this->_testMode 		= $testing;
		$this->_init			= $init;
		
		//cache time
		if ($this->_testMode == TRUE) {
			$this->_cacheFileTime   = 30; // file time cache in milliseconds, 3,600,000 = 1hr  
		}
		else {
			$this->_cacheFileTime   = 3600000; // file time cache in milliseconds, 3,600,000 = 1hr  
		}
				
		//init functions
		if ($this->_init == TRUE) {
			echo "creating the database table for twitterSearch<br />";
			$this->initDatabase();
		}
		
		//echo the host
		if ($this->_testMode == TRUE) {
			echo "heroku db host = " .DB_HOST .'<br />';
		}
		
		if ($this->_testMode == TRUE) {
			$this->testDatabase();
		}
		else {
			$this->getResults();
		}
	}  

	private function getResults()
	{	
		if($this->isCached()){
			
			if ($this->_testMode) { echo "returning the cached results <br />"; }
			$this->_results = $this->readFromCache();
		} else {
			$this->ch = curl_init();
			$curlURL = $this->_searchURL . '?' . $this->_queryString;
			
			if($this->_testMode == TRUE) { echo"<br />url = " .$curlURL .'<br />'; }
			
			curl_setopt($this->ch, CURLOPT_URL, $curlURL);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($this->ch, CURLOPT_HEADER, false);    
		    $this->_results = curl_exec($this->ch);
			
			if($this->_testMode == TRUE) { echo"<br />results = " .$this->_results .'<br />'; }
				    
		    if(strlen((string) $this->_results) > 0){
		    	$this->_results = $this->_results;
		    	$this->cacheThis();
		    	curl_close($this->ch);
			} else {
				$this->_results = $this->readFromCache();
			}
		}
	}
	
	private function readFromCache()
	{
		//connect to the database and get the json results
		$q = 'SELECT searchResults FROM twittersearch WHERE searchTerm = "' .$this->_searchString .'"';
		$r = mysql_query($q, CONN) or die('could not fetch the cached search results');
		$arr = mysql_fetch_array($r);
		$searchResults = $arr['searchResults'];
		
		if ($this->_testMode) { echo "fetching the cached<br />"; }
		
		return $searchResults;
		//var_dump($fData);
	}
	
	private function cacheThis()
	{
		//cache this in the database
		$q = 'UPDATE twittersearch SET searchResults = "' .addslashes($this->_results) .'" WHERE searchTerm = "' .$this->_searchString .'"';
		$r = mysql_query($q, CONN) or die('could not update the results in the database for caching');
	}
	
	private function isCached()
	{
		//check the database for cached results
		$q = 'SELECT UNIX_TIMESTAMP(lastResult) as lastUpdated FROM twittersearch WHERE searchTerm = "' .$this->_searchString .'"';
		$r = mysql_query($q, CONN) or die('could not query the database for caching check');
		$arr = mysql_fetch_array($r);
		
		$now          	= time();
		$diff         	= $now - $arr[0];
		
		if ($this->_testMode) {
			
			print "is cached? results --------------------";
			print "<pre>";
				print_r($arr);
			print "</pre><br />";
			
			print "array count = " .count($arr) .'<br />';

			print "is cached? q = " .$q ."<br>";
			print "is cached? last modified = " .$arr[0] ."<br>";
			print "is cached? now = " .$now ."<br>";
			print "is cached? diff = " .$diff ."<br>";
		}
		
		if (!isset($arr['lastUpdated'])) {
			die('the search term has not been setup properly');
		}
		
		if($diff > $this->_cacheFileTime){
			if ($this->_testMode) {print "is cached? returning false<br>";}
			return false;
		} else {
			if ($this->_testMode) {print "is cached? returning true<br>";}
			return true;
		}
		
		
		if ($this->_testMode) {print "is cached? returning true<br>";}
		return true;
	}
	
	public function display()
	{
		print (string) $this->_results;
	}
	
	private function initDatabase() {
		$q = '
		CREATE TABLE `twitterSearch` (
										  `recno` int(11) NOT NULL AUTO_INCREMENT,
										  `searchTerm` varchar(100) DEFAULT NULL,
										  `searchResults` text,
										  `lastResult` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
										  PRIMARY KEY (`recno`)
										) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1
		';
		$r = mysql_query($q, CONN);
		if ((mysql_error()) && (mysql_error() == "Table 'twittersearch' already exists")) {
			echo 'could not create the twittersearchTable, db says: ' .mysql_error() .'<br />';
			echo 'creating the search term entry <br />';
			$this->initSearchTerm();
			
		} else {
			echo "there was a larger issue, aborting. db says: " .mysql_error() .'<br />';
			die();
		}
	}
	
	private function initSearchTerm() {
		//check to see if there is a row for this search term already
		$q = 'SELECT count(*) FROM twittersearch WHERE searchTerm = "' .$this->_searchString .'"';
		if ($this->_testMode) {print "(init) created a  row, q = " .$q ."<br> ";}
		$r = mysql_query($q, CONN) or die('could not select the number of rows for the search term in initting');
		$arr = mysql_fetch_array($r);
		$numRows = $arr['count(*)'];
		
		if ($numRows == 0) {
			$q = 'INSERT INTO twittersearch (searchTerm) VALUES ("' .$this->_searchString .'")';
			$r = mysql_query($q, CONN) or die('could not create the new row for the search term in init()');
			if ($this->_testMode) {print "(init) created a  row, q = " .$q ."<br> ";}
		}
		else {
			if ($this->_testMode) {print "(init) no need to create a row<br>";}
		}
	}
	
	private function testDatabase() {
		$q = "select * from twittersearch";
		$r = mysql_query($q, CONN);
		$arr = mysql_fetch_array($r);
		echo('database select test-------------------------------------<br />');
		echo('<pre>');
			print_r($arr);
		echo('</pre><br />');
		
		$this->getResults();
	}
}

if ((isset($_GET['test'])) && ($_GET['test'] == 'true')) {
	$testing = TRUE;
}
else {
	$testing = FALSE;
}

if ((isset($_GET['init'])) && ($_GET['init'] == 'true')) {
	$init = TRUE;
}
else {
	$init = FALSE;
}

if ($testing == TRUE) { 
	echo('search term  = ' .$_GET['searchTerm']. '<br/>');
}



$t = new TwitterJSONSearchProxy(urlencode($_GET['searchTerm']), $testing, $init);
$t->display();
?>