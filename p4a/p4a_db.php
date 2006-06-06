<?php

/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */

/**
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_DB
{
	/**
	 * Connects to the configured database.
	 * Database is configured by setting P4A_DSN constant.
	 * @access public
	 * @throws onDBConnectionError
	 */
	function &singleton($DSN = "")
  	{
		//If DSN is not specified I use default connection
		if (!strlen($DSN) and defined("P4A_DSN")){
			$DSN = P4A_DSN;
		}
		
		if (strlen($DSN)) {
			$dbconn = base64_encode($DSN);
			$dbconn = "db" . str_replace(array('=','+','/'),'',$dbconn);
			global $$dbconn; //static $$ doesn't work 
		}
		
		if(!isset($$dbconn) or $$dbconn == null) {
			if(strlen($DSN)) {
				$$dbconn = MDB2::factory($DSN);
    			if (MDB2::isError($$dbconn)) {
					$e = new P4A_ERROR('Database connection failed', $this, $$dbconn);
    				if ($this->errorHandler('onDBConnectionError', $e) !== PROCEED) {
    					die();
    				}
    			}
    			$$dbconn->setFetchMode(MDB2_FETCHMODE_ASSOC);
				$$dbconn->setOption('seqcol_name', 'id');
				$$dbconn->loadModule('Reverse', null, true);
				$$dbconn->loadModule('Extended', null, false);
			} else {
				$$dbconn = null;
			}
		}
		return $$dbconn;
	}

	/**
	 * Connects to the configured database.
	 * Database is configured by setting P4A_DSN constant.
	 * @access private
	 */
	function &connect($DSN = "")
	{
		return P4A_DB::singleton($DSN);
	}

	/**
	 * Close the connection to the database.
	 * @access private
	 */
	function close($DSN = "")
	{
		$db = P4A_DB::singleton($DSN);
		if(is_object($db)) {
			$db->disconnect();
		}
	}
}
?>