<?php
define('IN_MANAGER_MODE', 'true');
define('MODX_API_MODE', true);
include('./index.php');
$modx->loadExtension('phpass');

// パスワード変更処理
if(isset($_POST['mode']) && $_POST['mode'] === 'pw_update') {
    $users = getUsers();
    foreach ($users as $user) {
        // 除外処理
        if(!empty($_POST['exclusion']) && in_array($user['id'], $_POST['exclusion'])) {
            continue;
        }

        // パスワード変更
        $_password = genPassword(32);
        updateNewHash($user['username'], $_password);

        echo 'ユーザ名：' . $user['username'] . '<br>' . PHP_EOL;
        echo 'パスワード：' . $_password . '<br>' . PHP_EOL;
        echo '<br>' . PHP_EOL;
    }
}

function updateNewHash($username,$password) {
    global $modx;

    $field = array();
    $field['password'] = $modx->phpass->HashPassword($password);
    $modx->db->update($field, '[+prefix+]manager_users', "username='{$username}'");
}

function getUsers() {
    global $modx;
    $rs = $modx->db->select('id, username, password','[+prefix+]manager_users',"");
    while ($row = $modx->db->getRow($rs))
    {
        $users[] = $row;
    }
    return $users;
}

function genPassword($length = 32)
{
    return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MODX パスワード一括返還</title>
</head>
<body>
パスワードの変更を除外するユーザを選択してください。
<form action="" method="POST">
<table>
<thead>
    <tr>
        <th>除外</th>
        <th>ID</th>
        <th>username</th>
    </tr>
</thead>
<tbody>
<?php
$users = getUsers();
foreach ($users as $user) {
    printf(
        '<tr><td><input type="checkbox" name="exclusion[]" value="%s"></td><td>%s</td><td>%s</td></tr>',
        $user['id'],
        $user['id'],
        $user['username']
    );
}
?>
</tbody>
</table>
<input type="hidden" name="mode" value="pw_update">
<input type="submit" value="パスワード一括変更">
</form>
</body>
</html>
