<?php
require_once 'config.php';


$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("database is not connected");

//function for showing pages
function showPage($page, $data = "")
{
    include("assets/pages/$page.php");
}



//for getting ids of chat users
function getActiveChatUserIds()
{
    global $db;


    $current_user_id = $_SESSION['userdata']['id'];

    /*

    $query = "SELECT from_user_id,to_user_id FROM messages WHERE to_user_id=$current_user_id || from_user_id=$current_user_id ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    $data =  mysqli_fetch_all($run, true); */

    $query = "SELECT from_user_id, to_user_id FROM messages WHERE to_user_id=? OR from_user_id=? ORDER BY id DESC";
    $sentencia = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($sentencia, "ii", $current_user_id, $current_user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    $data = mysqli_fetch_all($resultado, MYSQLI_ASSOC);


    $ids = array();
    foreach ($data as $ch) {
        if ($ch['from_user_id'] != $current_user_id && !in_array($ch['from_user_id'], $ids)) {
            $ids[] = $ch['from_user_id'];
        }

        if ($ch['to_user_id'] != $current_user_id && !in_array($ch['to_user_id'], $ids)) {
            $ids[] = $ch['to_user_id'];
        }
    }

    return $ids;
}




function getMessages($user_id)
{
    global $db;
    $current_user_id = $_SESSION['userdata']['id'];


    /*
    $query = "SELECT * FROM messages WHERE (to_user_id=$current_user_id && from_user_id=$user_id) || (from_user_id=$current_user_id && to_user_id=$user_id) ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return  mysqli_fetch_all($run, true);
*/


    $query = "SELECT * FROM messages WHERE (to_user_id=? AND from_user_id=?) OR (from_user_id=? AND to_user_id=?) ORDER BY id DESC";
    $sentencia = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($sentencia, "iiii", $current_user_id, $user_id, $current_user_id, $user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);

    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}



function sendMessage($user_id, $msg)
{
    global $db;

    /*
    $current_user_id = $_SESSION['userdata']['id'];
    $query = "INSERT INTO messages (from_user_id,to_user_id,msg) VALUES($current_user_id,$user_id,'$msg')";
    return mysqli_query($db, $query);
    */

    $current_user_id = $_SESSION['userdata']['id'];

    $query = "INSERT INTO messages (from_user_id, to_user_id, msg) VALUES (?, ?, ?)";
    $sentencia = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($sentencia, "iis", $current_user_id, $user_id, $msg);
    $resultado = mysqli_stmt_execute($sentencia);

    return $resultado;
}



function newMsgCount()
{
    global $db;
    $current_user_id = $_SESSION['userdata']['id'];

    /*
    $query = "SELECT COUNT(*) as 'row' FROM messages WHERE to_user_id=$current_user_id && read_status=0";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
    */

    $query = "SELECT COUNT(*) as 'row' FROM messages WHERE to_user_id=? AND read_status=0";
    $sentencia = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($sentencia, "i", $current_user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);


    return mysqli_fetch_assoc($resultado)['row'];
}




function updateMessageReadStatus($user_id)
{
    $cu_user_id = $_SESSION['userdata']['id'];
    global $db;

    /*
    $query = "UPDATE messages SET read_status=1 WHERE to_user_id=$cu_user_id && from_user_id=$user_id";
    return mysqli_query($db, $query);
*/

    $query = "UPDATE messages SET read_status=1 WHERE to_user_id=? AND from_user_id=?";
    $sentencia = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($sentencia, "ii", $cu_user_id, $user_id);
    $resultado = mysqli_stmt_execute($sentencia);
    return $resultado;
}



function gettime($date)
{
    return date('H:i - (F jS, Y )', strtotime($date));
}




function getAllMessages()
{
    $active_chat_ids = getActiveChatUserIds();
    $conversation = array();
    foreach ($active_chat_ids as $index => $id) {
        $conversation[$index]['user_id'] = $id;
        $conversation[$index]['messages'] = getMessages($id);
    }
    return $conversation;
}



//function for follow the user
function followUser($user_id)
{
    global $db;
    $cu = getUser($_SESSION['userdata']['id']);
    $current_user = $_SESSION['userdata']['id'];


    /*
    $query = "INSERT INTO follow_list(follower_id,user_id) VALUES($current_user,$user_id)";
    createNotification($cu['id'], $user_id, "started following you !");
    return mysqli_query($db, $query);
*/

    $query = "INSERT INTO follow_list (follower_id, user_id) VALUES (?, ?)";
    $sentencia = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($sentencia, "ii", $current_user, $user_id);
    $resultado = mysqli_stmt_execute($sentencia);

    createNotification($cu['id'], $user_id, "started following you !");

    return $resultado;
}






//function for blocking the user
function blockUser($blocked_user_id)
{
    global $db;
    $cu = getUser($_SESSION['userdata']['id']);
    $current_user = $_SESSION['userdata']['id'];


    /*
    $query = "INSERT INTO block_list(user_id,blocked_user_id) VALUES($current_user,$blocked_user_id)";

    createNotification($cu['id'], $blocked_user_id, "blocked you");
    $query2 = "DELETE FROM follow_list WHERE follower_id=$current_user && user_id=$blocked_user_id";
    mysqli_query($db, $query2);
    $query3 = "DELETE FROM follow_list WHERE follower_id=$blocked_user_id && user_id=$current_user";
    mysqli_query($db, $query3);

    return mysqli_query($db, $query);
    */

    $query = "INSERT INTO block_list (user_id, blocked_user_id) VALUES (?, ?)";
    $sentencia = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($sentencia, "ii", $current_user, $blocked_user_id);
    $resultado = mysqli_stmt_execute($sentencia);

    createNotification($cu['id'], $blocked_user_id, "blocked you");

    $query2 = "DELETE FROM follow_list WHERE follower_id=? AND user_id=?";
    $sentencia2 = mysqli_prepare($db, $query2);
    mysqli_stmt_bind_param($sentencia2, "ii", $current_user, $blocked_user_id);
    mysqli_stmt_execute($sentencia2);

    $query3 = "DELETE FROM follow_list WHERE follower_id=? AND user_id=?";
    $sentencia3 = mysqli_prepare($db, $query3);
    mysqli_stmt_bind_param($sentencia3, "ii", $blocked_user_id, $current_user);
    mysqli_stmt_execute($sentencia3);

    return $resultado;
}

//for unblocking the user
function unblockUser($user_id)
{
    global $db;
    $current_user = $_SESSION['userdata']['id'];

    /*
    $query = "DELETE FROM block_list WHERE user_id=$current_user && blocked_user_id=$user_id";
    createNotification($current_user, $user_id, "Unblocked you !");
    return mysqli_query($db, $query);
    */

    $sentencia = mysqli_prepare($db, "DELETE FROM block_list WHERE user_id=? && blocked_user_id=?");
    mysqli_stmt_bind_param($sentencia, "ii", $current_user, $user_id);

    createNotification($current_user, $user_id, "Unblocked you !");

    $resultado = mysqli_stmt_execute($sentencia);
    return  $resultado;
}

//function checkLikeStatus
function checkLikeStatus($post_id, $reaction)
{
    global $db;
    $current_user = $_SESSION['userdata']['id'];
    $react = $reaction;

    /*
    $query = "SELECT count(*) as 'row' FROM likes WHERE user_id=$current_user && post_id=$post_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
    */

    $sentencia = mysqli_prepare($db, "SELECT count(*) as 'row' FROM likes WHERE user_id=? && post_id=? && reaction=?");
    mysqli_stmt_bind_param($sentencia, "iis", $current_user, $post_id, $react);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado)['row'];
}

