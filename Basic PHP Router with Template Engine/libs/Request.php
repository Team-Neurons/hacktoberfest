<?php
/**
 * Author: Timo Strüker
 * Filename: Request.php
 *
 * @version 1.1
 * Date: 15.11.2018
 * Time: 08:26
 * LastEdit: 06.02.2019
 *
 * Content
 *  Request Model
 *
 * Used Functions
 *  Session::start_session()
 *
 * Defined Functions
 *  public static create_from_globals(): Request
 *  public __construct($Method, $Resource, $Globals)
 *  public get_method(): mixed
 *  public get_resource(): mixed
 *  public get_globals(): mixed
 *  public set_globals($Globals)
 *
 * Global Variables
 *  private $METHOD
 *  private $RESOURCE
 *  private $Globals
 *
 */

require_once 'Security.php';

class Request
{
  private $METHOD;

  private $RESOURCE;

  private $Globals;

  /**
   * @function create_from_globals
   * @return Request
   * @throws Exception
   *
   * Description
   *  Creates Request instance form $_SERVER Variables
   *
   * Used Functions
   *  Security::start_session()
   */
  public static function create_from_globals()
  {
    Security::start_session();
    $Conf = json_decode(file_get_contents("conf/config.json"), true);
    $Globals = array(
      'GET' => $_GET,
      'POST' => $_POST,
      'REQUEST' => $_REQUEST,
      'COOKIE' => $_COOKIE,
      'SERVER' => $_SERVER,
      'FILES' => $_FILES,
      'ENV' => $_ENV
    );
    if(isset($_COOKIE[$Conf['session']['session_name']])){
      $Globals['SESSION'] = $_SESSION;
    }
    $RequestPage = "/";
    if(isset($_GET['request_page']))
    {
      $RequestPage = $_GET['request_page'];
    }
    return new self($_SERVER['REQUEST_METHOD'], $RequestPage, $Globals);
  }

  /**
   * @function __construct
   * @param $Method
   * @param $Resource
   * @param $Globals
   *
   * Description
   *  Request constructor.
   */
  public function __construct($Method, $Resource, $Globals)
  {
    $this->METHOD = $Method;
    $this->RESOURCE = $Resource;
    $this->Globals = $Globals;
  }

  /**
   * @function get_method
   * @return mixed
   *
   * Description
   *  Returns Request Method
   */
  public function get_method()
  {
    return $this->METHOD;
  }

  /**
   * @function get_resource
   * @return mixed
   *
   * Description
   *  Returns Request Path
   */
  public function get_resource()
  {
    return $this->RESOURCE;
  }

  /**
   * @function get_globals
   * @return mixed
   *
   * Description
   *  Returns Globals Array
   */
  public function get_globals()
  {
    return $this->Globals;
  }

  /**
   * @function set_globals
   * @param mixed $Globals
   *
   * Description
   *  Sets $Globals Variable
   */
  public function set_globals($Globals)
  {
    $this->Globals = $Globals;
  }

}