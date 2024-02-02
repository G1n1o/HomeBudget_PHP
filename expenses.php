<?php

session_start();
if (!isset($_SESSION['logged_id'])) {
  header('Location: balance.php');
  exit();
}

require_once('database.php');

if (isset($_POST['price'])) {

    $amount = $_POST['price'];
    $date = $_POST['date'];
    $category = $_POST['category'];
    $comment =$_POST['comment'];

    $expense = $db->prepare('INSERT INTO expenses VALUES (NULL, :user_ID, :category, :amount, :date, :comment)');
    $expense->bindValue(':user_ID', $_SESSION['logged_id'], PDO::PARAM_INT);
    $expense->bindValue(':category', $category, PDO::PARAM_INT);
    $expense->bindValue(':amount', $amount, PDO::PARAM_STR);
    $expense->bindValue(':date', $date, PDO::PARAM_STR);
    $expense->bindValue(':comment', $comment, PDO::PARAM_STR);
    $expense->execute();

    $accepted= 'Dodano wydatek';

}
 
$expensesQuery = $db->query("SELECT id, name  FROM expenses_category_assigned_to_users WHERE user_id = {$_SESSION['logged_id']}");
$expenses = $expensesQuery->fetchAll();


?>

<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HomeBudget</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>

  <nav class="nav">
    <div class="nav-logo">
      <svg class="nav-logo-icon" fill="#fff" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg" stroke="#fff">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M820 257q-13-8-53-20-36-11-48-17l-4-2q-20-11-31-15-17-7-32-10-37-5-85 15-73 30-115 68t-63 91q-5 12-8 24l-4 15q-1 4-2 5.5t-6 2.5q-11 10-38 21-15 6-47.5 17T244 467q-12 7-24 21-8 9-20 27-9 14-21 46l-7 17q-4 12-11 20-4 4-13 10-6 4-8.5 7t-2.5 8q0 9 9 12t21-.5 21-12.5 16-25q5-10 12-30 8-25 13-34 10-17 25.5-26.5t35-8.5 34.5 14q14 11 20 26 3 6 7 27 5 22 9 34 6 19 15 29 16 19 39 28 25 11 56 6 24-3 46 3t32.5 17.5 5 23T527 721q-12 2-35 2h-1q-12 0-23-1-2-8-6-13.5t-8-8.5l-4-2q-20-14-49-17-26-2-51 5 7-13 5-32.5T343 624q-26-25-77-23.5T193 627q-9 11-9.5 31.5T192 690q-30-4-57 2-35 8-46 32-6 13-2 38 2 20 27.5 33t58 13 57-14 25.5-35q2-29-7-42 29 5 59-3-5 8-4 33l1 5q1 22 26 36 29 15 66 13 40-2 63-24l4-6q4-8 6-20 10 2 37 2 23 0 44.5-6.5T580 729q10-14 9-40 0-15-5.5-44t-2.5-35q16-11 48-5 19 3 54 16 21 7 27 8 34 5 74-10 29-10 58-28 18-12 23-17l10-10q37-43 39-119 2-60-21-109-25-52-73-79zM170 769q-20 0-38-6-27-9-27-26.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T208 763q-18 6-38 6zm100-89q-21 0-39-6-26-8-26-25.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T308 674q-18 6-38 6zm117 81q-21 0-39-6-26-8-26-25.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T425 755q-18 6-38 6zm194-178q-4-40-21-77-18-42-45-64-20-16-49-26-22-8-44-10-18-2-22.5.5t-5-1 5.5-9.5 16-10q13-6 29-8 39-4 73 10 39 16 63 54 16 26 20.5 54.5t-1 52T581 583z">
          </path>
        </g>
      </svg>
      <a href="balance.php">Home<span>Budget</span></a>
    </div>

    <div class="nav-buttons one">
      <div class="nav-user">
        Zalogowany: <?= $_SESSION['username']; ?>
      </div>

      <a href="logout.php"> <button class="log-out">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">


            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M19 23H11C10.4477 23 10 22.5523 10 22C10 21.4477 10.4477 21 11 21H19C19.5523 21 20 20.5523 20 20V4C20 3.44772 19.5523 3 19 3L11 3C10.4477 3 10 2.55229 10 2C10 1.44772 10.4477 1 11 1L19 1C20.6569 1 22 2.34315 22 4V20C22 21.6569 20.6569 23 19 23Z"
              fill="#fff"></path>
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M2.48861 13.3099C1.83712 12.5581 1.83712 11.4419 2.48862 10.6902L6.66532 5.87088C7.87786 4.47179 10.1767 5.32933 10.1767 7.18074L10.1767 9.00001H16.1767C17.2813 9.00001 18.1767 9.89544 18.1767 11V13C18.1767 14.1046 17.2813 15 16.1767 15L10.1767 15V16.8193C10.1767 18.6707 7.87786 19.5282 6.66532 18.1291L2.48861 13.3099ZM4.5676 11.3451C4.24185 11.7209 4.24185 12.2791 4.5676 12.6549L8.1767 16.8193V14.5C8.1767 13.6716 8.84827 13 9.6767 13L16.1767 13V11L9.6767 11C8.84827 11 8.1767 10.3284 8.1767 9.50001L8.1767 7.18074L4.5676 11.3451Z"
              fill="#fff"></path>

          </svg>
          Wyloguj</button></a>
    </div>
  </nav>

  <header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
      <div class="container-fluid">
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#navbarsExample08" aria-controls="navbarsExample08" aria-expanded="false"
          aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse justify-content-md-center collapse" id="navbarsExample08">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="income.php"><i class="bi bi-cash-coin"></i>Dodaj przychód</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="expenses.php">
                <i class="bi bi-basket"></i>Dodaj wydatek</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="balance.php"><i class="bi bi-bar-chart"></i>Przeglądaj bilans</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="settings.html"><i class="bi bi-gear"></i>Ustawienia</a>
            </li>

            <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Action</a></li>
                      <li><a class="dropdown-item" href="#">Another action</a></li>
                      <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                  </li> -->

          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <div class="hero">
      <div class="hero-shadow"></div>
      <div class="hero-main">
        <div class="wrapper">
          <div class="top">
            Dodaj wydatek
          </div>
          <form method="post" class="bottom">
            <label for="price">Kwota</label>
            <input type="number" name="price" class="input" id="price" step="0.01" required  placeholder="Podaj kwotę">
            <label for="date">Data</label>
            <input type="date" name="date" class="input" id="date" required>
            <label for="category">Kategoria</label>
            <select id="category" name="category" required>
              <option disabled selected>- wybierz kategorię -</option>
              <?php
                foreach ($expenses as $expense) {
                    echo "<option value={$expense['id']}>{$expense['name']}</option>";
                }
                ?>
             
            </select>
            <label for="comment">Komantarz</label>
            <input type="text" name="comment" class="input" id="comment" placeholder="Wpisz komentarz">


            <div class="buttons">
              <?php
                
                if(isset($accepted)) {
                  echo '<p>'.$accepted.'</p>';
                  unset($accepted);
              }       

              ?>
              <input type="submit" value= "Dodaj" class="btn blue-btn">
            </div>

          </form>

        </div>
      </div>
    </div>
  </main>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
</body>

</html>