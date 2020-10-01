<?php
/**
 * Source: PHP.net
 * Author: Timo Strüker
 * Filename: UUID.php
 *
 * @version 1.0
 * Date: 12.11.2018
 * Time: 08:17
 * LastEdit: 06.02.2019
 *
 * Content:
 *  Generator and Validator for Unique Universal Identifiers
 *
 * Used Functions:
 *
 * Defined Functions:
 *  public static v3(string $Namespace, string $Name): string|bool
 *  public static v4(): string
 *  public static v5(string $Namespace, string $Name): string|bool
 *  public static is_valid(string $Namespace): bool
 *
 */

class UUID
{
  /**
   * @function v3
   * @param $Namespace
   * @param $Name
   * @return bool|string
   *
   * Description:
   *  Generates a UUIDv3
   *
   * Used Functions
   *  is_valid()
   */
  public static function v3($Namespace, $Name)
  {
    if(!self::is_valid($Namespace)) return false;

    // Get hexadecimal components of namespace
    $Hex = str_replace(array('-','{','}'), '', $Namespace);

    // Binary Value
    $String = '';

    // Convert Namespace UUID to bits
    for($i = 0; $i < strlen($Hex); $i+=2)
    {
      $String .= chr(hexdec($Hex[$i].$Hex[$i+1]));
    }

    // Calculate hash value
    $Hash = md5($String . $Name);

    return sprintf('%08s-%04s-%04x-%04x-%12s',

      // 32 bits for "time_low"
      substr($Hash, 0, 8),

      // 16 bits for "time_mid"
      substr($Hash, 8, 4),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 3
      (hexdec(substr($Hash, 12, 4)) & 0x0fff) | 0x3000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($Hash, 16, 4)) & 0x3fff) | 0x8000,

      // 48 bits for "node"
      substr($Hash, 20, 12)
    );
  }

  /**
   * @function v4
   * @return string
   *
   * Description:
   *  Generates a UUIDv4
   */
  public static function v4()
  {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }

  /**
   * @function v5
   * @param $Namespace
   * @param $Name
   * @return bool|string
   *
   * Description
   *  Generates a UUIDv5
   *
   * Used Functions:
   *  is_valid()
   */
  public static function v5($Namespace, $Name)
  {
    if(!self::is_valid($Namespace)) return false;

    // Get hexadecimal components of namespace
    $Hex = str_replace(array('-','{','}'), '', $Namespace);

    // Binary Value
    $String = '';

    // Convert Namespace UUID to bits
    for($i = 0; $i < strlen($Hex); $i+=2)
    {
      $String .= chr(hexdec($Hex[$i].$Hex[$i+1]));
    }

    // Calculate hash value
    $Hash = sha1($String . $Name);

    return sprintf('%08s-%04s-%04x-%04x-%12s',

      // 32 bits for "time_low"
      substr($Hash, 0, 8),

      // 16 bits for "time_mid"
      substr($Hash, 8, 4),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 5
      (hexdec(substr($Hash, 12, 4)) & 0x0fff) | 0x5000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($Hash, 16, 4)) & 0x3fff) | 0x8000,

      // 48 bits for "node"
      substr($Hash, 20, 12)
    );
  }

  /**
   * @function is_valid
   * @param $Uuid
   * @return bool
   *
   * Description:
   *  Checks if Uuid is Valid
   */
  public static function is_valid($Uuid)
  {
    return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
        '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $Uuid) === 1;
  }
}