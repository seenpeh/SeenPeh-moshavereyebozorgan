<?php
    $input = file_get_contents('people.json');
    $bozorgan = json_decode($input);
    $names_array = array();
    $message_array = array();
    $i = 0;
    foreach ($bozorgan as $key => $value) {
        $names_array[$i] = $key;
        $i++;
    }
    $answer = fopen("messages.txt", "r");
    for ($i = 0; feof($answer) == 0; $i++) {
        $message_array[$i] = fgets($answer);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $en_name = $_POST['person'];
        foreach($bozorgan as $key => $value) {
            if ($key == $en_name) {
                $fa_name = $value;
                break;
            }
        }
        $question = $_POST['question'];
        $hashed = hash('adler32', $question." ".$en_name);
        $hashed = hexdec($hashed);
        $msg = $message_array[$hashed % 16];
    }
    else {
        $question = '';
        $msg = "سوال خود را بپرس";
        $en_name = $names_array[array_rand($names_array)];
        foreach($bozorgan as $key => $value) {
            if ($key == $en_name) {
                $fa_name = $value;
                break;
            }
        }
    }
    if(!preg_match("/^آیا/iu", $question) || (!preg_match("/\?$/i" , $question)  &&  !preg_match("/؟$/u" , $question))) {
        $msg = "سوال درستی پرسیده نشده";
    }
    if (empty($question)){
        $title = '';
        $msg = "سوال خود را بپرس";
    }
    else {
        $title = 'پرسش:';
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="styles/default.css">
    <title>مشاوره بزرگان</title>
</head>

<body>
    <p id="copyright">تهیه شده برای درس کارگاه کامپیوتر،دانشکده کامییوتر، دانشگاه صنعتی شریف</p>
    <div id="wrapper">
        <div id="title">
            <span id="label"><?php echo $title ?></span>
            <span id="question">
                <?php echo $question ?>
            </span>
        </div>
        <div id="container">
            <div id="message">
                <p>
                    <?php echo $msg ?>
                </p>
            </div>
            <div id="person">
                <div id="person">
                    <img src="images/people/<?php echo "$en_name.jpg" ?>"/>
                    <p id="person-name">
                        <?php print $fa_name ?>
                    </p>
                </div>
            </div>
        </div>
        <div id="new-q">
            <form method="post" action="<?php print $_SERVER['PHP_SELF'];?>">
                سوال
                <input type="text" name="question" value="<?php echo $question ?>" maxlength="150" placeholder="..." />
                را از
                <select name="person">
                    <?php
                        $input = file_get_contents('people.json');
                        $list = json_decode($input);
                        foreach($list as $key => $value){
                            if ($en_name == $key) {
                                print "<option value=$key selected> $value </option>";
                            }
                            else{
                                print "<option value=$key> $value </option>";
                            }
                        }
                    ?>
                </select>
                <input type="submit" value="بپرس"/>
            </form>
        </div>
    </div>
</body>

</html>