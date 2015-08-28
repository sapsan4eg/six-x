<?php
/**
 * six-x Version
 * @var string
 */
	define('VERSION', '0.6.0.9');
/*
 * ------------------------------------------------------
 *  Load the framework constants
 * ------------------------------------------------------
 */
	require_once('../config.php');
/*
 * ------------------------------------------------------
 *  Load the startup file
 * ------------------------------------------------------
 */
	require_once(DIR_SYSTEM . 'startup.php');
/*
 * ------------------------------------------------------
 *  Start the conveyor
 * ------------------------------------------------------
 */
	$conveyor = new Conveyor();
/* End of file index.php */
/* Location: ./index.php */