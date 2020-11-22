<?php
wp_enqueue_style('mainstyle', get_template_directory_uri() . '/style.css', false, null, 'all');
wp_enqueue_style('custom', get_template_directory_uri() . '/library/css/shortcodes.css', false, null, 'all');

// edit by David-el Yosef and Daphne Ly
wp_enqueue_style('persons', get_template_directory_uri() . '/library/css/persons.css', false, null, 'all');
wp_enqueue_style('contact', get_template_directory_uri() . '/library/css/contact.css', false, null, 'all');
wp_enqueue_style('recipes', get_template_directory_uri() . '/library/css/recipes.css', false, null, 'all');
wp_enqueue_style('savta', get_template_directory_uri() . '/library/css/savta.css', false, null, 'all');

if (function_exists('register_nav_menus')) {
    register_nav_menus(
        array(
            'primary-menu' => __('Primary Menu')
        )
    );
}

// Our custom post type function
function create_person()
{

    register_post_type('person',
        // CPT Options
        array(
            'labels' => array(
                'name' => __('אדם'),
                'singular_name' => __('אדם'),
                'add_new' => __('Add new Person'),
                'add_new_item' => __('Add new Person'),
                'edit_item' => __('Edit Person')
            ),
            'taxonomies' => array('post_tag'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'person'),
            'menu_icon' => 'dashicons-universal-access',
            'supports' => array(
                'title',
                'author',
                'thumbnail'
            )
        )
    );
}

add_action('init', 'create_person');

// Our custom post type function
function create_recipe()
{

    register_post_type('recipe',
        // CPT Options
        array(
            'labels' => array(
                'name' => __('מתכון'),
                'singular_name' => __('מתכון'),
                'add_new' => __('Add new Recipe'),
                'add_new_item' => __('Add new Recipe'),
                'edit_item' => __('Edit Recipe')
            ),
            'taxonomies' => array('post_tag'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'recipe'),
            'menu_icon' => 'dashicons-carrot',
            'supports' => array(
                'title',
                'author',
                'thumbnail'
            )
        )
    );
}

add_action('init', 'create_recipe');

function load_my_script(){
    wp_register_script(
        'my_script',
        get_template_directory_uri() . '/js/myscript.js',
        array( 'jquery' )
    );
    wp_enqueue_script( 'my_script' );
}
add_action('wp_enqueue_scripts', 'load_my_script');

function partners_slider_func()
{
    $n = 5;
    $slider = '
        <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>';

    $slider .= '<div class="row">
          <div class="works-slideshow text-center">
            <div class="owl-item">
              <div class="col-sm-12 mb-sm-20 wow bounceIn">
                <div class="slide-item">';

    for ($i = 1; $i <= $n; $i++) {
        $slide = get_field('partner_' . $i);
        if ($slide['logo'] != "") {
            $url = ($slide['url'] != "") ? $slide['url'] : "#";
            $slider .= '<div class="slide-image">
                    <a href="'.$url.'" target="_blank">
                        <img src="' . $slide['logo'] . '" alt="' . $slide['title'] . '" />
                        <p>'.$slide["title"].'</p>
                    </a>
                  </div>';
        }
    }

    $slider .= '</div>
              </div>
            </div>
          </div>
        </div>';
//
    $slider .= '<script>
        slick_slider();
    </script>';

    $slider .= '<style>
        .works-slideshow .slide-image{
            text-align: center;
        }
        .works-slideshow .slide-image img{
            margin: auto;
        }
        .works-slideshow .slide-image p{
            font-family: Open Sans Hebrew;
            font-style: normal;
            font-weight: normal;
            font-size: 18px;
            line-height: 25px;
            text-align: center;
            color: #2F3032;
            padding-top: 10px;
        }    
        .works-slideshow .slide-image:hover img{
            filter: brightness(55%);
        }    
        .works-slideshow .slide-image:hover p{
            text-decoration-line: underline;
            color: #53B5D3;
        }

		.slick-prev, .slick-next {
			z-index: 100 !important;
			font-size: 0 !important;
			line-height: 0 !important;
			position: absolute !important;
			top: 50% !important;
			display: block !important;
			width: 20px !important;
			height: 20px !important;
			padding: 0 !important;
			-webkit-transform: translate(0, -50%) !important;
			-ms-transform: translate(0, -50%) !important;
			transform: translate(0, -50%) !important;
			cursor: pointer !important;
			color: transparent !important;
			border: none !important;
			outline: none !important;
			background: transparent !important;
		}
		.slick-prev:before, .slick-next:before {
			    color: #53B5D3 !important;
                font-size: 32px;
//                box-shadow: 0px 0px 4px #272727;
//                border-radius: 20px;
			}
        </style>';

    return $slider;
}

add_shortcode('partners_slider', 'partners_slider_func');



