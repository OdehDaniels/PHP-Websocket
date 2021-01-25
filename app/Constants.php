<?php

/**
 * Class ActiveStatus
 */

final class ActiveStatus
{
   const INACTIVE = '0';
   const ACTIVE = '1';
   const DORMANT = '2';

   /**
    * Returns respective value.
   *
   * @param $x
   *
   * @return null
   */
   public static function getValue($x)
   {
       $value = null;
       switch ($x) {
           case '0':
               $value = "Inactive";
               break;
           case '1':
               $value = "Active";
               break;
       case '2':
           $value = "Dormant";
           break;
       }

       return $value;
   }

   /**
    * Returns respective value.
   *
   * @param $x
   *
   * @return null
   */
   public static function getValueInHtml($x)
   {
       $value = null;
       switch ($x) {
           case '0':
               $value = '<span class="badge badge-secondary"> Inactive </span>';
               break;
           case '1':
               $value = '<span class="badge badge-success"> Active </span>';
               break;
           case '2':
               $value = '<span class="badge badge-warning"> Dormant </span>';
               break;
   }

       return $value;
   }
}

?>