<?php
	/*
	 * cliServer script
	 * 
	 * Arguments 
	 * $argv[1]	iCloud user name (Mandatory)
	 * $argv[2] iCloud user password (Mandatory)
	 * $argv[3] Reload time to get device location, in secounds. (Optional. Default 15 secounds)
	 *
	 * The new site will be created with these administrator credentials: 	
	 *	- Username: admin
	 *  - Password: admin 
	 *  - Administrator email: admin@example.com 
	 *
	 */

	define("DEFAULT_REALOAD_TIME",	15);

	include_once __DIR__ . '/PHP/trackerService.php';
	include_once __DIR__ . '/logger/logger.php';

	if (empty($argv[1])) {
		echo "\nPlease inform the iCloud username.";  
		exit(0);
	}

	if (empty($argv[2])) {
		echo "\nPlease inform the iCloud password.";  
		exit(0);
	}

	/*** default reload time tracking position ***/
	if (!empty($argv[3]) && is_numeric($argv[3])) {
		$reloadTime = $argv[3];  
	} else {
		$reloadTime = DEFAULT_REALOAD_TIME;  
	}

	/*** icloud username ***/
	$username = trim($argv[1]);
	/*** icloud password ***/
	$password = trim($argv[2]);
	
	try	{
		/*** a new trackerService instance ***/
	    $trackerService = trackerService::getInstance();
	    /*** set the params to start tracking location ***/
	    $trackerService->setParams($username, $password, $reloadTime);
	    while (true) {
	       	if ($trackerService->storeLocation()) {
	    		sleep($reloadTime); 
            } else {
                throw new Exception("Error Processing Request", 1);
                break;
            }
        }
    } catch(Exception $e) {
	    echo $e->getMessage();
	}
?>