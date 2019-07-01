<?php
error_reporting(0);
$db_conn=mysqli_connect("localhost","root","","");
$result = mysqli_query($db_conn,"SHOW DATABASES");
// $result = mysqli_query($db_conn,"SHOW DATABASES"); while ($row = mysqli_fetch_array($result)) { echo $row[0]."<br>"; }
?>
<html>
    <head>
        <title>NZTech IT Solutions - Database Backup</title>
        <link rel="stylesheet" href="boot.css">
        <script src="jquery.js"></script>
    
    </head>
    <body>
        <div class="container">
            <div class="row" style="margin-top: 20px">
                <div class="col-md-2"></div>
                <div class="col-md-7 text-center">
                    <h3>NZTech IT Solutions <br> MYSQL <span style="font-weight: bold; color: orange">DATABASE</span><span style="font-weight: bold; color: 6B78AF"> BACKUP </span> SYSTEM</h3></center>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="hostname">Hostname:</label>
                        <input type="text" class="form-control" id="hostname" name="hostname" value="localhost" required placeholder="Type localhost or your server ip">
                    </div>

                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" value="" required placeholder="Type your Database USERNAME">
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" value=""  placeholder="Type your Database PASSWORD">
                    </div>

                    <div class="form-group">
                        <label for="database">SELECT DATABASE:</label>     
                     
                        <select name="database" id="database" class="form-control">
                        <option value="">--Please Select Database--</option>
                        <?php
                         while ($row = mysqli_fetch_array($result)) { echo "<option>" . $row[0]."</option>"; }

                         ?>
                        </select>
                        <div style="margin-top: 10px; margin-bottom: 10px">  
                         <input type="text" class="form-control" id="database2" style="display: none" name="database2" value=""  placeholder="Type DB name to Download">
                         <input type="checkbox" onClick="dbSecond()" class="checkbox_check"><span id="check_label"> Input DB name</span> </div>
                       
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary pull-right btn-lg" name="submit">Proceed</button>
                        </div>

                </form>
                </div>
            </div><!--row-->

            <div class="row" style="margin-top: 20px">
                <div class="col-md-12 pull-right">
                    <span class="pull-right"><small>Created by: NZTech IT Solutions | Nikko Zabala | nikkozabala.com</small> </span>
                </div>
            </div>
        </div><!--container-->
    </body>

</html>

<script>

function dbSecond()
{
    console.log('ssss');
    if ($('input.checkbox_check').is(':checked')) {
        $('#database2').show();
        $('#database').hide();
        $('#check_label').text(" Select From List");
        
    }else{
        $('#database2').hide();
        $('#database').show();
        $('#check_label').text(" Input DB name");
    }
}


</script>


<?php
try {
    
    
    
if ( isset($_POST['submit']) )
{

/* 
##### EXAMPLE #####
   EXPORT_DATABASE("localhost","user","pass","db_name" ); 
   
##### Notes #####
     * (optional) 5th parameter: to backup specific tables only,like: array("mytable1","mytable2",...)   
     * (optional) 6th parameter: backup filename (otherwise, it creates random name)
     * IMPORTANT NOTE ! Many people replaces strings in SQL file, which is not recommended. READ THIS:  http://itask.software/tools/wordpress-migrator
     * If you need, you can check "import.php" too
*/
// by https://github.com/tazotodua/useful-php-scripts //
function EXPORT_DATABASE($host,$user,$pass,$name,       $tables=false, $backup_name=false)
{ 
	set_time_limit(3000); $mysqli = new mysqli($host,$user,$pass,$name); $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
	$queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }	if($tables !== false) { $target_tables = array_intersect( $target_tables, $tables); } 
	$content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `".$name."`\r\n--\r\n\r\n\r\n";
	foreach($target_tables as $table){
		if (empty($table)){ continue; } 
		$result	= $mysqli->query('SELECT * FROM `'.$table.'`');  	$fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows; 	$res = $mysqli->query('SHOW CREATE TABLE '.$table);	$TableMLine=$res->fetch_row(); 
		$content .= "\n\n".$TableMLine[1].";\n\n";   $TableMLine[1]=str_ireplace('CREATE TABLE `','CREATE TABLE IF NOT EXISTS `',$TableMLine[1]);
		for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) {
			while($row = $result->fetch_row())	{ //when started (and every after 100 command cycle):
				if ($st_counter%100 == 0 || $st_counter == 0 )	{$content .= "\nINSERT INTO ".$table." VALUES";}
					$content .= "\n(";    for($j=0; $j<$fields_amount; $j++){ $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); if (isset($row[$j])){$content .= '"'.$row[$j].'"' ;}  else{$content .= '""';}	   if ($j<($fields_amount-1)){$content.= ',';}   }        $content .=")";
				//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
				if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";}	$st_counter=$st_counter+1;
			}
		} $content .="\n\n\n";
	}
	$content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
	$backup_name = $backup_name ? $backup_name : $name.'___('.date('MdY').'_'.date('His').').sql';
	ob_get_clean(); header('Content-Type: application/octet-stream');  header("Content-Transfer-Encoding: Binary");  header('Content-Length: '. (function_exists('mb_strlen') ? mb_strlen($content, '8bit'): strlen($content)) );    header("Content-disposition: attachment; filename=\"".$backup_name."\""); 
	echo $content; exit;
}

$hostname = $_POST['hostname'];
$username = $_POST['username'];
$password = $_POST['password'];

if(empty($_POST['database']) AND empty($_POST['database2']))
{
    echo "<script>alert('Please Select Database to Download')</script>";
}else
{
    if(empty($_POST['database2']))
    {
       $database = $_POST['database'];
    }else
    {
       $database = $_POST['database2'];
    }
    EXPORT_DATABASE($hostname,$username,$password,$database ); 
}


}; // If Isset.
} catch (\Throwable $th) {
    echo "There was an error Please Check if the database selected is Empty";
}


?>