//function for like the post
function like($post_id, $reaction)
{
    global $db;
    $react = $reaction;


    /*
    $current_user = $_SESSION['userdata']['id'];
    $query = "INSERT INTO likes(post_id,user_id) VALUES($post_id,$current_user)";

    $poster_id = getPosterId($post_id);

    if ($poster_id != $current_user) {  
        createNotification($current_user, $poster_id, "liked your post !", $post_id);
    }
    return mysqli_query($db, $query);
    */

    $current_user = $_SESSION['userdata']['id'];
    $sentencia = mysqli_prepare($db, "INSERT INTO likes(post_id,user_id,reaction) VALUES(?,?,?)");
    mysqli_stmt_bind_param($sentencia, "iis", $post_id, $current_user, $react);


    $poster_id = getPosterId($post_id);

    if ($poster_id != $current_user) {
        createNotification($current_user, $poster_id, "liked your post !", $post_id);
    }

    return mysqli_stmt_execute($sentencia);
}




//function for creating comments
function addComment($post_id, $comment)
{
    global $db;



    /*
    $comment = mysqli_real_escape_string($db, $comment);
    $current_user = $_SESSION['userdata']['id'];
    $query = "INSERT INTO comments(user_id,post_id,comment) VALUES($current_user,$post_id,'$comment')";
    $poster_id = getPosterId($post_id);

    if ($poster_id != $current_user) {
        createNotification($current_user, $poster_id, "commented on your post", $post_id);
    }
    return mysqli_query($db, $query);
    */

    $comment = mysqli_real_escape_string($db, $comment);
    $current_user = $_SESSION['userdata']['id'];
    $sentencia = mysqli_prepare($db, "INSERT INTO comments(user_id, post_id, comment) VALUES(?, ?, ?)");
    mysqli_stmt_bind_param($sentencia, "iis", $current_user, $post_id, $comment);


    $poster_id = getPosterId($post_id);
    if ($poster_id != $current_user) {
        createNotification($current_user, $poster_id, "commented on your post", $post_id);
    }
    return mysqli_stmt_execute($sentencia);
}





