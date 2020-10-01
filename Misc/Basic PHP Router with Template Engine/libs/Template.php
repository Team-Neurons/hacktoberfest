<?php
/**
 * Author: Echelon101
 * Filename: Template.php
 *
 * @version 2.1
 * Date: 09.11.2018
 * Time: 11:45
 * LastEdit: 06.02.2019
 *
 * Content:
 *  Template Engine to interpret Template Files
 *
 * Used Functions:
 *
 * Defined Functions:
 *  public __construct(string $TemplateDir = "", $LanguageDir = "")
 *  private load(string $File): bool
 *  private assign(string $Replace, string $Replacement)
 *  private load_language(array $Files): array
 *  private replace_lang_vars(array $Lang): void
 *  private parse_functions(): void
 *  private display(): bool
 *  private assign_globals(): void
 *  public static render(string $Template, array $Vars = null, array $Lang = null): bool
 *  private assign_array(string $Key, array $Values): void
 *  private parse_conditionals(array $AssignableValues): void
 *
 * Global Variables
 *  private $TEMPLATE_DIR
 *  private $LANGUAGE_DIR
 *  private $LEFT_DELIMITER
 *  private $RIGHT_DELIMITER
 *  private $LEFT_DELIMITER_FUNCTIONS
 *  private $RIGHT_DELIMITER_FUNCTIONS
 *  private $LEFT_DELIMITER_COMMENTS
 *  private $RIGHT_DELIMITER_COMMENTS
 *  private $LEFT_DELIMITER_LANGUAGE
 *  private $RIGHT_DELIMITER_LANGUAGE
 *  private $TEMPLATE_FILE
 *  private $LANGUAGE_FILES
 *  private $TEMPLATE_NAME
 *  private $Template
 *  private $Lang
 *  private $CONFIG
 *  private $ArrayLoopKey
 *  private $ArrayLoopValue
 *  private $ConditionalAssignArray
 */

class Template
{
  private $TEMPLATE_DIR = 'templates/';

  private $LANGUAGE_DIR = 'translations/';

  private $LEFT_DELIMITER = '{$';

  private $RIGHT_DELIMITER = '}';

  private $LEFT_DELIMITER_FUNCTIONS = '{';

  private $RIGHT_DELIMITER_FUNCTIONS = '}';

  private $LEFT_DELIMITER_COMMENTS = '\{\*';

  private $RIGHT_DELIMITER_COMMENTS  = '\*\}';

  private $LEFT_DELIMITER_LANGUAGE = '\{L_';

  private $RIGHT_DELIMITER_LANGUAGE = '\}';

  private $TEMPLATE_FILE = "";

  private $LANGUAGE_FILES = "";

  private $TEMPLATE_NAME = "";

  private $Template = "";

  private $Lang;

  private $CONFIG;

  private $ArrayLoopKey;

  private $ArrayLoopValues;

  private $ConditionalAssignArray;

  /**
   * @function __construct
   * @param string $TemplateDir
   * @param string $LanguageDir
   *
   * Description
   *  Template constructor.
   */
  public function __construct($TemplateDir = "", $LanguageDir = "")
  {
    if(!empty($TemplateDir))
    {
      $this->TEMPLATE_DIR = $TemplateDir;
    }

    if(!empty($LanguageDir))
    {
      $this->LANGUAGE_DIR = $LanguageDir;
    }

    $this->CONFIG = json_decode(file_get_contents("conf/config.json"), true);
  }

  /**
   * @function load
   * @param $File
   * @return bool
   * @throws Exception
   *
   * Description
   *  Loads Template File into a Variable
   */
  private function load($File)
  {
    $this->TEMPLATE_NAME = $File;
    $this->TEMPLATE_FILE = $this->TEMPLATE_DIR.$File;

    if(!empty($this->TEMPLATE_FILE))
    {
      if(file_exists($this->TEMPLATE_FILE))
      {
        $this->Template = file_get_contents($this->TEMPLATE_FILE);
      }
      else
      {
        throw new Exception("Template File: " . $File . " doesn't exist");
      }
    }
    else
    {
      return false;
    }

    $this->parse_functions();
    return true;
  }

  /**
   * @function assign
   * @param $Replace
   * @param $Replacement
   *
   * Description
   *  Replaces Template Keys with actual Values
   */
  private function assign($Replace, $Replacement)
  {
    $this->Template = str_replace($this->LEFT_DELIMITER . $Replace . $this->RIGHT_DELIMITER, $Replacement, $this->Template);
  }

  /**
   * @function load_language
   * @param array $Files
   * @return bool
   *
   * Description
   *  Loads Language Files into Variable
   */
  private function load_language(array $Files)
  {
    $this->LANGUAGE_FILES = $Files;

    for($i = 0; $i < count($this->LANGUAGE_FILES); $i++)
    {
      if(!file_exists($this->LANGUAGE_DIR . $this->LANGUAGE_FILES[$i]))
      {
        return false;
      }
      else
      {
        /** @noinspection PhpIncludeInspection */
        $Lang = (include $this->LANGUAGE_DIR . $this->LANGUAGE_FILES[$i]);
      }
    }

    /** @var array $Lang */
    $this->replace_lang_vars($Lang);

    return $Lang;
  }

