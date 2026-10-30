<?php
/**
 * Otomatik site içi bağlantılama: haber/makale içeriğinde geçen kategori ve
 * etiket isimlerini ilk geçtikleri yerde ilgili arşiv sayfasına bağlar.
 * SEO ve kullanıcı gezinmesi için — her yazıda en fazla 4 otomatik link.
 */

function veng_auto_internal_links( $content ) {
	if ( ! is_singular( array( 'post', 'makale' ) ) || is_admin() || is_feed() ) {
		return $content;
	}

	global $post;
	if ( ! $post ) {
		return $content;
	}

	$current_url = get_permalink( $post );
	$targets = array();

	foreach ( get_categories() as $cat ) {
		$url = get_category_link( $cat->term_id );
		if ( $url !== $current_url ) {
			$targets[ $cat->name ] = $url;
		}
	}
	foreach ( get_tags() as $tag ) {
		$url = get_tag_link( $tag->term_id );
		if ( $url !== $current_url ) {
			$targets[ $tag->name ] = $url;
		}
	}

	if ( ! $targets ) {
		return $content;
	}

	// Uzun terimler önce denensin ("Yapay Zeka" "Zeka"dan önce eşleşsin).
	uksort( $targets, function ( $a, $b ) {
		return mb_strlen( $b ) <=> mb_strlen( $a );
	} );

	$max_links = 4;
	$linked = 0;
	$used = array();

	// İçeriği etiketlere göre parçala; yalnızca düz metin parçalarında link ekle,
	// mevcut <a>...</a> blokları ve diğer HTML etiketlerine dokunma.
	$parts = preg_split( '/(<a[^>]*>.*?<\/a>|<[^>]+>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( ! $parts ) {
		return $content;
	}

	foreach ( $parts as &$part ) {
		if ( $linked >= $max_links ) {
			break;
		}
		if ( '' === $part || '<' === $part[0] ) {
			continue; // HTML etiketi / mevcut link bloğu — dokunma.
		}

		// Bir parça içinde birden fazla eşleşme olabilir; her eşleşmeden sonra
		// arama imlecini eklenen <a> bloğunun SONRASINA taşı — yoksa kısa bir
		// terim (ör. "Amed") az önce eklenen uzun eşleşmenin ("Koma Amed")
		// href'i içindeki metne tekrar eşleşip iç içe bozuk HTML üretir.
		$offset = 0;
		while ( $linked < $max_links ) {
			$best_name  = null;
			$best_start = null;
			$best_end   = null;

			foreach ( $targets as $name => $url ) {
				if ( isset( $used[ $name ] ) || mb_strlen( $name ) < 4 ) {
					continue;
				}
				$pattern = '/\b(' . preg_quote( $name, '/' ) . ')\b/iu';
				if ( preg_match( $pattern, $part, $m, PREG_OFFSET_CAPTURE, $offset ) ) {
					$start = $m[0][1];
					if ( null === $best_start || $start < $best_start ) {
						$best_start = $start;
						$best_end   = $start + strlen( $m[0][0] );
						$best_name  = $name;
					}
				}
			}

			if ( null === $best_name ) {
				break;
			}

			$matched_text = substr( $part, $best_start, $best_end - $best_start );
			$replacement  = '<a href="' . esc_url( $targets[ $best_name ] ) . '" class="veng-auto-link">' . $matched_text . '</a>';
			$part         = substr( $part, 0, $best_start ) . $replacement . substr( $part, $best_end );

			$used[ $best_name ] = true;
			$linked++;
			$offset = $best_start + strlen( $replacement );
		}
	}
	unset( $part );

	return implode( '', $parts );
}
add_filter( 'the_content', 'veng_auto_internal_links', 20 );
