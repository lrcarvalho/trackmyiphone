<?php

	require 'trackerService.php';

	/*** icloud username ***/
	$username = "";
	/*** icloud password ***/
	$password = "";
	/*** default reload time tracking position ***/
	$reloadTime = 5;

	try	{
	    
	    /*** the file to write to ***/
	    while (true) {
	    	/*** a new trackerService instance ***/
	    	$trackerService = trackerService::getInstance();
	    	/*** set the params to start tracking location ***/
	    	$trackerService->setParams($username, $password, $reloadTime);
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