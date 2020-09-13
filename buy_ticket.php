<?php
include("dbconnect.php");
include_once ("get_test.php");
include_once ("main.css");
//include_once ("dantous.js");

$movie_id=$_GET["movie_id"];
$seat_string=$_GET["seat_string"];
$total=$_GET["total"];
$seat=str_split($seat_string,3);
$check=$_GET["check"];
//echo $seat[($total-1)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>蛋頭售票系統好棒棒</title>	
	<script type="text/javascript" src='dantous.js'></script>
</head>
<body>
	<div id="mystyle" class="mystyle">
		<?php
			$selectSql = "SELECT * FROM online_movie where movie_id='$movie_id'";//呼叫query方法(SQL語法)
			$memberData = $connect->query($selectSql);//有資料筆數大於0時才執行
			$online_movie = $memberData->fetch_assoc();
			
			$movie_name=$online_movie['movie_name'];
			$selectSql = "SELECT * FROM movie where movie_name='$movie_name'";
			$memberData = $connect->query($selectSql);//有資料筆數大於0時才執行
			$movie = $memberData->fetch_assoc();
		?>
			<div class='row'>
				<div class='photo_detail'>		
					<img src="<?php echo $movie['photo'];?>" width="450px" height="600px" >
				</div>
				<div class='column'>
					<a href="http://120.126.16.74/108_oose/select_movie.php"><div class='flowchart'>選擇電影</div></a>
					<a href="http://120.126.16.74/108_oose/movie_detail.php?movie_name=<?php echo $movie_name;?>"><div class='flowchart'>電影資訊</div></a>
					<a href="http://120.126.16.74/108_oose/choose_seat.php?movie_id=<?php echo $movie_id;?>"><div class='flowchart'>選擇座位</div></a>
					<div class='main_flowchart'>確認訂票</div>
				</div>
			</div>
			<div class='movie_name_detail'>		
				<?php echo $movie_name;?>
			</div>
			<div class='row'>
				<div class='ticket_bounding'>
					以下是您的訂單：<br>
					<?php 
						echo "電影名稱：".$movie_name."<br>";
						echo "放映日期：".$online_movie['date']."<br>";
						echo "放映影廳：".$online_movie['theater']."<br>";
						echo "入場時間：".$online_movie['start_time']."<br>"."<br>";
						echo "您選擇的座位是：  ";
						for($i=0;$i<$total;$i++)
						{
							echo $seat[$i];
							if($i!=($total-1)) echo "、";
							else echo "<br>";
						}
						echo "總共費用：".$movie['price']*$total."<br>";
					?>
				</div>
				

				<?php 
					$success_seat="";
					$fail_seat="";
					if($check==1)
					{
						echo "<div class='ticket_result'>";
						for($i=0;$i<$total;$i++)
						{
							$current_seat=$seat[$i];
							$updatatheater_Sql = "UPDATE theater SET $current_seat=1 WHERE movie_id=$movie_id";
							$connect->query($updatatheater_Sql);
							
							$selectSql = "SELECT * FROM theater where movie_id='$movie_id'";//呼叫query方法(SQL語法)
							$memberData = $connect->query($selectSql);//有資料筆數大於0時才執行
							$theater = $memberData->fetch_assoc();
							
							$check_ticket_sql="SELECT * FROM ticket WHERE movie_id='$movie_id' AND choose_seat='$current_seat' AND state=0";
							$numberdata=$connect->query($check_ticket_sql);
							$theater_id=$theater['theater_id'];
							$insertticket_Sql = "INSERT INTO ticket (ticket_id,movie_id,choose_seat,state,user_id,theater_id) VALUES ('','$movie_id','$current_seat',0,1,'$theater_id')";
							if ($numberdata->num_rows == 0) {
							  $connect->query($insertticket_Sql);
							  $success_seat=$success_seat.$current_seat." ";
							}
							else $fail_seat=$fail_seat.$current_seat." ";
							//$test='abbhsgdagh';
							/*$success_seat=str_replace("A",111,$success_seat);
							$success_seat=str_replace("B",222,$success_seat);
							$fail_seat=str_replace("A",111,$fail_seat);
							$fail_seat=str_replace("B",222,$fail_seat);*/
							
							//$seat_tojs=$success_seat.$fail_seat;
							
						}
						if($success_seat=="") $success_seat=404;
						if($fail_seat=="") $fail_seat=404;
						$js_function="ticket_result(".$success_seat.",".$fail_seat.");";
						//echo $js_function;
						if($fail_seat==404) echo "訂單完成 恭喜你!!";
						else if($success_seat==404) echo "被別人搶先一步囉QAQ";
						else 
						{
							echo "你只搶到一部份的票哈哈!<br>";
							echo "成功的有：".$success_seat;
							echo "失敗的有：".$fail_seat;
						}
						echo "</div>";
					}
					else
					{
						echo "<div class='buy_ticket' onclick='";
						$seat_string_tojs=str_replace("A",91,$seat_string);
						$seat_string_tojs=str_replace("B",92,$seat_string_tojs);
						$js_function="check(".$movie_id.",".$seat_string_tojs.",".$total.")";
						echo $js_function;
						echo"'>";
						echo "確定嗎?";
					}
				?>
				</div>
			</div>
    </div>
</body>
</html>