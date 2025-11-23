<?php
    include ('page-init.php');
?>
<html><head>
</head>
<body>
    <h4>Updating view....</h4>
</body>
</html>
<?php
if (isset($_POST['poolId'])){
    $user->changeLastPoolID($_POST['poolId']);
    $user->unsetDb();
    unset($_SESSION['user']);
    $_SESSION['user']=serialize($user);
    cbRedirect("/", false, false);
}
?>