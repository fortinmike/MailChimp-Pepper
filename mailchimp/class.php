<?php
/******************************************************************************
 Pepper

 Developer      : Michaël Fortin
 Plug-in Name   : MailChimp

 [irradiated.net](http://www.irradiated.net/)

 ******************************************************************************/
 if (!defined('MINT')) { header('Location:/'); }; // Prevent viewing this file directly
 
 $installPepper = "MF_MailChimp";
 
 class MF_MailChimp extends Pepper
 {
 	var $version = 100;
 	var $info = array
 	(
 	    'pepperName' => 'MailChimp',
 	    'pepperUrl' => 'http://www.irradiated.net/',
 	    'pepperDesc' => 'The MailChimp pepper shows information about a MailChimp mailing list of your choice.',
 	    'developerName' => 'Michaël Fortin',
 	    'developerUrl'  => 'http://www.irradiated.net/'
 	);
 	var $panes = array
 	(
 	    'MailChimp' => array
 	    (
 	        'Total'
 	    )
 	);
 	var $manifest = array
 	(
 	    'visit' => array
 	    (
 	        'mailchimp' => "TINYINT(5) NOT NULL"
 	    )
 	);
 	
 	
	/**************************************************************************
	 isCompatible()
	 **************************************************************************/
	function isCompatible()
	{
		if ($this->Mint->version >= 200)
		{
			return array
			(
				'isCompatible'	=> true
			);
		}
		else
		{
			return array
			(
				'isCompatible'	=> false,
				'explanation'	=> '<p>This Pepper is only compatible with Mint 2.0 and higher.</p>'
			);
		}
	}
	
	
	/**************************************************************************
	 onDisplay()
	 **************************************************************************/
	function onDisplay($pane, $tab, $column = '', $sort = '')
	{
		require_once 'mailchimp-api/MCAPI.class.php';
	
		$html = "";
		
		$apikey = $this->prefs['mailchimp-api-key'];
		$listid = $this->prefs['mailchimp-list-id'];
		$listname = $this->prefs['mailchimp-list-name'];
		$apiUrl = 'http://api.mailchimp.com/1.3/';
		
		// Create the main MailChimp object
		$api = new MCAPI($apikey);
		
		// Obtain a list of members
		$retval = $api->listMembers($listid, 'subscribed', null, 0, 5000 );
		
		$err = $api->errorCode;
		if ($err){
			$html .= "Error $err. Please make sure a valid API key and list ID are set in the pepper's preferences.";
			$html .= "\n\nError Message: ".$api->errorMessage;
		} else {
			$total = $retval['total'];
			$html .= "<div style='text-align: center; padding: 2px 0;'><span style='font-size: 4em; line-height: 1.5em;'>$total</span><br/>$listname Subscribers</div>";
		}
		
		return $html;
	}
	
	/**************************************************************************
	 onDisplayPreferences()
	 **************************************************************************/
	function onDisplayPreferences() 
	{
		$defaultGroups = get_class_vars('SI_WindowWidth');
		
		/* Global *************************************************************/
		$preferences['MailChimp API Key'] = <<<HERE
			<table>
				<tr>
					<td><input type="text" name="mailchimp-api-key" value="{$this->prefs['mailchimp-api-key']}"></td>
				</tr>
				<tr>
					<td>A valid API Key. See http://admin.mailchimp.com/account/api to get one.</td>
				</tr>
			</table>
HERE;

		$preferences['MailChimp List ID'] = <<<HERE
			<table>
				<tr>
					<td><input type="text" name="mailchimp-list-id" value="{$this->prefs['mailchimp-list-id']}"></td>
				</tr>
				<tr>
					<td>Login to your MailChimp account, go to List, then List Tools, and look for the List ID entry.</td>
				</tr>
			</table>	
HERE;

		$preferences['MailChimp List Name'] = <<<HERE
			<table>
				<tr>
					<td><input type="text" name="mailchimp-list-name" value="{$this->prefs['mailchimp-list-name']}"></td>
				</tr>
				<tr>
					<td>Enter a name for the list. Optional.</td>
				</tr>
			</table>	
HERE;

		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{
		$this->prefs['mailchimp-api-key'] = $this->escapeSQL($_POST['mailchimp-api-key']);
		$this->prefs['mailchimp-list-id'] = $this->escapeSQL($_POST['mailchimp-list-id']);
		$this->prefs['mailchimp-list-name'] = $this->escapeSQL($_POST['mailchimp-list-name']);
	}
	
}
?>