  /**
   * @function replace_lang_vars
   * @param array $Lang
   *
   * Description
   *  Replaces Template Language Keys with Translation
   */
  private function replace_lang_vars(array $Lang)
  {
    $this->Lang = $Lang;

    $this->Template = preg_replace_callback("/".$this->LEFT_DELIMITER_LANGUAGE ."(.*)". $this->RIGHT_DELIMITER_LANGUAGE ."/isU",
      function ($Matches)
      {
        if(array_key_exists(strtolower($Matches[1]), $this->Lang)){
          return $this->Lang[strtolower($Matches[1])];
        }
        return ltrim($this->LEFT_DELIMITER_LANGUAGE, '\\') . $Matches[1] . ltrim($this->RIGHT_DELIMITER_LANGUAGE, '\\');
      },
      $this->Template);
  }

  /**
   * @function parse_functions
   *
   * Description
   *  Deletes Template Comments and Loads included Template Files into template variable
   */
  private function parse_functions()
  {
    while (preg_match("/". $this->LEFT_DELIMITER_FUNCTIONS . "include file=\"(.*)\.(.*)\"". $this->RIGHT_DELIMITER_FUNCTIONS . "/isUe", $this->Template))
    {
      $this->Template = preg_replace_callback("/". $this->LEFT_DELIMITER_FUNCTIONS . "include file=\"(.*)\.(.*)\"" . $this->RIGHT_DELIMITER_FUNCTIONS . "/isU",
        function ($Matches)
        {
          return file_get_contents($this->TEMPLATE_DIR. $Matches[1] . "." . $Matches[2]);
        },
        $this->Template);
    }

    $this->Template = preg_replace("/". $this->LEFT_DELIMITER_COMMENTS."(.*)".$this->RIGHT_DELIMITER_COMMENTS."/isU", "", $this->Template);
  }

  /**
   * @function display
   * @return bool
   *
   * Description
   *  Outputs template variable to Response
   */
  private function display()
  {
    echo $this->Template;
    return true;
  }

  /**
   * @function assign_globals
   *
   * Description
   *  Replaces Template Keys with values of config File
   */
  private function assign_globals()
  {
    $Globals = $this->CONFIG['globals'];
    foreach ($Globals as $Key => $Value)
    {
      $this->assign($Key, $Value);
    }
  }

  /**
   * @function render
   * @param string $Template
   * @param array $Vars
   * @param array|null $Lang
   * @return bool
   * @throws Exception
   *
   * Description
   *  Performs all necessary Actions to Render a Template File
   */
  public static function render(string $Template, array $Vars = null, array $Lang = null)
  {
    $TemplateEngine = new self();
    $TemplateEngine->load($Template);

    $TemplateEngine->assign_globals();
    if($Vars !== null)
    {
      $TemplateEngine->parse_conditionals($Vars);

      foreach ($Vars as $Key => $Value)
      {
        if (is_array($Value))
        {
          $TemplateEngine->assign_array($Key, $Value);
        }
        else
        {
          $TemplateEngine->assign($Key, $Value);
        }
      }
    }
    if($Lang !== null)
    {
      $TemplateEngine->load_language($Lang);
    }
    return $TemplateEngine->display();
  }

  /**
   * @function assign_array
   * @param string $Key
   * @param array $Values
   *
   * Description
   *  Interprets Array Parts in Templates
   */
  private function assign_array(string $Key, array $Values)
  {
    $this->ArrayLoopKey = $Key;
    $this->ArrayLoopValues = $Values;

    $this->Template = preg_replace_callback("/" . $this->LEFT_DELIMITER_FUNCTIONS . "for (.*) in " . $Key . $this->RIGHT_DELIMITER_FUNCTIONS . "(.*)" . $this->LEFT_DELIMITER_FUNCTIONS . "endfor". $this->RIGHT_DELIMITER_FUNCTIONS. "/isU",
      function ($Matches)
      {
        $ReturnValue = "";
        $PrimaryKey = $Matches[1];

        foreach($this->ArrayLoopValues as $Key => $ArrayValues)
        {
          $Temp = $Matches[2];
          if(is_array($ArrayValues))
          {
            foreach ($ArrayValues as $SubKey => $Value)
            {
              $Temp = str_replace($this->LEFT_DELIMITER. $PrimaryKey . "." . $SubKey. $this->RIGHT_DELIMITER, $Value ,$Temp);
            }
          }
          else
          {
            $Temp = str_replace($this->LEFT_DELIMITER. $PrimaryKey . $this->RIGHT_DELIMITER, $ArrayValues, $Temp);
          }
          $ReturnValue .= $Temp;
        }
        return $ReturnValue;
      }
      ,$this->Template);

    $this->ArrayLoopKey = null;
    $this->ArrayLoopValues = null;

  }

