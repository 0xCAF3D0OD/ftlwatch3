<?php


function getUserPages($user_id)
{
  $query = "SELECT pages.id, pages.name, 
  COALESCE(pages.icon, submenus.icon) AS icon,
  COALESCE(pages.route, submenus.route) AS route, pages.basefilter, 
  COALESCE(submenus.id, -1) AS submenu_id, 
  submenus.name AS subname, submenus.icon AS subicon FROM pages 
  LEFT JOIN submenus ON pages.submenu_id = submenus.id

  ORDER BY submenus.order, pages.order
  ";
  // WHERE users_groups.user_id = :user_id

  // $data = array(":user_id" => $user_id);
  $data = array();
  //   print_r($query);

  require_once("model/dbConnector.php");
  $result = executeQuerySelect($query, $data);

  // jsonlogger('asd', $result, LOGGER_DEBUG());

  $realResult = array();
  foreach ($result as $elem) {

    $submenu = $elem["submenu_id"];

    if ($submenu != -1) {
      $flag = false;

      foreach ($realResult as &$testResult) {
        if ($testResult["submenu_id"] == $submenu) {

          unset($elem["subname"]);
          unset($elem["subicon"]);

          array_push($testResult["list"], $elem);
          $flag = true;
          break;
        }
      }

      if ($flag == false) {
        $submenu = $elem;

        unset($elem["subname"]);
        unset($elem["subicon"]);

        // $submenu["id"] = "sub_" . $submenu["id"];
        $submenu["name"] = $submenu["subname"];
        $submenu["icon"] = $submenu["subicon"];

        unset($submenu["subname"]);
        unset($submenu["subicon"]);
        unset($submenu["basefilter"]);

        $submenu["list"] = array($elem);
        array_push($realResult, $submenu);
      }

    } else {
      unset($elem["subname"]);
      unset($elem["subicon"]);

      array_push($realResult, $elem);
    }
  }

  return $realResult;
}


function getUserGroups()
{
  $query = "SELECT groups.id, groups.name 
  FROM groups
  WHERE groups.owner_id IS NULL";
  $data = array();

  require_once("model/dbConnector.php");
  $groups = executeQuerySelect($query, $data);
  $group_ids = array_fill_keys(array_column($groups, "id"), false);


  $query = "SELECT 
    login_users.id, 
    login_users.login, 
    groups.id AS group_id
    FROM login_users 
    
    LEFT JOIN groups_login_users ON groups_login_users.login_user_id = login_users.id
    LEFT JOIN groups ON groups.id = groups_login_users.group_id
    
    WHERE groups.owner_id IS NULL";

  $data = array();

  require_once("model/dbConnector.php");
  $users = executeQuerySelect($query, $data);


  $users_groups = array();

  foreach (array_unique(array_column($users, 'id')) as $user) {
    $users_groups[$user] = $group_ids;
  }

  foreach ($users as $user) {
    $users_groups[$user['id']]['id'] = $user['id'];
    $users_groups[$user['id']]['login'] = $user['login'];
    if ($user['group_id'] != null) {
      $users_groups[$user['id']][$user['group_id']] = true;
    }
  }


  return array($groups, array_values($users_groups));
}

function setUserGroup($userId, $groupId, $value)
{
  $query = "SELECT id FROM groups_login_users
  WHERE login_user_id = :user_id AND group_id = :group_id";

  $data = array(":user_id" => $userId, ":group_id" => $groupId);

  require_once("model/dbConnector.php");
  $user_group = executeQuerySelect($query, $data);

  if (count($user_group) >= 1 && $value == 'false') {

    $query = "DELETE FROM groups_login_users
    WHERE login_user_id = :user_id AND group_id = :group_id";

    $data = array(":user_id" => $userId, ":group_id" => $groupId);

    return executeQueryAction($query, $data);
  }
  else if (count($user_group) == 0 && $value == 'true') {

    $query = "INSERT INTO groups_login_users (login_user_id, group_id)
    VALUES (:user_id, :group_id)";

    $data = array(":user_id" => $userId, ":group_id" => $groupId);

    return executeQueryAction($query, $data);
  }
  else {
    return false;
  }

  return true;
}



