
<?php

session_start();
if (isset($_SESSION['logged_id'])) {
    header('Location: balance.php');
    exit();   
}

if (isset($_POST['username'])) {

    $OK = true;

    $password =$_POST['password'];

    if((strlen($password)<8) || (strlen($password)>20)) {
        $OK=false;
        $_SESSION['error']="Hasło musi posiadać od 8 do 20 znaków!";
      }
          
      $pass_hash = password_hash($password, PASSWORD_DEFAULT);

	
	$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    require_once('database.php');
	
	if (empty($email)) {
		
		$_SESSION['error']='To nie jest poprawny adres e-mail!';
        $OK = false;
		
	} else {

        

        $userEmail = $db->prepare('SELECT id FROM users WHERE email = :email');
        $userEmail->bindValue(':email', $email, PDO::PARAM_STR);    
        $userEmail->execute();
        
        if ($userEmail->rowCount() > 0) {
                $OK=false;
                $_SESSION['error']="Istnieje już konto przypisane do takiego adresu e-mail!";
            } 
	    }

        $_SESSION['fr_first'] = $_POST['username'];
        $_SESSION['fr_last'] = $_POST['lastname'];
        $_SESSION['fr_email'] = $_POST['email'];
        $_SESSION['fr_pass'] = $password;


        if ($OK) {
 
        $_SESSION['registered'] = 'Dziękujemy za rejestrację - Teraz możesz się już zalogować';
            
        $query = $db->prepare('INSERT INTO users VALUES (NULL, :username, :lastname, :password, :email)');
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->bindValue(':username', $_POST['username'], PDO::PARAM_STR);
        $query->bindValue(':lastname', $_POST['lastname'], PDO::PARAM_STR);
        $query->bindValue(':password', $pass_hash, PDO::PARAM_STR);
        $query->execute();

        
        $newUser = $db->query('SELECT LAST_INSERT_ID() AS id')->fetch();
        
        
        $expenses = $db->prepare ('INSERT INTO expenses_category_assigned_to_users (name, user_id)
        SELECT name, :lastID AS user_id FROM expenses_category_default');
        $expenses->bindValue(':lastID', $newUser['id'], PDO::PARAM_INT);
        $expenses->execute();

        $incomes = $db->prepare ('INSERT INTO incomes_category_assigned_to_users (name, user_id)
        SELECT name, :lastID AS user_id FROM incomes_category_default');
        $incomes->bindValue(':lastID', $newUser['id'], PDO::PARAM_INT);
        $incomes->execute();


        header('Location: login.php');

        }
	
}


?>


<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeBudget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
        integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <nav class="nav">

        <div class="nav-logo">
            <svg class="nav-logo-icon" fill="#fff" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg"
                stroke="#fff">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <path
                        d="M820 257q-13-8-53-20-36-11-48-17l-4-2q-20-11-31-15-17-7-32-10-37-5-85 15-73 30-115 68t-63 91q-5 12-8 24l-4 15q-1 4-2 5.5t-6 2.5q-11 10-38 21-15 6-47.5 17T244 467q-12 7-24 21-8 9-20 27-9 14-21 46l-7 17q-4 12-11 20-4 4-13 10-6 4-8.5 7t-2.5 8q0 9 9 12t21-.5 21-12.5 16-25q5-10 12-30 8-25 13-34 10-17 25.5-26.5t35-8.5 34.5 14q14 11 20 26 3 6 7 27 5 22 9 34 6 19 15 29 16 19 39 28 25 11 56 6 24-3 46 3t32.5 17.5 5 23T527 721q-12 2-35 2h-1q-12 0-23-1-2-8-6-13.5t-8-8.5l-4-2q-20-14-49-17-26-2-51 5 7-13 5-32.5T343 624q-26-25-77-23.5T193 627q-9 11-9.5 31.5T192 690q-30-4-57 2-35 8-46 32-6 13-2 38 2 20 27.5 33t58 13 57-14 25.5-35q2-29-7-42 29 5 59-3-5 8-4 33l1 5q1 22 26 36 29 15 66 13 40-2 63-24l4-6q4-8 6-20 10 2 37 2 23 0 44.5-6.5T580 729q10-14 9-40 0-15-5.5-44t-2.5-35q16-11 48-5 19 3 54 16 21 7 27 8 34 5 74-10 29-10 58-28 18-12 23-17l10-10q37-43 39-119 2-60-21-109-25-52-73-79zM170 769q-20 0-38-6-27-9-27-26.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T208 763q-18 6-38 6zm100-89q-21 0-39-6-26-8-26-25.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T308 674q-18 6-38 6zm117 81q-21 0-39-6-26-8-26-25.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T425 755q-18 6-38 6zm194-178q-4-40-21-77-18-42-45-64-20-16-49-26-22-8-44-10-18-2-22.5.5t-5-1 5.5-9.5 16-10q13-6 29-8 39-4 73 10 39 16 63 54 16 26 20.5 54.5t-1 52T581 583z">
                    </path>
                </g>
            </svg>
            <a href="index.html">Home<span>Budget</span></a>
        </div>

        <div class="nav-buttons">
            <a href="signup.php"><button class="signup">
                    <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-46.08 -46.08 604.16 604.16"
                        xml:space="preserve" fill="#000000" stroke="#000000" stroke-width="0.00512">

                        <g>
                            <path class="st0"
                                d="M259.993,460.958c14.498,14.498,75.487-23.002,89.985-37.492l59.598-59.606l-52.494-52.485l-59.597,59.597 C282.996,385.462,245.504,446.46,259.993,460.958z">
                            </path>
                            <path class="st0"
                                d="M493.251,227.7c-14.498-14.49-37.996-14.49-52.485,0l-71.68,71.678l52.494,52.486l71.671-71.68 C507.741,265.695,507.741,242.198,493.251,227.7z M399.586,308.882l-9.008-8.999l50.18-50.18l8.991,8.99L399.586,308.882z">
                            </path>
                            <path class="st0"
                                d="M374.714,448.193c-14.071,14.055-67.572,51.008-104.791,51.008c-0.008,0,0,0-0.008,0 c-17.47,0-28.484-7.351-34.648-13.516c-44.758-44.775,36.604-138.56,37.492-139.439l4.123-4.124 c-3.944-4.354-5.644-10.348-5.644-22.302c0-8.836,0-25.256,0-40.403c11.364-12.619,15.497-11.048,25.103-60.596 c19.433,0,18.178-25.248,27.34-47.644c7.479-18.238,1.212-25.632-5.072-28.655c5.14-66.463,5.14-112.236-70.296-126.435 c-27.349-23.438-68.606-15.48-88.158-11.57c-19.536,3.911-37.159,0-37.159,0l3.355,31.49 C97.74,70.339,112.05,116.112,107.44,142.923c-5.994,3.27-11.407,10.809-4.269,28.254c9.17,22.396,7.906,47.644,27.339,47.644 c9.614,49.548,13.747,47.976,25.111,60.596c0,15.148,0,31.567,0,40.403c0,25.248-8.58,25.684-28.134,36.612 c-47.14,26.35-108.572,41.659-119.571,124.01C5.902,495.504,92.378,511.948,213.434,512 c121.04-0.052,207.524-16.496,205.518-31.558c-3.168-23.702-10.648-41.547-20.68-55.806L374.714,448.193z">
                            </path>
                        </g>
                    </svg>
                    Zarejestruj</button></a>
            <a href="login.php"> <button class="login">
                    <svg viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000">
                        <title>log-in</title>
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="icon" fill="#ffffff" transform="translate(42.666667, 42.666667)">
                                <path
                                    d="M405.333333,3.55271368e-14 L405.333333,426.666667 L170.666667,426.666667 L170.666667,341.333333 L213.333333,341.333333 L213.333333,384 L362.666667,384 L362.666667,42.6666667 L213.333333,42.6666667 L213.333333,85.3333333 L170.666667,85.3333333 L170.666667,3.55271368e-14 L405.333333,3.55271368e-14 Z M74.6666667,138.666667 C108.491057,138.666667 137.06239,161.157677 146.241432,192.000465 L320,192 L320,234.666667 L298.666667,234.666667 L298.666667,277.333333 L234.666667,277.333333 L234.666667,234.666667 L146.241432,234.666202 C137.06239,265.508989 108.491057,288 74.6666667,288 C33.4294053,288 7.10542736e-15,254.570595 7.10542736e-15,213.333333 C7.10542736e-15,172.096072 33.4294053,138.666667 74.6666667,138.666667 Z M74.6666667,181.333333 C56.9935547,181.333333 42.6666667,195.660221 42.6666667,213.333333 C42.6666667,231.006445 56.9935547,245.333333 74.6666667,245.333333 C92.3397787,245.333333 106.666667,231.006445 106.666667,213.333333 C106.666667,195.660221 92.3397787,181.333333 74.6666667,181.333333 Z"
                                    id="Combined-Shape"> </path>
                            </g>
                        </g>

                    </svg>
                    Zaloguj</button></a>
        </div>
    </nav>

    <main>
        <div class="hero">
            <div class="hero-shadow"></div>
            <div class="hero-main">
                <img src="./img/budget.png" alt="budget-image">
                <div class="hero-main-text">
                    <form class="form" method="post">
                        <h1>
                            <svg class="nav-logo-icon" fill="#fff" viewBox="0 0 1000 1000"
                                xmlns="http://www.w3.org/2000/svg" stroke="#fff">
                                <path
                                    d="M820 257q-13-8-53-20-36-11-48-17l-4-2q-20-11-31-15-17-7-32-10-37-5-85 15-73 30-115 68t-63 91q-5 12-8 24l-4 15q-1 4-2 5.5t-6 2.5q-11 10-38 21-15 6-47.5 17T244 467q-12 7-24 21-8 9-20 27-9 14-21 46l-7 17q-4 12-11 20-4 4-13 10-6 4-8.5 7t-2.5 8q0 9 9 12t21-.5 21-12.5 16-25q5-10 12-30 8-25 13-34 10-17 25.5-26.5t35-8.5 34.5 14q14 11 20 26 3 6 7 27 5 22 9 34 6 19 15 29 16 19 39 28 25 11 56 6 24-3 46 3t32.5 17.5 5 23T527 721q-12 2-35 2h-1q-12 0-23-1-2-8-6-13.5t-8-8.5l-4-2q-20-14-49-17-26-2-51 5 7-13 5-32.5T343 624q-26-25-77-23.5T193 627q-9 11-9.5 31.5T192 690q-30-4-57 2-35 8-46 32-6 13-2 38 2 20 27.5 33t58 13 57-14 25.5-35q2-29-7-42 29 5 59-3-5 8-4 33l1 5q1 22 26 36 29 15 66 13 40-2 63-24l4-6q4-8 6-20 10 2 37 2 23 0 44.5-6.5T580 729q10-14 9-40 0-15-5.5-44t-2.5-35q16-11 48-5 19 3 54 16 21 7 27 8 34 5 74-10 29-10 58-28 18-12 23-17l10-10q37-43 39-119 2-60-21-109-25-52-73-79zM170 769q-20 0-38-6-27-9-27-26.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T208 763q-18 6-38 6zm100-89q-21 0-39-6-26-8-26-25.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T308 674q-18 6-38 6zm117 81q-21 0-39-6-26-8-26-25.5t26-26.5q18-6 38.5-6t38.5 6q26 9 26 26.5T425 755q-18 6-38 6zm194-178q-4-40-21-77-18-42-45-64-20-16-49-26-22-8-44-10-18-2-22.5.5t-5-1 5.5-9.5 16-10q13-6 29-8 39-4 73 10 39 16 63 54 16 26 20.5 54.5t-1 52T581 583z">
                                </path>
                            </svg>
                            Home<span>Budget</span>
                        </h1>
                        <div class="name">
                            <div class="input-icons">
                                <label for="first">First Name</label>
                                <div class="icon-center">
                                    <input class="input" name="username" type="text" id="first" required
                                    value="<?php 
                                    if(isset($_SESSION['fr_first'])) {
                                    echo $_SESSION['fr_first'];
                                    unset($_SESSION['fr_first']);
                                    }
                                    ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                                        class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="input-icons">
                                <label for="last">Last Name</label>
                                <div class="icon-center">
                                    <input class="input" name="lastname" type="text" id="last" required
                                    value="<?php 
                                    if(isset($_SESSION['fr_last'])) {
                                    echo $_SESSION['fr_last'];
                                    unset($_SESSION['fr_last']);
                                    }
                                    ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                                        class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>


                        <div class="input-icons">
                            <label for="email">Email</label>
                            <div class="icon-center">
                                <input class="input" name="email" type="email" id="email" required  
                                value="<?php 
                                    if(isset($_SESSION['fr_email'])) {
                                    echo $_SESSION['fr_email'];
                                    unset($_SESSION['fr_email']);
                                    }
                                    ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    class="bi bi-envelope-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z" />
                                </svg>
                            </div>
                        </div>
                        <div class="input-icons">
                            <label for="password">Hasło</label>
                            <div class="icon-center">
                                <input class="input" name="password" type="password" id="password" required
                                value="<?php 
                                    if(isset($_SESSION['fr_pass'])) {
                                    echo $_SESSION['fr_pass'];
                                    unset($_SESSION['fr_pass']);
                                    }
                                    ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                    <path
                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                </svg>
                            </div>
                        </div>

                        <?php 
                    if(isset($_SESSION['error'])) {
                        echo '<p class="error">'.$_SESSION['error'].'</p>';
                        unset($_SESSION['error']);
                    }
                                       

                    ?>         
                        <div class="buttons">
                            <input type="submit" value="Zarejestruj" class="btn blue-btn">
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