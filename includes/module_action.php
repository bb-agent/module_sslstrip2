<? 
/*
	Copyright (C) 2013-2015 xtr4nge [_AT_] gmail.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["file"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];

if($service != "") {
    if ($action == "start") {
        // COPY LOG
        $exec = "$bin_cp $mod_logs $mod_path/includes/logs/$mod_name-".gmdate("Ymd-H-i-s").".log";
		exec_blackbulb($exec);
        
        $exec = "$bin_iptables -t nat -A PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10000";
		exec_blackbulb($exec);
        
        $exec = "$bin_sslstrip -a -s -l 10000 -w $mod_logs > /dev/null 2 &";
		exec_blackbulb($exec);
	
    } else if($action == "stop") {
    	$exec = "$bin_iptables -t nat -D PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10000";
		exec_blackbulb($exec);
		
		$exec = "ps aux|grep -E 'blackbulb.+sslstrip2.+sslstrip' | grep -v grep | awk '{print $2}'";
		exec($exec,$output);
		
		$exec = "kill " . $output[0];
		exec_blackbulb($exec);
    }
}


if ($install == "install_$mod_name") {

    $exec = "chmod 755 install.sh";
    exec_blackbulb($exec);

    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    exec_blackbulb($exec);
    
    header("Location: ../../install.php?module=$mod_name");
    exit;
}

if ($page == "status") {
    header('Location: ../../../action.php');
} else {
    header("Location: ../../action.php?page=$mod_name");
}

?>
