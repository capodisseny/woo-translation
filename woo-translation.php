<?php
/**
 * Plugin Name: Sal Translation 
 * Description: Translate WooCommerce products and categories
 * Version: 1.4
 * Author: Your Name
 */

function sal_curent_object($option = false,  $lang = NULL){

  return apply_filters("sal/current_object", get_queried_object() , $option , $lang);
}


//Bricks plugin
add_action("plugins_loaded", function( ){

  if(!class_exists("Bricks\Query")) return;

    add_filter("sal/current_object", function($option , $lang){

      if($option == "sub"){
        return \Bricks\Query::get_loop_object( );
      }
      return \Bricks\Query::get_loop_object(  ) ?: get_queried_object();
  } , 10, 2);
});

//transalge from get_field
function trans_field($fieldname, $option = false, $lang = NULL){

    $lang = $lang ?? pll_current_language() ;

    $field =  $fieldname . '_' . $lang;

    if($option == "sub"){

      $object = sal_current_object( $option = false, $lang = NULL);

      $isWp = (  $object instanceof WP_Post) || ( $object instanceof WP_Term) || ( $object instanceof WP_User);

      if($isWp) return  get_field($field,  $object);

		
      return   $object[$fieldname][$lang] ?? NULL;
    }
    
		
    return get_field($field, $option);
	
	
}




function sal_trans_field($name, $option = false, $lang = NULL){
   
    $transTitle = trans_field("title");

    if($transTitle) return $transTitle;

}



    
// add_filter("woocommerce_product_title", function ($title){

//   $transTitle = trans_field("title");

//   // if($transTitle)
//    return $transTitle + "new";
  
//   return $title;

// });

add_filter("woocommerce_short_description", function ($description ){

  $newdesc = trans_field("short_description");

  if($newdesc) {
      
      return $newdesc ;
  }
  
  return $description ;

});


add_filter("the_title", function ($post_title, $post_id ){

  $transTitle = trans_field("title") ;

   if($transTitle)  return $transTitle;
  
  return $post_title;

}, 90, 2);

add_filter("the_content", function ($content){

  $newContent = trans_field("content");

  if($newContent) return $newContent;
  
  return $content;

});




//TERMS

//term link

//term names

add_filter('get_term', function ($term, $taxonomy) {
  // return the term untuched if you are in the admin
  if( is_admin() )   return $term;

  // numeric value of term in another taxonomy
  $meta_value = trans_field("title",  $term) ;

  if ($meta_value)  $term->name = $meta_value;

  
  return $term;
}, 10, 2);

add_filter('term_name', function ($name, $term) {
  // return the term untuched if you are in the admin
  if( is_admin() )    return $name;

  // numeric value of term in another taxonomy
  $meta_value = trans_field("title", $term) ;

  if ($meta_value) {
      return $meta_value;
  }
  return "asdasdaaaaaa";

  return $name;
}, 10, 2);




function sal_remove_language_from_products(){
    if(!isset($_GET["reset"])) return;


    $q = new WP_Query(array(
        "fields" => "ids",
        "post_type" => "product",
        "posts_per_page" => -1,
    ));
    


    if($q->posts){

        foreach($q->posts as $id){

            // pll_set_post_language($id, "")
            // wp_remove_object_terms()
            wp_delete_object_term_relationships($id, "language");
        }
    }


    $q = new WP_Term_Query(array(
         "fields" => "ids",
        "taxonomy" => "product_cat",
    ));


    if($q->terms){

        foreach($q->terms as $id){

            wp_delete_object_term_relationships($id, "language");
        }
    }
}