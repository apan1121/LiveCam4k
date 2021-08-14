<?php
namespace Lib\SpreedSheet;

/**
 * Google_Spreadsheet_Client
 *
 * @class Client for Google Spreadsheet (Sheets API v4)
 */

class Google_Spreadsheet_Client {

  public $client = null;

  /**
   * @constructor
   * @param string|array $key
   */
  public function __construct ($key_file = null) {
    if ($key_file) {
      $this->client = new \Google_Client();
      $this->client->setAuthConfig($key_file);
      $this->client->setScopes(array(
        \Google_Service_Sheets::SPREADSHEETS_READONLY
      ));
    }
  }

  /**
   * Generate File instance
   *
   * @param string $id
   */
  public function file ($id) {
    return new Google_Spreadsheet_File($id, $this->client);
  }


  public function spreadSheet($id) {
    return new Google_Spreadsheet_Sheet($id, $this->client);
  }

}
