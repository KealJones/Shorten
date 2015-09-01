<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * ShortModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class ShortModel
{
    protected static $chars = "123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
    protected static $table = "short_urls";
    protected static $checkUrlExists = true;
    protected static $shortUrlPrefix = "http://xal.rocks/*";
    
    public static function createShort($url)
    {
        try {
            $code = ShortModel::urlToShortCode($url);
            Session::add('feedback_positive', 'SUCCESS! SHORT URL: ' . ShortModel::$shortUrlPrefix . $code);
            return true;
        }
        catch (Exception $e) {
            // log exception and then redirect to error page.
            Session::add('feedback_negative', 'URL SHORTENING FAILED');
            return false;
        }
    }
    
    public static function getUrlFromDb($code)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        
        $sql   = "SELECT id, long_url FROM " . ShortModel::$table . " WHERE short_code = :short_code LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(
            "short_code" => $code
        ));
        
        $result = $query->fetch();
        // $print_r($result);
        return (empty($result)) ? false : $result;
    }
    
    protected static function incrementCounter($id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        
        $sql   = "UPDATE " . ShortModel::$table . " SET counter = counter + 1 WHERE id = :id";
        $query = $database->prepare($sql);
        $stmt->execute(array(
            "id" => $id
        ));
    }
    
    public static function urlToShortCode($url)
    {
        
        if (empty($url)) {
            throw new Exception("No URL was supplied.");
        }
        
        if (ShortModel::validateUrlFormat($url) == false) {
            throw new Exception("URL does not have a valid format.");
        }
        
        $shortCode = ShortModel::getUrlFromDb($url);
        if ($shortCode == false) {
            $shortCode = ShortModel::createShortCode($url);
        }
        
        //printf('<p>short code='. $shortCode . '</p>');
        return $shortCode;
    }
    
    protected static function validateUrlFormat($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }
    
    protected static function verifyUrlExists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return (!empty($response) && $response != 404);
    }
    
    protected static function urlExistsInDb($url)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql      = "SELECT short_code FROM " . ShortModel::$table . " WHERE long_url = :long_url LIMIT 1";
        $query    = $database->prepare($sql);
        $params   = array(
            "long_url" => $url
        );
        $query->execute($params);
        
        $result = $query->fetch();
        return (empty($result)) ? false : true;
    }
    
    protected static function createShortCode($url)
    {
        $id        = ShortModel::insertUrlInDb($url);
        $shortCode = ShortModel::convertIntToShortCode($id);
        ShortModel::insertShortCodeInDb($id, $shortCode);
        return $shortCode;
    }
    
    protected static function insertUrlInDb($url)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql      = "INSERT INTO " . ShortModel::$table . " (long_url) VALUES (:long_url)";
        $query    = $database->prepare($sql);
        $params   = array(
            "long_url" => $url
        );
        $query->execute($params);
        
        return $database->lastInsertId();
    }
    
    protected static function convertIntToShortCode($id)
    {
        $id = intval($id);
        if ($id < 1) {
            throw new Exception("The ID is not a valid integer");
        }
        
        $length = intval(strlen(ShortModel::$chars));
        // make sure length of available characters is at
        // least a reasonable minimum - there should be at
        // least 10 characters
        if ($length < 10) {
            throw new Exception("Length of chars is too small");
        }
        
        $code = "";
        while ($id > $length - 1) {
            // determine the value of the next higher character
            // in the short code should be and prepend
            $code = ShortModel::$chars[intval(fmod($id, $length))] . $code;
            // reset $id to remaining value to be converted
            $id   = intval(floor($id / $length));
        }
        
        // remaining value of $id is less than the length of
        // ShortModel::$chars
        $code = ShortModel::$chars[$id] . $code;
        
        return $code;
    }
    
    protected static function insertShortCodeInDb($id, $code)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        if ($id == null || $code == null) {
            throw new Exception("Input parameter(*) invalid.");
        }
        $sql    = "UPDATE " . ShortModel::$table . " SET short_code = :short_code WHERE id = :id";
        $query  = $database->prepare($sql);
        $params = array(
            "short_code" => $code,
            "id" => $id
        );
        $query->execute($params);
        
        if ($query->rowCount() < 1) {
            throw new Exception("Row was not updated with short code.");
        }
        
        return true;
    }
    public static function shortCodeToUrl($code, $increment = true)
    {
        if (empty($code)) {
            throw new Exception("No short code was supplied.");
        }
        
        if (ShortModel::validateShortCode($code) == false) {
            throw new Exception("Short code does not have a valid format.");
        }
        
        $urlRow = ShortModel::getUrlFromDb($code);
        
        if (empty($urlRow)) {
            throw new Exception("Short code does not appear to exist.");
        }
        
        if ($increment == true) {
            ShortModel::incrementCounter($urlRow["id"]);
        }
        
        return $urlRow["long_url"];
    }
    
    protected static function validateShortCode($code)
    {
        return preg_match("|[" . ShortModel::$chars . "]+|", $code);
    }
    
    
}
