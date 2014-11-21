<?php

/** @brief Class for MySqli static helper functions.
 *
 * Helper functions does things that MySli does not support natively. 
 * Such as getting array results instead of single values.
 * Also has a custom connection function used for PHP websites. 
 */
class DataBase{
   //! @name Constant SQL Variables
   //@{
   /** @brief Hard coded SQL variable for connection. 
    *
    * They are hard coded into the file for security and modularity. 
    * These constant variables are used for connecting to MySQL.
    */
   const host = "localhost";
   const pass = "";
   //@}
   
   //!
   //! Return an array of data from SQL. Key are values from SQL. 
   //! In array, returns all data in SQL except for $id key and value.
   //! It is very redundant to return the value in which you passed.
   //!
   public static function getResult($sql, $table ,$id, $idVal){
      $stmt = $sql->prepare("SELECT * FROM ".$table." WHERE ".$id." = '".$idVal."'");
      $stmt->execute();
      
      $meta = $stmt->result_metadata();
      
      while($field = $meta->fetch_field()){
         $var = $field->name;
         $$var = null;
         $parameters[$field->name] = &$$var;
      }
      
      call_user_func_array(array($stmt, 'bind_result'), $parameters);
      
      while($stmt->fetch()){
         $stmt->close();
         unset($parameters[$id]);
         return $parameters;
      }
      
      $stmt->close();
      echo "Error fetching data";
      return false;
   }

   //!
   //! Connects to MySQL given a user and data. Returns Mysli object.
   //! host and password is hard coded. Mainly because of security and modularity.
   //!
   public static function connectMySQL($user, $database){
      /* Database config */

      $db_host	= self::host;
      $db_user	= $user;
      $db_pass	= self::pass;
      $db_database = $database; 

      /* End config */

      $link = new mysqli($db_host,$db_user,$db_pass);

      if ($link->connect_error) {
          die('Connect Error (' . $link->connect_errno . ') '
                  . $link->connect_error);
      }

      $link->select_db($db_database);
      $link->set_charset("utf8");
      $link->query("SET names utf8");
      return $link;
   }
}

?>