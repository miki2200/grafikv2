<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] != true) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel e-grafik</title>
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/main.css">
    <script src="./assets/jquery.js"></script>
    <script src="./assets/js/main.js"></script>
    <script src="./assets/js/admin.js"></script>
</head>

<body>
    <?php require "menu.php";
    renderMenu("adminManage") ?>
    <div class="position-absolute bottom-0 end-0" id="alert_placeholder"></div>
    <div class="centered">
        <?php
        require_once "config.php";
        $conn = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);
        if ($conn->connect_error) {
            die('<script> showalert("Błąd bazy","' . $conn->connect_error . '","alert-danger" </script>');
        }
        $conn->query("set names utf8;");
        $sql = "SELECT akceptaction.id as id,akceptaction.user as username,akceptaction.name as imie,akceptaction.mail as mail,grupyZawodowe.Etykieta as grupaZawodowa from akceptaction LEFT JOIN grupyZawodowe ON grupyZawodowe.id = akceptaction.grupaZawodowa";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo '<h2>Użytkownicy do akceptacji</h2><table class="centered adminListing">';
            echo '<tr>';
            echo '<th>Id</th>';
            echo '<th>Nazwa Użytkownika</th>';
            echo '<th>Imię</th>';
            echo '<th>e-mail</th>';
            echo '<th>Grupa Zawodowa</th>';
            echo '<th>Akcept</th>';
            echo '<th>Remove</th>';
            echo '</tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['username'].'</td>';
                echo '<td>'.$row['imie'].'</td>';
                echo '<td>'.$row['mail'].'</td>';
                echo '<td>'.$row['grupaZawodowa'].'</td>';
                echo '<td><button onclick="aceptRequest('.$row['id'].')" class="btn btn-success">Akceptuj</button></td>';
                echo '<td><button onclick="deleteRequest('.$row['id'].')" class="btn btn-danger">Usuń</button></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        $sql = "SELECT users.id as id,users.user as username,users.name as imie,users.mail as mail,grupyZawodowe.Etykieta as grupaZawodowa from users LEFT JOIN grupyZawodowe ON grupyZawodowe.id = users.grupaZawodowa";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo '<h2>Aktualni użytkownicy</h2><table class="centered adminListing">';
            echo '<tr>';
            echo '<th>Id</th>';
            echo '<th>Nazwa Użytkownika</th>';
            echo '<th>Imię</th>';
            echo '<th>e-mail</th>';
            echo '<th>Grupa Zawodowa</th>';
            echo '<th>Remove</th>';
            echo '</tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['username'].'</td>';
                echo '<td>'.$row['imie'].'</td>';
                echo '<td>'.$row['mail'].'</td>';
                echo '<td>'.$row['grupaZawodowa'].'</td>';
                echo '<td><button onclick="deleteUser('.$row['id'].')" class="btn btn-danger">Usuń</button></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        $sql = "SELECT max(logLogowan.timestamp) as time,users.name as imie,users.user as username FROM logLogowan LEFT JOIN users on logLogowan.user = users.id GROUP BY logLogowan.user";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo '<h2>Ostatnie logowania</h2><table class="centered adminListing">';
            echo '<tr>';
            echo '<th>Data</th>';
            echo '<th>Użytkownik</th>';
            echo '<th>Imię</th>';
            echo '</tr>';
            while ($row = $result->fetch_assoc()) {
                $czas = new DateTime($row['time'],new DateTimeZone('UTC'));
                $czas -> setTimezone(new DateTimeZone('Europe/Warsaw'));
                echo '<tr>';
                echo '<td>'.$czas->format('d-m-Y H:i:s').'</td>';
                echo '<td>'.$row['username'].'</td>';
                echo '<td>'.$row['imie'].'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        $conn->close();
        ?>
</body>

</html>