//function for creating comments
function createNotification($from_user_id, $to_user_id, $msg, $post_id = 0)
{
    global $db;

    /*
    $query = "INSERT INTO notifications(from_user_id,to_user_id,message,post_id) VALUES($from_user_id,$to_user_id,'$msg',$post_id)";
    mysqli_query($db, $query);
    */

    $sentencia = mysqli_prepare($db, "INSERT INTO notifications(from_user_id, to_user_id, message, post_id) VALUES(?, ?, ?, ?)");
    mysqli_stmt_bind_param($sentencia, "iisi", $from_user_id, $to_user_id, $msg, $post_id);
    mysqli_stmt_execute($sentencia);
}



//function for getting likes count
function getComments($post_id)
{
    global $db;


    /*
    $query = "SELECT * FROM comments WHERE post_id=$post_id ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */

    $sentencia = mysqli_prepare($db, "SELECT * FROM comments WHERE post_id=? ORDER BY id DESC");
    mysqli_stmt_bind_param($sentencia, "i", $post_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);

    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}

//get notifications

function getNotifications()
{
    $cu_user_id = $_SESSION['userdata']['id'];

    global $db;
    /*
    $query = "SELECT * FROM notifications WHERE to_user_id=$cu_user_id ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */

    $sentencia = mysqli_prepare($db, "SELECT * FROM notifications WHERE to_user_id=? ORDER BY id DESC");
    mysqli_stmt_bind_param($sentencia, "i", $cu_user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}



function getUnreadNotificationsCount()
{
    $cu_user_id = $_SESSION['userdata']['id'];

    global $db;

    /*
    $query = "SELECT count(*) as 'row' FROM notifications WHERE to_user_id=$cu_user_id && read_status=0 ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
    */

    $sentencia = mysqli_prepare($db, "SELECT count(*) as 'row' FROM notifications WHERE to_user_id=? && read_status=0 ORDER BY id DESC");
    mysqli_stmt_bind_param($sentencia, "i", $cu_user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado)['row'];
}



function show_time($time)
{
    return '<time style="font-size:small" class="timeago text-muted text-small" datetime="' . $time . '"></time>';
}





function setNotificationStatusAsRead()
{
    $cu_user_id = $_SESSION['userdata']['id'];
    global $db;

    /*
    $query = "UPDATE notifications SET read_status=1 WHERE to_user_id=$cu_user_id";
    return mysqli_query($db, $query);
    */

    $sentencia = mysqli_prepare($db, "UPDATE notifications SET read_status=1 WHERE to_user_id=?");
    mysqli_stmt_bind_param($sentencia, "i", $cu_user_id);
    return mysqli_stmt_execute($sentencia);
}



//function for getting likes count
function getLikes($post_id)
{
    global $db;

    /*
    $query = "SELECT * FROM likes WHERE post_id=$post_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */

    $sentencia = mysqli_prepare($db, "SELECT * FROM likes WHERE post_id=?");
    mysqli_stmt_bind_param($sentencia, "i", $post_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}



//function for unlike the post
function unlike($post_id, $reaction)
{
    global $db;
    $current_user = $_SESSION['userdata']['id'];

    /*
    $query = "DELETE FROM likes WHERE user_id=$current_user && post_id=$post_id";

    $poster_id = getPosterId($post_id);
    if ($poster_id != $current_user) {
        createNotification($current_user, $poster_id, "unliked your post !", $post_id);
    }
    return mysqli_query($db, $query);
    */

    $sentencia = mysqli_prepare($db, "DELETE FROM likes WHERE user_id=? && post_id=? && reaction=?");
    mysqli_stmt_bind_param($sentencia, "iis", $current_user, $post_id, $reaction);


    $poster_id = getPosterId($post_id);
    if ($poster_id != $current_user) {
        createNotification($current_user, $poster_id, "unliked your post !", $post_id);
    }

    return mysqli_stmt_execute($sentencia);
}




function unfollowUser($user_id)
{
    global $db;
    $current_user = $_SESSION['userdata']['id'];

    /*
    $query = "DELETE FROM follow_list WHERE follower_id=$current_user && user_id=$user_id";

    createNotification($current_user, $user_id, "Unfollowed you !");
    return mysqli_query($db, $query);
    */

    $sentencia = mysqli_prepare($db, "DELETE FROM follow_list WHERE follower_id=? && user_id=?");
    mysqli_stmt_bind_param($sentencia, "ii", $current_user, $user_id);


    createNotification($current_user, $user_id, "Unfollowed you !");
    return mysqli_stmt_execute($sentencia);
}


//function for show errors
function showError($field)
{
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        if (isset($error['field']) && $field == $error['field']) {
?>
            <div class="alert alert-danger my-2" role="alert">
                <?= $error['msg'] ?>
            </div>
<?php
        }
    }
}


//function for show prevformdata
function showFormData($field)
{
    if (isset($_SESSION['formdata'])) {
        $formdata = $_SESSION['formdata'];
        return $formdata[$field];
    }
}


//for checking duplicate email
function isEmailRegistered($email)
{
    global $db;


    /*
    $query = "SELECT count(*) as 'row' FROM users WHERE email='$email'";
    $run = mysqli_query($db, $query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row']; 
    */


    $sentencia = mysqli_prepare($db, "SELECT count(*) as 'row' FROM users WHERE email=?");
    mysqli_stmt_bind_param($sentencia, "s", $email);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado)['row'];
}







