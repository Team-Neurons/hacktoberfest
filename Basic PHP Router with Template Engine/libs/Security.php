<?php
/**
 * Author: Timo Strüker
 * Filename: Security.php
 *
 * @version 2.1
 * Date: 29.11.2018
 * Time: 11:17
 * LastEdit: 06.02.2019
 *
 * Content
 *  Contains all security related functions
 *  Handles all security interactions
 *
 * Used Functions
 *  Kernel->redirect_to_route()
 *  Kernel->get_routes()
 *  UUID::v5()
 *
 * Defined Functions
 *  public __construct(Kernel $Kernel): void
 *  public static start_session(): void
 *  public check_login(): bool
 *  public login(string $Email, string $Password, string $UserAgent): bool
 *  public logout(string $SessionUuid = null): bool
 *  public check_priv(string $Route): bool
 *
 * Global Variables
 *  private $CONFIG
 *  private $PDO
 *  private $KERNEL
 */

require_once "UUID.php";

class Security
{
  private $CONFIG;

  private $PDO;

  private $KERNEL;

  /**
   * @function __construct
   * @param Kernel $Kernel
   *
   * Description
   *  Security constructor.
   */
  public function __construct(Kernel $Kernel)
  {
    $this->CONFIG = json_decode(file_get_contents("conf/config.json"), true);
    //$this->routes = json_decode(file_get_contents("conf/routes.json"), true);
    $ConnectionString = sprintf("%s:host=%s;dbname=%s;port=%s;", $this->CONFIG['db']['driver'], $this->CONFIG['db']['host'], $this->CONFIG['db']['dbname'], $this->CONFIG['db']['port']);
    $this->PDO = new PDO($ConnectionString, $this->CONFIG['db']['username'], $this->CONFIG['db']['password']);
    $this->KERNEL = $Kernel;
  }

  /**
   * @function start_session
   * @throws Exception
   *
   * Description
   *  Initiates secure session
   */
  public static function start_session()
  {
    $Config = json_decode(file_get_contents("conf/config.json"),true)['session'];
    //var_dump($conf);
    $SessionName = $Config['session_name'];   // vergib einen Sessionnamen
    $Secure = $Config['secure'];
    // Damit wird verhindert, dass JavaScript auf die session id zugreifen kann.
    $HttpOnly = $Config['httponly'];
    // Zwingt die Sessions nur Cookies zu benutzen.
    if (@ini_set('session.use_only_cookies', 1) === FALSE)
    {
      throw new Exception("Could not initiate a safe session (ini_set)");
      //header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
      //exit();
    }
    // Holt Cookie-Parameter.
    $CookieParams = session_get_cookie_params();
    @session_set_cookie_params($CookieParams["lifetime"],
      $CookieParams["path"],
      $CookieParams["domain"],
      $Secure,
      $HttpOnly);
    // Setzt den Session-Name zu oben angegebenem.
    @session_name($SessionName);
    @session_start();            // Startet die PHP-Sitzung
    @session_regenerate_id();
  }

