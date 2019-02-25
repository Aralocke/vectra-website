<?php
    if (!defined('IN_PARSERS') OR !IN_PARSERS) {
        exit;
    } else if (empty($_GET['loc'])) {
        print "ERROR: Missing argument `loc'\n";
    } else {
        $_Link      = array(
                        "m.wund.com",
                        "/cgi-bin/findweather/getForecast?query=" . str_replace(' ', "%20", $_GET['loc'])
                      );
        $cacheFile  = "Weather." . str_replace(' ', '_', $_GET['loc']);
        $src        = httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120);
        if (!is_object($src)) {
            if (strpos($src, "City Not Found")) {
                print "ERROR: No results for {$_GET['loc']}\n";
            } else if (strpos($src, "Updated:")) {
                $info = array();
                preg_match("@Updated: <b>([^<]+)</b><br />Observed at\s*<b>([^<]+)</b>\s*@i", $src, $info[]);
                preg_match_all('@<span class="nowrap"><b>([^<]+)</b>&deg;F</span>\s*/\s*<span class="nowrap"><b>([^<]+)</b>&deg;C</span>@i', $src, $info[]);
                preg_match("@<td>Humidity</td>\s*<td><b>([^<]+)</b></td>@i", $src, $info[]);
                preg_match('@<td>Wind</td>\s*<td>\s*<b>([^<]+)</b> at\s*<span class="nowrap"><b>([^<]+)</b>&nbsp;mph</span>\s*/\s*<span class="nowrap"><b>([^<]+)</b>&nbsp;km/h</span>@i', $src, $info[]);
                preg_match('@<td>Pressure</td>\s*<td>\s*<span class="nowrap"><b>([^<]+)</b>&nbsp;in</span>\s*/\s*<span class="nowrap"><b>([^<]+)</b>&nbsp;hPa</span>@i', $src, $info[]);
                preg_match_all('@<b>(Today|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)</b><br />\s*<img .+?/><br />\s*([^<]+)\s*<br /><br />@i', $src, $forecast);
                $headers    = array(
                                    "STATION", 
                                    "UPDATED", 
                                    "TEMPERATURE", 
                                    "WINDCHILL", 
                                    "HUMIDITY", 
                                    "DEWPOINT", 
                                    "WIND", 
                                    "PRESSURE"
                                   );
                $values     = array(
                                    $info[0][2], 
                                    $info[0][1],
                                    "{$info[1][1][0]}F/{$info[1][2][0]}C",
                                    "{$info[1][1][1]}F/{$info[1][2][1]}C",
                                    $info[2][1],
                                    "{$info[1][1][2]}F/{$info[1][2][2]}C",
                                    "{$info[1][1][0]}F/{$info[1][2][0]}C",
                                    "{$info[3][1]} @ {$info[3][2]}mph/{$info[3][3]}kph",
                                    "{$info[4][1]}in/{$info[4][2]}hPa"
                                   );
                for ($i = 0; $i < count($headers); $i++)
                    print "{$headers[$i]}: {$values[$i]}\n";
                $out = '';
                for ($i = 0; $i < count($forecast[1]); $i++) {
                    $end    = ($i < count($forecast[1])-1) ? " | ":"\n";
                    $cond   = str_replace(array("\n","\r", "\t"), array(' ', '', ''), $forecast[2][$i]);
                    $out   .= "{$forecast[1][$i]} ({$cond})";
                    if (isset($_GET['fc']) AND $_GET['fc'] == true)
                        $out .= $end;
                    else {
                        $out .= "\n";
                        break;
                    }
                }
                print "FORECAST: {$out}";
            }
        } else {
            cacheErrorHandler($src);
        }
    }
?>