function setUserGroupBySlugs($userId, $groupsSlugs)
{
  $query = "SELECT id, slug FROM groups";

  $data = array();

  require_once("model/dbConnector.php");
  $groups = executeQuerySelect($query, $data);

  // jsonLogger('affasf', $groups, LOGGER_DEBUG());

  $groups = array_filter($groups, function ($v) use($groupsSlugs) { return in_array($v["slug"], $groupsSlugs); });


  $query = "INSERT INTO groups_login_users (login_user_id, group_id)
  VALUES (:user_id, :group_id)";

  $newdata = array_map(function ($v) use($userId) {return array(":user_id" => $userId, ":group_id" => $v);}, array_column($groups, "id"));
  // jsonLogger('affasf', $newdata, LOGGER_DEBUG());

  return executeQueryAction($query, $newdata, true);
}


function getGroupPerms()
{
  $query = "SELECT permissions.id, permissions.name FROM permissions";
  $data = array();

  require_once("model/dbConnector.php");
  $perms = executeQuerySelect($query, $data);
  $perms_ids = array_fill_keys(array_column($perms, "id"), false);


  $query = "SELECT 
      groups.id, 
      groups.name, 
      permissions.id AS permission_id
      FROM groups 
      
      LEFT JOIN groups_permissions ON groups_permissions.group_id = groups.id
      LEFT JOIN permissions ON permissions.id = groups_permissions.permission_id";

  $data = array();

  require_once("model/dbConnector.php");
  $groups = executeQuerySelect($query, $data);

  // jsonLogger('', $groups, LOGGER_DEBUG());

  $groups_perms = array();

  foreach (array_unique(array_column($groups, 'id')) as $group) {
    $groups_perms[$group] = $perms_ids;
  }

  foreach ($groups as $group) {
    $groups_perms[$group['id']]['id'] = $group['id'];
    $groups_perms[$group['id']]['name'] = $group['name'];
    if ($group['permission_id'] != null) {
      $groups_perms[$group['id']][$group['permission_id']] = true;
    }
  }

  return array($perms, array_values($groups_perms));
}


function setGroupPerm($groupId, $permId, $value)
{
  $query = "SELECT id FROM groups_permissions
  WHERE group_id = :group_id AND permission_id = :perm_id";

  $data = array(":group_id" => $groupId, ":perm_id" => $permId);

  require_once("model/dbConnector.php");
  $group_perm = executeQuerySelect($query, $data);

  if (count($group_perm) >= 1 && $value == 'false') {

    $query = "DELETE FROM groups_permissions
    WHERE group_id = :group_id AND permission_id = :permission_id";

    $data = array(":group_id" => $groupId, ":permission_id" => $permId);

    return executeQueryAction($query, $data);
  }
  else if (count($group_perm) == 0 && $value == 'true') {

    $query = "INSERT INTO groups_permissions (group_id, permission_id)
    VALUES (:group_id, :permission_id)";

    $data = array(":group_id" => $groupId, ":permission_id" => $permId);

    return executeQueryAction($query, $data);
  }
  else {
    return false;
  }

  return true;
}

function needOnePermission($perms)
{

  return true;

  // $query = "SELECT permissions.id, permissions.name, users_groups FROM permissions 
  // JOIN groups_permissions ON permissions.id = groups_permissions.permission_id
  // JOIN groups ON groups_permissions.group_id = groups.id
  // JOIN users_groups ON groups.id = users_groups.group_id
  // JOIN users ON users_groups.user_id = users.id

  // WHERE users_groups.user_id = :user_id";

  // $data = array(":user_id" => $user_id);
  // //   print_r($query);

  // require_once("model/dbConnector.php");
  // $result = executeQuerySelect($query, $data);

  // if (count($result) === 1) {
  //   $result = $result[0];

  //   if ($checkPassword) {
  //     return (password_verify($password, $result["password"]));
  //   }

  //   return true;
  // }

  // return false;
}




