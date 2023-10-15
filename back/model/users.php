<?php

function getUsersShort()
{
  $query = "SELECT 
  users.id,
  users.login,
  users.avatar_url,
  users.kind,
  users.is_staff,
  users.has_cursus21,
  users.has_cursus9,
  poolfilters.name AS poolfilter,
  users.hidden
  
  FROM users
  JOIN poolfilters ON users.poolfilter_id = poolfilters.id
  ORDER BY login
  ";

  $data = array();

  require_once("model/dbConnector.php");
  $result = executeQuerySelect($query, $data);

  return $result;
}

function getUsers($poolfilter = '')
{
  $query = "SELECT 
  users.id,
  users.login,
  users.first_name,
  users.last_name,
  users.display_name,
  users.avatar_url,
  users.grade,
  users.level,
  users.kind,
  users.is_staff,
  users.nbcursus,
  users.has_cursus21,
  users.has_cursus9,
  poolfilters.name AS poolfilter
  
  FROM users
  JOIN poolfilters ON users.poolfilter_id = poolfilters.id
  WHERE users.hidden = false
  AND (
       (:poolfilter = 'all')
    OR (:poolfilter = 'cursus' AND users.has_cursus21 = TRUE)
    OR (poolfilters.name LIKE CONCAT(:poolfilter,'%'))
    )
  ORDER BY login
  ";

  $data = array(":poolfilter" => $poolfilter);

  require_once("model/dbConnector.php");
  $result = executeQuerySelect($query, $data);

  return $result;
}

function setUser($userId, $value)
{
  $query = "UPDATE users
  SET hidden = :hidden
  WHERE id = :user_id";

  $data = array(":user_id" => $userId, ":hidden" => $value == "true" ? "TRUE" : "FALSE");

  return executeQueryAction($query, $data);
}




function get_user_projects($poolfilter, $projects)
{
  $query = "SELECT 
  users.id AS user_id,
  users.login,
  users.first_name,
  users.last_name,
  users.display_name,
  users.avatar_url,
  projects.id AS project_id,
  projects.slug AS project_slug,
  projects.main_cursus,
  MAX(teams.final_mark) AS final_mark
  
  FROM users

  JOIN team_user ON team_user.user_id = users.id
  JOIN teams ON teams.id = team_user.team_id
  JOIN projects ON projects.id = teams.project_id
  JOIN cursus ON cursus.id = projects.main_cursus
  LEFT JOIN project_types ON project_types.id = projects.project_type_id


  JOIN poolfilters ON users.poolfilter_id = poolfilters.id
  WHERE users.hidden = false
  AND (
       (:poolfilter = 'all')
    OR (:poolfilter = 'cursus' AND users.has_cursus21 = TRUE)
    OR (poolfilters.name LIKE CONCAT(:poolfilter,'%'))
    )
  AND projects.has_lausanne = TRUE
  AND (
       (:projects = cursus.slug)
    OR (:projects = project_types.name)
    )

  GROUP BY users.id, projects.id
  ORDER BY projects.corder, projects.id
  ";

  $data = array(":poolfilter" => $poolfilter, ":projects" => $projects);

  require_once("model/dbConnector.php");
  $result = executeQuerySelect($query, $data);

  return $result;
}