  /**
   * @function parse_conditionals
   * @param array $AssignableValues
   *
   * Description
   *  Interprets If Statements in Template Files
   */
  private function parse_conditionals(array $AssignableValues)
  {
    $this->ConditionalAssignArray = $AssignableValues;

    while(preg_match("/" . $this->LEFT_DELIMITER_FUNCTIONS . "if (.*)" . $this->RIGHT_DELIMITER_FUNCTIONS . "(.*)" . $this->LEFT_DELIMITER_FUNCTIONS . "endif" . $this->RIGHT_DELIMITER_FUNCTIONS . "/isUe", $this->Template)) {
      $this->Template = preg_replace_callback("/" . $this->LEFT_DELIMITER_FUNCTIONS . "if (.*)" . $this->RIGHT_DELIMITER_FUNCTIONS . "(.*)" . $this->LEFT_DELIMITER_FUNCTIONS . "endif" . $this->RIGHT_DELIMITER_FUNCTIONS . "/isU",
        function ($Matches)
        {
          $Condition = "&& " . $Matches[1];
          $Body = $Matches[2];
          $Done = false;
          $CurrentResult = true;

          while (!$Done)
          {
            /*
             * 0 - Operator
             * 1 - Condition Val 1
             * 2 - Condition Operator
             * 3 - Condition Val 2
             * 4 - SScanF Length
             */
            $Result = sscanf($Condition, "%s %s %s %s%n");
            //var_dump($result);
            if (is_array($Result) && count(array_keys($Result)) == 5)
            {
              if ($Result[0] == "&&")
              {
                if (!$CurrentResult)
                {
                  $Done = true;
                  continue;
                }
              } elseif ($Result[0] == "||")
              {
                if ($CurrentResult)
                {
                  $Done = true;
                  continue;
                }
              }
              $ConditionValue1 = $Result[1];
              $ConditionValue2 = $Result[3];

              if (preg_match('/^\$[a-z0-9]/i', $Result[1]) == 1)
              {
                if (!array_key_exists(ltrim($ConditionValue1, "$"), $this->ConditionalAssignArray))
                {
                  throw new Exception(sprintf("Value %s in Assign Array doesnt exist", $ConditionValue1));
                }
                $ConditionValue1 = $this->ConditionalAssignArray[ltrim($ConditionValue1, "$")];
              }

              if (preg_match('/^\$[a-z0-9]/i', $Result[3]) == 1 && array_key_exists(ltrim($ConditionValue2, "$"), $this->ConditionalAssignArray))
              {
                if (!array_key_exists(ltrim($ConditionValue2, "$"), $this->ConditionalAssignArray))
                {
                  throw new Exception(sprintf("Value %s in Assign Array doesnt exist", $ConditionValue2));
                }
                $ConditionValue2 = $this->ConditionalAssignArray[ltrim($ConditionValue2, "$")];
              }

              switch ($Result[2])
              {
                case '==':
                  if ($ConditionValue1 == $ConditionValue2)
                  {
                    $CurrentResult = true;
                  }
                  else
                  {
                    $CurrentResult = false;
                  }
                  break;
                case '!=':
                  if ($ConditionValue1 != $ConditionValue2)
                  {
                    $CurrentResult = true;
                  }
                  else
                  {
                    $CurrentResult = false;
                  }
                  break;
                case "<=":
                  if ($ConditionValue1 <= $ConditionValue2)
                  {
                    $CurrentResult = true;
                  }
                  else
                  {
                    $CurrentResult = false;
                  }
                  break;
                case ">=":
                  if ($ConditionValue1 >= $ConditionValue2)
                  {
                    $CurrentResult = true;
                  }
                  else
                  {
                    $CurrentResult = false;
                  }
                  break;
                case "<":
                  if ($ConditionValue1 < $ConditionValue2)
                  {
                    $CurrentResult = true;
                  }
                  else
                  {
                    $CurrentResult = false;
                  }
                  break;
                case ">":
                  if ($ConditionValue1 > $ConditionValue2)
                  {
                    $CurrentResult = true;
                  }
                  else
                  {
                    $CurrentResult = false;
                  }
                  break;
              }
              $Condition = substr($Condition, $Result[4]);

            }
            else
            {
              $Done = true;
            }
          }

          $ElseCount = substr_count($Body, "{else}");

          if ($ElseCount > 1)
          {
            throw new Exception("You Cant have more than one else statement");

          }
          if ($ElseCount == 1)
          {
            $Split = explode("{else}", $Body);
            $ConditionTrueBody = $Split[0];
            $ConditionFalseBody = $Split[1];
            if ($CurrentResult)
            {
              return $ConditionTrueBody;
            }
            else
            {
              return $ConditionFalseBody;
            }
          }
          if ($CurrentResult)
          {
            return $Body;
          }
          else
          {
            return "";
          }
        },
        $this->Template);
    }
    $this->ConditionalAssignArray = null;
  }
}