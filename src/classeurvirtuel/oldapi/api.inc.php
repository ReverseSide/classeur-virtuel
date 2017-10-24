<?php


function print_array( &$a, $str = "" )
{   if ( $str[ 0 ] ) echo "$str =" ;
    echo ' array( ' ;
    foreach ( $a as $k => $v ) {
        echo "[$k]".' => ';
        if ( is_array( $v ) )   print_array( $v ) ; 
        else					echo "<strong>$a[$k]</strong> " ;
    }
	echo ')  ' ;
}

function return_array( $array, $html = false, $level = 0 ) {
    $space = $html ? "&nbsp;" : " " ;
    $newline = $html ? "<br />" : "\n" ;
    $spaces = "" ;
    for ( $i = 1; $i <= 6; $i++ ) { $spaces .= $space ; }
    $tabs = $spaces ;
    for ( $i = 1; $i <= $level; $i++ ) { $tabs .= $spaces ; }
    $output = "Array" . $newline . $newline ;
    foreach ( $array as $key => $value ) {
        if ( is_array( $value ) ) {
            $level++ ;
            $value = return_array( $value, $html, $level ) ;
            $level-- ;
        } $output .= $tabs . "[" . $key . "] => " . $value . $newline ;
    } return $output ;
}

function api_redirect( $location )
{	http_response_code( 302 );
	header( "location:$location" );
}

// S'il manque des champs dans la requête, retourner une erreur 400 (Bad request)
function api_get_ordie( $param ) //, $errtext )
{	$ret = isset( $_GET[''.$param.''] );
	if ( ! $ret )
	{   	http_response_code( 400 );
			die( "Required parameters are: $param" );
	} else 	return $_GET[''.$param.''] ;
}
function api_post_ordie( $param ) //, $errtext )
{	$ret = isset( $_POST[''.$param.''] );
	if ( ! $ret )
	{   	http_response_code( 400 );
			die( "Required parameters are: $param" );
	} else 	return $_POST[''.$param.''] ;
}
function api_get( $param ) //, $errtext )
{	$ret = isset( $_GET[''.$param.''] );
	if ( ! $ret )	return "";
	else 			return $_GET[''.$param.''] ;
	
}

function api_post( $param ) //, $errtext )
{	$ret = isset( $_POST[''.$param.''] );
	if ( ! $ret )	return "";
	else 			return $_POST[''.$param.''] ;
	
}
?>