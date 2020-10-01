<?php
/**
 * Author: Echelon101
 * Filename: Kernel.php
 *
 * @version 2.0
 * Date: 14.11.2018
 * Time: 07:44
 * LastEdit: 06.02.2019
 *
 * Content
 *  Contains Router and Route Functions
 *
 * Used Functions
 *  Request->get_resource()
 *  Security->check_priv()
 *  Template::render()
 *
 * Defined Functions
 *  public __construct(): void
 *  public handle(Request $Request): bool|mixed
 *  public redirect_to_route(string $Route): bool
 *  public get_route(): array
 *  public add_route(string $Name, string $Route, int $Priv, string $Function): bool
 *  public remove_route(int $Id): bool
 *  public random_door_code(int $Length): string
 *
 * Global Variables
 *  private $PDO
 *  private $CONFIG
 *  private $MAIL
 */

use PHPMailer\PHPMailer\PHPMailer;

require_once 'Security.php';
require_once 'Template.php';
require_once 'Controller.php';

class Kernel
{
  private $CONFIG;

  private $PDO;

  private $MAIL;

  /**
   * @function __construct
   * Description
   *  Kernel constructor.
   */
  public function __construct()
  {
    $this->CONFIG = json_decode(file_get_contents('conf/config.json'), true);
    $ConnectionString = sprintf("%s:host=%s;dbname=%s;port=%s;charset=%s", $this->CONFIG['db']['driver'], $this->CONFIG['db']['host'], $this->CONFIG['db']['dbname'], $this->CONFIG['db']['port'], $this->CONFIG['db']['charset']);
    $this->PDO = new PDO($ConnectionString, $this->CONFIG['db']['username'], $this->CONFIG['db']['password']);

    $this->MAIL = new PHPMailer(true);
    $this->MAIL->SMTPDebug = $this->CONFIG['mail']['debug'];
    $this->MAIL->isSMTP();
    $this->MAIL->Host = $this->CONFIG['mail']['host'];
    $this->MAIL->SMTPAuth = $this->CONFIG['mail']['smtpauth'];
    $this->MAIL->Username = $this->CONFIG['mail']['username'];
    $this->MAIL->Password = $this->CONFIG['mail']['password'];
    $this->MAIL->SMTPSecure = $this->CONFIG['mail']['smtpsecure'];
    $this->MAIL->Port = $this->CONFIG['mail']['port'];
  }

  /**
   * @function handle
   * @param Request $Request
   * @return bool|mixed
   * @throws Exception
   *
   * Description
   *  Compares requested resource against available routes
   *
   * Used Functions
   *  Request->get_resource()
   *  Security->check_priv()
   *  Template::render()
   *  Controller->? (any passed function)
   */
  public function handle(Request $Request)
  {
    foreach ($this->get_routes() as $Route => $Info)
    {
      if(rtrim(ltrim($Request->get_resource(), "/"), '/') . '/' == rtrim(ltrim($Info['route'], "/"),'/'). '/')
      {
        $Security = new Security($this);
        $Controller = new Controller($Request, $this->PDO, $Security, $this, $this->CONFIG, $this->MAIL);

        if($Security->check_priv($Route))
        {
          return call_user_func(array($Controller, $Info['function']));
        }
        else
        {
          http_response_code(403);
          return Template::render("Error/accessDenied.tpl", ['request_path' => $Route, 'title' => '403 Access Denied']);
        }
      }
    }

    http_response_code(404);
    return Template::render('Error/404.tpl', ['title' => '404 Page Not Found', 'request_path' => $Request->get_resource()], ['de_de.php']);

  }

  /**
   * @function redirect_to_route
   * @param string $Route
   * @return bool
   * @throws Exception
   *
   * Description
   *  Sets Header Location to passed Route
   *
   * Used Functions
   *  Template::render()
   */
  public function redirect_to_route(string $Route)
  {
    if(!array_key_exists($Route, $this->get_routes()))
    {
      http_response_code(500);
      Template::render('Error/500.tpl');
    }
    header('location: '. "/". ltrim($this->CONFIG['router']['base']).rtrim(ltrim($this->get_routes()[$Route]['route'], "/"), "/"));
    return true;
  }

  /**
   * @function get_routes
   * @return array
   * @throws Exception
   *
   * Description
   *  Returns all available Routes
   */
  public function get_routes()
  {
    $Statement = $this->PDO->prepare("SELECT r.id, r.name, r.route, r.function, p.name as priv_name, p.id as priv_id, p.priv_level FROM routes r JOIN priv p on r.priv = p.id");
    $Result = $Statement->execute();
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    $Routes = array();

    while($Route = $Statement->fetch(PDO::FETCH_ASSOC))
    {
      $Routes[$Route['name']] = $Route;
    }

    return $Routes;
  }

  /**
   * @function add_route
   * @param string $Name
   * @param string $Route
   * @param int $Priv
   * @param string $Function
   * @return bool
   * @throws Exception
   *
   * Description
   *  Adds Route to Database
   */
  public function add_route(string $Name, string $Route, int $Priv, string $Function)
  {
    $Statement = $this->PDO->prepare("INSERT INTO routes (name, route, priv, function) VALUES (:name, :route, :priv, :function)");
    $Result = $Statement->execute([
      "name" => $Name,
      "route" => $Route,
      "priv" => $Priv,
      "function" => $Function
    ]);

    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    return true;
  }

  /**
   * @function remove_route
   * @param int $Id
   * @return bool
   * @throws Exception
   *
   * Description
   *  Removes Route by Id from Database
   */
  public function remove_route(int $Id)
  {
    $Statement = $this->PDO->prepare("DELETE FROM routes WHERE id = ?");
    $Result = $Statement->execute([$Id]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    return true;
  }

  /**
   * @function random_door_code
   * @param int $Length
   * @return string
   *
   * Description
   *  Generates a random digit code of variable length
   */
  public function random_door_code(int $Length)
  {
    $Characters = "0123456789";
    $Pass = array(); //remember to declare $pass as an array
    $CharLenth = strlen($Characters) - 1; //put the length -1 in cache
    for ($i = 0; $i < $Length; $i++)
    {
      $n = rand(0, $CharLenth);
      $Pass[] = $Characters[$n];
    }
    return implode($Pass); //turn the array into a string
  }
}