// verifica si es usuario ya esta registrado
function isUsernameRegistered($username)
{
    global $db;

    /*

        global $db;
    $query = "SELECT count(*) as 'row' FROM users WHERE username='$username'";
    $run = mysqli_query($db, $query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];

    */

    $sentencia = mysqli_prepare($db, "SELECT count(*) as 'row' FROM users WHERE username=?");
    mysqli_stmt_bind_param($sentencia, "s", $username);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado)['row'];
}




//for checking duplicate username by other
function isUsernameRegisteredByOther($username)
{
    /*
    global $db;
    $user_id = $_SESSION['userdata']['id'];
    $query = "SELECT count(*) as 'row' FROM users WHERE username='$username' && id!=$user_id";
    $run = mysqli_query($db, $query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];
    */

    global $db;
    $user_id = $_SESSION['userdata']['id'];

    $sentencia = mysqli_prepare($db, "SELECT count(*) as 'row' FROM users WHERE username=? AND id!=?");
    mysqli_stmt_bind_param($sentencia, "si", $username, $user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado)['row'];
}

//for validating the signup form
function validateSignupForm($form_data)
{
    $response = array();
    $response['status'] = true;

    if (!$form_data['password']) {
        $response['msg'] = "¡.llene el campo contraseña!";
        $response['status'] = false;
        $response['field'] = 'password';
    }

    if (!$form_data['username']) {
        $response['msg'] = "¡.llene el campo usuario!";
        $response['status'] = false;
        $response['field'] = 'username';
    }

    if (!$form_data['email']) {
        $response['msg'] = "¡.llene el campo email.!";
        $response['status'] = false;
        $response['field'] = 'email';
    }

    if (!$form_data['last_name']) {
        $response['msg'] = "¡.llene el campo apellido.!";
        $response['status'] = false;
        $response['field'] = 'last_name';
    }
    if (!$form_data['first_name']) {
        $response['msg'] = "¡.llene el campo nombre.!";
        $response['status'] = false;
        $response['field'] = 'first_name';
    }
    if (isEmailRegistered($form_data['email'])) {
        $response['msg'] = "¡.correo ya registrado.!";
        $response['status'] = false;
        $response['field'] = 'email';
    }
    if (isUsernameRegistered($form_data['username'])) {
        $response['msg'] = "¡.usuario ya registrado.!";
        $response['status'] = false;
        $response['field'] = 'username';
    }

    return $response;
}


//for validate the login form
function validateLoginForm($form_data)
{
    $response = array();
    $response['status'] = true;
    $blank = false;

    if (!$form_data['password']) {
        $response['msg'] = "¡.llene el campo contraseña.!";
        $response['status'] = false;
        $response['field'] = 'password';
        $blank = true;
    }

    if (!$form_data['username_email']) {
        $response['msg'] = "¡.llene el campo email.!";
        $response['status'] = false;
        $response['field'] = 'username_email';
        $blank = true;
    }

    if (!$blank && !checkUser($form_data)['status']) {
        $response['msg'] = "usuario y/o contraseña inválidos";
        $response['status'] = false;
        $response['field'] = 'checkuser';
    } else {
        $response['user'] = checkUser($form_data)['user'];
    }






    return $response;
}


//for checking the user
function checkUser($login_data)
{

    /*
    global $db;
    $username_email = $login_data['username_email'];
    $password = $login_data['password'];

    $query = "SELECT * FROM users WHERE (email='$username_email' || username='$username_email')";
    $run = mysqli_query($db, $query);
    $data['user'] = mysqli_fetch_assoc($run) ?? array();

    if (count($data['user']) > 0 && password_verify($password, $data['user']['password'])) {
        $data['status'] = true;
    } else {
        $data['status'] = false;
    }

    return $data;

    */


    global $db;
    $username_email = $login_data['username_email'];
    $password = $login_data['password'];

    $sentencia = mysqli_prepare($db, "SELECT * FROM users WHERE (email=? OR username=?)");
    mysqli_stmt_bind_param($sentencia, "ss", $username_email, $username_email);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    $data['user'] = mysqli_fetch_assoc($resultado) ?? array();

    if (count($data['user']) > 0 && password_verify($password, $data['user']['password'])) {
        $data['status'] = true;
    } else {
        $data['status'] = false;
    }

    return $data;
}


