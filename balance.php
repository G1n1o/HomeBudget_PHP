<?php
session_start();

require_once('database.php');

if (!isset($_SESSION['logged_id'])) {

    if (isset($_POST['email'])) {

        $email = filter_input(INPUT_POST,'email');
        $password = filter_input(INPUT_POST,'password');
        
        $_SESSION['inputedEmail'] = $email;
               

        $userQuery = $db->prepare('SELECT id, username, password  FROM users WHERE email = :login');
        $userQuery->bindValue(':login', $email, PDO::PARAM_STR);    
        $userQuery->execute();

      

        $user = $userQuery->fetch();
        
         if($user && password_verify($password, $user['password'])) {
            $_SESSION['logged_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            unset ($_SESSION['bad_attempt']);

        } else {
            $_SESSION['bad_attempt'] = true;
            header('Location: login.php');
            exit();
        }

    } else {
        header('Location: login.php');
        exit();
    }
  }
//

 if (!isset($_SESSION['date_begin'])){
header('Location: current_month.php');
exit();
}



$incomesQuery = $db->prepare("SELECT incomes.amount, incomes_category_assigned_to_users.name, incomes.date_of_income, incomes.income_comment FROM incomes JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id WHERE incomes.user_id = :user_ID
AND incomes.date_of_income BETWEEN :date_begin AND :date_end ORDER BY incomes.date_of_income DESC");
$incomesQuery->bindValue(':user_ID', $_SESSION['logged_id'], PDO::PARAM_INT);   
$incomesQuery->bindValue(':date_begin', $_SESSION['date_begin'], PDO::PARAM_STR);   
$incomesQuery->bindValue(':date_end', $_SESSION['date_end'], PDO::PARAM_STR);   
$incomesQuery->execute();

$incomes = $incomesQuery->fetchAll();

$expensesQuery = $db->prepare("SELECT expenses.amount, expenses_category_assigned_to_users.name, expenses.date_of_expense, expenses.expense_comment FROM expenses JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE expenses.user_id = :user_ID 
AND expenses.date_of_expense BETWEEN :date_begin AND :date_end ORDER BY expenses.date_of_expense DESC");
$expensesQuery->bindValue(':user_ID', $_SESSION['logged_id'], PDO::PARAM_INT);   
$expensesQuery->bindValue(':date_begin', $_SESSION['date_begin'], PDO::PARAM_STR);   
$expensesQuery->bindValue(':date_end', $_SESSION['date_end'], PDO::PARAM_STR);   
$expensesQuery->execute();

$expenses = $expensesQuery->fetchAll();

$totalIncomesQuery = $db->prepare("SELECT SUM(incomes.amount) AS sum FROM incomes WHERE incomes.user_id = :user_ID AND incomes.date_of_income BETWEEN :date_begin AND :date_end");
$totalIncomesQuery->bindValue(':user_ID', $_SESSION['logged_id'], PDO::PARAM_INT);
$totalIncomesQuery ->bindValue(':date_begin', $_SESSION['date_begin'], PDO::PARAM_STR);   
$totalIncomesQuery ->bindValue(':date_end', $_SESSION['date_end'], PDO::PARAM_STR); 
$totalIncomesQuery -> execute();  

$totalIncomes = $totalIncomesQuery->fetch(PDO::FETCH_ASSOC)['sum'];

$totalExpensesQuery = $db->prepare("SELECT SUM(expenses.amount) AS sum FROM expenses WHERE expenses.user_id = :user_ID AND expenses.date_of_expense BETWEEN :date_begin AND :date_end");
$totalExpensesQuery->bindValue(':user_ID', $_SESSION['logged_id'], PDO::PARAM_INT);
$totalExpensesQuery ->bindValue(':date_begin', $_SESSION['date_begin'], PDO::PARAM_STR);   
$totalExpensesQuery ->bindValue(':date_end', $_SESSION['date_end'], PDO::PARAM_STR); 
$totalExpensesQuery -> execute();  

$totalExpenses = $totalExpensesQuery->fetch(PDO::FETCH_ASSOC)['sum'];

$balance = $totalIncomes - $totalExpenses;

if ($balance >= 0) {
  $result = "<p> Gratulacje.Świetnie zarządzasz finansami!😎 <p>";
} else {
  $result = "<p class='error'> Jesteś pod kreską! Musisz się bardziej postarać !<p>";
}

$popularExpensesQuery =$db-> prepare("SELECT SUM(expenses.amount) AS sum, name FROM expenses JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id WHERE expenses.user_id = :user_ID 
AND expenses.date_of_expense BETWEEN :date_begin AND :date_end GROUP BY expenses_category_assigned_to_users.name");
$popularExpensesQuery->bindValue(':user_ID', $_SESSION['logged_id'], PDO::PARAM_INT);
$popularExpensesQuery ->bindValue(':date_begin', $_SESSION['date_begin'], PDO::PARAM_STR);   
$popularExpensesQuery ->bindValue(':date_end', $_SESSION['date_end'], PDO::PARAM_STR); 
$popularExpensesQuery -> execute(); 

$popularExpenses= $popularExpensesQuery->fetchAll();

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
              <a class="nav-link" aria-current="page" href="expenses.php">
                <i class="bi bi-basket"></i>Dodaj wydatek</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="balance.php"><i class="bi bi-bar-chart"></i>Przeglądaj bilans</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="settings.html"><i class="bi bi-gear"></i>Ustawienia</a>
            </li>
            <li class="nav-item">
              <a class="nav-lin empty" href="#">EMPTY, EMPTY</a>
            </li>

            <li class="nav-item dropdown ">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Wybierz
                okres</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="current_month.php">Bieżący miesiąc</a></li>
                <li><a class="dropdown-item" href="previous_month.php">Poprzedni miesiąc</a></li>
                <li><a class="dropdown-item" href="year.php">Obecny rok</a></li>
                <li><a class="dropdown-item" href="#exampleModal" data-bs-toggle="modal">Niestandardowy</a></li>
              </ul>
            </li>

          </ul>
        </div>
      </div>
    </nav>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Wybierz okres</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" action="custom.php">
          <div class="modal-body">
            <label for="start-date"> Od</label>
            <input type="date" name="begin" id="start-date">
            <label for="end-date"> Do</label>
            <input type="date" name="end" id="end-date">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
            <input  type="submit" value="Zastosuj" class="btn btn-primary" data-bs-dismiss="modal"></button>
          </div>
          </form>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div class="hero">
      <div class="hero-shadow"></div>

      <div class="hero-main balance">
        <div class="wrapper">
          <div class="top">
            <h1><?= $_SESSION['title']?></h1>
          </div>
        </div>

        <div class="tab">
          <div class="wrapper">
            <div class="top">
              Przychody
            </div>
            <div class="bottom">
              <ul>
              <?php
                foreach ($incomes as $income) {
                  echo "<li>
                   <p class='amount'>{$income['amount']}</p>
                   <p class='category'>{$income['name']}</p>
                   <p class='date'>{$income['date_of_income']}</p>
                   <p class='comment'>{$income['income_comment']}</p>
                   <p class='buttons'>
                    <button class='edit'><i class='bi bi-pencil'></i></button>
                    <button class='delete'><i class='bi bi-trash'></i></button>
                  </p>                              
                  </li>";                 
              }
                ?>
              </ul>
            </div>
          </div>

          <div class="wrapper">
            <div class="top">
              Wydatki
            </div>
            <div class="bottom">
              <ul>
              <?php
                foreach ($expenses as $expense) {
                  echo "<li>
                   <p class='amount'>{$expense['amount']}</p>
                   <p class='category'>{$expense['name']}</p>
                   <p class='date'>{$expense['date_of_expense']}</p>
                   <p class='comment'>{$expense['expense_comment']}</p>
                   <p class='buttons'>
                    <button class='edit'><i class='bi bi-pencil'></i></button>
                    <button class='delete'><i class='bi bi-trash'></i></button>
                  </p>                              
                  </li>";                 
              }
                ?>
                
              </ul>
            </div>
          </div>
        </div>

        <div class="wrapper">
          <div class="top">
            bilans
          </div>
          <div class="bottom">
            <h2>Wynik: <span><?= $balance ?></span></h2>
            <?= $result ?>

          </div>
        </div>
      </div>
      <a href="#piechart"><i class="bi bi-chevron-down bounce-top"></i></a>
    </div>
  </main>

  <section class="piechart" id="piechart">
    <div class="hero">
      <div class="hero-shadow"></div>
      <div class="hero-main piechart-main">
        <h2>Na co najwięcej wydajesz?</h2>
        <div class="chart">
        <canvas id="pie"></canvas>
        </div>
      </div>
    </div>

  </section>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"> </script> 
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    
const languagesData = {
	labels: [ 
		<?php
      foreach ($popularExpenses as $popularExpense) {
        echo "'" . $popularExpense['name'] . "',";
      }
    ?>
	],
	datasets: [
		{
			data: [
        <?php
      foreach ($popularExpenses as $popularExpense) {
        echo "'" . $popularExpense['sum'] . "',";
      }
    ?>
      ],
			backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#9C27B0'],
		},
	],
}
const config = {
	type: 'pie',
	data: languagesData,
	options: {
		responsive: true,
		plugins: {
			legend: {
				position: 'right',
			},
			title: {
				display: false,
				text: 'Wydatki',
			},
		},
	},
}

const ctx = document.getElementById('pie').getContext('2d')

new Chart(ctx, config)

  </script>

</body>

</html>