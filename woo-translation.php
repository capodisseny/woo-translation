


function sal_curent_object($option = false,  $lang = NULL){

  return apply_filters("sal/current_object", get_queried_object() , $option , $lang);
}


//Bricks plugin
add_action("plugins_loaded", function( $option , $lang){

  if(!class_exists("Bricks\Query")) return;

  add_filter("sal/current_object", function(){

    if($option == "sub"){
      return \Bricks\Query::get_loop_object( )
    }
    return \Bricks\Query::get_loop_object(  ) ?: get_queried_object();
  } );
})

//transalge from get_field
function trans_field($fieldname, $option = false, $lang = NULL){

    $lang = $lang ?? pll_current_language() ;

    

    $object = sal_current_object($fieldname, $option = false, $lang = NULL)
  
    $field =  $fieldname . '_' . $lang;

    $isWp = (  $object instanceof WP_Post) || ( $object instanceof WP_Term) || ( $object instanceof WP_User);
		
	
		  if($isWp) return  get_field($field,  $object);

		
       return   $object[$fieldname][$lang] ?? NULL;


}