  /**
   * @function check_login
   * @return bool
   * @throws Exception
   *
   * Description
   *  Checks if a User is logged in
   *
   * Used Functions
   *  Kernel->redirect_to_route()
   */
  public function check_login()
  {
    if(isset($_SESSION['user_uuid']) && !empty($_SESSION['user_uuid']))
    {
      $Statement = $this->PDO->prepare("SELECT * FROM session WHERE session_uuid = ?");
      $Result = $Statement->execute([$_SESSION['session_uuid']]);
      if(!$Result)
      {
        throw new Exception(print_r($Statement->errorInfo(), true));
      }
      if($Statement->rowCount() < 1)
      {
        $this->KERNEL->redirect_to_route("logoff");
        return false;
      }
      $Data = $Statement->fetch(PDO::FETCH_ASSOC);
      if($Data['active'] != 1 || $_SERVER['HTTP_USER_AGENT'] != $Data['user_agent'])
      {
        $this->KERNEL->redirect_to_route("logoff");
        return false;
      }

      if(strtotime($Data['session_timestamp']) < time() - 60*60*12)
      {
        $InactiveStatement = $this->PDO->prepare("UPDATE session SET active = 0 WHERE session_uuid = ?");
        $InactiveResult = $InactiveStatement->execute([$_SESSION['session_uuid']]);
        if(!$InactiveResult)
        {
          throw new Exception(print_r($InactiveStatement->errorInfo(), true));
        }
        $this->KERNEL->redirect_to_route("logoff");
        return false;
      }

      $ActiveStatement = $this->PDO->prepare("UPDATE session SET session_timestamp = ? WHERE session_uuid = ?");
      $ActiveResult = $ActiveStatement->execute([date("Y-m-d H:i:s", time()), $_SESSION['session_uuid']]);
      if(!$ActiveResult)
      {
        throw new Exception(print_r($ActiveStatement->errorInfo(), true));
      }
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * @function login
   * @param string $Email
   * @param string $Password
   * @param string $UserAgent
   * @return bool
   * @throws Exception
   *
   * Description
   *  Logs in User
   *
   * Used Functions
   *  UUID::v5()
   */
  public function login(string $Email, string $Password, string $UserAgent)
  {
    if(empty($Email) || empty($Password))
    {
      return false;
    }
    $CustomerStatement = $this->PDO->prepare("SELECT c.email, c.password, c.uuid, p.priv_level, p.name AS priv_name, c.locked FROM customer c JOIN priv p ON c.priv_id = p.id WHERE email = ?");
    $CustomerResult = $CustomerStatement->execute([$Email]);

    $EmployeeStatement = $this->PDO->prepare("SELECT e.email, e.password, e.uuid, p.priv_level, p.name AS priv_name FROM employee e JOIN priv p ON e.priv_id = p.id WHERE email = ?");
    $EmployeeResult = $EmployeeStatement->execute([$Email]);

    if(!$CustomerResult)
    {
      throw new Exception(print_r($CustomerStatement->errorInfo(), true));
    }

    if(!$EmployeeResult)
    {
      throw new Exception(print_r($EmployeeStatement->errorInfo(), true));
    }

    //$employee = false;

    if($EmployeeStatement->rowCount() > 0)
    {
      //$employee = true;
      $EmployeeData = $EmployeeStatement->fetch(PDO::FETCH_ASSOC);
      if($EmployeeData !== false && password_verify($Password, $EmployeeData['password']))
      {
        $SessionEmployeeStatement = $this->PDO->prepare("INSERT INTO session (session_uuid, user_uuid, login_timestamp, active, user_agent) VALUES (?,?,?,?,?)");
        $SessionUuid = UUID::v5($EmployeeData['uuid'], time());
        $SessionEmployeeResult = $SessionEmployeeStatement->execute([$SessionUuid, $EmployeeData['uuid'], date("Y-m-d H:i:s", time()), true, $UserAgent]);
        if(!$SessionEmployeeResult)
        {
          throw new Exception(print_r($SessionEmployeeStatement->errorInfo(), true));
        }

        $_SESSION['user_uuid'] = $EmployeeData['uuid'];
        $_SESSION['user_email'] = $EmployeeData['email'];
        $_SESSION['session_uuid'] = $SessionUuid;
        $_SESSION['priv_name'] = $EmployeeData['priv_name'];
        $_SESSION['priv_level'] = $EmployeeData['priv_level'];
        $_SESSION['account_locked'] = false;
        $_SESSION['is_customer'] = false;

        if($EmployeeData['priv_level'] == 0)
        {
          $_SESSION['is_admin'] = true;
        }
        else
        {
          $_SESSION['is_admin'] = false;
        }

        if($EmployeeData['priv_level'] == 350)
        {
          $_SESSION['is_accountant'] = true;
        }
        else
        {
          $_SESSION['is_accountant'] = false;
        }
        return true;
      }
    }
    if($CustomerStatement->rowCount() > 0)
    {
      $CustomerData = $CustomerStatement->fetch(PDO::FETCH_ASSOC);
      if($CustomerData !== false && password_verify($Password, $CustomerData['password'])){
        if($CustomerData['locked'] == 1)
        {
          $_SESSION['account_locked'] = true;
          return false;
        }
        $CustomerSessionStatement = $this->PDO->prepare("INSERT INTO session (session_uuid, user_uuid, login_timestamp, active, user_agent) VALUES (?,?,?,?,?)");
        $SessionUuid = UUID::v5($CustomerData['uuid'], time());
        $CustomerSessionResult = $CustomerSessionStatement->execute([$SessionUuid, $CustomerData['uuid'], date("Y-m-d H:i:s", time()), true, $UserAgent]);
        if(!$CustomerSessionResult)
        {
          throw new Exception(print_r($CustomerSessionStatement->errorInfo(), true));
        }

        $_SESSION['user_uuid'] = $CustomerData['uuid'];
        $_SESSION['user_email'] = $CustomerData['email'];
        $_SESSION['session_uuid'] = $SessionUuid;
        $_SESSION['priv_name'] = $CustomerData['priv_name'];
        $_SESSION['priv_level'] = $CustomerData['priv_level'];
        $_SESSION['account_locked'] = false;
        $_SESSION['is_customer'] = true;
        $_SESSION['is_admin'] = false;
        $_SESSION['is_accountant'] = false;
        return true;
      }
    }
    $_SESSION['account_locked'] = false;
    return false;

  }

  /**
   * @function logout
   * @param string $SessionUuid
   * @return bool
   * @throws Exception
   *
   * Description
   *  Destroys Session
   */
  public function logout(string $SessionUuid = null)
  {
    if($SessionUuid !== null)
    {
      $Statement = $this->PDO->prepare("UPDATE session SET active = 0 WHERE session_uuid = ?");
      $Result = $Statement->execute([$SessionUuid]);
      if(!$Result)
      {
        throw new Exception(print_r($Statement->errorInfo(), true));
      }
    }
    session_destroy();
    return true;
  }

  /**
   * @function check_priv
   * @param string $Route
   * @return bool
   * @throws Exception
   *
   * Description
   *  Checks if logged in user has the privilege to access a specified route
   */
  public function check_priv(string $Route)
  {
    $CurrentRoute = $this->KERNEL->get_routes()[$Route];
    if(!$this->check_login())
    {
      //$this->logout();
      //self::start_session();
      $_SESSION['priv_level'] = 999;
      $_SESSION['priv_name'] = "anonymous";
      $_SESSION['is_customer'] = false;
      $_SESSION['is_admin'] = false;
    }

    if($_SESSION['priv_level'] <= $CurrentRoute['priv_level'])
    {
      if($_SESSION['is_customer'] == false && $CurrentRoute['priv_id'] == 3)
      {
        return false;
      }

      if($_SESSION['is_admin'] == true && ($CurrentRoute['priv_id'] == 4 || $CurrentRoute['priv_id'] == 5))
      {
        return false;
      }
      return true;
    }

    return false;
  }
}