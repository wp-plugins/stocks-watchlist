<?php
/*
Plugin Name: Stocks Watchlist
Plugin URI: http://www.traderknowledge.com/free/stocks-watchlist-wordpress-plugin/
Description: Manage and share your stocks watchlist in Wordpress. Use <em>Manage->Stocks Watchlist</em> to set options. Use <em>&lt;?php swl_output() ?&gt;</em> to output your Watchlist.
Author: Dominic Foster
Version: 1.0.0
Author URI: http://www.traderknowledge.com/
*/

/*
Stocks Watchlist is a Wordpress Plugin that allows you to manage and display your stock market watchlist.
Copyright (C) 2007 Dominic Foster

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define('SWL_TABLE', $table_prefix . 'stock_watchlist');

$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
$stockID = !empty($_REQUEST['stockID']) ? $_REQUEST['stockID'] : '';

//admin menu
function tk_watchlist_admin() {
	if (function_exists('add_options_page')) {
		add_management_page('tk-watchlist', 'Stocks Watchlist', 1, basename(__FILE__), 'tk_watchlist_admin_panel');
  }
}

function tk_watchlist_admin_panel() {

	global $wpdb, $table_prefix;
	$alt = 'alternate';
	$buttontext = "Add Stock &raquo;";

	//Get the action for the form
	if(!empty($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
	}
	else {
		$action = "add";
	}

	//Get ID of Edit/Delete
	if(!empty($_REQUEST['stkID'])) { $stkid = $_REQUEST['stkID'];	}

	//First time run - allow to build new table
	$tableExists = false;

	$tables = $wpdb->get_results("show tables;");

	foreach ( $tables as $table )
	{
		foreach ( $table as $value )
		{
			if ( $value == SWL_TABLE )
			{
				$tableExists=true;
				break;
			}
		}
	}

	if ( !$tableExists )
	{
		$sql = "CREATE TABLE " . SWL_TABLE . " (
					swl_ID INT(11) NOT NULL AUTO_INCREMENT,
					swl_symbol TEXT NOT NULL,
					swl_description TEXT,
					swl_visible  ENUM( 'yes', 'no' ) NOT NULL ,
					swl_date DATETIME NOT NULL,
					PRIMARY KEY ( swl_ID )
				)";
		$wpdb->get_results($sql);
	}

	//perform Add/Edit/Delete
	switch ($action) {
		case 'add':
			//check that we have the necessary variables
			if(!empty($_REQUEST['stkName'])) {
				$stkname = $_REQUEST['stkName'];
				$stkdesc = $_REQUEST['stkDescription'];
				$stkvis = $_REQUEST['stkVisible'];

				//echo $stkname . $stkdesc . $stkvis;
				$sql = "INSERT INTO " . SWL_TABLE . " (swl_symbol, swl_description, swl_visible, swl_date)
								VALUES ('" . $stkname . "', '" . $stkdesc . "', '" . $stkvis . "', NOW())";
				$wpdb->get_results($sql);
			}
			break;
		case 'edit':
			if(empty($_REQUEST['save'])) {
				if(!empty($_REQUEST['stkID'])) {
					$sql = "SELECT swl_ID, swl_symbol, swl_description, swl_visible FROM " . SWL_TABLE . " WHERE swl_ID=" . $_REQUEST['stkID'];
					$stockedit = $wpdb->get_results($sql);
					$stockedit = $stockedit[0];
					$buttontext = "Save Stock &raquo;";
					$save = "&amp;save=yes";
				}
			} else {
				//check that we have the necessary variables
				if(!empty($_REQUEST['stkName'])) {
					$stkname = $_REQUEST['stkName'];
					$stkdesc = $_REQUEST['stkDescription'];
					$stkvis = $_REQUEST['stkVisible'];

					echo $stkname . $stkdesc . $stkvis;
					$sql = "UPDATE " . SWL_TABLE . "
									SET
									swl_symbol='" . $stkname . "',
									swl_description='" . $stkdesc . "',
									swl_visible='" . $stkvis . "'
									WHERE swl_ID=" . $_REQUEST['stockID'];
					$wpdb->get_results($sql);
					$action = "add";
				}
			}
			break;
		case 'delete':
			$sql = "DELETE FROM " . SWL_TABLE . " WHERE swl_ID=" . $_REQUEST['stkID'];
			$wpdb->get_results($sql);

			break;
	}

	?>

	<div class="wrap">

		<h2>Stock Watchlist (<a href="#addstock">Add New</a>)</h2>

		<table class="widefat">
			<thead>
				<tr>
					<th scope="col"><div style="text-align: center">Symbol</div></th>
					<th scope="col">Description</th>
					<th scope="col">Visible</th>
					<th colspan="3" style="text-align: center">Action</th>
				</tr>
			</thead>

			<tbody>

			<?php
			$stocks = $wpdb->get_results("SELECT swl_ID, swl_symbol, swl_description, swl_visible FROM " . SWL_TABLE);

			foreach ( $stocks as $stock ) {
				$class = ('alternate' == $class) ? '' : 'alternate';
			?>

				<tr id='post-7' class='<?php echo $class; ?>'>
					<th scope="row" style="text-align: center"><?php echo $stock->swl_symbol; ?></th>
					<td><?php echo $stock->swl_description; ?></td>
					<td><?php echo $stock->swl_visible; ?></td>
					<td><a href="edit.php?page=stocks-watchlist&amp;action=edit&amp;stkID=<?php echo $stock->swl_ID; ?>#addstock" class="delete"><?php echo __('Edit'); ?></a></td>
					<td><a href="edit.php?page=stocks-watchlist&amp;action=delete&amp;stkID=<?php echo $stock->swl_ID; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this stock?')"><?php echo __('Delete'); ?></a></td>
				</tr>

			<?php

				if ($alt = 'alternate') { $alt = ''; } elseif ($alt = '') { $alt = 'alternate'; }

			}
			?>

			</tbody>
		</table>

	</div>

	<div class="wrap">

		<h2>Add Stock</h2>

		<form name="addstock" id="addstock" method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?page=stocks-watchlist.php' . $save ?>">
			<input type="hidden" name="action" value="<?php echo $action ?>" />
			<input type="hidden" name="stockID" value="<?php echo $stkid ?>" />

			<table class="editform" width="100%" cellspacing="2" cellpadding="5">
				<tr>
					<th width="33%" scope="row" valign="top"><label for="stkName"><?php _e('Symbol:') ?></label></th>
					<td width="67%">
					<input name="stkName" id="stkName" type="text" value="<?php echo attribute_escape($stockedit->swl_symbol); ?>" size="40" /></td>
				</tr>
				<tr>
					<th scope="row" valign="top"><label for="stkDescription"><?php _e('Description:') ?></label></th>
					<td>
					<textarea name="stkDescription" id="stkDescription" cols="50"><?php echo attribute_escape($stockedit->swl_description); ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><label for="stkVisible"><?php _e('Visible:') ?></label></th>
					<td>
						<input type="radio" name="stkVisible" class="input" value="yes"
						<?php if ( empty($stockedit) || $stockedit->swl_visible=='yes' ) echo "checked" ?>/> Yes
						<br />
						<input type="radio" name="stkVisible" class="input" value="no"
						<?php if ( !empty($stockedit) && $stockedit->swl_visible=='no' ) echo "checked" ?>/> No
					</td>
				</tr>
			</table>

			<p class="submit"><input type="submit" name="submit" value="<?php echo $buttontext ?>" /></p>

		</form>

	</div>

	<?php
}


//hooks
add_action('admin_menu', 'tk_watchlist_admin');

//function to output watchlist to WP
function swl_output() {
	global $wpdb;
	$yahoo = new yahoo;

	$sql = "select * from " . SWL_TABLE . " where swl_visible='yes'";

	$results = $wpdb->get_results($sql);

	?>
	<ul>
	<?php

	foreach( $results as $result ) {
		$yahoo->get_stock_quote($result->swl_symbol);

	?>

		<li><a href='http://finance.yahoo.com/q?s=<?php echo $result->swl_symbol ?>' title='<?php echo $result->swl_description ?>' target="_blank"><?php echo $result->swl_symbol ?></a> - <?php echo $yahoo->last ?></li>

	<?php
	}

	?>
	</ul>
	<?php

}


Class yahoo
{
    /* Function. */
    function get_stock_quote($symbol)
    {
        // Yahoo! Finance URL to fetch the CSV data.
        $url = sprintf("http://finance.yahoo.com/d/quotes.csv?s=$symbol&f=sl1d1t1c1ohgv&e=.csv", $symbol);
        //$fp  = fopen($url, 'r');

				$ch = curl_init();
				$timeout = 10; // set to zero for no timeout
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$fp = curl_exec($ch);
				curl_close($ch);
				$fp = str_replace('"', '', $fp);

        if (!fp) {
            echo 'Error : cannot recieve stock quote data.';
        } else {
            $data = explode(',', $fp);
            $this->symbol = $data[0]; // Stock symbol.
            $this->last   = $data[1]; // Last Trade (current price).
            //$this->date   = $data[2];
            //$this->time   = $data[3];
            $this->change = $data[4]; // + or - amount change.
            //$this->open   = $data[5];
            //$this->high   = $data[6];
            //$this->low    = $data[7];
            //$this->volume = $data[8];
        }
    }
}

?>