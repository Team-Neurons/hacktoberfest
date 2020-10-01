<?php
/**
 * Author: Timo Strüker
 * Author: Florian Wichert
 * Author: Tom Marvin Brandt
 * Filename: Controller.php
 *
 * @version 4.2
 * Date: 29.11.2018
 * Time: 12:48
 * LastEdit: 08.02.2019
 *
 * Content
 *  Contains all Page logic
 *
 * Used Functions
 *  Request->get_globals()
 *  Request->get_method()
 *  Template::render()
 *  Kernel->get_routes()
 *  Kernel->add_route()
 *  Kernel->redirect_to_route()
 *  Kernel->random_door_code()
 *  Security->login()
 *  Security->logout()
 *  UUID::v4()
 *
 * Defined Functions
 *  public __construct(Request $Request, PDO $Pdo, Security $Security, Kernel $Kernel, array $Config, PhpMailer $Mail)
 *  public index(): bool
 *  public login(): bool
 *  public register(): bool
 *  public logoff(): bool
 *  public dump()
 *  public admin_user_create(): bool
 *  public test_array(): bool
 *  public customer_profile(): bool
 *  public customer_profile_edit(): bool
 *  public list_routes(): bool
 *  public create_route(): bool
 *  public employee_profile(): bool
 *  public employee_profile_edit(): bool
 *  public customer_dashboard(): bool
 *  public employee_dashboard(): bool
 *  public admin_dashboard(): bool
 *  public debug_if(): bool
 *  public order_actions(): bool
 *  public user_actions(): bool
 *  public key_actions(): bool
 *  public list_employees(): bool
 *  public create_order(): bool
 *  private insert_order(int $CustomerId, array $RoomIds)
 *  public employee_actions(): bool
 *  public list_customers(): bool
 *  public list_orders(): bool
 *  public employee_user_create(): bool
 *  public employee_edit(): bool
 *  public list_keys(): bool
 *  public create_rooms(): bool
 *  public legal(): bool
 *  public room_evaluation(): bool
 *  public password_reset(): bool
 *  public password_new(): bool
 *  public create_csv(): bool
 *  public csv_actions(): bool
 *  private str_putcsv($Input, $Delimiter = ',', $Enclosure = '"'): string
 *  public download(): bool
 *
 * Global Variables
 *  private $PDO
 *  private $REQUEST
 *  private $SECURITY
 *  private $KERNEL
 *  private $CONFIG
 *  private $MAIL
 */

use PHPMailer\PHPMailer\PHPMailer;

require_once 'Template.php';

class Controller
{
  private $PDO;

  private $REQUEST;

  private $SECURITY;

  private $KERNEL;

  private $CONFIG;

  private $MAIL;

  /**
   * @function __construct
   * @param Request $Request
   * @param PDO $Pdo
   * @param Security $Security
   * @param Kernel $Kernel
   * @param array $Config
   * @param PHPMailer $Mail
   *
   * Description
   *  Controller constructor.
   */
  public function __construct(Request $Request, PDO $Pdo, Security $Security, Kernel $Kernel, array $Config, PhpMailer $Mail)
  {
    $this->REQUEST = $Request;
    $this->PDO = $Pdo;
    $this->SECURITY = $Security;
    $this->KERNEL = $Kernel;
    $this->CONFIG = $Config;
    $this->MAIL = $Mail;
  }

  /**
   * @function index
   * @return bool
   * @throws Exception
   *
   * Description
   *  renders the landingpage
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Template::render()
   */
  public function index()
  {
    $LoginStatus = "0";
    $IsCustomer = "1";
    $IsAdmin = "0";
    $IsAccountant = "0";

    if(isset($this->REQUEST->get_globals()['SESSION']['user_uuid']) && !empty($this->REQUEST->get_globals()['SESSION']['user_uuid']))
    {
      $LoginStatus = "1";
      if(!$this->REQUEST->get_globals()['SESSION']['is_customer'])
      {
        $IsCustomer = 0;
      }
      if($this->REQUEST->get_globals()['SESSION']['is_admin'])
      {
        $IsAdmin = 1;
      }
      if($this->REQUEST->get_globals()['SESSION']['is_accountant'])
      {
        $IsAccountant = 1;
      }
    }

    return Template::render("index.tpl", ['login_status' => $LoginStatus, 'is_customer' => $IsCustomer, 'is_admin' => $IsAdmin, "is_accountant" => $IsAccountant, 'title' => "Startseite",], ['de_de.php']);
  }

  /**
   * @function login
   * @return bool
   * @throws Exception
   *
   * Description
   *  shows login page and handles the login action
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Security->login()
   *  Template::render()
   */
  public function login()
  {
    if(isset($this->REQUEST->get_globals()['REQUEST']['email'])){
      $Globals = $this->REQUEST->get_globals();
      $State = $this->SECURITY->login($Globals['REQUEST']['email'], $Globals['REQUEST']['password'], $Globals['SERVER']['HTTP_USER_AGENT']);
      if($State)
      {
        if($_SESSION['is_customer'] == true)
        {
          return $this->KERNEL->redirect_to_route("customer_dashboard");
        }
        else
        {
          return $this->KERNEL->redirect_to_route("index");
        }
      }
      else
      {
        if($_SESSION['account_locked'] == true)
        {
          return Template::render("Frontend/Login/login.tpl", ['title' => 'Login', 'error' => "Your Account is locked"], ['de_de.php']);
        }
        else
        {
          return Template::render("Frontend/Login/login.tpl", ['title' => 'Login', 'error' => "<div class=\"ui error message\">The Password or Username is incorrect</div>"], ['de_de.php']);
        }

      }
    }
    return Template::render("Frontend/Login/login.tpl", ['title' => "Login", 'error' => ""], ['de_de.php']);
  }

  /**
   * @function register
   * @return bool
   * @throws Exception
   *
   * Description
   *  shows renderform and handle the register action
   *
   * Used Functions
   *  Request->get_method()
   *  Request->get_globals()
   *  Template::render()
   *  Kernel->redirect_to_route()
   *  UUID::v4()
   */
  public function register()
  {
    if($this->REQUEST->get_method() == "POST")
    {
      $Post = $this->REQUEST->get_globals()['POST'];
      if($Post['passwd'] !== $Post['passwd1'])
      {
        return Template::render("Frontend/Login/register.tpl", ['error' => "Passwords don't Match"]);
      }
      $HashedPassword = password_hash($Post['passwd'], PASSWORD_DEFAULT);
      $Statement = $this->PDO->prepare("INSERT INTO customer (firstname, lastname, email, password, company, iban, bic, street, hsnr, city, zip, country, uuid, priv_id, tel) VALUES (:fname, :lname, :email, :passwd, :company, :iban, :bic, :street, :hsnr, :city, :zip, :country, :uuid, :priv, :tel)");
      $Result = $Statement->execute([
        "fname" => $Post['firstname'],
        "lname" => $Post['lastname'],
        "email" => $Post['email'],
        "passwd" => $HashedPassword,
        "company" => $Post['company'],
        "iban" => $Post['iban'],
        "bic" => $Post['bic'],
        "street" => $Post['street'],
        "hsnr" => $Post['number'],
        "city" => $Post['city'],
        "zip" => $Post['zip'],
        "country" => $Post['country'],
        "uuid" => UUID::v4(),
        "priv" => "3",
        "tel" => $Post['tel']
      ]);
      if(!$Result)
      {
        throw new Exception(print_r($Statement->errorInfo(), true));
      }
      return $this->KERNEL->redirect_to_route("index");
    }
    return Template::render("Frontend/Login/register.tpl", ['title' => "Register", 'error' => ''], ['de_de.php']);
  }

  /**
   * @function logoff
   * @return bool
   * @throws Exception
   *
   * Description
   *  send session uuid to security.php and redirect to index
   *
   * Used Functions
   *  Security->logout()
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   */
  public function logoff()
  {
    $this->SECURITY->logout($this->REQUEST->get_globals()['SESSION']['session_uuid']);
    return $this->KERNEL->redirect_to_route("index");
  }

  /**
   * @function dump
   * @throws Exception
   *
   * Description
   *  create a dump off variables for testing and debugging
   *
   * Used Functions
   *  Kernel->get_routes()
   */
  public function dump()
  {
    var_dump($this->REQUEST);
    var_dump($this->KERNEL->get_routes());
  }

