<?php
/*
  Concerto Testing Platform,
  Web based adaptive testing platform utilizing R language for computing purposes.

  Copyright (C) 2011  Psychometrics Centre, Cambridge University

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!isset($ini))
{
    require_once'../Ini.php';
    $ini = new Ini(false);
}

class Setup
{

    public static function php_version_check()
    {
        $v = phpversion();
        $nums = explode(".", $v);
        if ($nums[0] < 5) return false;
        if ($nums[0] == 5 && $nums[1] < 3) return false;
        if ($nums[0] == 5 && $nums[1] >= 3) return true;
        if ($nums[0] > 5) return true;
    }

    public static function php_safe_mode_check()
    {
        return !ini_get("safe_mode");
    }

    public static function php_short_open_tag_check()
    {
        return ini_get("short_open_tag");
    }

    public static function file_paths_check($path)
    {
        if (file_exists($path) && is_file($path)) return true;
        else return false;
    }

    public static function directory_paths_check($path)
    {
        if (file_exists($path) && is_dir($path)) return true;
        else return false;
    }

    public static function directory_writable_check($path)
    {
        if (is_writable($path)) return true;
        else return false;
    }

    public static function rscript_check($path)
    {
        if ($path == "") return false;
        $array = array();
        $return = 0;
        exec("'" . $path . "' -e 1+1", $array, $return);
        return ($return == 0);
    }

    public static function url_exists_check($url, $slash_check = false)
    {
// Version 4.x supported
        if ($url == "") return false;
        if ($slash_check && substr($url, strlen($url) - 1, 1) != '/')
                return false;
        $handle = curl_init($url);
        if (false === $handle)
        {
            return false;
        }
        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
        curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15")); // request as if Firefox   
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
        $connectable = curl_exec($handle);
        curl_close($handle);
        return $connectable;
    }

    public static function mysql_connection_check($host, $port, $login, $password)
    {
        if (@mysql_connect($host . ":" . $port, $login, $password)) return true;
        else return false;
    }

    public static function mysql_select_db_check($db_name)
    {
        if (@mysql_select_db($db_name)) return true;
        else return false;
    }

    public static function r_package_check($path, $package)
    {
        $array = array();
        $return = 0;
        exec("'" . $path . "' -e 'library(" . $package . ")'", $array, $return);
        return ($return == 0);
    }

}
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Concerto Platform - test page</title>
        <link rel="stylesheet" href="../cms/css/styles.css" />

        <script type="text/javascript" src="../cms/js/lib/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="../cms/js/lib/jquery-ui/ui/minified/jquery-ui.min.js"></script>
        <script type="text/javascript" src="../cms/js/Methods.js"></script>
        <script src="../cms/js/lib/themeswitcher/jquery.themeswitcher.min.js"></script>
        <script src="../cms/lib/jfeed/build/dist/jquery.jfeed.js"></script>

        <script>
            
            $(function(){
                $('#switcher').themeswitcher({
                    loadTheme:"Cupertino",
                    imgpath: "../cms/js/lib/themeswitcher/images/",
                    onSelect:function(){
                    }
                });
            })
        </script>
    </head>

    <body>
        <div id="switcher"></div>
        <div align="center" class="ui-widget-header ui-corner-all margin"><h2>Concerto platform - <?= Ini::$version != "" ? "v" . Ini::$version . " - " : "" ?>test page</h2></div>
        <br/>
        <div align="center">
            <table class="margin">
                <thead>
                    <tr>
                        <th class="ui-widget-header">test description</th>
                        <th class="ui-widget-header">test result</th>
                        <th class="ui-widget-header">recommendation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ok = true;

                    if ($ok)
                    {
                        ?>
                    <script>
                        $(function(){
                            Methods.checkLatestVersion(function(isNewerVersion,version){
                                if(isNewerVersion==1) 
                                {
                                    $("#tdVersionCheckResult").removeClass("ui-state-highlight");
                                    $("#tdVersionCheckResult").addClass("ui-state-error");
                                    $("#tdVersionCheckResult").html("newer version is available: <b>v"+version+"</b>. Your current version <b>v<?= Ini::$version ?></b> <b style='color:red;'>IS OUTDATED</b>");
                                    $("#tdVersionCheckReccomendations").html("You can find the latest version at the link below:<br/><a href='http://code.google.com/p/concerto-platform'>http://code.google.com/p/concerto-platform</a>");
                                }
                                else
                                {
                                    $("#tdVersionCheckResult").html("your current version: <b>v<?= Ini::$version ?></b> <b style='color:green;'>IS UP TO DATE</b>");
                                }
                            },"../cms/lib/jfeed/proxy.php");
                        });
                    </script>
                    <tr>
                        <td class="ui-widget-content">Check for the latest <b>Concerto Platform</b> version</td>
                        <td id="tdVersionCheckResult" class="ui-state-highlight">...checking the latest version...</td>
                        <td id="tdVersionCheckReccomendation"class="ui-widget-content" align="center">-</td>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::php_version_check();
                        ?>
                        <td class="ui-widget-content">PHP version at least <b>v5.3</b></td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">your PHP version: <b><?= phpversion() ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Update your PHP to v5.3 or higher") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::php_safe_mode_check();
                        ?>
                        <td class="ui-widget-content">PHP <b>'safe mode'</b> must be turned <b>OFF</b></td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">your PHP <b>'safe mode'</b> is turned <b><?= ($test ? "OFF" : "ON") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Ask your server administrator to turn PHP 'safe mode' OFF") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::php_short_open_tag_check();
                        ?>
                        <td class="ui-widget-content">PHP <b>'short open tag'</b> must be turned <b>ON</b></td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">your PHP <b>'short open tag'</b> is turned <b><?= ($test ? "ON" : "OFF") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Ask your server administrator to turn PHP 'short open tag' ON") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        include'../SETTINGS.php';
                        $test = Setup::mysql_connection_check($db_host, $db_port, $db_user, $db_password);
                        ?>
                        <td class="ui-widget-content"><b>MySQL</b> connection test</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">Host: <b><?= $db_host ?></b>, Port: <b><?= $db_port ?></b>, Login: <b><?= $db_user ?></b> <b><?= ($test ? "CONNECTED" : "CAN'T CONNECT") ?></b> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Set <b>db_host, db_port, db_user, db_password</b> in /SETTINGS.php file.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::mysql_select_db_check($db_name);
                        ?>
                        <td class="ui-widget-content"><b>MySQL</b> database connection test</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>"><b>MySQL</b> database <b><?= $db_name ?></b> <b><?= ($test ? "IS CONNECTABLE" : "IS NOT CONNECTABLE") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Set <b>db_name</b> in <b>/SETTINGS.php</b> file. Check if database name is correct and if it is - check if MySQL user has required permissions to access this database.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    $ini = new Ini();
                    ?>
                    <tr>
                        <?php
                        $test = $ini->check_db_structure();
                        if (!$test) $ini->update_db_structure();
                        ?>
                        <td class="ui-widget-content"><b>MySQL</b> database tables structure test</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>"><b>MySQL</b> database <b><?= $db_name ?></b> tables structure <b><?= ($test ? "IS CORRECT" : "IS NOT CORRECT") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Tables structure has been automaticaly recreated. Any present previous Concerto tables has been removed. No further action is required.") ?></td>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::rscript_check(Ini::$path_r_script);
                        ?>
                        <td class="ui-widget-content"><b>Rscript</b> file path must be set.</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">your <b>Rscript</b> file path: <b><?= Ini::$path_r_script ?></b> <b><?= ($test ? "EXISTS" : "DOESN'T EXISTS") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center">
                            <?php
                            if ($test) echo"-";
                            else
                            {
                                ?>
                                Rscript file path not set or set incorrectly. If you don't have this file on your system it could mean that your <b>R</b> installation is of version lower than <b>v2.12</b>. If that's the case you should update your R to higher version and set your Rscript path then.<br/>
                                Usually the Rscript file path is <b>/usr/bin/Rscript</b>. Set your Rscript path in <b>/SETTINGS.php</b> file.
                        <?php } ?>
                        </td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::url_exists_check(Ini::$path_external, true);
                        ?>
                        <td class="ui-widget-content">You must set your application URL address.</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">Your application URL: <b><?= Ini::$path_external ?></b> <b><?= ($test ? "IS CORRECT" : "IS INCORRECT") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center">
                            <?php
                            if ($test) echo"-";
                            else
                            {
                                ?>
                                Application URL address must be set. It isn`t right now or it is set incorrectly. It must contain protocol prefix and end with a slash character. Set your application URL address in <b>/SETTINGS.php</b> file.
                        <?php } ?>
                        </td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::directory_writable_check(Ini::$path_temp);
                        ?>
                        <td class="ui-widget-content"><b>/temp</b> directory path must be writable</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">your <b>/temp</b> directory: <b><?= Ini::$path_temp ?></b> <b><?= ($test ? "IS WRITABLE" : "IS NOT WRITABLE") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Set <b>/temp</b> directory rigths to 0777.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $path = Ini::$path_internal . "cms/js/lib/fileupload/php/files";
                        $test = Setup::directory_writable_check($path);
                        ?>
                        <td class="ui-widget-content"><b>/cms/js/lib/fileupload/php/files</b> directory path must be writable</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">your <b>/cms/js/lib/fileupload/php/files</b> directory: <b><?= $path ?></b> <b><?= ($test ? "IS WRITABLE" : "IS NOT WRITABLE") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Set <b>/cms/js/lib/fileupload/php/files</b> directory rigths to 0777.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::directory_writable_check(Ini::$path_internal_media);
                        ?>
                        <td class="ui-widget-content"><b>/media</b> directory path must be writable</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">your <b>/media</b> directory: <b><?= Ini::$path_internal_media ?></b> <b><?= ($test ? "IS WRITABLE" : "IS NOT WRITABLE") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Set <b>/media</b> directory rigths to 0777.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::directory_writable_check(Ini::$path_internal . "cms/lib/ckeditor/plugins/pgrfilemanager/PGRThumb/cache");
                        ?>
                        <td class="ui-widget-content"><b>/cms/lib/ckeditor/plugins/pgrfilemanager/PGRThumb/cache</b> directory path must be writable</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>">your <b>/cms/lib/ckeditor/plugins/pgrfilemanager/PGRThumb/cache</b> directory: <b><?= Ini::$path_internal . "cms/lib/ckeditor/plugins/pgrfilemanager/PGRThumb/cache" ?></b> <b><?= ($test ? "IS WRITABLE" : "IS NOT WRITABLE") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Set <b>/cms/lib/ckeditor/plugins/pgrfilemanager/PGRThumb/cache</b> directory rigths to 0777.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::r_package_check(Ini::$path_r_script, "session");
                        ?>
                        <td class="ui-widget-content"><b>session</b> R package must be installed.</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>"><b>session</b> package <b><?= ($test ? "IS INSTALLED" : "IS NOT INSTALLED") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Install <b>session</b> package to main R library directory.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::r_package_check(Ini::$path_r_script, "RMySQL");
                        ?>
                        <td class="ui-widget-content"><b>RMySQL</b> R package must be installed.</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>"><b>RMySQL</b> package <b><?= ($test ? "IS INSTALLED" : "IS NOT INSTALLED") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Install <b>RMySQL</b> package to main R library directory.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
                <?php } ?>

                <?php
                if ($ok)
                {
                    ?>
                    <tr>
                        <?php
                        $test = Setup::r_package_check(Ini::$path_r_script, "catR");
                        ?>
                        <td class="ui-widget-content"><b>catR</b> R package must be installed.</td>
                        <td class="<?= ($test ? "ui-state-highlight" : "ui-state-error") ?>"><b>catR</b> package <b><?= ($test ? "IS INSTALLED" : "IS NOT INSTALLED") ?> - <b style="color:<?= ($test ? "green" : "red") ?>"><?= ($test ? "PASSED" : "FAILED") ?></b></td>
                        <td class="ui-widget-content" align="center"><?= ($test ? "-" : "Install <b>catR</b> package to main R library directory.") ?></td>
                    <?php $ok = $ok && $test; ?>
                    </tr>
<?php } ?>
                </tbody>
            </table>
        </div>
        <br/>
        <?php
        if (!$ok)
        {
            ?>
            <h1 class="ui-state-error" align="center">Please correct your problems using recommendations and run the test again.</h1>
            <?php
        }
        else
        {
            ?>
            <h1 class="" align="center" style="color:green;">Test completed. Every item passed correctly.</h1>
            <h1 class="ui-state-highlight" align="center" style="color:blue;">IT IS STRONGLY RECOMMENDED TO DELETE THIS <b>/setup</b> DIRECTORY NOW ALONG WITH ALL IT'S CONTENTS FOR SECURITY REASONS!</h1>
            <h2 class="" align="center"><a href="<?= Ini::$path_external . "cms/index.php" ?>">click here to launch Concerto Platform panel</a> - if this is fresh installation of Concerto then default admin account is <b>login:admin/password:admin</b></h2>
<?php } ?>
        <div style="display:none;" id="divGeneralDialog">
        </div>
    </body>
</html>