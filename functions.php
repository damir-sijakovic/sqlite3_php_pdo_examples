<?php

require_once (__DIR__ . '/config.php');

/**********************************************************************\
 Creates new database in file 'project.db' and returns $pdo object.
 If already file 'project.db' exists it skips creation.
 Return PDO object. PDO object is created only once.
\**********************************************************************/
function initDatabase()
{
    static $pdo;
    
    if (isset($pdo))
    {
        return $pdo;
    }
    
    if (!file_exists(SQLT_EXMP_DB_FILE))
    {
        echo "Creating database... <br/>";
        $pdo = new \PDO('sqlite:' . SQLT_EXMP_DB_FILE);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);       
        $sql = file_get_contents(SQLT_EXMP_SQL_FILE);
        $pdo->exec('PRAGMA foreign_keys = ON');            
        $pdo->exec($sql);
        return $pdo;
    }
    else
    {
        $pdo = new \PDO('sqlite:' . SQLT_EXMP_DB_FILE);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);      
        return $pdo;  
    }  
}

/**********************************************************************\
 Inserts $username, $email, $password data. 
 Returns last insert id.
\**********************************************************************/
function insertUser($username, $email, $password)
{    
    $pdo = initDatabase();
    
    $sql = "INSERT INTO users(username, email, password, createdAt) VALUES ( :username , :email , :password , datetime('now') )";
    $stmt = $pdo->prepare($sql);  

    $data = [
        'username' => $username, 
        'email' =>    $email, 
        'password' => $password,
    ];

    try 
    {  
        $stmt->execute($data);                 
        $lastId = intval($pdo->lastInsertId());
        return $lastId;
    }
    catch (\PDOException $e)
    {           
       echo ('ERROR => ' . $e);
    } 
    
    return -1; 
}


/**********************************************************************\
 Returns all records from table users.
\**********************************************************************/
function getAll()
{    
    $pdo = initDatabase();
    
    $sql = " SELECT * FROM users ";
    $stmt = $pdo->prepare($sql);  

    try 
    {  
        if ($stmt->execute())
        {      
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($result)
            {
                return $result;
            }
            else
            {
                echo 'No data found.';
                return null;
            } 
        }
    }
    catch (\PDOException $e)
    {           
        echo ('ERROR => ' . $e);
    }  
    
    return null;
}
 
/**********************************************************************\
 Returns single record or null if nothing was found.
\**********************************************************************/
function findExact($username)
{    
    $pdo = initDatabase();
    
    $sql = " SELECT * FROM users WHERE username = :username ";
    $stmt = $pdo->prepare($sql);  

    $data = [
        'username' => $username, 
    ];

    try 
    {  
        if ($stmt->execute($data))
        {      
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);  
            if ($result)
            {
                return $result;
            }
            else
            {
                echo 'findExact => Nothing found!';
                return null;
            }
        }
    }
    catch (\PDOException $e)
    {           
        echo ('ERROR => ' . $e);
    }  
    
    return null;
}
 
/**********************************************************************\
 Search, returns all records with username like... 
\**********************************************************************/
function findLike($username)
{    
    $pdo = initDatabase();
    
    $sql = " SELECT * FROM users WHERE username LIKE :username ";
    $stmt = $pdo->prepare($sql);  

    $prepare = [     
        'username' => "%". $username ."%"  
    ]; 

    try 
    {  
        if ($stmt->execute($prepare))
        {      
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($result)
            {                
                return $result;  
            }
            else
            {
                echo 'findLike => Nothing found!';
                return null;
            } 
        }
    }
    catch (\PDOException $e)
    {           
        echo ('ERROR => ' . $e);
    }  
    
    return null;
}
 
/**********************************************************************\
 Return id of last inserted record or -1. 
\**********************************************************************/
function getLastRecord()
{        
    $pdo = initDatabase();
    $sql = " SELECT * FROM users ORDER BY id DESC LIMIT 1 ";
    $stmt = $pdo->prepare($sql);  
    
    try 
    {  
        if ($stmt->execute())
        {      
            $result = $stmt->fetch(\PDO::FETCH_ASSOC); 
            if ($result)
            {
                return $result;
            }
            else
            {
                return -1;
            }
        }
    }
    catch (\PDOException $e)
    {      
        echo ('ERROR => ' . $e);
    }  
    
    return -1;        
} 
 
 
/**********************************************************************\
 Delete record by id. Returns null if nothing was changed.
\**********************************************************************/
function deleteById($id)
{        
    $pdo = initDatabase();
    $sql = "DELETE FROM users WHERE id = :id ";
    $stmt = $pdo->prepare($sql);  
    
    $prepare = [
        'id' => $id  
    ];
    
    try 
    {  
        if ($stmt->execute($prepare))
        {
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0)
            {
                return $stmt->rowCount();
            }
            else
            {
               return null; 
            }
        }
    }
    catch (\PDOException $e)
    {      
        echo ('ERROR => ' . $e);
    }  
    
    return null;        
} 
 
 
/**********************************************************************\
 Is table empty?
\**********************************************************************/
function isEmpty($id)
{      
    $pdo = initDatabase();  
    $sql = " SELECT count(*) FROM (select 0 from users limit 1) ";
    $stmt = $pdo->prepare($sql);  
    
    try 
    {  
        if ($stmt->execute())
        {      
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);  
            if ($result['count(*)'])
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }
    catch (\PDOException $e)
    {           
        echo ('ERROR => ' . $e);        
    }       
} 
 
 
/**********************************************************************\
 Update user name. Returns null if nothing was changed.
\**********************************************************************/
function updateUserName($id, $username)
{      
    $pdo = initDatabase();  
       
    $sql = "UPDATE users SET username = :username WHERE id = :id ";
    $stmt = $pdo->prepare($sql);  
        
    $prepare = [
        'id' => $id,
        'username' => $username,  
    ];    
        
    try 
    {  
        if ($stmt->execute($prepare))
        {
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0)
            {
                return $stmt->rowCount();
            }
            else
            {
               return null; 
            }
        }  
        
        return null;               
    }
    catch (\PDOException $e)
    {           
        echo ('ERROR => ' . $e);
    }       
} 
 
  
 
/**********************************************************************\
 Debug helper function
\**********************************************************************/
function debugDump($obj)
{    
    echo "<pre>";
    print_r($obj);
    echo "</pre>";
}
 
 
 
 
 