function custom_grid_func( $atts ){
    $types = ["recipe", "person"];

    $attributes = shortcode_atts( array(
        'count' => '',
        'type' => '',
        'author' => '',
        'button' => '',
        'button_url' => '',
		'category'	=> '',
		'cycle'		=> '',
		'counter'	=> false
    ), $atts );

    if(intval($attributes['count']) > 0){
        $count = intval($attributes['count']);
    }
    elseif (intval($attributes['count']) == -1){
        $count = -1;
    }
    else{
        $count = 6;
    }

    $ret = "";

    if($attributes['type'] != "" && in_array($attributes['type'], $types)){
        $type = $attributes['type'];
        if($type == "recipe"){
            $extra_class = "recipe";
        } else{
            $extra_class = "";
        }

        $args = array(
            'post_type'=> $type,
            'numberposts' => $count,
            'order'    => 'ASC'
        );
		
		if ( 'recipe' === $type && ! empty( $attributes['category'] ) ) {
			$args['meta_query'] = [
				[
					'key'	=> 'category',
					'value'	=> $attributes['category']
				]
			];
		} elseif ( 'person' === $type && ! empty( $attributes['cycle'] ) ) {
			$args['meta_query'] = [
				[
					'key'	=> 'cycle',
					'value'	=> $attributes['cycle']
				]
			];
		}

        if($attributes['author'] == "show" && $type == "recipe"){
            $args["meta_query"] = array(
                array(
                    "key" => "author", // name of custom field
                    "value" => get_the_ID(),
                    "compare" => "LIKE"
                )
            );
        } elseif ($attributes['author'] == "my" && $type == "recipe"){
            $args['post__not_in'] = array(get_the_ID());
            $args["meta_query"] = array(
                array(
                    "key" => "author", // name of custom field
                    "value" =>  get_field("author", get_the_ID())[0],
                    "compare" => "LIKE"
                )
            );
        }

        $persons = get_posts($args);

        if($attributes['author'] == "show"  || $attributes['counter'] ){
			$label = 'person' === $type ? 'אנשים' : 'מתכונים';
            $recipes_count = "<div class='recipes_count'>".count($persons)." $label</div>";
        } else{
            $recipes_count = "";
        }

        $ret .= $recipes_count . "<div class='persons_grid'>";
        foreach ($persons as $person) {

            $ret .= "<div class='person_block ".$extra_class."'>";
            $ret .= "<a class='' href='".get_permalink($person)."'>";
            if($type == "recipe" && get_field("is_for_child", $person)){
                $ret .= "<div class='for_child'><img src='".get_template_directory_uri()."/images/star.png'>מנת ילדות</div>";
            }
            $ret .= "<div class='category'>".get_field("category", $person)."</div>";
            $ret .= "<img class='photo' src='".get_field("photo", $person)."' alt='photo'>";
            $ret .= "<div class='name'>".get_field("name", $person)."</div>";
            $ret .= "<div class='description'>".get_field("description", $person)."</div>";
            $ret .= "<div class='recipes'>".get_field("recipes", $person)."</div>";
            $ret .= "</a>";
            $ret .= "<div class='share'>שיתוף<img src='".get_template_directory_uri()."/images/share.png'></div>";
            $ret .= "</div>";
        }
        $ret .= "</div>";

	    if(isset( $attributes['button'] ) && $attributes['button'] != "" ){
	    	$button_url = isset( $attributes['button_url'] ) ? $attributes['button_url'] : "javascript:void(0)";
		    $ret .= "<a class='persons_next' href='" . $button_url . "'>" . $attributes['button'] . "<i aria-hidden=\"true\" class=\"fas fa-arrow-left\"></i></a>";
	    }
    }

    return $ret;
}
add_shortcode('custom_grid', 'custom_grid_func');

add_shortcode( 'volunteers_grid', 'volunteers_grid_func' );
function volunteers_grid_func( $atts ) {
	$atts = shortcode_atts( [
		'cycle'	=> '',
		'count'	=> '-1',
	], $atts, 'volunteers_grid' );
	$args = [
		'post_type'	=> 'person',
		'numberposts'	=> $atts['count']
	];
	if ( ! empty( $atts['cycle'] ) ) {
		$args['meta_query'] = [
			[
				'key'	=> 'cycle',
				'value'	=> $atts['cycle']
			]
		];
	}
	$volunteers = "<div class='persons_grid'>";
	foreach ( get_posts( $args ) as $person ) {
		$volunteers .= "<div class='person_block'>";
		$volunteers .= "<img class='photo' src='" . get_field( 'volunteer_picture', $person ) . "' alt='volunteer_picture'>";
		$volunteers .= "<div class='name'>" . get_field( 'volunteer', $person ) . "</div>";
		$volunteers .= "<div class='description'>" . get_field( 'volunteer_description', $person ) . "</div>";
		$volunteers .= ' התנדב אצל: ' . "<a class='' href='" . get_post_permalink( $person ) . "'>" .  get_the_title( $person ) . "</a>" . '. ';
		$volunteers .= "</div>";
	}
	$volunteers .= "</div>";
	return $volunteers;
}


add_filter( 'wpseo_breadcrumb_links', 'my_breadcrumb_filter_function' );
function my_breadcrumb_filter_function( $crumbs ) {
	if ( is_singular( 'person' ) ) {
		$crumbs[0]['url'] = str_replace( 'person', 'persons', $crumbs[0]['url'] );
		$crumbs[1] = '';
	}
	if ( is_singular( 'recipe' ) ) {
		$crumbs[0]['url'] = str_replace( 'recipe', 'recipes', $crumbs[0]['url'] );
		$crumbs[1] = '';
	}
	return $crumbs;
}