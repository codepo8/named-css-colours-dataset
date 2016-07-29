<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Guess the named CSS colour!</title>
  <link rel="stylesheet" href="csscolournames.css">
</head>
<body>
<section>
<form method="post" action="index.php">
<h1>Guess the named CSS colour!</h1>

<?php
include('api.php');
$levels = array('easy-40-20','normal-80-15','hard-120-10','expert-139-10');
foreach (array('c','tc') as $a) {
    if ( isset($_POST[$a]) && !in_array($_POST['c'], array_keys($colours))){
        die ('Invalid colour');
    }
}
if(isset($_POST['ac']) && !preg_match("/^[_0-9A-F]*$/",$_POST['ac'])) {
    die('invalid colour collection');
}
$gamestate = $_POST['gs'];
if(isset($_POST['lv'])) {
    $p = split('-', $levels[$_POST['lv']]);
    $n = intval($p[1]);
    $rc = 0;
    $lv = intval($_POST['lv']);
    $am = intval($p[2]);
    $m = 0;
    $gamestate = 'p';
    $rand = array_rand($colours, $n);
    $tc = $rand[array_rand($rand,1)];
    $ac = join($rand,'_');
}
if(isset($_POST['c'])){
    $n = intval(split('-', $levels[$_POST['lv']])[1]);
    $ac = $_POST['ac'];
    $am = intval($_POST['am']);
    $m = intval($_POST['m']);
    $rc = intval($_POST['rc']);
    $l = $_POST['lv'];
    $tc = $_POST['tc'];
    $c = $_POST['c'];
    $gamestate = 'p';
    if ($m > $am - 2) {
        $gamestate = 'o';
    } else {
        if ($c != $tc) {
            $gamestate = 'p';
            $m++;
            $nopecol = $colours[$c];
            $rand = split('_', $ac);
        } else {
            $gamestate = 'p';
            $rc++;
            $rand = array_rand($colours, $n);
            $tc = $rand[array_rand($rand,1)];
            $ac = join($rand,')');
        }
    }
}
?>

<?php if (!$gamestate) { ?>
<header>
<p id="intro">A simple game to see if you can detect named CSS colours.</p>
<div id="query"></div><div id="result"></div><div id="gameover"></div><div id="list"></div>
</header>
<div id="levels">
<p>Choose your level:</p>
<ul id="levelbuttons">
    <?php foreach($levels as $c => $lv) { 
        echo '<li><button name="lv" data-level="'.$lv.'" value="'.$c.'">'.split('-',$lv)[0].'</button></li>';   
    }
    ?>
</ul>
</div>
<?php } ?>
<?php if ($gamestate === 'o') { ?>
    <p>Game over</p>
    <p>You scored <?php echo $rc?> on the <?php echo split('-',$levels[$l])[0]?> level</p>
    <p>Again?</p>
    <ul id="levelbuttons">
        <?php foreach($levels as $c => $lv) { 
            echo '<li><button name="lv" data-level="'.$lv.'" value="'.$c.'">'.split('-',$lv)[0].'</button></li>';   
        }
        ?>
    </ul>
<?php } ?>
<?php if ($gamestate === 'p') { ?>
<p>Find the colour <?php echo $colours[$tc];?> moves left: <?php echo $am-$m?></p>
<?php if($nopecol) {?>
<p>Nope… <?php echo $nopecol?></p>
<?php }?>

<?php
$list = '<ul class="cols" data-target="'.$tc.'x'.$colours[$tc].'">';
        foreach ($rand as $r) {
           $list .= '<li><button name="c" value="'.$r.'"  style="background:#'.$r.'"></li>';
        }
    $list .= '</ul>';
echo $list; ?>

<?php foreach (array('ac','tc','am','m','lv','rc') as $f) {
    echo '<input type="hidden" name="'.$f.'" value="'.$$f.'">';
}?>
<?php } ?>
</form>
</section>

<?php if (!$gamestate) { ?>
<script src="csscolourgame.js"></script>
<?php } ?>
</body>
</html>