  /**
   * @function admin_user_create
   * @return bool
   * @throws Exception
   *
   * Description
   *  add an user with admin priv into the employee table and redirect to admin\dashboard
   *
   * Used Functions
   *  Request->get_method()
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Template::render()
   */
  public function admin_user_create()
  {
    if($this->REQUEST->get_method() == "POST")
    {
      $Post = $this->REQUEST->get_globals()['POST'];
      $HashedPassword = password_hash($this->REQUEST->get_globals()['POST']['password'], PASSWORD_DEFAULT);
      $Statement = $this->PDO->prepare("INSERT INTO employee (priv_id, firstname, lastname, email, password, uuid) VALUES (?,?,?,?,?,?)");
      $Result = $Statement->execute([1, $Post['firstname'], $Post['lastname'], $Post['email'], $HashedPassword, UUID::v4()]);
      if(!$Result)
      {
        throw new Exception(print_r($Statement->errorInfo(), true));
      }
      return $this->KERNEL->redirect_to_route("admin_dashboard");
    }
    else
    {
      return Template::render("Admin/create_user.tpl", ['title' => "Create new Admin"], ['de_de.php']);
    }
  }

  /**
   * @function test_array
   * @return bool
   * @throws Exception
   *
   * Description
   *  For testing the template array interpreter
   *
   * Used Functions
   *  Template::render()
   */
  public function test_array()
  {
    $TestArray = [
      [
        "test1" => "testtext block1",
        "test2" => "testtext2 block1"
      ],
      [
        "test1" => "testtext block2",
        "test2" => "testtext2 block2"
      ],
      [
        "test1" => "testtext block3",
        "test2" => "testtext block3"
      ]
    ];

    /*$testArray = [
        "block1",
        "block2",
        "block3"
    ];*/

    return Template::render("Test/testArray.tpl", ["tests" => $TestArray]);
  }

  /**
   * @function customer_profile
   * @return bool
   * @throws Exception
   *
   * Description
   *  get all information from logged in customer and hand it over to the template
   *
   * Used Functions
   *  Request->get_globals()
   *  Template::render()
   */
  public function customer_profile()
  {
    $Statement = $this->PDO->prepare("SELECT * FROM customer WHERE uuid = ?");
    $Result = $Statement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }

