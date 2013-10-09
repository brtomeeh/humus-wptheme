<?php
class Humus_Header_Menu_Walker extends Walker_Nav_Menu {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		if(in_array('explore', $item->classes)) {
			$item_output .= $this->explore();
		}
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function explore() {

		$output = '';

		$axes = get_terms('axis', array('hide_empty' => false));
		$sections = get_terms('section', array('hide_empty' => false));

		if(!$axes && !$sections)
			return '';

		// axes
		if($axes) {

			$output .= '<li class="axes">';
			$output .= '<h3>' . __('Axes', 'humus') . '</h3>';
			$output .= '<ul>';
			foreach($axes as $axis) {
				$output .= '<li><a href="' . get_term_link($axis, 'axis') . '" title="' . $axis->name . '">' . $axis->name . '</a></li>';
			}
			$output .= '</ul>';
			$output .= '</li>';

		}

		if($sections) {

			$output .= '<li class="sections">';
			$output .= '<h3>' . __('Sections', 'humus') . '</h3>';
			$output .= '<ul class="clearfix">';
			foreach($sections as $section) {
				$menu_icon = get_field('term_menu_icon', 'section_' . $section->term_id);
				$menu_icon_attr = '';
				if($menu_icon)
					$menu_icon_attr = 'style="background-image: url(' . $menu_icon . ');"';
				$output .= '<li><a href="' . get_term_link($section, 'section') . '" title="' . $section->name . '" ' . $menu_icon_attr . '>' . $section->name . '</a></li>';
			}
			$output .= '</ul>';
			$output .= '</li>';

		}

		return '<ul class="explore-menu"> ' . $output . '</ul>';

	}

}
?>