// //Request given user's password and verify it
// function has_permission($login, $password, $checkPassword = true)
// {
//   $query = "SELECT id, login, password FROM login_users WHERE login = :login";
//   $data = array(":login" => $login);
//   //   print_r($query);

//   require_once("model/dbConnector.php");
//   $result = executeQuerySelect($query, $data);

//   if (count($result) === 1) {
//     $result = $result[0];

//     if ($checkPassword) {
//       return (password_verify($password, $result["password"]));
//     }

//     return true;
//   }

//   return false;
// }



// function static_permission($perm_name, $should_have = true)
// {

//   if ($perm_name == "self") {

//   }


//   $query = "SELECT id, login, password FROM login_users WHERE login = :login";
//   $data = array(":login" => $login);
//   //   print_r($query);

//   require_once("model/dbConnector.php");
//   $result = executeQuerySelect($query, $data);

//   if (count($result) === 1) {
//     $result = $result[0];

//     if ($checkPassword) {
//       return (password_verify($password, $result["password"]));
//     }

//     return true;
//   }

//   return false;
// }



// //Get user informations for session storage, (username, join date, score)
// function getUserInfos($login)
// {
//   $query = "SELECT id, login, display_name, avatar_url
//   FROM login_users
//   WHERE login = :login";

//   $data = array(":login" => $login);

//   require_once("model/dbConnector.php");
//   $result = executeQuerySelect($query, $data);

//   if (count($result) === 1) {
//     $result = $result[0];

//     return array(
//       "id" => $result["id"],
//       "login" => $result["login"],
//       "display_name" => $result["display_name"],
//       "avatar_url" => $result["avatar_url"]
//     );
//   }

//   return array("error" => "Not found");
// }

// function createAccount($id, $login, $firstname, $lastname, $displayname, $avatar_url, $color)
// {
//   $query = "INSERT INTO login_users (id, login, password, first_name, last_name, display_name, avatar_url, color)
//   VALUES (:id, :login, NULL, :first_name, :last_name, :display_name, :avatar_url, :color)";

//   $data = array(
//     ":id" => $id,
//     ":login" => $login,
//     ":first_name" => $firstname,
//     ":last_name" => $lastname,
//     ":display_name" => $displayname,
//     ":avatar_url" => $avatar_url,
//     ":color" => $color,
//   );

//   $success = executeQueryAction($query, $data);

//   return $success;
// }


// function updateAccount($id, $login, $firstname, $lastname, $displayname, $avatar_url, $color)
// {
//   $query = "UPDATE login_users SET 
//     first_name = :first_name, 
//     last_name = :last_name, 
//     display_name = :display_name, 
//     avatar_url = :avatar_url, 
//     color =  :color
//     WHERE id = :id AND login = :login";

//   $data = array(
//     ":id" => $id,
//     ":login" => $login,
//     ":first_name" => $firstname,
//     ":last_name" => $lastname,
//     ":display_name" => $displayname,
//     ":avatar_url" => $avatar_url,
//     ":color" => $color,
//   );

//   $success = executeQueryAction($query, $data);

//   return $success;
// }

// //Add a new user to the database
// // function createUser($userEmail, $userName, $userPassword)
// // {
// //   $query = "SELECT COUNT(id) AS count FROM users WHERE email = :email";
// //   $data = array(":email" => $userEmail);

// //   require_once("model/dbConnector.php");
// //   $emailExist = executeQuerySelect($query, $data);

// //   //Stops if user's email already exists
// //   if($emailExist[0]["count"] > 0)
// //   {
// //     // throw new EmailAlreadyExistException();
// //   }

// //   $hashedPassword = password_hash($userPassword, PASSWORD_BCRYPT);
// //   $creationDate = date("Y-m-d");

// //   $query = "INSERT INTO users (email, username, password, creationDate)
// //   VALUES (:email, :username, :password, :creationDate)";

// //   $data = array(":email" => $userEmail, ":username" => $userName,
// //   ":password" => $hashedPassword, ":creationDate" => $creationDate);

// //   $success = executeQueryAction($query, $data);

// //   return $success;
// // }