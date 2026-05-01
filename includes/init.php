<?php 
/**
 * 
 * HELPER FUNCTIONS - REGISTER CLASS
 * Praktikum Aplikasi Web - Universitas Tidar
 * 
 */

spl_autoload_register(function ($class) {
	$path = str_replace('\\', '/', $class);
	$path = explode('/', $path);
	// Mencari posisi class pada folder yang sesuai
	$folder = strtolower($path[0]);
	$class = end($path);
	require_once ROOT_PATH . "$folder" . "/" . "$class" . ".php";
	

});

