<?php
// Copyright 2018 Dave Diamond
// See LICENSE

function rel2abs($base0, $rel0) {
  // init
  $base = parse_url($base0);
  $rel = parse_url($rel0);

  // init paths so we can blank the base path if we have a rel host
  if (array_key_exists("path", $rel)) {
    $relPath = $rel["path"];
  } else {
    $relPath = "";
  }
  if (array_key_exists("path", $base)) {
    $basePath = $base["path"];
  } else {
    $basePath = "";
  }

  // if rel has scheme, it has everything
  if (array_key_exists("scheme", $rel)) {
    return $rel0;
  }

  // else use base scheme
  if (array_key_exists("scheme", $base)) {
    $abs = $base["scheme"];
  } else {
    $abs = "";
  }

  if (strlen($abs) > 0) {
    $abs .= "://";
  }

  // if rel has host, it has everything, so blank the base path
  // else use base host and carry on
  if (array_key_exists("host", $rel)) {
    $abs .= $rel["host"];
    if (array_key_exists("port", $rel)) {
      $abs .= ":";
      $abs .= $rel["port"];
    }
    $basePath = "";
  } else if (array_key_exists("host", $base)) {
    $abs .= $base["host"];
    if (array_key_exists("port", $base)) {
      $abs .= ":";
      $abs .= $base["port"];
    }
  }

  // if rel starts with slash, that's it
  if (strlen($relPath) > 0 && $relPath[0] == "/") {
    return $abs . $relPath;
  }

  // split the base path parts
  $parts = array();
  $absParts = explode("/", $basePath);
  foreach ($absParts as $part) {
    array_push($parts, $part);
  }

  // remove the first empty part
  while (count($parts) >= 1 && strlen($parts[0]) == 0) {
    array_shift($parts);
  }
  
  // split the rel base parts
  $relParts = explode("/", $relPath);

  if (count($relParts) > 0 && strlen($relParts[0]) > 0) {
    array_pop($parts);
  }

  // iterate over rel parts and do the math
  $addSlash = false;
  foreach ($relParts as $part) {
    if ($part == "") {
    } else if ($part == ".") {
      $addSlash = true;
    } else if ($part == "..") {
      array_pop($parts);
      $addSlash = true;
    } else {
      array_push($parts, $part);
      $addSlash = false;
    }
  }

  // combine the result
  foreach ($parts as $part) {
    $abs .= "/";
    $abs .= $part;
  }

  if ($addSlash) {
    $abs .= "/";
  }

  if (array_key_exists("query", $rel)) {
    $abs .= "?";
    $abs .= $rel["query"];
  }
  
  if (array_key_exists("fragment", $rel)) {
    $abs .= "#";
    $abs .= $rel["fragment"];
  }
  
  return $abs;
}