//for getting userdata by id
function getUser($user_id)
{
    /*
    global $db;
    $query = "SELECT * FROM users WHERE id=$user_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run);
    */
    global $db;

    $sentencia = mysqli_prepare($db, "SELECT * FROM users WHERE id=?");
    mysqli_stmt_bind_param($sentencia, "i", $user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado);
}


//for filtering the suggestion list
function filterFollowSuggestion()
{
    $list = getFollowSuggestions();
    $filter_list  = array();
    foreach ($list as $user) {
        if (!checkFollowStatus($user['id']) && !checkBS($user['id']) && count($filter_list) < 5) {
            $filter_list[] = $user;
        }
    }

    return $filter_list;
}

//for checking the user is followed by current user or not
function checkFollowStatus($user_id)
{
    /*
    global $db;
    $current_user = $_SESSION['userdata']['id'];
    $query = "SELECT count(*) as 'row' FROM follow_list WHERE follower_id=$current_user && user_id=$user_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
    */

    global $db;
    $current_user = $_SESSION['userdata']['id'];

    $sentencia = mysqli_prepare($db, "SELECT count(*) as 'row' FROM follow_list WHERE follower_id=? AND user_id=?");
    mysqli_stmt_bind_param($sentencia, "ii", $current_user, $user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado)['row'];
}



//for checking the user is followed by current user or not
function checkBlockStatus($current_user, $user_id)
{
    /*
    global $db;

    $query = "SELECT count(*) as 'row' FROM block_list WHERE user_id=$current_user && blocked_user_id=$user_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
    */

    global $db;

    $sentencia = mysqli_prepare($db, "SELECT count(*) as 'row' FROM block_list WHERE user_id=? AND blocked_user_id=?");
    mysqli_stmt_bind_param($sentencia, "ii", $current_user, $user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado)['row'];
}




function checkBS($user_id)
{
    /*
    global $db;
    $current_user = $_SESSION['userdata']['id'];
    $query = "SELECT count(*) as 'row' FROM block_list WHERE (user_id=$current_user && blocked_user_id=$user_id) || (user_id=$user_id && blocked_user_id=$current_user)";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['row'];
    */
    global $db;
    $current_user = $_SESSION['userdata']['id'];

    $sentencia = mysqli_prepare($db, "SELECT count(*) as 'row' FROM block_list WHERE (user_id=? AND blocked_user_id=?) OR (user_id=? AND blocked_user_id=?)");
    mysqli_stmt_bind_param($sentencia, "iiii", $current_user, $user_id, $user_id, $current_user);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_assoc($resultado)['row'];
}
//

//for getting users for follow suggestions
function getFollowSuggestions()
{
    /*
    global $db;

    $current_user = $_SESSION['userdata']['id'];
    $query = "SELECT * FROM users WHERE id!=$current_user";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */
    global $db;

    $current_user = $_SESSION['userdata']['id'];

    //$sentencia = mysqli_prepare($db, "SELECT * FROM users WHERE id IN (SELECT user_id FROM follow_list WHERE follower_id = ?)");
    $sentencia = mysqli_prepare($db, "SELECT * FROM users WHERE id!=?");


    mysqli_stmt_bind_param($sentencia, "i", $current_user);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}



