<?php
// Copyright 2020 Catchpoint Systems Inc.
// Use of this source code is governed by the Polyform Shield 1.0.0 license that can be
// found in the LICENSE.md file.

/**
* Determine the colors to use for each request
*/
function GetRequestColors($content_type, $thumbnail, $is_mime,
                          $is_state = false, $url = null) {
    if (GetSetting('new_colors')) {
        // new color scheme (proposed by Steve Souders)
        $colors = array(
            'error'    => array(204,0,0),
            'warning'  => array(255,56,166),
            'wait'     => array(255,254,214),
            'dns'      => array(55, 123, 53),
            'connect'  => array(177,73,62),
            'ssl'      => array(69, 110, 175),
            'ttfb'     => array(208,137,14),
            'download' => array(118,71,130),
            'js'       => array(255,173,255),
            );
    } else {
        // classic colors
        if ($thumbnail) {
            $colors = array(
                'wait'     => array(255,254,214),
                'dns'      => array(48,150,158),
                'connect'  => array(255, 157, 66),
                'ssl'      => array(213,102, 223),
                'ttfb'     => array(64, 255, 64),
                'download' => array(52,150,255),
                'js'       => array(255,173,255),
                );
        } else {
            $colors = array(
                'wait'     => array(255,254,214),
                'dns'      => array(0,123,132),
                'connect'  => array(255, 123, 0),
                'ssl'      => array(207, 37, 223),
                'ttfb'     => array(0, 255, 0),
                'download' => array(0,123,255),
                'js'       => array(255,173,255),
                );
        }
    }
    if ($is_mime) {
        $base_color = GetMimeColor($content_type, $url);
        $ttfb_color = ScaleRgb($base_color, 0.65);
        $colors['ttfb'] = $ttfb_color;
        $colors['download'] = $base_color;

        if (!$is_state) {
            $colors['dns'] = $ttfb_color;
            $colors['connect'] = $ttfb_color;
            $colors['ssl'] = $ttfb_color;
        }
    }
    return $colors;
}

/**
* Return an array of the defined colors for various mime types
*/
function GetMimeColor($mime, $url = null) {
    $mime_colors = MimeColors();
    $type = ContentType($mime, $url);
    if (!array_key_exists($type, $mime_colors)) {
        $type = 'other';
    }
    return $mime_colors[$type];
}

/**
* Return an array of the defined colors for various mime types
*/
function MimeColors() {
    $colors = array(
        'html'  => array(130,181,252),
        'js'    => array(254,197,132),
        'css'   => array(178,234,148),
//        'text'  => array(254,241,132),
        'image' => array(196,154,232),
        'flash' => array(45,183,193),
        'font' => array(255,82,62),
        'video' => array(33, 194, 162),
        'other' => array(196,196,196)
        );
    return $colors;
}

/**
* Bucket the content types into our known categories
*/
function ContentType($mime, $url = null) {
    $pos = strpos($mime, ';');
    if ($pos !== false)
      $mime = substr($mime, 0, $pos);
    $content_type = 'other';
    if (stripos($mime, 'javascript') !== false ||
        stripos($mime, 'ecmascript') !== false ||
        !strncasecmp($mime, 'text/js', 7)) {
        // Collapse the various javascript content types together.
        $content_type = 'js';
    } elseif (!strncasecmp($mime, 'text/css', 8)) {
        $content_type = 'css';
    } elseif (!strncasecmp($mime, 'text/html', 9)) {
        $content_type = 'html';
//    } elseif (!strncasecmp($mime, 'text/', 5)) {
//        $content_type = 'text';
    } elseif (!strncasecmp($mime, 'image/', 6)) {
        $content_type = 'image';
    } elseif (!strncasecmp($mime, 'video/', 6)) {
        $content_type = 'video';
    } elseif (!strcasecmp($mime, 'application/x-shockwave-flash')) {
        $content_type = 'flash';
    } elseif (stripos($mime, 'font') !== false) {
        $content_type = 'font';
    } elseif (isset($url)) {
      $url = parse_url($url, PHP_URL_PATH);
      if (preg_match('/(?P<ext>\.[a-zA-Z0-9]+)$/', $url, $matches)) {
        $ext = strtolower($matches['ext']);
        if ($ext == '.js')
          $content_type = 'js';
        elseif ($ext == '.js')
          $content_type = 'css';
        elseif ($ext == '.htm' || $ext == '.html')
          $content_type = 'html';
        elseif ($ext == '.png' || $ext == '.gif' || $ext == '.jpg' || $ext == '.avif' || $ext == '.jxl')
          $content_type = 'image';
        elseif ($ext == '.swf')
          $content_type = 'flash';
        elseif ($ext == '.eot' || $ext == '.ttf' ||
                $ext == '.woff' || $ext == '.woff2' ||
                $ext == '.otf')
          $content_type = 'font';
	else if ($ext == '.mp4' ||  
		 $ext == '.f4v' || $ext == '.flv')
	  $content_type = 'video';
      }
    }
    
    return $content_type;
}
?>