    $Data = $Statement->fetch(PDO::FETCH_ASSOC);
    //var_dump($data);
    return Template::render("Customer/customer_profile.tpl",['customers' => [$Data], 'title' => "Profil"], ['de_de.php']);
  }

  /**
   * @function customer_profile_edit
   * @return bool
   * @throws Exception
   *
   * Description
   *  render template and can update the customer table if there is a post
   *
   * Used functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Template::render()
   */
  public function customer_profile_edit()
  {
    if(isset($this->REQUEST->get_globals()['POST']['ident']) && $this->REQUEST->get_globals()['POST']['ident'] === "customer_edit")
    {
      $Post = $this->REQUEST->get_globals()['POST'];
      $EditStatement = $this->PDO->prepare("UPDATE customer SET firstname = :firstname, lastname = :lastname, email = :email, company = :company, iban = :iban, bic = :bic, street = :street, hsnr = :hsnr, city = :city, zip = :zip , tel = :tel WHERE uuid = :uuid");
      $EditResult = $EditStatement->execute([
        "firstname" => $Post['firstname'],
        "lastname" => $Post['lastname'],
        "email" => $Post['email'],
        "company" => $Post['company'],
        "iban" => $Post['iban'],
        "bic" => $Post['bic'],
        "street" => $Post['street'],
        "hsnr" => $Post['hsnr'],
        "city" => $Post['city'],
        "zip" => $Post['zip'],
        "uuid" => $this->REQUEST->get_globals()['SESSION']['user_uuid'],
        "tel" => $Post['tel']
      ]);
      if(!$EditResult)
      {
        throw new Exception(print_r($EditStatement->errorInfo(), true));
      }
      return $this->KERNEL->redirect_to_route("customer_profile");
    }
    $Statement = $this->PDO->prepare("SELECT * FROM customer WHERE uuid = ?");
    $Result = $Statement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }

    $Data = $Statement->fetch(PDO::FETCH_ASSOC);
    //var_dump($data);
    return Template::render("Customer/customer_edit.tpl",['customers' => [$Data],'title'=>'Profil edit'], ['de_de.php']);
  }

  /**
   * @function list_routes
   * @return bool
   * @throws Exception
   *
   * Description
   *  read all routes with their priv out of their tables and list them off
   *
   * Used functions
   *  Kernel->get_routes()
   *  Template::render()
   */
  public function list_routes()
  {
    $Statement = $this->PDO->prepare("SELECT p.id, p.name FROM priv p");
    $Result = $Statement->execute();
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    $Priv = $Statement->fetchAll(PDO::FETCH_ASSOC);
    $Routes = $this->KERNEL->get_routes();
    //var_dump($data);
    return Template::render("Admin/routes.tpl",['routes' => $Routes, 'error' => "", 'privs' => $Priv, 'title' => "Routes"], ['de_de.php']);
  }

  /**
   * @function create_route
   * @return bool
   * @throws Exception
   *
   * Description
   *  Form for creating a new route
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->add_route()
   *  Kernel->redirect_to_route()
   */
  public function create_route()
  {
    if(isset($this->REQUEST->get_globals()['POST']['priv']) && !empty($this->REQUEST->get_globals()['POST']['priv']))
    {
      $Post = $this->REQUEST->get_globals()['POST'];
      $Priv = explode("-", $Post['priv']);
      $PrivId = rtrim($Priv[0], ' ');
      $this->KERNEL->add_route($Post['name'], $Post['route'], $PrivId, $Post['function']);
    }
    return $this->KERNEL->redirect_to_route("routes");
  }

  /**
   * @fuction employee_profile
   * @return bool
   * @throws Exception
   *
   * Description
   *  get all information from logged in employee and hand it over to the template
   *
   * Used Functions
   *  Request->get_globals()
   *  Template::render()
   */
  public function employee_profile()
  {
    $Statement = $this->PDO->prepare("SELECT e.email, e.firstname, e.lastname, p.name as priv_name, e.hsnr, e.street, e.city, e.zip, e.tel from employee e JOIN priv p on e.priv_id = p.id WHERE e.uuid = ?");
    $Result = $Statement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }

    $Data = $Statement->fetch(PDO::FETCH_ASSOC);
    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }
    //var_dump($data);
    return Template::render("Employee/employee_profile.tpl",['employees' => [$Data], 'title' => "Profile", 'is_employer' => $IsEmployer], ['de_de.php']);
  }

  /**
   * @employee_profile_edit
   * @return bool
   * @throws Exception
   *
   * Description
   *  render template and can update the employee table if there is a post
   *
   * Used functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Template::render()
   */
  public function employee_profile_edit()
  {
    if(isset($this->REQUEST->get_globals()['POST']['ident']) && $this->REQUEST->get_globals()['POST']['ident'] === "employee_edit")
    {
      $Post = $this->REQUEST->get_globals()['POST'];
      $EditStatement = $this->PDO->prepare("UPDATE employee SET firstname = :firstname, lastname = :lastname WHERE uuid = :uuid");
      $EditResult = $EditStatement->execute([
        "firstname" => $Post['firstname'],
        "lastname" => $Post['lastname'],
        "uuid" => $this->REQUEST->get_globals()['SESSION']['user_uuid'],
      ]);
      if(!$EditResult)
      {
        throw new Exception(print_r($EditStatement->errorInfo(), true));
      }
      return $this->KERNEL->redirect_to_route("employee_profile");

    }
    $Statement = $this->PDO->prepare("SELECT * FROM employee WHERE uuid = ?");
    $Result = $Statement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }

    $Data = $Statement->fetch(PDO::FETCH_ASSOC);
    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }
    //var_dump($data);
    return Template::render("Employee/employee_edit.tpl",['employees' => [$Data],'title'=>'Profile edit', "is_employer" => $IsEmployer], ['de_de.php']);
  }

  /**
   * @function customer_dashboard
   * @return bool
   * @throws Exception
   *
   * Description
   *  renders a template with multiple arrays handed over for different tables
   *
   * Used Functions
   *  Request->get_globals()
   *  Template::render()
   */
  public function customer_dashboard()
  {
    $Statement = $this->PDO->prepare("SELECT * FROM customer WHERE uuid = ?");
    $Result = $Statement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }

    $Data = $Statement->fetch(PDO::FETCH_ASSOC);

    $OrderStatement = $this->PDO->prepare("SELECT o.id, os.status, count(*) as total_rooms FROM `order` o JOIN customer c on o.customer_id = c.id JOIN order_status os on o.status_id = os.id JOIN order_room r on o.id = r.order_id JOIN room r2 on r.room_id = r2.id WHERE c.uuid = ? GROUP BY o.id;");
    $OrderResult = $OrderStatement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$OrderResult)
    {
      throw new Exception(print_r($OrderStatement->errorInfo(), true));
    }
    //$o_data = $o_statement->fetchAll(PDO::FETCH_ASSOC);
    $Orders = array();

    while ($OrderData = $OrderStatement->fetch(PDO::FETCH_ASSOC))
    {
      $OrderData['actions'] = "";
      if($OrderData['status'] == "open" || $OrderData['status'] == "running")
      {
        $OrderData['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/orders/?id=". $OrderData['id'] ."&action=cancel\" class=\"ui basic red button\">Cancel</a>";
      }
      array_push($Orders, $OrderData);
    }

    $RoomKeyStatement = $this->PDO->prepare("SELECT rk.id, rk.room_key, r.name, l.street, l.hsnr, l.zip, l.city, l.country, o2.id as order_id, rks.status FROM room_key rk JOIN room r on rk.id = r.room_key_id JOIN order_room o on r.id = o.room_id JOIN `order` o2 on o.order_id = o2.id JOIN customer c on o2.customer_id = c.id JOIN location l on r.location_id = l.id JOIN room_key_status rks on rk.status_id = rks.id WHERE o.active = true AND c.uuid = ? AND o2.active = true ORDER BY rk.id ASC");
    $RoomKeyResult = $RoomKeyStatement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$RoomKeyResult)
    {
      throw new Exception(print_r($RoomKeyStatement->errorInfo(), true));
    }
    $Rooms = array();

    while($RoomKeyData = $RoomKeyStatement->fetch(PDO::FETCH_ASSOC))
    {
      if($RoomKeyData['status'] == "revoked")
      {
        $RoomKeyData['actions'] = "<p>Key got Revoked</p>";
      }
      elseif ($RoomKeyData['status'] == "expired")
      {
        $RoomKeyData['actions'] = "<p>Key is Expired</p>";
      }
      else
      {
        $RoomKeyData['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/keys/?id=". $RoomKeyData['id'] ."&action=rekey\" class=\"ui basic grey button\">Reissue Key</a>";
      }

      array_push($Rooms, $RoomKeyData);
    }


    $CostStatement = $this->PDO->prepare("SELECT rt.size, count(*) as rooms, rt.price  FROM `order` o JOIN order_room r on o.id = r.order_id JOIN room r2 on r.room_id = r2.id JOIN room_type rt on r2.room_type_id = rt.id Join customer c on o.customer_id = c.id WHERE c.uuid = ? AND o.active = true GROUP BY rt.id");
    $CostResult = $CostStatement->execute([$_SESSION['user_uuid']]);

    if(!$CostResult)
    {
      throw new Exception(print_r($CostStatement->errorInfo(),true));
    }
    $Costs = array();
    $TotalCost = 0;
    $TotalCount = 0;
    while ($CostData = $CostStatement->fetch(PDO::FETCH_ASSOC))
    {

      $CostData['total'] = $CostData['price'] * $CostData['rooms'];
      $TotalCost += $CostData['total'];
      $TotalCount += $CostData['rooms'];
      array_push($Costs, $CostData);
    }
    return Template::render("Customer/dashboard.tpl",['customers' => [$Data], 'orders' => $Orders, "rooms" => $Rooms, "costs" => $Costs, "total_booked" => $TotalCount, "total_cost" => $TotalCost,  'title' => "Dashboard"], ['de_de.php']);
  }

  /**
   * @function employee_dashboard
   * @return bool
   * @throws Exception
   *
   * Description
   *  renders a template with multiple arrays handed over for different tables
   *
   * Used Functions
   *  Request->get_globals()
   *  Template::render()
   */
  public function employee_dashboard()
  {
    $EmployeeStatement = $this->PDO->prepare("SELECT e.firstname, e.lastname, e.email, p.name as priv_name, e.street, e.hsnr, e.zip, e.city FROM employee e JOIN priv p on e.priv_id = p.id WHERE uuid = ?;");
    $EmployeeResult = $EmployeeStatement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$EmployeeResult)
    {
      throw new Exception(print_r($EmployeeStatement->errorInfo(), true));
    }

    $EmployeeData = $EmployeeStatement->fetch(PDO::FETCH_ASSOC);
    $EmployeeData['priv_name'] = ucfirst($EmployeeData['priv_name']);
    $AllEmployeeStatement = $this->PDO->prepare("SELECT e.firstname, e.lastname, e.email, e.hsnr, e.street, e.city, e.zip, e.tel, p.name as priv FROM employee e JOIN priv p on e.priv_id = p.id");
    $AllEmployeeResult = $AllEmployeeStatement->execute();
    if(!$AllEmployeeResult)
    {
      throw new Exception(print_r($AllEmployeeStatement->errorInfo(), true));
    }

    $AllEmployeeData = $AllEmployeeStatement->fetchAll(PDO::FETCH_ASSOC);

    $OrderStatement = $this->PDO->prepare("SELECT o.id, os.status, count(*) as total_rooms, c.email FROM `order` o JOIN customer c on o.customer_id = c.id JOIN order_status os on o.status_id = os.id JOIN order_room r on o.id = r.order_id JOIN room r2 on r.room_id = r2.id  WHERE r.active = true GROUP BY o.id ORDER BY o.id DESC");
    $OrderResult = $OrderStatement->execute();
    if(!$OrderResult)
    {
      throw new Exception(print_r($OrderStatement->errorInfo(), true));
    }
    //$orders = $o_statement->fetchAll(PDO::FETCH_ASSOC);
    $Orders = $OrderStatement->fetchAll(PDO::FETCH_ASSOC);

    $CustomerStatement = $this->PDO->prepare("SELECT c.id, c.firstname, c.lastname, c.email, c.company, c.street, c.hsnr, c.zip, c.city, c.country, c.tel, c.locked FROM customer c ORDER BY c.id ASC");
    $CustomerResult = $CustomerStatement->execute();
    if(!$CustomerResult)
    {
      throw new Exception(print_r($CustomerStatement->errorInfo(), true));
    }
    //$customers = $c_statement->fetchAll(PDO::FETCH_ASSOC);
    $Customers = $CustomerStatement->fetchAll(PDO::FETCH_ASSOC);

    $RoomKeyStatement = $this->PDO->prepare("SELECT rk.id, r.name, rk.room_key, rks.status, c.email FROM room_key rk JOIN room_key_status rks on rk.status_id = rks.id JOIN room r on rk.id = r.room_key_id JOIN order_room o on r.id = o.room_id JOIN `order` o2 on o.order_id = o2.id  JOIN customer c on o2.customer_id = c.id WHERE rk.status_id = 1 AND o.active = 1 ORDER BY rk.id ASC");
    $RoomKeyResult = $RoomKeyStatement->execute();
    if(!$RoomKeyResult)
    {
      throw new Exception(print_r($RoomKeyStatement->errorInfo(), true));
    }
    $RoomKeys = $RoomKeyStatement->fetchAll(PDO::FETCH_ASSOC);

    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }
    //var_dump($e_all_data);
    return Template::render("Employee/dashboard.tpl",['logged_in_employee' => [$EmployeeData], 'orders' => $Orders, 'customers' => $Customers, "keys" => $RoomKeys, "is_employer" => $IsEmployer, "employees" => $AllEmployeeData, "title" => "Dashboard"], ['de_de.php']);
  }

  /**
   * @function admin_dashboard
   * @return bool
   * @throws Exception
   *
   * Description
   *  renders a template with the information of this user
   *
   * Used Functions
   *  Request->get_globals()
   *  Template::render()
   */
  public function admin_dashboard()
  {
    $Statement = $this->PDO->prepare("SELECT * FROM employee WHERE uuid = ?");
    $Result = $Statement->execute([$this->REQUEST->get_globals()['SESSION']['user_uuid']]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }

    $Data = $Statement->fetch(PDO::FETCH_ASSOC);
    return Template::render("Admin/dashboard.tpl",['employees' => [$Data], 'title' => "Dashboard"], ['de_de.php']);
  }

  /**
   * @function debug_if
   * @return bool
   * @throws Exception
   *
   * Description
   *  For Testing the Template If Interpreter
   *
   * Used Functions
   *  Template::render()
   */
  public function debug_if()
  {
    return Template::render("Test/test_if.tpl", ["loginstatus" => 0]);
  }

  /**
   * @function order_actions
   * @return bool
   * @throws Exception
   *
   * Description
   *  create different actions for interacting with new and consisting orders
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   */
  public function order_actions()
  {
    if(!isset($this->REQUEST->get_globals()['GET']['action']) || empty($this->REQUEST->get_globals()['GET']['action']))
    {
      if($_SESSION['is_customer'] == true)
      {
        return $this->KERNEL->redirect_to_route("customer_dashboard");
      }
      elseif ($_SESSION['is_customer'] == false)
      {
        return $this->KERNEL->redirect_to_route("employee_dashboard");
      }

    }
    $OrderId = $this->REQUEST->get_globals()['GET']['id'];
    $Action = $this->REQUEST->get_globals()['GET']['action'];
    switch ($Action)
    {
      case "approve":
        if($_SESSION['is_customer'] == false)
        {
          $OrderRoomApproveStatement = $this->PDO->prepare("UPDATE order_room SET active = 1 WHERE order_id = ?");
          $OrderRoomApproveResult = $OrderRoomApproveStatement->execute([$OrderId]);
          if(!$OrderRoomApproveResult)
          {
            throw new Exception(print_r($OrderRoomApproveStatement->errorInfo(), true));
          }

          $OrderApproveStatement = $this->PDO->prepare("UPDATE `order` SET status_id = 2, last_edit_date = now(), last_edit_employee_id = ?, active = 1, contract_begin = now() WHERE id = ?");
          $OrderApproveResult = $OrderApproveStatement->execute([1, $OrderId]);
          if(!$OrderApproveResult)
          {
            throw new Exception(print_r($OrderApproveStatement->errorInfo(), true));
          }
          return $this->KERNEL->redirect_to_route("employee_dashboard");
        }
        break;
      case "decline":
        if($_SESSION['is_customer'] == false)
        {
          $OrderRoomDeclineStatement = $this->PDO->prepare("UPDATE order_room SET active = 0 WHERE order_id = ?");
          $OrderRoomDeclineResult = $OrderRoomDeclineStatement->execute([$OrderId]);
          if(!$OrderRoomDeclineResult)
          {
            throw new Exception(print_r($OrderRoomDeclineStatement->errorInfo(), true));
          }

          $OrderDeclineStatement = $this->PDO->prepare("UPDATE `order` SET status_id = 3, last_edit_date = now(), last_edit_employee_id = ?, active = 0 WHERE id = ?");
          $OrderDeclineResult = $OrderDeclineStatement->execute([1,$OrderId]);
          if(!$OrderDeclineResult)
          {
            throw new Exception(print_r($OrderDeclineStatement->errorInfo(), true));
          }
          return $this->KERNEL->redirect_to_route("employee_dashboard");
        }
        break;
      case "cancel":
        if($_SESSION['is_customer'] == true)
        {
          $OrderRoomCancelStatement = $this->PDO->prepare("UPDATE order_room SET active = 0 WHERE order_id = ?");
          $OrderRoomCancelResult = $OrderRoomCancelStatement->execute([$OrderId]);
          if(!$OrderRoomCancelResult)
          {
            throw new Exception(print_r($OrderRoomCancelStatement->errorInfo(), true));
          }

          $OrderCancelStatement = $this->PDO->prepare("UPDATE `order` SET status_id = 4, last_edit_date = now(), contract_end = now() WHERE id = ?");
          $OrderCancelResult = $OrderCancelStatement->execute([$OrderId]);
          if(!$OrderCancelResult)
          {
            throw new Exception(print_r($OrderCancelStatement->errorInfo(), true));
          }

          return $this->KERNEL->redirect_to_route("customer_dashboard");
        }
        break;
      default:
        return $this->KERNEL->redirect_to_route("index");
        break;
    }
    return $this->KERNEL->redirect_to_route("index");
  }

  /**
   * @function user_actions
   * @return bool
   * @throws Exception
   *
   * Description
   *  create different actions for interacting with new and consisting customers
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   */
  public function user_actions()
  {
    if(!isset($this->REQUEST->get_globals()['GET']['action']) || empty($this->REQUEST->get_globals()['GET']['action']))
    {
      if ($_SESSION['is_customer'] == false)
      {
        return $this->KERNEL->redirect_to_route("employee_dashboard");
      }
      else
      {
        return $this->KERNEL->redirect_to_route("index");
      }
    }
    $UserId = $this->REQUEST->get_globals()['GET']['id'];
    $Action = $this->REQUEST->get_globals()['GET']['action'];

    switch ($Action)
    {
      case "lock":
        $LockStatement = $this->PDO->prepare("UPDATE customer SET locked = 1 WHERE id = ?");
        $LockResult = $LockStatement->execute([$UserId]);
        if(!$LockResult)
        {
          throw new Exception(print_r($LockStatement->errorInfo(), true));
        }
        break;
      case "unlock":
        $UnlockStatement = $this->PDO->prepare("UPDATE customer SET locked = 0 WHERE id = ?");
        $UnlockResult = $UnlockStatement->execute([$UserId]);
        if(!$UnlockResult)
        {
          throw new Exception(print_r($UnlockStatement->errorInfo(), true));
        }
        break;
      default:
        break;
    }
    return $this->KERNEL->redirect_to_route("employee_dashboard");
  }

  /**
   * @function key_actions
   * @return bool
   * @throws Exception
   *
   * Description
   *  create different actions for interacting with new and consisting keys
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Kernel->random_door_code()
   *  UUID::v4()
   */
  public function key_actions()
  {
    if(!isset($this->REQUEST->get_globals()['GET']['action']) || empty($this->REQUEST->get_globals()['GET']['action']))
    {
      if ($_SESSION['is_customer'] == false)
      {
        return $this->KERNEL->redirect_to_route("employee_dashboard");
      }
      elseif($_SESSION['is_customer'] == true)
      {
        return $this->KERNEL->redirect_to_route("customer_dashboard");
      }
    }
    $KeyId = $this->REQUEST->get_globals()['GET']['id'];
    $Action = $this->REQUEST->get_globals()['GET']['action'];

    switch ($Action)
    {
      case "revoke":
        if($_SESSION['is_customer'] == false)
        {
          $RevokeStatement = $this->PDO->prepare("UPDATE room_key SET status_id = 3 WHERE id = ?");
          $RevokeResult = $RevokeStatement->execute([$KeyId]);
          if(!$RevokeResult)
          {
            throw new Exception(print_r($RevokeStatement->errorInfo(), true));
          }
        }
        break;
      case "rekey":
        $RoomStatement = $this->PDO->prepare("SELECT id FROM room WHERE room_key_id = ?");
        $RoomResult = $RoomStatement->execute([$KeyId]);
        if(!$RoomResult)
        {
          throw new Exception(print_r($RoomStatement->errorInfo(), true));
        }

        $RoomId = $RoomStatement->fetch(PDO::FETCH_ASSOC)['id'];
        $InvalidateStatement = $this->PDO->prepare("UPDATE room_key SET status_id = 3 WHERE id = ?");
        $InvalidateResult = $InvalidateStatement->execute([$KeyId]);
        if(!$InvalidateResult)
        {
          throw new Exception(print_r($InvalidateStatement->errorInfo(), true));
        }

        $Uuid = UUID::v4();
        $CreateKeyStatement = $this->PDO->prepare("INSERT INTO room_key (status_id, room_key, uuid) VALUES (?,?,?)");
        $CreateKeyResult = $CreateKeyStatement->execute([
          1,
          $this->KERNEL->random_door_code($this->CONFIG['door_codes']['length']),
          $Uuid]);
        if(!$CreateKeyResult)
        {
          throw new Exception(print_r($CreateKeyStatement->errorInfo(), true));
        }

        $NewKeyIdStatement = $this->PDO->prepare("SELECT id FROM room_key WHERE uuid = ?");
        $NewKeyIdResult = $NewKeyIdStatement->execute([$Uuid]);
        if(!$NewKeyIdResult)
        {
          throw new Exception(print_r($NewKeyIdStatement->errorInfo(), true));
        }
        $NewKeyId = $NewKeyIdStatement->fetch(PDO::FETCH_ASSOC)['id'];

        $RekeyStatement = $this->PDO->prepare("UPDATE room SET room_key_id = ? WHERE id = ?");
        $RekeyResult = $RekeyStatement->execute([$NewKeyId, $RoomId]);
        if(!$RekeyResult)
        {
          throw new Exception(print_r($RekeyStatement->errorInfo(), true));
        }
        break;
      default:
        break;
    }
    if ($_SESSION['is_customer'] == false)
    {
      return $this->KERNEL->redirect_to_route("employee_dashboard");
    }
    elseif($_SESSION['is_customer'] == true)
    {
      return $this->KERNEL->redirect_to_route("customer_dashboard");
    }
    else
    {
      return $this->KERNEL->redirect_to_route("index");
    }
  }

  /**
   * @function list_employees
   * @return bool
   * @throws Exception
   *
   * Description
   *  shows a list of all employees with action buttons
   *
   * Used Functions
   *  Template::render()
   */
  public function list_employees()
  {
    $IsEmployee = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployee = 1;
    }

    $Statement = $this->PDO->prepare("SELECT e.id, e.firstname, e.lastname, e.email, e.hsnr, e.street, e.city, e.zip, e.tel, p.id as priv_id, p.name as priv, e.uuid FROM employee e JOIN priv p on e.priv_id = p.id");
    $Result = $Statement->execute();
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    $Employees = array();
    while($Employee = $Statement->fetch(PDO::FETCH_ASSOC))
    {
      $Employee['actions'] = "";
      if($Employee['priv_id'] == 5)
      {
        $Employee['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "employee/employee/edit/?uuid=". $Employee['uuid'] ."\" class=\"ui basic red button\">Edit</a><a href=\"" . $this->CONFIG['globals']['base'] . "actions/employee/?id=". $Employee['id'] ."&action=demote\" class=\"ui basic blue button\">Make Employee</a>";
      }
      elseif ($Employee['priv_id'] == 4)
      {
        $Employee['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "employee/employee/edit/?uuid=". $Employee['uuid'] ."\" class=\"ui basic red button\">Edit</a><a href=\"" . $this->CONFIG['globals']['base'] . "actions/employee/?id=". $Employee['id'] ."&action=promote\" class=\"ui basic blue button\">Make Employer</a>";
      }
      elseif ($Employee['priv_id'] == 1)
      {
        $Employee['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "employee/employee/edit/?uuid=". $Employee['uuid'] ."\" class=\"ui basic red button\">Edit</a>";
      }

      array_push($Employees, $Employee);
    }
    //$employees = $statement->fetchAll(PDO::FETCH_ASSOC);

    //var_dump($data);
    return Template::render("Employee/employee_list.tpl",['employees' => $Employees, 'error' => "", 'title' => "Employee List", "is_employer" => $IsEmployee], ['de_de.php']);
  }

  /**
   * @function create_order
   * @return bool
   * @throws Exception
   *
   * Description
   *  renders a form for placing an order
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Template::render()
   */
  public function create_order()
  {
    $AvailableStatement = $this->PDO->prepare("SELECT r.id, r.name, rt.size, rt.price FROM room r LEFT JOIN order_room o ON r.id = o.room_id JOIN room_type rt on r.room_type_id = rt.id WHERE o.active = 0 OR o.active IS NULL GROUP BY r.name ORDER BY r.id ASC");
    $AvailableResult = $AvailableStatement->execute();
    if(!$AvailableResult)
    {
      throw new Exception(print_r($AvailableStatement->errorInfo(), true));
    }
    $AvailableRooms = array();
    $AvailableRoomSizes = array();
    while ($AvailableRoom = $AvailableStatement->fetch(PDO::FETCH_ASSOC))
    {
      array_push($AvailableRooms, $AvailableRoom);
      array_push($AvailableRoomSizes, $AvailableRoom['size']);
      //var_dump($available_room);
    }


    if(isset($this->REQUEST->get_globals()['POST']['ident']) && !empty($this->REQUEST->get_globals()['POST']['ident']))
    {
      $PostData = $this->REQUEST->get_globals()["POST"];
      unset($PostData['ident']);

      $SelectedRooms = array();
      foreach ($PostData as $Size => $Amount)
      {
        $Count = 0;
        foreach ($AvailableRooms as $AvailableRoom)
        {
          if($Count == $Amount)
          {
            break;
          }
          if($AvailableRoom['size'] == $Size)
          {
            array_push($SelectedRooms, $AvailableRoom);
            $Count++;
          }
        }
      }
      $CustomerIdStatement = $this->PDO->prepare("SELECT id FROM customer WHERE uuid = ?");
      $CustomerIdResult = $CustomerIdStatement->execute([$_SESSION['user_uuid']]);
      if(!$CustomerIdResult)
      {
        throw new Exception(print_r($CustomerIdStatement->errorInfo()));
      }
      $CustomerId = $CustomerIdStatement->fetch(PDO::FETCH_ASSOC)['id'];
      //var_dump($selected_rooms);
      $this->insert_order($CustomerId, $SelectedRooms);
      return $this->KERNEL->redirect_to_route("customer_dashboard");
    }


    $TypeStatement = $this->PDO->prepare("SELECT * FROM room_type");
    $TypeResult = $TypeStatement->execute();
    if(!$TypeResult)
    {
      throw new Exception(print_r($TypeStatement->errorInfo(),true));
    }
    //$type_data = $type_statement->fetchAll(PDO::FETCH_ASSOC);
    $RoomCounts = array_count_values($AvailableRoomSizes);
    //var_dump($room_counts);
    $RoomTypes = array();
    while ($RoomType = $TypeStatement->fetch(PDO::FETCH_ASSOC))
    {
      if(array_key_exists($RoomType['size'], $RoomCounts))
      {
        $RoomType['max'] = $RoomCounts[$RoomType['size']];
        //$room_type['size2'] = $room_type['size'];
      }
      else
      {
        $RoomType['max'] = 0;
        // $room_type['size2'] = $room_type['size'];
      }
      array_push($RoomTypes, $RoomType);
    }

    return Template::render("Customer/create_order.tpl", ['rooms_available' => $RoomTypes, 'room_types' => $RoomTypes, 'title' => "New Order"], ['de_de.php']);
  }

  /**
   * @function insert_order
   * @param int $CustomerId
   * @param array $RoomIds
   * @throws Exception
   *
   * Description
   *  function for placing an order with customer and order information
   *
   * Used Functions
   *  UUID::v4()
   */
  private function insert_order(int $CustomerId, array $RoomIds)
  {
    $Uuid = UUID::v4();
    $Total = 0;
    foreach ($RoomIds as $RoomId)
    {
      $Total += $RoomId['price'];
    }
    $OrderStatement = $this->PDO->prepare("INSERT INTO `order` (customer_id, status_id, last_edit_date, uuid, cost) VALUES (?,1 ,now(),?, ?)");
    $OrderResult = $OrderStatement->execute([$CustomerId, $Uuid, $Total]);
    if(!$OrderResult)
    {
      throw new Exception(print_r($OrderStatement->errorInfo(), true));
    }

    $OrderIdStatement = $this->PDO->prepare("SELECT id FROM `order` WHERE uuid = ?");
    $OrderIdResult = $OrderIdStatement->execute([$Uuid]);
    if(!$OrderIdResult)
    {
      throw new Exception(print_r($OrderIdStatement->errorInfo(), true));
    }
    $OrderId = $OrderIdStatement->fetch(PDO::FETCH_ASSOC)['id'];

    $RoomStatement = $this->PDO->prepare("INSERT INTO order_room (order_id, room_id, active) VALUES (?,?, 1)");
    foreach ($RoomIds as $RoomId)
    {
      $RoomResult = $RoomStatement->execute([$OrderId, $RoomId['id']]);
      if(!$RoomResult)
      {
        throw new Exception(print_r($RoomStatement->errorInfo(), true));
      }
    }
  }

  /**
   * @function employee_actions
   * @return bool
   * @throws Exception
   *
   * Description
   *  create different actions for interacting with new and consisting employees
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   */
  public function employee_actions()
  {
    $EmployeeId = $this->REQUEST->get_globals()['GET']['id'];
    $Action = $this->REQUEST->get_globals()['GET']['action'];

    switch ($Action)
    {
      case "demote":
        $Statement = $this->PDO->prepare("UPDATE employee SET priv_id = 4 WHERE id = ?");
        $Result = $Statement->execute([$EmployeeId]);
        if(!$Result)
        {
          throw new Exception(print_r($Statement->errorInfo(), true));
        }
        break;
      case "promote":
        $Statement = $this->PDO->prepare("UPDATE employee SET priv_id = 5 WHERE id = ?");
        $Result = $Statement->execute([$EmployeeId]);
        if(!$Result)
        {
          throw new Exception(print_r($Statement->errorInfo(), true));
        }
        break;
      default:
        break;
    }
    return $this->KERNEL->redirect_to_route("employee_list");
  }

  /**
   * @function list_customers
   * @return bool
   * @throws Exception
   *
   * Description
   *  shows a list of all customers with action buttons
   *
   * Used Functions
   *  Template::render()
   */
  public function list_customers()
  {
    $IsEmployee = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployee = 1;
    }

    $Statement = $this->PDO->prepare("SELECT * FROM customer");
    $Result = $Statement->execute();
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    $Customers = array();
    while($Customer = $Statement->fetch(PDO::FETCH_ASSOC))
    {
      $Customer['actions'] = "";
      if($Customer['locked'] == 0)
      {
        $Customer['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/users/?id=". $Customer['id'] ."&action=lock\" class=\"ui basic red button\">Lock</a>";
      }
      else
      {
        $Customer['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/users/?id=". $Customer['id'] ."&action=unlock\" class=\"ui basic green button\">Unlock</a>";
      }
      array_push($Customers, $Customer);
    }
    //$employees = $statement->fetchAll(PDO::FETCH_ASSOC);

    //var_dump($data);
    return Template::render("Employee/customer_list.tpl",['customers' => $Customers, 'error' => "", 'title' => "Customer List", "is_employer" => $IsEmployee], ['de_de.php']);
  }

  /**
   * @function list_orders
   * @return bool
   * @throws Exception
   *
   * Description
   *  shows a list of all active orders with action buttons
   *
   * Used Functions
   *  Template::render()
   */
  public function list_orders()
  {
    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }

    $Statement = $this->PDO->prepare("SELECT o.id, os.status, count(*) as total_rooms, c.email FROM `order` o JOIN customer c on o.customer_id = c.id JOIN order_status os on o.status_id = os.id JOIN order_room r on o.id = r.order_id JOIN room r2 on r.room_id = r2.id  WHERE r.active = true GROUP BY o.id ORDER BY o.id DESC");
    $Result = $Statement->execute();
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    //$orders = $o_statement->fetchAll(PDO::FETCH_ASSOC);
    $Orders = array();
    while ($Order = $Statement->fetch(PDO::FETCH_ASSOC))
    {
      $Order['actions'] = "";
      if($Order['status'] == "open")
      {
        $Order['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/orders/?id=". $Order['id'] ."&action=approve\" class=\"ui basic green button\">Approve</a><a href=\"" . $this->CONFIG['globals']['base'] . "orders/?id=". $Order['id'] ."&action=decline\" class=\"ui basic red button\">Decline</a>";
      }
      elseif ($Order['status'] == "running")
      {
        $Order['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/orders/?id=". $Order['id'] ."&action=cancel\" class=\"ui basic red button\">Cancel</a>";
      }
      elseif ($Order['status'] == "declined")
      {
        $Order['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/orders/?id=". $Order['id'] ."&action=approve\" class=\"ui basic green button\">Approve</a>";
      }

      array_push($Orders, $Order);
    }
    //var_dump($data);
    return Template::render("Employee/orders_list.tpl",['orders' => $Orders, 'error' => "", 'title' => "Order List", "is_employer" => $IsEmployer], ['de_de.php']);
  }

  /**
   * @function employee_user_create
   * @return bool
   * @throws Exception
   *
   * Description
   *  insert into the employee table a new employee user with priv.lv 4
   *
   * Used Functions
   *  Request->get_globals()
   *  Template::render()
   *  Kernel->redirect_to_route()
   *  UUID::v4()
   */
  public function employee_user_create()
  {
    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }
    $Post = $this->REQUEST->get_globals()['POST'];
    if($Post['passwd'] !== $Post['passwd1'])
    {
      return Template::render("Employee/employee_list.tpl", ['error' => "Passwords don't Match", "is_employer" => $IsEmployer]);
    }
    $HashedPassword = password_hash($Post['passwd'], PASSWORD_DEFAULT);
    $Statement = $this->PDO->prepare("INSERT INTO employee (firstname, lastname, email, password, street, hsnr, city, zip, uuid, priv_id, tel) VALUES (:fname, :lname, :email, :passwd, :street, :hsnr, :city, :zip, :uuid, :priv, :tel)");
    $Result = $Statement->execute([
      "fname" => $Post['firstname'],
      "lname" => $Post['lastname'],
      "email" => $Post['email'],
      "passwd" => $HashedPassword,
      "street" => $Post['street'],
      "hsnr" => $Post['hsnr'],
      "city" => $Post['city'],
      "zip" => $Post['zip'],
      "uuid" => UUID::v4(),
      "priv" => "4",
      "tel" => $Post['tel']
    ]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    return $this->KERNEL->redirect_to_route("employee_list");
  }

  /**
   * @function employee_edit
   * @return bool
   * @throws Exception
   *
   * Description
   *  render template and can update the user in employee table if there is a post
   *
   * Used functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Template::render()
   */
  public function employee_edit()
  {
    if (isset($this->REQUEST->get_globals()['POST']['ident']) && !empty($this->REQUEST->get_globals()['POST']['ident']))
    {
      $Post = $this->REQUEST->get_globals()['POST'];
      $EditStatement = $this->PDO->prepare("UPDATE employee SET firstname = :firstname, lastname = :lastname, email = :email, street = :street, hsnr = :hsnr, city = :city, zip = :zip, tel = :tel WHERE uuid = :uuid");
      $EditResult = $EditStatement->execute([
        "firstname" => $Post['firstname'],
        "lastname" => $Post['lastname'],
        "email" => $Post['email'],
        "street" => $Post['street'],
        "hsnr" => $Post['hsnr'],
        "city" => $Post['city'],
        "zip" => $Post['zip'],
        "tel" => $Post['tel'],
        "uuid"=> $Post['uuid']
      ]);
      if(!$EditResult)
      {
        throw new Exception(print_r($EditStatement->errorInfo(), true));
      }
      return $this->KERNEL->redirect_to_route("employee_list");
    }
    elseif(isset($this->REQUEST->get_globals()['GET']['uuid']) && !empty($this->REQUEST->get_globals()['GET']['uuid']))
    {
      $IsEmployer = 0;
      if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
      {
        $IsEmployer = 1;
      }

      $Statement = $this->PDO->prepare("SELECT * FROM employee WHERE uuid = ?");
      $Result = $Statement->execute([$this->REQUEST->get_globals()['GET']['uuid']]);
      if(!$Result)
      {
        throw new Exception(print_r($Statement->errorInfo(), true));
      }

      $Data = $Statement->fetch(PDO::FETCH_ASSOC);
      //var_dump($data);
      return Template::render("Employee/employee_list_edit.tpl",['error' => "",'employees' => [$Data], 'title' => "Edit", "is_employer" => $IsEmployer], ['de_de.php']);
    }

    return $this->KERNEL->redirect_to_route("employee_dashboard");
  }

  /**
   * @function list_keys
   * @return bool
   * @throws Exception
   *
   * Description
   *  shows a list of all active keys with action buttons
   *
   * Used Functions
   *  Template::render()
   */
  public function list_keys()
  {
    $Statement = $this->PDO->prepare("SELECT rk.id, r.name, rk.room_key, rks.status, c.email FROM room_key rk JOIN room_key_status rks on rk.status_id = rks.id JOIN room r on rk.id = r.room_key_id JOIN order_room o on r.id = o.room_id JOIN `order` o2 on o.order_id = o2.id  JOIN customer c on o2.customer_id = c.id WHERE rk.status_id = 1 AND o.active = 1 ORDER BY rk.id ASC");
    $Result = $Statement->execute();
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }
    $Keys = array();
    while ($Key = $Statement->fetch(PDO::FETCH_ASSOC))
    {
      $Key['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/keys/?id=". $Key['id'] ."&action=revoke\" class=\"ui basic red button\">Revoke</a><a href=\"" . $this->CONFIG['globals']['base'] . "actions/keys/?id=". $Key['id'] ."&action=rekey\" class=\"ui basic grey button\">Rekey</a>";
      array_push($Keys, $Key);
    }

    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }
    //var_dump($e_all_data);
    return Template::render("Employee/key_list.tpl",["keys" => $Keys, "is_employer" => $IsEmployer,"error" =>"", "title" => "Key List"], ['de_de.php']);
  }

  /**
   * @function create_room
   * @return bool
   * @throws Exception
   *
   * Description
   *  renders a template for creating a room by insert into the room table
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Template::render()
   *  UUID::v4()
   */
  public function create_room()
  {
    if(isset($this->REQUEST->get_globals()['POST']['location']) && !empty($this->REQUEST->get_globals()['POST']['location']))
    {
      $Size = explode(" ",$this->REQUEST->get_globals()['POST']['size'])[0];
      $LocationId = explode(":",explode("|", $this->REQUEST->get_globals()['POST']['location'])[2])[1];
      $Amount = $_POST['amount'];

      for($i = 0; $i < $Amount; $i++)
      {
        //GET ROOM TYPE COUNT
        $RoomCountStatement = $this->PDO->prepare("SELECT Count(*) as total FROM room r JOIN room_type rt on r.room_type_id = rt.id WHERE rt.size = ? GROUP BY rt.size");
        $RoomCountResult = $RoomCountStatement->execute([$Size]);
        if(!$RoomCountResult)
        {
          throw new Exception(print_r($RoomCountStatement->errorInfo(), true));
        }
        $RoomCount = $RoomCountStatement->fetch(PDO::FETCH_ASSOC)['total'];
        $RoomNumber = $RoomCount + 1;
        $RoomName = $Size . "_" . $RoomNumber;

        $RoomTypeStatement = $this->PDO->prepare("SELECT id FROM room_type WHERE size = ?");
        $RoomTypeResult = $RoomTypeStatement->execute([$Size]);
        if(!$RoomTypeResult)
        {
          throw new Exception(print_r($RoomTypeStatement->errorInfo(), true));
        }
        $RoomTypeId = $RoomTypeStatement->fetch(PDO::FETCH_ASSOC)['id'];

        $RoomKeyUuid = UUID::v4();
        $RoomKey = $this->KERNEL->random_door_code($this->CONFIG['door_codes']['length']);
        $CreateRoomKeyStatement = $this->PDO->prepare("INSERT INTO room_key (status_id, room_key, uuid) VALUES (?,?,?)");
        $CreateRoomKeyResult = $CreateRoomKeyStatement->execute([
          1,
          $RoomKey,
          $RoomKeyUuid
        ]);
        if(!$CreateRoomKeyResult)
        {
          throw new Exception(print_r($CreateRoomKeyStatement->errorInfo(), true));
        }
        $RoomKeyStatement = $this->PDO->prepare("SELECT id FROM room_key WHERE uuid = ?");
        $RoomKeyResult = $RoomKeyStatement->execute([$RoomKeyUuid]);
        if(!$RoomKeyResult)
        {
          throw new Exception(print_r($RoomKeyStatement->errorInfo(), true));
        }
        $RoomKeyId = $RoomKeyStatement->fetch(PDO::FETCH_ASSOC)['id'];

        $CreateRoomStatement = $this->PDO->prepare("INSERT INTO room (room_type_id, room_key_id, location_id, name) VALUES (?,?,?,?)");
        $CreateRoomResult = $CreateRoomStatement->execute([$RoomTypeId, $RoomKeyId, $LocationId, $RoomName]);
        if(!$CreateRoomResult)
        {
          throw new Exception(print_r($CreateRoomStatement->errorInfo(), true));
        }
      }
      return $this->KERNEL->redirect_to_route("employee_dashboard");
    }
    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }

    $LocationStatement = $this->PDO->prepare("SELECT * FROM location");
    $LocationResult = $LocationStatement->execute();
    if(!$LocationResult)
    {
      throw new Exception(print_r($LocationStatement->errorInfo(), true));
    }
    $Locations = $LocationStatement->fetchAll();

    $SizeStatement = $this->PDO->prepare("SELECT * FROM room_type");
    $SizeResult = $SizeStatement->execute();
    if(!$SizeResult)
    {
      throw new Exception(print_r($SizeStatement->errorInfo(), true));
    }
    $Sizes = $SizeStatement->fetchAll();

    return Template::render("Employee/employer_create_room.tpl", ["sizes" => $Sizes, "locations" => $Locations, "is_employer" => $IsEmployer],['de_de.php']);
  }

  /**
   * @function legal
   * @return bool
   * @throws Exception
   *
   * Description
   *  renders a template with the german legal notes (german language only)
   *
   * Used Functions
   *  Template::render()
   */
  public function legal()
  {
    return Template::render("legal.tpl",["title" => "Impressum"], ['de_de.php']);
  }

  /**
   * @function room_evaluation
   * @return bool
   * @throws Exception
   *
   * Description
   *  shows an overview over the booked rooms and the workload
   *
   * Used Functions
   *  Request->get_globals()
   *  Template::render()
   */
  public function room_evaluation()
  {
    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }

    $DateTimeBegin = new DateTime("first day of this month");
    $DateTimeEnd = new DateTime("last day of this month");
    $Begin = $DateTimeBegin->format("Y-m-d H:i:s");
    $End = $DateTimeEnd->format("Y-m-d H:i:s");

    if(isset($this->REQUEST->get_globals()['POST']['ident']) && !empty($this->REQUEST->get_globals()['POST']['ident']))
    {
      if($this->REQUEST->get_globals()['POST']['ident'] == "eval")
      {
        $DateTimeEnd = DateTime::createFromFormat("d.m.Y", $this->REQUEST->get_globals()['POST']['end']);
        $DateTimeBegin = DateTime::createFromFormat("d.m.Y", $this->REQUEST->get_globals()['POST']['begin']);
        $Begin = $DateTimeBegin->format("Y-m-d H:i:s");
        $End = $DateTimeEnd->format("Y-m-d H:i:s");
      }
    }

    $Statement = $this->PDO->prepare("SELECT rt.size, Count(*) as booked_rooms FROM room r JOIN room_type rt on r.room_type_id = rt.id JOIN order_room o2 on r.id = o2.room_id LEFT JOIN `order` o on o2.order_id = o.id WHERE (o.contract_begin BETWEEN :begin AND :end AND o.contract_end IS NULL) OR (o.contract_begin <= :begin AND o.contract_end IS NULL) OR (o.contract_begin BETWEEN :begin AND :end AND o.contract_end BETWEEN :begin AND :end) OR (o.contract_begin <= :begin AND o.contract_end BETWEEN :begin AND :end) GROUP BY rt.size");
    $Result = $Statement->execute([
      "begin" => $Begin,
      "end" => $End
    ]);
    if(!$Result)
    {
      throw new Exception(print_r($Statement->errorInfo(), true));
    }

    $RoomStatement = $this->PDO->prepare("SELECT rt.size, count(*) as total_rooms FROM room r JOIN room_type rt on r.room_type_id = rt.id GROUP BY rt.size");
    $RoomResult = $RoomStatement->execute();
    if(!$RoomResult)
    {
      throw new Exception(print_r($RoomStatement->errorInfo(), true));
    }
    $Data = $RoomStatement->fetchAll(PDO::FETCH_ASSOC);

    $Rooms = array();
    while ($Room = $Statement->fetch(PDO::FETCH_ASSOC))
    {
      foreach ($Data as $Item)
      {
        if($Room['size'] == $Item['size'])
        {
          $Room['total'] = $Item['total_rooms'];
          $Room['utilization'] = $Room['booked_rooms'] * 100 / $Item['total_rooms'];
        }
      }
      array_push($Rooms, $Room);
    }
    return Template::render("Employee/employer_room_evaluation.tpl",["title" => "Room Evaluation", "is_employer" => $IsEmployer, "begin_date" => $DateTimeBegin->format("m/d/Y"), "end_date" => $DateTimeEnd->format("m/d/Y"), "rooms" => $Rooms], ['de_de.php']);
  }

  /**
   * @function password_reset
   * @return bool
   * @throws Exception
   *
   * Description
   *  small form for sending an password reset email
   *
   * Used Functions
   *  Request->get_globals()
   *  Template::render()
   */
  public function password_reset()
  {
    if(isset($this->REQUEST->get_globals()['POST']['email']) && !empty($this->REQUEST->get_globals()['POST']['email']))
    {
      $CustomerStatement = $this->PDO->prepare("SELECT uuid FROM customer WHERE email = ?");
      $EmployeeStatement = $this->PDO->prepare("SELECT uuid FROM employee WHERE email = ?");

      $CustomerResult = $CustomerStatement->execute([$this->REQUEST->get_globals()['POST']['email']]);
      $EmployeeResult = $EmployeeStatement->execute([$this->REQUEST->get_globals()['POST']['email']]);

      if(!$CustomerResult || !$EmployeeResult)
      {
        throw new Exception(print_r($CustomerStatement->errorInfo(), true) . "\n" . print_r($EmployeeStatement->errorInfo(),true));
      }
    }

    return Template::render("Frontend/password_reset.tpl", ['title' => 'Password reset','error' =>'',], ['de_de.php']);
  }

  /**
   * @function password_new
   * @return bool
   * @throws Exception
   *
   * Description
   *  shows a small form for setting a new password
   *
   * Used Functions
   *  Template::render()
   */
  public function password_new()
  {
    return Template::render("Frontend/password_new.tpl", ['title' => 'Password new','error' =>'',], ['de_de.php']);
  }

  /**
   * @function create_csv
   * @return bool
   * @throws Exception
   *
   * Description
   *  creates an .csv file with the order and paying information in a time period
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  UUID::v4()
   */
  public function create_csv()
  {
    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }

    $DateTimeBegin = new DateTime("first day of this month");
    $DateTimeEnd = new DateTime("last day of this month");

    if(isset($this->REQUEST->get_globals()['POST']['ident']) && !empty($this->REQUEST->get_globals()['POST']['ident']))
    {
      if($this->REQUEST->get_globals()['POST']['ident'] == "generate")
      {
        $DateTimeBegin = DateTime::createFromFormat("d.m.Y", $this->REQUEST->get_globals()['POST']['begin']);
        $DateTimeEnd = DateTime::createFromFormat("d.m.Y", $this->REQUEST->get_globals()['POST']['end']);
        $Begin = $DateTimeBegin->format("Y-m-d H:i:s");
        $End = $DateTimeEnd->format("Y-m-d H:i:s");

        $ExportStatement = $this->PDO->prepare("SELECT o.id, o.cost, c.firstname, c.lastname, c.street, c.hsnr, c.zip, c.city, c.country, c.bic, c.iban FROM `order` o JOIN customer c on o.customer_id = c.id WHERE (o.contract_begin BETWEEN :begin AND :end AND o.contract_end IS NULL) OR (o.contract_begin <= :begin AND o.contract_end IS NULL) OR (o.contract_begin BETWEEN :begin AND :end AND o.contract_end BETWEEN :begin AND :end) OR (o.contract_begin <= :begin AND o.contract_end BETWEEN :begin AND :end)");
        $ExportResult = $ExportStatement->execute([
          "begin" => $Begin,
          "end" => $End
        ]);
        if(!$ExportResult)
        {
          throw new Exception(print_r($ExportStatement->errorInfo(),true));
        }
        $ExportData = $ExportStatement->fetchAll(PDO::FETCH_ASSOC);
        if($ExportStatement->rowCount() > 0)
        {
          $ExportHeaders = array_keys($ExportData[0]);
          $CsvString = "\xEF\xBB\xBF";

          $CsvString .= $this->str_putcsv($ExportHeaders);

          foreach ($ExportData as $Item)
          {
            $CsvString .= "\n";
            $CsvString .= $this->str_putcsv($Item);
          }
          $TrueFilename = UUID::v4();
          $Filename = "Export_" . $DateTimeBegin->format("Y-m-d") . "_" . $DateTimeEnd->format("Y-m-d") . ".csv";
          $SaveExportStatement = $this->PDO->prepare("INSERT INTO exports (sha, begin_export, end_export, filename, create_date) VALUES (:sha, :begin, :end, :filename, NOW())");
          $SaveExportResult  = $SaveExportStatement->execute([
            "sha" => $TrueFilename,
            "begin" => $Begin,
            "end" => $End,
            "filename" => $Filename
          ]);
          if(!$SaveExportResult)
          {
            throw new Exception(print_r($SaveExportStatement->errorInfo(), true));
          }

          $Handle = fopen("exports/" . $TrueFilename . ".csv", "w");
          fwrite($Handle, $CsvString);
          fclose($Handle);
        }

        return $this->KERNEL->redirect_to_route("create_csv");
      }
    }

    $CsvStatement = $this->PDO->prepare("SELECT * FROM exports");
    $CsvResult = $CsvStatement->execute();
    if(!$CsvResult)
    {
      throw new Exception(print_r($CsvStatement->errorInfo(), true));
    }

    $CsvData = array();
    while ($Data = $CsvStatement->fetch(PDO::FETCH_ASSOC))
    {
      $Data['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/csv/?id=". $Data['sha'] ."&action=download\" class=\"ui basic red button\">Download</a>";
      array_push($CsvData, $Data);
    }

    $Begin = $DateTimeBegin->format("d.m.Y");
    $End = $DateTimeEnd->format("d.m.Y");

    return Template::render("Employee/employer_create_csv.tpl", ["begin" => $Begin, "end" => $End, "exports" => $CsvData, "is_employer" => $IsEmployer], ["de_de.php"]);
  }

  /**
   * @function csv_actions
   * @return bool
   * @throws Exception
   *
   * Description
   *  create different actions for interacting with new and consisting csv files
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   */
  public function csv_actions()
  {
    $DownloadId = $this->REQUEST->get_globals()['GET']['id'];
    $Action = $this->REQUEST->get_globals()['GET']['action'];

    switch ($Action)
    {
      case "download":
        $Statement = $this->PDO->prepare("SELECT * FROM exports WHERE sha = ?");
        $Result = $Statement->execute([$DownloadId]);
        if(!$Result)
        {
          throw new Exception(print_r($Statement->errorInfo(), true));
        }
        $Data = $Statement->fetch(PDO::FETCH_ASSOC);

        if(file_exists("exports/". $Data['sha'] . ".csv"))
        {
          copy("exports/". $Data['sha'] . ".csv", "temp/". $Data['sha'] . ".csv");
          $File = "temp/". $Data['sha'] . ".csv";
          if (file_exists($File)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($Data['filename']));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($File));
            ob_clean();
            flush();
            readfile($File);
            @unlink($File);
          }
        }
        break;
      default:
        break;
    }

    if ($_SESSION['priv_level'] == 350)
    {
      return $this->KERNEL->redirect_to_route("download");
    }
    elseif ($_SESSION['priv_level'] == 300 || $_SESSION['priv_level'] == 200)
    {
      return $this->KERNEL->redirect_to_route("create_csv");
    }
    else
    {
      return $this->KERNEL->redirect_to_route("index");
    }
  }

  /**
   * @function str_putcsv
   * @param $Input
   * @param string $Delimiter
   * @param string $Enclosure
   * @return string
   *
   * Description
   *  Creates CSV String by writing to php://temp and reading from it
   */
  private function str_putcsv($Input, $Delimiter = ',', $Enclosure = '"')
  {
    // Open a memory "file" for read/write...
    $FileHandle = fopen('php://temp', 'r+');
    // ... write the $input array to the "file" using fputcsv()...
    fputcsv($FileHandle, $Input, $Delimiter, $Enclosure);
    // ... rewind the "file" so we can read what we just wrote...
    rewind($FileHandle);
    // ... read the entire line into a variable...
    $Data = fread($FileHandle, 1048576);
    // ... close the "file"...
    fclose($FileHandle);
    // ... and return the $data to the caller, with the trailing newline from fgets() removed.
    return rtrim($Data, "\n");
  }

  /**
   * @function download
   * @return bool
   * @throws Exception
   *
   * Description
   *  renders a small form for downloading a .csv file
   *
   * Used Functions
   *  Template::render()
   */
  public function download()
  {
    $CsvStatement = $this->PDO->prepare("SELECT * FROM exports");
    $CsvResult = $CsvStatement->execute();
    if(!$CsvResult)
    {
      throw new Exception(print_r($CsvStatement->errorInfo(), true));
    }

    $CsvData = array();
    while ($Data = $CsvStatement->fetch(PDO::FETCH_ASSOC))
    {
      $Data['actions'] = "<a href=\"" . $this->CONFIG['globals']['base'] . "actions/csv/?id=". $Data['sha'] ."&action=download\" class=\"ui basic red button\">Download</a>";
      array_push($CsvData, $Data);
    }
    return Template::render("Frontend/download.tpl", ["exports" => $CsvData], ['de_de.php']);
  }

  /**
   * @function create_location
   * @return bool
   * @throws Exception
   *
   * Description
   *  Creates Location in Database form Form Data
   *
   * Used Functions
   *  Request->get_globals()
   *  Kernel->redirect_to_route()
   *  Template::render()
   */
  public function create_location()
  {
    $IsEmployer = 0;
    if($_SESSION['priv_level'] <= 200 && $_SESSION['priv_level'] > 0)
    {
      $IsEmployer = 1;
    }

    if(isset($this->REQUEST->get_globals()['POST']['ident']) && !empty($this->REQUEST->get_globals()['POST']['ident']))
    {
      if($this->REQUEST->get_globals()['POST']['ident'] == "location")
      {
        $Post = $this->REQUEST->get_globals()['POST'];
        $CreateStatement = $this->PDO->prepare("INSERT INTO location (street, hsnr, city, zip, country) VALUES (:steet, :hsnr, :city, :zip, :county)");
        $CreateResult = $CreateStatement->execute([
          "street" => $Post['street'],
          "hsnr" => $Post['number'],
          "city" => $Post['city'],
          "zip" => $Post['zip'],
          "country" => $Post['country']
        ]);
        if(!$CreateResult)
        {
          throw new Exception(print_r($CreateStatement->errorInfo(), true));
        }
        return $this->KERNEL->redirect_to_route("create_location");
      }
    }

    $LocationStatement = $this->PDO->prepare("SELECT * FROM location");
    $LocationResult = $LocationStatement->execute();
    if(!$LocationResult)
    {
      throw new Exception(print_r($LocationStatement->errorInfo(), true));
    }
    $Locations = $LocationStatement->fetchAll(PDO::FETCH_ASSOC);

    return Template::render("Employee/employer_create_location.tpl", ["locations" => $Locations, "is_employer" => $IsEmployer], ["de_de.php"]);
  }
}