//get followers count
function getFollowers($user_id)
{
    /*
    global $db;
    $query = "SELECT * FROM follow_list WHERE user_id=$user_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */
    global $db;

    $sentencia = mysqli_prepare($db, "SELECT * FROM follow_list WHERE user_id=?");
    mysqli_stmt_bind_param($sentencia, "i", $user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}



//get followers count
function getFollowing($user_id)
{
    /*
    global $db;
    $query = "SELECT * FROM follow_list WHERE follower_id=$user_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */

    global $db;

    $sentencia = mysqli_prepare($db, "SELECT * FROM follow_list WHERE follower_id=?");
    mysqli_stmt_bind_param($sentencia, "i", $user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}

//for getting posts by id
function getPostById($user_id)
{
    /*
    global $db;
    $query = "SELECT * FROM posts WHERE user_id=$user_id ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */
    global $db;
    $sentencia = mysqli_prepare($db, "SELECT * FROM posts WHERE user_id=? ORDER BY id DESC");
    mysqli_stmt_bind_param($sentencia, "i", $user_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}

//for getting post
function getPosterId($post_id)
{
    /*
    global $db;
    $query = "SELECT user_id FROM posts WHERE id=$post_id";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run)['user_id'];
    */

    global $db;

    $sentencia = mysqli_prepare($db, "SELECT user_id FROM posts WHERE id=?");
    mysqli_stmt_bind_param($sentencia, "i", $post_id);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);
    $fila = mysqli_fetch_assoc($resultado);

    return $fila['user_id'];
}

//for searching the users
function searchUser($keyword)
{
    /*
    global $db;
    $query = "SELECT * FROM users WHERE username LIKE '%" . $keyword . "%' || (first_name LIKE '%" . $keyword . "%' || last_name LIKE '%" . $keyword . "%') LIMIT 5";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */

    global $db;

    $sentencia = mysqli_prepare($db, "SELECT * FROM users WHERE username LIKE ? OR (first_name LIKE ? OR last_name LIKE ?) LIMIT 5");

    $keyword = '%' . $keyword . '%';
    mysqli_stmt_bind_param($sentencia, "sss", $keyword, $keyword, $keyword);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);

    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}




//for getting userdata by username
function getUserByUsername($username)
{
    /*
    global $db;
    $query = "SELECT * FROM users WHERE username='$username'";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_assoc($run);
    */

    global $db;

    $sentencia = mysqli_prepare($db, "SELECT * FROM users WHERE username=?");
    mysqli_stmt_bind_param($sentencia, "s", $username);
    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);

    return mysqli_fetch_assoc($resultado);;
}



//for getting posts
function getPost()
{
    /*
    global $db;
    $query = "SELECT users.id as uid,posts.id,posts.user_id,posts.post_img,posts.post_text,posts.created_at,users.first_name,users.last_name,users.username,users.profile_pic FROM posts JOIN users ON users.id=posts.user_id ORDER BY id DESC";

    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
    */

    global $db;

    $sentencia = mysqli_prepare($db, "SELECT users.id as uid,posts.id,posts.user_id,posts.post_img,posts.post_text,posts.created_at,users.first_name,users.last_name,users.username,users.profile_pic FROM posts JOIN users ON users.id=posts.user_id ORDER BY id DESC");

    mysqli_stmt_execute($sentencia);
    $resultado = mysqli_stmt_get_result($sentencia);

    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}



// eliminar post

function deletePost($post_id)
{
    /*
    global $db;
    $user_id = $_SESSION['userdata']['id'];
    $dellike = "DELETE FROM likes WHERE post_id=$post_id && user_id=$user_id";
    mysqli_query($db, $dellike);
    $delcom = "DELETE FROM comments WHERE post_id=$post_id && user_id=$user_id";
    mysqli_query($db, $delcom);
    $not = "UPDATE notifications SET read_status=2 WHERE post_id=$post_id && to_user_id=$user_id";
    mysqli_query($db, $not);


    $query = "DELETE FROM posts WHERE id=$post_id";
    return mysqli_query($db, $query);
    */

    global $db;
    $user_id = $_SESSION['userdata']['id'];

    $dellike = "DELETE FROM likes WHERE post_id=? AND user_id=?";
    $stmt1 = mysqli_prepare($db, $dellike);
    mysqli_stmt_bind_param($stmt1, "ii", $post_id, $user_id);
    mysqli_stmt_execute($stmt1);

    $delcom = "DELETE FROM comments WHERE post_id=? AND user_id=?";
    $stmt2 = mysqli_prepare($db, $delcom);
    mysqli_stmt_bind_param($stmt2, "ii", $post_id, $user_id);
    mysqli_stmt_execute($stmt2);

    $not = "UPDATE notifications SET read_status=2 WHERE post_id=? AND to_user_id=?";
    $stmt3 = mysqli_prepare($db, $not);
    mysqli_stmt_bind_param($stmt3, "ii", $post_id, $user_id);
    mysqli_stmt_execute($stmt3);

    $query = "DELETE FROM posts WHERE id=?";
    $stmt4 = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt4, "i", $post_id);
    return mysqli_stmt_execute($stmt4);
}



// eliminar comentario

function deleteComment($post_id, $commenta)
{
    /* original comentada
    global $db;
    $user_id = $_SESSION['userdata']['id'];

    $delcom = "DELETE FROM comments WHERE post_id=$post_id && user_id=$user_id && comment='" . $commenta . "'";
    echo $delcom;
    mysqli_query($db, $delcom);
    return  mysqli_query($db, $delcom);
    */

    /* actualizar la notifiacion de que se borro un comentario en desarrollo beta*/
    /*
    $not = "UPDATE notifications SET read_status=2 WHERE post_id=$post_id && to_user_id=$user_id";
    mysqli_query($db, $not);
    */

    global $db;
    $user_id = $_SESSION['userdata']['id'];

    $delcom = "DELETE FROM comments WHERE post_id=? AND user_id=? AND comment=?";
    $stmt = mysqli_prepare($db, $delcom);
    mysqli_stmt_bind_param($stmt, "iis", $post_id, $user_id, $commenta);
    return mysqli_stmt_execute($stmt);
}





