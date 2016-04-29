<?php

function native_search_expressions_change()
{
    global $wp_rewrite;

    $wp_rewrite->pagination_base = __('strona', 'otwarte2013');
    $wp_rewrite->search_base = get_post(get_option('oz_search_page'))->post_name;

    $wp_rewrite->flush_rules();
}

add_action('init', 'native_search_expressions_change');

function echo_search_results()
{
    //cook for query
    global $folders_page, $current_user;

    $selected = esc_attr(get_the_author_meta('user_postshow', $current_user->ID));
    empty($selected) ? $selected = 20 : '';

    isset($_POST['posts_per_page']) ? $selected = $_POST['posts_per_page'] : '';
    isset($_POST['paged']) ? $paged = $_POST['paged'] : $paged = 1;
    isset($_POST['post_type']) ? $post_type = $_POST['post_type'] : $post_type = array('document', 'monument');
    isset($_POST['order']) ? $order = $_POST['order'] : $order = 'ASC';
    isset($_POST['orderby']) ? $orderby = $_POST['orderby'] : $orderby = 'name';
    isset($_POST['doctype']) ? $doctype = $_POST['doctype'] : $doctype = '';

    // get my_folders_specific
    isset($_POST['folders_page']) ? $folders_page = $_POST['folders_page'] : $folders_page = false;
    isset($_POST['f_name']) ? $f_name = $_POST['f_name'] : $f_name = false;

    // folder title for my folders page
    if ($folders_page && $f_name) :
        echo '<div class="row">';
        echo '<div class="large-12 columns">';
        echo '<h2 id="my-fol-title">';
        echo '<span class="ico mycats-ico"> </span> ';
        echo '<span> ' . $f_name . '</span>';
        echo '</h2>';
        echo '</div>';
        echo '</div>';
    endif;

    echo '<div class="row objects-cnt">';

    global $otwarte_objects;

    $args = array(
        "post_type" => $post_type,
        "posts_per_page" => $selected,
        "post_status" => 'publish',
        "paged" => $paged,
        "order" => $order,
        "orderby" => $orderby
    );

    if (isset($_POST['orderby']) && ($_POST['orderby'] == 'meta_value')) {
        $args['meta_key'] = 'oz_year';
    }

    if (isset($_POST['posts'])) {
        $args['post__in'] = $_POST['posts'];
    }

    if (isset($_POST['doctype'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'document_type',
                'field' => 'slug',
                'terms' => $_POST['doctype']
            )
        );
    }

    if (isset($_POST['doctype'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'document_type',
                'field' => 'slug',
                'terms' => $_POST['doctype']
            )
        );
    }

    if (isset($_POST['montype'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'monument_type',
                'field' => 'slug',
                'terms' => $_POST['montype']
            )
        );
    }

    if (isset($_POST['keyword'])) {
        $args['s'] = $_POST['keyword'];
    }

    $otwarte_objects = new WP_Query($args);
    //echo $otwarte_objects->query_vars['paged'];
    $count = 0;
    if ($otwarte_objects->have_posts()):
        while ($otwarte_objects->have_posts()): $otwarte_objects->the_post();
            get_template_part('partials/object', 'thumb');
            $count++;
            echo $count % 4 == 0 ? '<div class="row"></div>' : '';
        endwhile;
    endif;
    if ($otwarte_objects->post_count == 0) {
        echo '<h3 class="ta-center">';
        _e('Nie posiadamy obiektów spełniających kryteria wyszukiwania', 'otwarte2013');
        echo '</h3>';
    }
    echo '</div>';

    echo '<div class="pagination-centered">';
    echo '<ul class="pagination">';

    global $otwarte_objects, $wp_rewrite;
    //var_dump($otwarte_objects);
    $big = 999999999; // need an unlikely integer
    $args = array(
        'base' => '%_%',//str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '/' . $wp_rewrite->search_base . '/#' . __('strona', 'otwarte2013') . '%#%',
        'total' => $otwarte_objects->max_num_pages,
        'current' => max($paged, get_query_var('paged')),
        'show_all' => false,
        'end_size' => 3,
        'mid_size' => 2,
        'prev_next' => True,
        'prev_text' => __('&laquo;'),
        'next_text' => __(' &raquo;'),
        'type' => 'list',
    );
    echo paginate_links($args);
    posts_nav_link(' &#183; ', 'previous page', 'next page');
    echo '</ul>';
    echo '</div>';
}


?>