//for getting posts dynamically
function filterPosts()
{
    $list = getPost();
    $filter_list  = array();
    foreach ($list as $post) {
        if (checkFollowStatus($post['user_id']) || $post['user_id'] == $_SESSION['userdata']['id']) {
            $filter_list[] = $post;
        }
    }

    return $filter_list;
}



//for creating new user
function createUser($data)
{
    /*

    global $db;
    $first_name = mysqli_real_escape_string($db, $data['first_name']);
    $last_name = mysqli_real_escape_string($db, $data['last_name']);
    $gender = $data['gender'];
    $email = mysqli_real_escape_string($db, $data['email']);
    $username = mysqli_real_escape_string($db, $data['username']);
    $password = mysqli_real_escape_string($db, $data['password']);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $acstus = 1;

    $query = "INSERT INTO users(first_name, last_name, gender, email, username, password, ac_status) ";
    $query .= "VALUES ('$first_name', '$last_name', $gender, '$email', '$username', '$password', $acstus)";
    return mysqli_query($db, $query);
    */

    global $db;
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $gender = $data['gender'];
    $email = $data['email'];
    $username = $data['username'];
    $password = mysqli_real_escape_string($db, $data['password']);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $ac_status = 1;

    $query = "INSERT INTO users(first_name, last_name, gender, email, username, password, ac_status) ";
    $query .= "VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ssisssi", $first_name, $last_name, $gender, $email, $username, $password, $ac_status);

    return mysqli_stmt_execute($stmt);
}




//function for verify email
function verifyEmail($email)
{
    /*
    global $db;
    $query = "UPDATE users SET ac_status=1 WHERE email='$email'";
    return mysqli_query($db, $query);
    */

    /*
    global $db;
    $query = "UPDATE users SET ac_status=1 WHERE email=?";

    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);

    return mysqli_stmt_execute($stmt);
    */
    global $db;
    $query = "UPDATE users SET ac_status=1 WHERE email=?";

    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);

    return mysqli_stmt_execute($stmt);
}




//function for verify email
function resetPassword($email, $password)
{
    /*
    global $db;
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password='$password' WHERE email='$email'";
    return mysqli_query($db, $query);
    */

    global $db;
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password=? WHERE email=?";

    $sentencia = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($sentencia, "ss", $password, $email);

    return mysqli_stmt_execute($sentencia);
}




//for validating update form
function validateUpdateForm($form_data, $image_data)
{
    $response = array();
    $response['status'] = true;


    if (!$form_data['username']) {
        $response['msg'] = "¡.llene el campo usuario.!";
        $response['status'] = false;
        $response['field'] = 'username';
    }

    if (!$form_data['last_name']) {
        $response['msg'] = "¡.llene el campo de apellidos!";
        $response['status'] = false;
        $response['field'] = 'last_name';
    }
    if (!$form_data['first_name']) {
        $response['msg'] = "¡.llene el campo de nombre!";
        $response['status'] = false;
        $response['field'] = 'first_name';
    }

    if (isUsernameRegisteredByOther($form_data['username'])) {
        $response['msg'] = $form_data['username'] . " is already registered";
        $response['status'] = false;
        $response['field'] = 'username';
    }

    if ($image_data['name']) {
        $image = basename($image_data['name']);
        $type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $size = $image_data['size'] / 1000;

        if ($type != 'jpg' && $type != 'jpeg' && $type != 'png') {
            $response['msg'] = "only jpg,jpeg,png images are allowed";
            $response['status'] = false;
            $response['field'] = 'profile_pic';
        }

        if ($size > 4500) {
            $response['msg'] = "Sube una imagen de menos de 4.5 MB";
            $response['status'] = false;
            $response['field'] = 'profile_pic';
        }
    }

    return $response;
}


//function for updating profile

function updateProfile($data, $imagedata)
{
    /*
    global $db;
    $first_name = mysqli_real_escape_string($db, $data['first_name']);
    $last_name = mysqli_real_escape_string($db, $data['last_name']);
    $username = mysqli_real_escape_string($db, $data['username']);
    $password = mysqli_real_escape_string($db, $data['password']);

    if (!$data['password']) {
        $password = $_SESSION['userdata']['password'];
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $_SESSION['userdata']['password'] = $password;
    }

    $profile_pic = "";
    if ($imagedata['name']) {
        $image_name = time() . basename($imagedata['name']);
        $image_dir = "../images/profile/$image_name";
        move_uploaded_file($imagedata['tmp_name'], $image_dir);
        $profile_pic = ", profile_pic='$image_name'";
    }

    $query = "UPDATE users SET first_name = '$first_name', last_name='$last_name', username='$username', password='$password' $profile_pic WHERE id=" . $_SESSION['userdata']['id'];
    return mysqli_query($db, $query);
    */

    global $db;
    $first_name = mysqli_real_escape_string($db, $data['first_name']);
    $last_name = mysqli_real_escape_string($db, $data['last_name']);
    $username = mysqli_real_escape_string($db, $data['username']);
    $password = mysqli_real_escape_string($db, $data['password']);

    if (!$data['password']) {
        $password = $_SESSION['userdata']['password'];
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $_SESSION['userdata']['password'] = $password;
    }

    $profile_pic = "";
    $image_name = "";
    if ($imagedata['name']) {
        $image_name = time() . basename($imagedata['name']);
        $image_dir = "../images/profile/$image_name";
        move_uploaded_file($imagedata['tmp_name'], $image_dir);
        $profile_pic = ", profile_pic=?";
    }

    $query = "UPDATE users SET first_name=?, last_name=?, username=?, password=? $profile_pic WHERE id=?";

    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ssssi", $first_name, $last_name, $username, $password, $_SESSION['userdata']['id']);

    if ($profile_pic) {
        mysqli_stmt_bind_param($stmt, "s", $image_name);
    }

    return mysqli_stmt_execute($stmt);
}


//for validating add post form
function validatePostImage($image_data)
{
    $response = array();
    $response['status'] = true;


    if (!$image_data['name']) {
        $response['msg'] = "no ha seleccionado una imagen";
        $response['status'] = true;
        $response['field'] = 'post_img';
    }



    if ($image_data['name']) {
        $image = basename($image_data['name']);
        $type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $size = $image_data['size'] / 1000;

        if ($type != 'jpg' && $type != 'jpeg' && $type != 'png') {
            $response['msg'] = "only jpg,jpeg,png images are allowed";
            $response['status'] = false;
            $response['field'] = 'post_img';
        }

        if ($size > 6000) {
            $response['msg'] = "Subir imagen de menos de 3 MB";
            $response['status'] = false;
            $response['field'] = 'post_img';
        }
    }

    return $response;
}







//for creating new user
function createPost($text, $image, $es)
{
    /*
    global $db;
    $post_text = mysqli_real_escape_string($db, $text['post_text']);
    $user_id = $_SESSION['userdata']['id'];

    $image_name = time() . basename($image['name']);
    $image_dir = "../images/posts/$image_name";
    move_uploaded_file($image['tmp_name'], $image_dir);


    $query = "INSERT INTO posts(user_id,post_text,post_img)";
    $query .= "VALUES ($user_id,'$post_text','$image_name')";
    return mysqli_query($db, $query);
    */
    $shared_post_id = 0;


    /*
    if (is_numeric($es)) {
        echo "La variable \$es es un número.";
        sleep(20);
    } else {
        echo "La variable \$es no es un número.";
    } */




    global $db;
    $post_text = mysqli_real_escape_string($db, $text['post_text']);
    $user_id = $_SESSION['userdata']['id'];

    //si image es arreglo es agregar post nuevo, si es una cadena vacia es compartir post
    if (is_array($image)) {
        echo "El parámetro es un arreglo.";

        $image_name = time() . basename($image['name']);
        $image_dir = "../images/posts/$image_name";
        move_uploaded_file($image['tmp_name'], $image_dir);
        $shared_post_id = 0;
    } elseif (is_string($image)) {
        echo "El parámetro es una cadena de texto.";
        $shared_post_id = $es;
        $image_name = '';
    }



    $query = "INSERT INTO posts(user_id, post_text, post_img,shared_post_id) VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "issi", $user_id, $post_text, $image_name, $shared_post_id);

    return mysqli_stmt_execute($stmt);
}




// para agregar  mostrar historial del lecturas
function showReads()
{

    /*
    global $db;

    $query = "SELECT * FROM readsSensor";
    return mysqli_query($db, $query);
    */

    global $db;
    $query = "SELECT * FROM readsSensor";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


// para saber si una publicacion fue compartida

function showPostShared($post_id)
{
    /*
    global $db;
    $query = "UPDATE users SET ac_status=1 WHERE email='$email'";
    return mysqli_query($db, $query);
    */

    /*
    global $db;
    $query = "UPDATE users SET ac_status=1 WHERE email=?";

    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);

    return mysqli_stmt_execute($stmt);
    */
    global $db;
    // $query = "SELECT * FROM posts WHERE id = (SELECT shared_post_id FROM posts WHERE user_id=? AND shared_post_id !=0)";
    $query = "SELECT * FROM posts WHERE id=? AND shared_post_id !=0";

    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $post_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}


function showPostSharedContent($post_id)
{

    global $db;
    $query = "SELECT * FROM posts WHERE id=?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $post_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}


function showPostSharedUser($id_userS)
{

    global $db;
    $query = "SELECT * FROM users WHERE id=?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_userS);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}





?>